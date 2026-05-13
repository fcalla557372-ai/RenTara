<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    private array $reportTypes = [
        'overall' => 'Overall Business Report',
        'revenue' => 'Revenue Summary',
        'car_type' => 'Revenue by Car Type',
        'payment_method' => 'Payment Method Mix',
        'booking_status' => 'Booking Status Overview',
        'customer' => 'Customer Revenue',
    ];

    public function index(Request $request)
    {
        $reportType = $this->validReportType($request->input('report_type'));
        $query = $this->filteredBookings($request);

        $filteredBookings = (clone $query)->latest()->get();
        $bookings = $query->latest()->paginate(10)->withQueryString();
        $paidBookings = $filteredBookings->whereIn('payment_status', ['Partial Payment', 'Payment Confirmed', 'Completed']);

        $summarySections = $this->summarySections($reportType, $filteredBookings, $paidBookings);
        $totalRevenue = $paidBookings->sum('amount_paid');
        $totalBookings = $filteredBookings->count();
        $averageRevenue = $paidBookings->count() ? $totalRevenue / $paidBookings->count() : 0;
        $outstandingBalances = $filteredBookings->where('payment_status', 'Completed')->sum('remaining_balance');
        $carTypes = Car::withTrashed()->distinct()->orderBy('type')->pluck('type');

        return view('admin.report', [
            'bookings' => $bookings,
            'summarySections' => $summarySections,
            'reportTypes' => $this->reportTypes,
            'reportType' => $reportType,
            'carTypes' => $carTypes,
            'totalRevenue' => $totalRevenue,
            'totalBookings' => $totalBookings,
            'averageRevenue' => $averageRevenue,
            'outstandingBalances' => $outstandingBalances,
        ]);
    }

    public function export(Request $request)
    {
        $reportType = $this->validReportType($request->input('report_type'));
        $filteredBookings = $this->filteredBookings($request)->latest()->get();
        $paidBookings = $filteredBookings->whereIn('payment_status', ['Partial Payment', 'Payment Confirmed', 'Completed']);
        $summarySections = $this->summarySections($reportType, $filteredBookings, $paidBookings);

        $pdf = $this->buildReportPdf(
            $filteredBookings,
            $summarySections,
            $this->reportTypes[$reportType],
            $paidBookings->sum('amount_paid'),
            $request
        );
        $filename = 'rentara_' . $reportType . '_report_' . now()->format('Ymd_His') . '.pdf';

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    private function filteredBookings(Request $request): Builder
    {
        return Booking::with(['user', 'car', 'payment'])
            ->when($request->filled('start_date'), fn($query) => $query->where('pickup_date', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($query) => $query->where('pickup_date', '<=', $request->end_date))
            ->when($request->filled('payment_status'), fn($query) => $query->whereHas('payment', fn($q) => $q->where('payment_status', $request->payment_status)))
            ->when($request->filled('payment_method'), fn($query) => $query->whereHas('payment', fn($q) => $q->where('payment_method', $request->payment_method)))
            ->when($request->filled('car_type'), fn($query) => $query->whereHas('car', fn($car) => $car->where('type', $request->car_type)));
    }

    private function validReportType(?string $reportType): string
    {
        return array_key_exists($reportType, $this->reportTypes) ? $reportType : 'overall';
    }

    private function summarySections(string $reportType, Collection $bookings, Collection $paidBookings): Collection
    {
        if ($reportType !== 'overall') {
            return collect([
                [
                    'title' => $this->reportTypes[$reportType],
                    'rows' => $this->summaryRows($reportType, $bookings, $paidBookings),
                ],
            ]);
        }

        return collect(['revenue', 'car_type', 'payment_method', 'booking_status', 'customer'])
            ->map(fn($type) => [
                'title' => $this->reportTypes[$type],
                'rows' => $this->summaryRows($type, $bookings, $paidBookings),
            ]);
    }

    private function summaryRows(string $reportType, Collection $bookings, Collection $paidBookings): Collection
    {
        return match ($reportType) {
            'car_type' => $bookings->groupBy(fn($booking) => $booking->car->type)->map(fn($group, $type) => [
                'label' => $type,
                'bookings' => $group->count(),
                'revenue' => $group->whereIn('payment_status', ['Partial Payment', 'Payment Confirmed', 'Completed'])->sum('amount_paid'),
                'detail' => $group->where('payment_status', 'Completed')->count() . ' completed',
            ])->values(),

            'payment_method' => $bookings->groupBy('payment_method')->map(fn($group, $method) => [
                'label' => $method,
                'bookings' => $group->count(),
                'revenue' => $group->whereIn('payment_status', ['Partial Payment', 'Payment Confirmed', 'Completed'])->sum('amount_paid'),
                'detail' => round(($group->count() / max(1, $bookings->count())) * 100, 1) . '% of bookings',
            ])->values(),

            'booking_status' => $bookings->groupBy('payment_status')->map(fn($group, $status) => [
                'label' => $status,
                'bookings' => $group->count(),
                'revenue' => $group->whereIn('payment_status', ['Partial Payment', 'Payment Confirmed', 'Completed'])->sum('amount_paid'),
                'detail' => round(($group->count() / max(1, $bookings->count())) * 100, 1) . '% of total',
            ])->values(),

            'customer' => $bookings->groupBy('user_id')->map(fn($group) => [
                'label' => $group->first()->user->name,
                'bookings' => $group->count(),
                'revenue' => $group->whereIn('payment_status', ['Partial Payment', 'Payment Confirmed', 'Completed'])->sum('amount_paid'),
                'detail' => $group->first()->user->email,
            ])->sortByDesc('revenue')->values(),

            default => $paidBookings->groupBy(fn($booking) => $booking->pickup_date->format('Y-m-d'))->map(fn($group, $date) => [
                'label' => $date,
                'bookings' => $group->count(),
                'revenue' => $group->sum('amount_paid'),
                'detail' => $group->pluck('car.type')->unique()->count() . ' car type(s)',
            ])->sortByDesc('label')->values(),
        };
    }

    private function buildReportPdf(
        Collection $bookings,
        Collection $summarySections,
        string $reportTitle,
        float $totalRevenue,
        Request $request
    ): string {
        $pages = [];
        $lines = [
            ['RenTara - ' . $reportTitle, 18, true],
            ['Generated: ' . now()->format('M d, Y h:i A'), 9, false],
            ['Date range: ' . ($request->start_date ?: 'Any') . ' to ' . ($request->end_date ?: 'Any'), 9, false],
            ['Filters: Status=' . ($request->payment_status ?: 'Any') . ', Payment=GCash, Car Type=' . ($request->car_type ?: 'Any'), 8, false],
            ['', 8, false],
        ];

        foreach ($summarySections as $section) {
            $lines[] = ['', 8, false];
            $lines[] = [$section['title'], 12, true];

            if ($section['rows']->isEmpty()) {
                $lines[] = ['No summary data found.', 8, false];
                continue;
            }

            foreach ($section['rows'] as $row) {
                $lines[] = [
                    $row['label'] . ' | Bookings: ' . $row['bookings'] . ' | Revenue: PHP ' . number_format($row['revenue'], 2) . ' | ' . $row['detail'],
                    8,
                    false,
                ];
            }
        }

        $lines[] = ['Total Revenue: PHP ' . number_format($totalRevenue, 2), 11, true];
        $lines[] = ['', 8, false];
        $lines[] = ['Transactions', 12, true];
        $lines[] = ['Date | Customer | Type | Amount | Payment | Status', 8, true];

        foreach ($bookings as $booking) {
            $lines[] = [
                implode(' | ', [
                    $booking->pickup_date->format('Y-m-d'),
                    $booking->user->name,
                    $booking->car->type,
                    'PHP ' . number_format($booking->total_amount, 2),
                    $booking->payment_method,
                    $booking->payment_status,
                ]),
                8,
                false,
            ];
        }

        if ($bookings->isEmpty()) {
            $lines[] = ['No transactions found.', 10, false];
        }

        foreach (array_chunk($lines, 42) as $chunk) {
            $pages[] = $this->buildPdfPageContent($chunk);
        }

        return $this->assemblePdf($pages);
    }

    private function buildPdfPageContent(array $lines): string
    {
        $content = "1 0 0 1 0 0 cm\n";
        $y = 790;

        foreach ($lines as [$text, $size, $bold]) {
            if ($text === '') {
                $y -= 14;
                continue;
            }

            $font = $bold ? 'F2' : 'F1';
            $content .= "BT /{$font} {$size} Tf 48 {$y} Td (" . $this->pdfText($text) . ") Tj ET\n";
            $y -= $size + 8;
        }

        return $content;
    }

    private function assemblePdf(array $pageContents): string
    {
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>',
        ];

        $pageObjectNumbers = [];

        foreach ($pageContents as $content) {
            $contentObjectNumber = count($objects) + 1;
            $objects[] = "<< /Length " . strlen($content) . " >>\nstream\n{$content}endstream";

            $pageObjectNumbers[] = count($objects) + 1;
            $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] '
                . '/Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> '
                . "/Contents {$contentObjectNumber} 0 R >>";
        }

        $objects[1] = '<< /Type /Pages /Kids [' . implode(' ', array_map(fn($n) => "{$n} 0 R", $pageObjectNumbers))
            . '] /Count ' . count($pageObjectNumbers) . ' >>';

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    private function pdfText(string $text): string
    {
        $text = preg_replace('/[^\x20-\x7E]/', '', $text);
        $text = mb_strimwidth($text, 0, 112, '...');

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
