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
                        'Pending Balance' => '#0F766E',
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
                        @elseif($b->canPayBalance())
                            <button type="button"
                                    style="background:none; border:none; padding:0; font-size:.8rem; font-weight:700; color:#166534; cursor:pointer; text-decoration:underline; text-underline-offset:3px;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#payBalanceModal{{ $b->id }}">
                                Pay Balance
                            </button>
                        @elseif($b->balancePaymentPending())
                            <span style="font-size:.75rem; font-weight:600; color:#D97706;">
                                Awaiting confirmation
                            </span>
                        @else
                            <span style="color:#CBD5E1;">—</span>
                        @endif
                    </td>
                </tr>

                @if($b->canPayBalance())
                <div class="modal fade" id="payBalanceModal{{ $b->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
                        <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 8px 32px rgba(0,0,0,.12);">

                            {{-- Modal Header --}}
                            <div class="modal-header" style="border-bottom:1px solid #F1F5F9; padding:1.25rem 1.5rem;">
                                <div>
                                    <h5 class="modal-title" style="font-weight:800; color:#1E293B; font-size:1rem; margin:0;">
                                        Pay Remaining Balance
                                    </h5>
                                    <p style="font-size:.78rem; color:#94A3B8; margin:.2rem 0 0;">
                                        {{ $b->car->name }} — Booking #{{ str_pad($b->id, 5, '0', STR_PAD_LEFT) }}
                                    </p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="modal-body" style="padding:1.5rem;">

                                {{-- Balance Summary --}}
                                <div style="background:#F0FDF4; border:1px solid #BBF7D0; border-radius:10px; padding:1rem 1.25rem; margin-bottom:1.25rem;">
                                    <div style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#166534; margin-bottom:.5rem;">
                                        Amount Due
                                    </div>
                                    <div style="font-size:1.6rem; font-weight:900; color:#166534;">
                                        ₱{{ number_format($b->remaining_balance, 2) }}
                                    </div>
                                    <div style="font-size:.75rem; color:#64748B; margin-top:.25rem;">
                                        This is the remaining 50% balance for your {{ $b->car->name }} rental.
                                    </div>
                                </div>

                                {{-- GCash Info Banner --}}
                                <div style="background:#EDE9FE; border:1px solid #C4B5FD; border-radius:10px; padding:.875rem 1rem; margin-bottom:1.25rem; display:flex; gap:.75rem; align-items:flex-start;">
                                    <span style="font-size:1rem; flex-shrink:0;">💙</span>
                                    <div style="font-size:.8rem; color:#5B21B6; line-height:1.5;">
                                        Send <strong>₱{{ number_format($b->remaining_balance, 2) }}</strong> via GCash to our registered number, then enter your reference number and upload the receipt below.
                                    </div>
                                </div>

                                {{-- Payment Form --}}
                                <form method="POST"
                                      action="{{ route('customer.my-bookings.pay-balance', $b) }}"
                                      enctype="multipart/form-data"
                                      id="balanceForm{{ $b->id }}">
                                    @csrf

                                    {{-- GCash Reference Number --}}
                                    <div style="margin-bottom:1rem;">
                                        <label style="display:block; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748B; margin-bottom:.4rem;">
                                            GCash Reference Number *
                                        </label>
                                        <input type="text"
                                               name="balance_gcash_reference_no"
                                               placeholder="e.g. 7040 658 290157"
                                               maxlength="30"
                                               required
                                               style="width:100%; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:9px; color:#1E293B; font-size:.875rem; padding:10px 12px; outline:none; font-family:inherit;"
                                               onfocus="this.style.borderColor='#F59E0B'; this.style.boxShadow='0 0 0 3px rgba(245,158,11,.12)'"
                                               onblur="this.style.borderColor='#E2E8F0'; this.style.boxShadow='none'">
                                    </div>

                                    {{-- Upload Receipt --}}
                                    <div style="margin-bottom:1.25rem;">
                                        <label style="display:block; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#64748B; margin-bottom:.4rem;">
                                            Upload GCash Receipt Screenshot *
                                        </label>
                                        <div style="position:relative;">
                                            <input type="file"
                                                   name="balance_gcash_receipt"
                                                   accept=".jpg,.jpeg,.png"
                                                   required
                                                   id="balanceReceipt{{ $b->id }}"
                                                   onchange="showReceiptName(this, 'receiptName{{ $b->id }}')"
                                                   style="position:absolute; inset:0; opacity:0; cursor:pointer; z-index:2;">
                                            <div style="background:#F8FAFC; border:1px dashed #CBD5E1; border-radius:9px; padding:10px 12px; display:flex; align-items:center; gap:.5rem; font-size:.8rem; color:#94A3B8; cursor:pointer;">
                                                📎 Choose file or drag here
                                            </div>
                                        </div>
                                        <div id="receiptName{{ $b->id }}" style="font-size:.72rem; color:#166534; font-weight:600; margin-top:.3rem; display:none;"></div>
                                        <div style="font-size:.7rem; color:#94A3B8; margin-top:.25rem;">JPG or PNG only — max 10MB</div>
                                    </div>

                                    {{-- Submit --}}
                                    <button type="submit"
                                            style="width:100%; padding:12px; border:none; border-radius:10px; background:#F59E0B; color:#1E293B; font-size:.875rem; font-weight:800; cursor:pointer; font-family:inherit; transition:background .2s;"
                                            onmouseover="this.style.background='#D97706'"
                                            onmouseout="this.style.background='#F59E0B'">
                                        Submit Balance Payment →
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                @endif
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

@push('scripts')
<script>
function showReceiptName(input, nameId) {
    const el = document.getElementById(nameId);
    if (input.files.length) {
        el.textContent = '✓ ' + input.files[0].name;
        el.style.display = 'block';
    }
}
</script>
@endpush

@endsection