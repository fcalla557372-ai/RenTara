{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

<style>
:root {
  --brand:       #F59E0B; /* refined */
  --brand-dark:  #D97706; /* refined */
  --brand-tint:  rgba(245,158,11,.10); /* refined */
  --brand-glow:  rgba(245,158,11,.15); /* refined */

  --text-1: #1E293B; /* refined */
  --text-2: #64748B; /* refined */
  --text-3: #94A3B8; /* refined */

  --text-d1: #D4D0C8; /* refined */
  --text-d2: #7A7570; /* refined */
  --text-d3: #4A4640; /* refined */

  --bg-page:  #F1F5F9; /* refined */
  --bg-card:  #FFFFFF; /* refined */
  --bg-inset: #F8FAFC; /* refined */

  --bg-d-page:  #0D0D0D; /* refined */
  --bg-d-card:  #111111; /* refined */
  --bg-d-inset: #1A1A1A; /* refined */

  --border-light: #E2E8F0; /* refined */
  --border-dark:  #1E1E1E; /* refined */

  --sb-bg:     #0F172A; /* refined */
  --sb-border: #1E293B; /* refined */
  --sb-icon:   #475569; /* refined */
  --sb-text:   #64748B; /* refined */

  --shadow-card:  0 1px 2px rgba(0,0,0,.04), 0 4px 12px rgba(0,0,0,.06); /* refined */
  --shadow-modal: 0 8px 32px rgba(0,0,0,.10), 0 1px 3px rgba(0,0,0,.06); /* refined */
  --shadow-float: 0 2px 12px rgba(0,0,0,.06); /* refined */
}

.table-card thead th {
  color: var(--text-3);
  text-transform: uppercase;
  font-size: .72rem;
  letter-spacing: 1px;
  border-bottom: 1px solid var(--bg-page);
}

.table-card tbody tr {
  border-bottom: 1px solid var(--bg-page);
}

.table-card tbody tr:hover {
  background: #FAFAFA; /* refined */
}

.status-badge.badge-pending {
  background: #FEF3C7; /* refined */
  color: #92400E; /* refined */
}

.status-badge.badge-confirmed {
  background: #EFF6FF; /* refined */
  color: #1D4ED8; /* refined */
}

.status-badge.badge-completed {
  background: #F0FDF4; /* refined */
  color: #166534; /* refined */
}

.status-badge.badge-cancelled {
  background: #FEF2F2; /* refined */
  color: #991B1B; /* refined */
}
</style>

{{-- ── KPI Cards ── --}}
<div class="row g-2 mb-4">

    {{-- All Cars --}}
    <div class="col-12 col-sm-6 col-md col-lg">
        <a href="{{ route('admin.car-inventory') }}" class="text-decoration-none">
            <div class="stat-card" style="border-left:4px solid #94A3B8; cursor:pointer;
                        transition:box-shadow .2s, transform .2s;"
                 onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.06)' /* refined */"
                 onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:.9rem; font-weight:800; text-transform:uppercase;
                                    letter-spacing:1px; color:#94A3B8; margin-bottom:.25rem;">
                            All Cars
                        </div>
                        <div style="font-size:.85rem; color:#CBD5E1; margin-top:.2rem;">
                            View inventory →
                        </div>
                    </div>
                    <div style="text-align:right; min-width:70px;">
                        <div style="font-size:1.6rem; font-weight:900; color:#1E293B; line-height:1;">
                            {{ $allCars }}
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Available --}}
    <div class="col-12 col-sm-6 col-md col-lg">
        <a href="{{ route('admin.car-inventory') }}" class="text-decoration-none">
            <div class="stat-card" style="border-left:4px solid #6EE7B7; cursor:pointer;
                        transition:box-shadow .2s, transform .2s;"
                 onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.06)' /* refined */"
                 onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:.9rem; font-weight:800; text-transform:uppercase;
                                    letter-spacing:1px; color:#94A3B8; margin-bottom:.25rem;">
                            Available
                        </div>
                        <div style="font-size:.85rem; color:#CBD5E1; margin-top:.2rem;">
                            Ready to rent →
                        </div>
                    </div>
                    <div style="text-align:right; min-width:70px;">
                        <div style="font-size:1.6rem; font-weight:900; color:#1E293B; line-height:1;">
                            {{ $available }}
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Rented --}}
    <div class="col-12 col-sm-6 col-md col-lg">
        <a href="{{ route('admin.tracking', ['status' => 'Payment Confirmed']) }}" class="text-decoration-none">
            <div class="stat-card" style="border-left:4px solid #FCA5A5; cursor:pointer;
                        transition:box-shadow .2s, transform .2s;"
                 onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.06)' /* refined */"
                 onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:.9rem; font-weight:800; text-transform:uppercase;
                                    letter-spacing:1px; color:#94A3B8; margin-bottom:.25rem;">
                            Rented
                        </div>
                        <div style="font-size:.85rem; color:#CBD5E1; margin-top:.2rem;">
                            View active →
                        </div>
                    </div>
                    <div style="text-align:right; min-width:70px;">
                        <div style="font-size:1.6rem; font-weight:900; color:#1E293B; line-height:1;">
                            {{ $rented }}
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Pending Payment --}}
    <div class="col-12 col-sm-6 col-md col-lg">
        <a href="{{ route('admin.tracking', ['status' => 'Pending Payment']) }}" class="text-decoration-none">
            <div class="stat-card" style="border-left:4px solid #FCD34D; cursor:pointer;
                        transition:box-shadow .2s, transform .2s;"
                 onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.06)' /* refined */"
                 onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:.9rem; font-weight:800; text-transform:uppercase;
                                    letter-spacing:1px; color:#94A3B8; margin-bottom:.25rem;">
                            Pending Payment
                        </div>
                        <div style="font-size:.85rem; color:#CBD5E1; margin-top:.2rem;">
                            Needs action →
                        </div>
                    </div>
                    <div style="text-align:right; min-width:70px;">
                        <div style="font-size:1.6rem; font-weight:900; color:#1E293B; line-height:1;">
                            {{ $pending }}
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Pending Balance --}}
    <div class="col-12 col-sm-6 col-md col-lg">
        <a href="{{ route('admin.tracking', ['status' => 'Pending Balance']) }}" class="text-decoration-none">
            <div class="stat-card" style="border-left:4px solid #14B8A6; cursor:pointer;
                        transition:box-shadow .2s, transform .2s;"
                 onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.06)' /* refined */"
                 onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:.9rem; font-weight:800; text-transform:uppercase;
                                    letter-spacing:1px; color:#94A3B8; margin-bottom:.25rem;">
                            Pending Balance
                        </div>
                        <div style="font-size:.85rem; color:#CBD5E1; margin-top:.2rem;">
                            Awaiting settlement →
                        </div>
                    </div>
                    <div style="text-align:right; min-width:70px;">
                        <div style="font-size:1.6rem; font-weight:900; color:#1E293B; line-height:1;">
                            {{ $pendingBalance }}
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Today's Earnings --}}
    <div class="col-12 col-sm-6 col-md col-lg">
        <a href="{{ route('admin.report') }}" class="text-decoration-none">
            <div class="stat-card" style="border-left:4px solid #A5B4FC; cursor:pointer;
                        transition:box-shadow .2s, transform .2s;"
                 onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.06)' /* refined */"
                 onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div style="font-size:.9rem; font-weight:800; text-transform:uppercase;
                                    letter-spacing:1px; color:#94A3B8; margin-bottom:.25rem;">
                            Today's Earnings
                        </div>
                        <div style="font-size:.85rem; color:#CBD5E1; margin-top:.2rem;">
                            View report →
                        </div>
                    </div>
                    <div style="text-align:right; min-width:70px;">
                        <div style="font-size:1.6rem; font-weight:900; color:#1E293B; line-height:1;">
                            ₱{{ number_format($todayEarnings, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

</div>

{{-- ── Charts ── --}}
<div class="row g-3 mb-4">

    {{-- Donut Chart --}}
    <div class="col-lg-4">
        <div class="chart-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 style="font-size:.75rem; font-weight:800; text-transform:uppercase;
                               letter-spacing:1px; color:#94A3B8; margin:0;">
                        Fleet Distribution
                    </h6>
                    <p style="font-size:.8rem; color:#1E293B; font-weight:600; margin:.2rem 0 0;">
                        {{ $allCars }} vehicles total
                    </p>
                </div>
            </div>
            {{-- Fixed height container --}}
            <div style="position:relative; height:280px; max-height:280px;">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Bar Chart --}}
    <div class="col-lg-8">
        <div class="chart-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 style="font-size:.75rem; font-weight:800; text-transform:uppercase;
                               letter-spacing:1px; color:#94A3B8; margin:0;">
                        Cars by Type
                    </h6>
                    <p style="font-size:.8rem; color:#1E293B; font-weight:600; margin:.2rem 0 0;">
                        Fleet breakdown by category
                    </p>
                </div>
            </div>
            {{-- Fixed height container --}}
            <div style="position:relative; height:280px; max-height:280px;">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ── Recent Bookings ── --}}
<div class="table-card">
    <div class="d-flex justify-content-between align-items-center p-3 pb-2">
        <div>
            <h6 style="font-weight:800; color:#1E293B; margin:0; font-size:.95rem;">
                Recent Bookings
            </h6>
            <p style="font-size:.75rem; color:#94A3B8; margin:.15rem 0 0;">
                Latest rental activity
            </p>
        </div>
        <a href="{{ route('admin.tracking') }}"
           style="font-size:.8rem; font-weight:700; color:#475569; /* refined */ text-decoration:none;
                  background:#F1F5F9; /* refined */ padding:.35rem .85rem; border-radius:7px;
                  border:1px solid #E2E8F0; /* refined */">
            View all →
        </a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Car</th>
                    <th>Pick-up</th>
                    <th>Return</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($recentBookings as $b)
                <tr>
                    <td>
                        <div style="font-weight:600; font-size:.875rem; color:#1E293B;">
                            {{ $b->user->name }}
                        </div>
                        <div style="font-size:.72rem; color:#94A3B8;">{{ $b->user->email }}</div>
                    </td>
                    <td>
                        <div style="font-size:.875rem; font-weight:600; color:#1E293B;">
                            {{ $b->car->name }}
                        </div>
                        <div style="font-size:.72rem; color:#94A3B8;">{{ $b->car->type }}</div>
                    </td>
                    <td style="font-size:.875rem; color:#64748B; /* refined */">
                        {{ $b->pickup_date->format('M d, Y') }}
                    </td>
                    <td style="font-size:.875rem; color:#64748B; /* refined */">
                        {{ $b->return_date->format('M d, Y') }}
                    </td>
                    <td style="font-weight:700; font-size:.875rem; color:#1E293B;">
                        ₱{{ number_format($b->total_amount, 2) }}
                    </td>
                    <td>
                        <span style="background:#F8FAFC; /* refined */ color:#64748B; /* refined */ font-size:.72rem;
                                     font-weight:700; padding:.22rem .6rem; border-radius:6px;">
                            {{ $b->payment_method }}
                        </span>
                    </td>
                    <td>
                        <span style="font-size:.8rem; font-weight:700; color:{{ $b->payment_status === 'Pending Payment' ? '#D97706' : ($b->payment_status === 'Partial Payment' ? '#B45309' : ($b->payment_status === 'Payment Confirmed' ? '#1D4ED8' : ($b->payment_status === 'Pending Balance' ? '#0F766E' : ($b->payment_status === 'Completed' ? '#166534' : '#991B1B')))) }};">
                            {{ $b->payment_status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4" style="color:#94A3B8; font-size:.875rem;">
                        No bookings yet.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($recentBookings->hasPages())
    <div class="p-3 d-flex justify-content-between align-items-center"
         style="border-top:1px solid #F1F5F9;">
        <div style="font-size:.8rem; color:#94A3B8;">
            Showing {{ $recentBookings->firstItem() }}–{{ $recentBookings->lastItem() }}
            of {{ $recentBookings->total() }} bookings
        </div>
        {{ $recentBookings->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
// ── Professional muted palette ──────────────────────────────
// Deep Blue | Teal | Soft Purple | Muted Amber
const palette = ['#3B6FD4', '#2A9D8F', '#7C5CBF', '#E09F3E'];
const paletteLight = ['rgba(59,111,212,.15)', 'rgba(42,157,143,.15)',
                      'rgba(124,92,191,.15)', 'rgba(224,159,62,.15)'];

const labels = {!! json_encode(array_keys($fleetCounts)) !!};
const counts = {!! json_encode(array_values($fleetCounts)) !!};

// ── Donut Chart ─────────────────────────────────────────────
const pieCtx = document.getElementById('pieChart').getContext('2d');
new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels,
        datasets: [{
            data: counts,
            backgroundColor: palette,
            borderColor: '#fff',
            borderWidth: 3,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,   // ← key: respects parent height
        cutout: '68%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 16,
                    usePointStyle: true,
                    pointStyleWidth: 10,
                    font: { size: 12, weight: '600' },
                    color: '#475569',
                }
            },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.parsed} cars`
                }
            }
        }
    }
});

// ── Bar Chart ───────────────────────────────────────────────
const barCtx = document.getElementById('barChart').getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Cars',
            data: counts,
            backgroundColor: palette,
            borderColor: palette,
            borderWidth: 0,
            borderRadius: 8,
            borderSkipped: false,
            barThickness: 48,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,   // ← key: respects parent height
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.parsed.y} car${ctx.parsed.y !== 1 ? 's' : ''}`
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: { size: 12 },
                    color: '#94A3B8',
                },
                grid: {
                    color: '#F1F5F9',
                    drawBorder: false,
                },
                border: { display: false }
            },
            x: {
                ticks: {
                    font: { size: 12, weight: '600' },
                    color: '#475569',
                },
                grid: { display: false },
                border: { display: false }
            }
        }
    }
});
</script>
@endpush
@endsection
