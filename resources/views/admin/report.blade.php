{{-- resources/views/admin/report.blade.php --}}
@extends('layouts.admin')
@section('title', 'Report')
@section('content')

<style>
.report-page {
    padding: .75rem 0 0;
}
.report-page .page-header {
    padding: 1rem 1.25rem;
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 18px;
    margin-bottom: .75rem;
    box-shadow: 0 18px 50px rgba(15, 23, 42, .08);
}
.report-page .page-header .page-title {
    color: #0F172A;
}
.report-page .page-header .page-subtitle {
    color: #475569;
}
.report-page .page-header .btn-brand {
    background: #F59E0B;
    color: #0A0800;
}
.report-page .report-input-card,
.report-page .report-kpi-card,
.report-page .report-summary-card,
.report-page .report-history-card,
.report-page .report-pagination-card {
    border-radius: 18px;
    border: 1px solid #E5E7EB;
    background: #FFFFFF;
    box-shadow: 0 18px 50px rgba(15, 23, 42, .06);
}
.report-page .report-input-card {
    padding: 1.25rem;
}
.report-page .report-input-card .form-control,
.report-page .report-input-card .form-select {
    background: #F8FAFC;
    color: #0F172A;
    border: 1px solid #E5E7EB;
}
.report-page .report-input-card .form-control::placeholder,
.report-page .report-input-card .form-select {
    color: #94A3B8;
}
.report-page .report-input-card .form-control:focus,
.report-page .report-input-card .form-select:focus {
    border-color: rgba(245, 158, 11, .7);
    box-shadow: 0 0 0 .15rem rgba(245, 158, 11, .18);
}
.report-page .report-input-card label {
    color: #475569;
    font-weight: 600;
}
.report-page .report-kpi-card {
    padding: 1.25rem;
    min-height: 160px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.report-page .report-kpi-card .card-title {
    font-size: .72rem;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #6B7280;
}
.report-page .report-kpi-card .card-value {
    font-size: 2rem;
    font-weight: 800;
    color: #0F172A;
}
.report-page .report-kpi-card .card-note {
    color: #64748B;
    font-size: .86rem;
}
.report-page .report-summary-card,
.report-page .report-history-card {
    padding: 1.25rem;
    min-height: 100%;
}
.report-page .report-summary-card h6,
.report-page .report-history-card h6 {
    color: #0F172A;
}
.report-page .report-summary-card p,
.report-page .report-history-card p,
.report-page .report-header-note,
.report-page .report-pagination-card .text-muted {
    color: #64748B;
}
.report-page .report-summary-section {
    margin-bottom: 1rem;
}
.report-page .report-summary-section:last-child {
    margin-bottom: 0;
}
.report-page .report-summary-section h6 {
    color: #0F172A;
    margin-bottom: .9rem;
}
.report-page .report-summary-section table {
    color: #0F172A;
}
.report-page .report-summary-section .table thead th {
    background: #F8FAFC;
    border-bottom: 1px solid #E5E7EB;
    color: #6B7280;
    font-size: .72rem;
    letter-spacing: .08em;
}
.report-page .report-summary-section .table tbody tr {
    border-bottom: 1px solid #F1F5F9;
}
.report-page .report-summary-section .table tbody td {
    color: #0F172A;
    vertical-align: middle;
}
.report-page .report-table thead th {
    background: #F8FAFC;
    border-bottom: 1px solid #E5E7EB;
    color: #6B7280;
    font-size: .72rem;
    letter-spacing: .08em;
}
.report-page .report-table tbody tr {
    border-bottom: 1px solid #F1F5F9;
}
.report-page .report-table tbody tr:hover {
    background: #F8FAFC;
}
.report-page .report-table tbody td {
    color: #0F172A;
    vertical-align: middle;
}
.report-page .report-table .badge {
    padding: .35rem .8rem;
    font-size: .75rem;
}
.report-page .badge-pending { background: #FDE68A; color: #92400E; }
.report-page .badge-confirmed { background: #BFDBFE; color: #1D4ED8; }
.report-page .badge-completed { background: #BBF7D0; color: #166534; }
.report-page .badge-cancelled { background: #FECACA; color: #991B1B; }
.report-page .report-pagination-card {
    padding: 1rem 1.25rem;
    background: #FFFFFF;
}
.report-page .report-pagination-card .form-select {
    min-width: 90px;
    background: #F8FAFC;
    color: #0F172A;
    border-color: #E5E7EB;
}
.report-page .report-pagination-card .form-select:focus {
    border-color: rgba(245, 158, 11, .7);
    box-shadow: 0 0 0 .15rem rgba(245, 158, 11, .18);
}
.report-page .btn-outline-secondary {
    color: #0F172A;
    border-color: #E5E7EB;
}
.report-page .btn-outline-secondary:hover {
    border-color: #F59E0B;
    background: rgba(245, 158, 11, .08);
}
@media (max-width: 768px) {
    .report-page .page-header { flex-direction: column; align-items: stretch; gap: .75rem; }
}
</style>

<div class="report-page">
    <div class="page-header d-flex justify-content-between align-items-center gap-3">
        <div>
            <h1 class="page-title">Reports</h1>
        </div>
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="report-header-note">{{ now()->format('M d, Y') }}</div>
            <a href="{{ route('admin.report.export', request()->query()) }}" class="btn btn-brand d-flex align-items-center gap-2">
                <i class="bi bi-download"></i> Export PDF
            </a>
        </div>
    </div>

    <div class="report-input-card mb-2">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <label class="form-label">Report Type</label>
                    <select name="report_type" class="form-select">
                        @foreach($reportTypes as $key => $label)
                            <option value="{{ $key }}" {{ $reportType === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6">
                    <label class="form-label">Car Type</label>
                    <select name="car_type" class="form-select">
                        <option value="">All types</option>
                        @foreach($carTypes as $type)
                            <option value="{{ $type }}" {{ request('car_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6">
                    <label class="form-label">Status</label>
                    <select name="payment_status" class="form-select">
                        <option value="">All statuses</option>
                        @foreach(['Pending Payment', 'Payment Confirmed', 'Completed', 'Cancelled'] as $status)
                            <option value="{{ $status }}" {{ request('payment_status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-1 col-lg-4 col-md-6">
                    <label class="form-label">Payment</label>
                    <select name="payment_method" class="form-select">
                        <option value="">All</option>
                        <option value="GCash" {{ request('payment_method') === 'GCash' ? 'selected' : '' }}>GCash</option>
                    </select>
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.report') }}" class="btn btn-outline-secondary fw-bold px-4">Reset</a>
                    <button type="submit" class="btn btn-brand fw-bold px-4">Generate</button>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
<div class="row g-2 mb-2">
    <div class="col-md-4">
        <div class="report-kpi-card">
            <div class="card-title">Revenue</div>
            <div class="card-value">PHP {{ number_format($totalRevenue, 2) }}</div>
            <div class="card-note">Total amount paid for partial, confirmed, and completed bookings</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="report-kpi-card">
            <div class="card-title">Bookings</div>
            <div class="card-value">{{ number_format($totalBookings) }}</div>
            <div class="card-note">Bookings matching current filters</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="report-kpi-card">
            <div class="card-title">Outstanding Balance</div>
            <div class="card-value">PHP {{ number_format($outstandingBalances, 2) }}</div>
            <div class="card-note">Remaining balance for completed bookings</div>
        </div>
    </div>
</div>

{{-- Summary Sections and Transaction History --}}
<div class="row g-2 mb-2">
    <div class="col-12">
        <div class="report-summary-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h6 class="mb-1">Summary</h6>
                    <p class="mb-0 report-header-note">Quick insights across each grouping.</p>
                </div>
            </div>
            @foreach($summarySections as $section)
                <div class="report-summary-section">
                    <h6>{{ $section['title'] }}</h6>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Group</th>
                                    <th>Bookings</th>
                                    <th>Revenue</th>
                                    <th>Insight</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($section['rows'] as $row)
                                    <tr>
                                        <td class="fw-semibold">{{ $row['label'] }}</td>
                                        <td>{{ $row['bookings'] }}</td>
                                        <td>PHP {{ number_format($row['revenue'], 2) }}</td>
                                        <td class="text-muted" style="font-size:.8rem;">{{ $row['detail'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No summary data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-12">
        <div class="report-history-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="mb-1">Transaction History</h6>
                </div>
                <span class="report-header-note">Page {{ $bookings->currentPage() }} / {{ $bookings->lastPage() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-borderless report-table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $b)
                            <tr>
                                <td>{{ $b->pickup_date->format('M d, Y') }}</td>
                                <td>{{ $b->user->name }}</td>
                                <td>{{ $b->car->type }}</td>
                                <td class="fw-semibold">PHP {{ number_format($b->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-white text-dark" style="font-size:.75rem; font-weight:700;">{{ $b->payment_method }}</span>
                                </td>
                                <td>
                                    <span style="font-size:.8rem; font-weight:700; color:{{ $b->payment_status === 'Pending Payment' ? '#D97706' : ($b->payment_status === 'Payment Confirmed' ? '#1D4ED8' : ($b->payment_status === 'Completed' ? '#166534' : '#991B1B')) }};">
                                        {{ $b->payment_status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($bookings->hasPages())
                <div class="report-pagination-card mt-3 d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
                    <div class="text-muted" style="font-size:.88rem;">Showing {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} of {{ $bookings->total() }} transactions</div>
                    <div>{{ $bookings->withQueryString()->links() }}</div>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection
