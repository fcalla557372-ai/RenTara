{{-- resources/views/customer/my-bookings.blade.php --}}
@extends('layouts.customer')
@section('title', 'My Bookings')
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">My Bookings</h1>
        <p class="page-subtitle">Track and manage your rental reservations</p>
    </div>
</div>

<div class="alert border-0 mb-4 d-flex align-items-start gap-3"
     style="background:#EFF6FF; border-radius:12px; color:#1E40AF;">
    <i class="bi bi-info-circle-fill mt-1" style="color:#3B82F6; flex-shrink:0;"></i>
    <div style="font-size:.875rem; font-weight:500;">
        You can cancel a booking while it's still in
        <strong style="color:#1E293B;">Pending Payment</strong> status —
        once payment is confirmed, cancellation must go through our staff.
    </div>
</div>

<div class="content-card p-0" style="overflow:hidden;">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Car</th>
                    <th>Pick-up</th>
                    <th>Return</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Remaining</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($bookings as $b)
                @php
                    $statusColor = match($b->payment?->payment_status ?? 'Unknown') {
                        'Pending Payment' => '#D97706',
                        'Partial Payment' => '#B45309',
                        'Payment Confirmed' => '#1D4ED8',
                        'Completed' => '#166534',
                        default => '#991B1B',
                    };
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:600; font-size:.875rem; color:#1E293B;">
                            {{ $b->car->name }}
                        </div>
                        <div style="font-size:.75rem; color:#94A3B8;">{{ $b->car->type }}</div>
                    </td>
                    <td style="font-size:.875rem; color:#475569;">
                        {{ $b->pickup_date->format('M d, Y') }}
                    </td>
                    <td style="font-size:.875rem; color:#475569;">
                        {{ $b->return_date->format('M d, Y') }}
                    </td>
                    <td style="font-weight:700; color:#F59E0B; font-size:.875rem;">
                        ₱{{ number_format($b->total_amount, 2) }}
                    </td>
                    <td style="font-weight:700; color:#0F172A; font-size:.875rem;">
                        ₱{{ number_format($b->payment?->amount_paid ?? 0, 2) }}
                    </td>
                    <td style="font-weight:700; font-size:.875rem; color:{{ ($b->payment?->remaining_balance ?? $b->total_amount) > 0 ? '#991B1B' : '#475569' }};">
                        @if(($b->payment?->remaining_balance ?? $b->total_amount) > 0)
                            <span style="font-weight:800;">Balance Due ₱{{ number_format($b->payment?->remaining_balance ?? $b->total_amount, 2) }}</span>
                        @else
                            ₱0.00
                        @endif
                    </td>
                    <td>
                        <span style="font-size:.8rem; font-weight:700; color:{{ $statusColor }};">
                            {{ $b->payment?->payment_status ?? 'Unknown' }}
                        </span>
                        @if(($b->payment?->payment_status ?? 'Unknown') === 'Completed' && ($b->payment?->remaining_balance ?? 0) > 0)
                            <div style="margin-top:6px; display:inline-block; background:#FEF3C7; color:#92400E; padding:.35rem .75rem; border-radius:8px; font-size:.75rem;">
                                You have a remaining balance of ₱{{ number_format($b->payment?->remaining_balance ?? 0, 2) }}. Please settle with staff.
                            </div>
                        @endif
                    </td>
                    <td style="display:flex; align-items:center; gap:.85rem;">
                        @if(($b->payment?->payment_status ?? 'Unknown') === 'Pending Payment')
                            <form method="POST"
                                  action="{{ route('customer.my-bookings.cancel', $b) }}"
                                  onsubmit="return confirm('Cancel this booking?')" style="display:contents;">
                                @csrf
                                <button type="submit" style="background:none; border:none; padding:0; font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:underline; text-underline-offset:3px; color:#991B1B;">
                                    <i class="bi bi-x-lg me-1"></i>Cancel
                                </button>
                            </form>
                        @else
                            <span style="color:#CBD5E1; font-size:.85rem;">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div style="font-size:3rem; margin-bottom:.75rem;">🚗</div>
                        <div style="font-weight:700; color:#1E293B; margin-bottom:.25rem;">
                            No bookings yet
                        </div>
                        <div style="font-size:.875rem; color:#94A3B8; margin-bottom:1rem;">
                            Your rental history will appear here
                        </div>
                        <a href="{{ route('customer.booking') }}"
                           class="btn btn-sm"
                           style="background:#F59E0B; color:#1E293B; font-weight:700;
                                  border-radius:8px; padding:.5rem 1.25rem;">
                            Book a Car →
                        </a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($bookings->hasPages())
    <div class="p-3 d-flex justify-content-between align-items-center"
         style="border-top:1px solid #F1F5F9;">
        <div style="font-size:.8rem; color:#94A3B8;">
            Showing {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }}
            of {{ $bookings->total() }} bookings
        </div>
        {{ $bookings->links() }}
    </div>
    @endif
</div>

@endsection