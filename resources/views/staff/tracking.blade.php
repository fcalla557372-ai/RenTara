@extends('layouts.staff')
@section('title', 'Tracking')
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Booking Tracking</h1>
        <p class="page-subtitle">Monitor and manage all rental bookings</p>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label mb-1 fw-semibold" style="font-size:.78rem;">Status</label>
            <select name="status" class="form-select form-select-sm" style="border-radius:8px; border-color:#E2E8F0;">
                <option value="">All</option>
                @foreach(['Pending Payment', 'Partial Payment', 'Payment Confirmed', 'Pending Balance', 'Completed', 'Cancelled'] as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-5">
            <label class="form-label mb-1 fw-semibold" style="font-size:.78rem;">Search</label>
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Customer or car..."
                   value="{{ request('search') }}"
                   style="border-radius:8px; border-color:#E2E8F0;">
        </div>
        <div class="col-12 col-md-3 d-grid d-md-flex gap-2">
            <button class="btn btn-brand btn-sm flex-fill" type="submit" style="border-radius:8px;">
                <i class="bi bi-funnel me-1"></i> Apply
            </button>
            <a href="{{ route('staff.tracking') }}" class="btn btn-outline-secondary btn-sm flex-fill" style="border-radius:8px;">
                Reset
            </a>
        </div>
    </div>
</form>

{{-- Table --}}
<div class="table-card">
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
                    <th>Payment Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($bookings as $b)
                @php
                    $statusColor = match($b->payment_status) {
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
                        <div style="font-weight:600; font-size:.875rem;">{{ $b->user->name }}</div>
                        <div style="font-size:.75rem; color:#94A3B8;">{{ $b->user->email }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600; font-size:.875rem;">{{ $b->car->name }}</div>
                        <div style="font-size:.75rem; color:#94A3B8;">{{ $b->car->type }}</div>
                    </td>
                    <td style="font-size:.875rem;">{{ $b->pickup_date->format('M d, Y') }}</td>
                    <td style="font-size:.875rem;">{{ $b->return_date->format('M d, Y') }}</td>
                    <td style="font-weight:700; font-size:.875rem;">
                        ₱{{ number_format($b->total_amount, 2) }}
                    </td>
                    <td>
                        <span style="background:#F1F5F9; color:#475569; font-size:.72rem;
                                     font-weight:700; padding:.25rem .6rem; border-radius:6px; white-space:nowrap;">
                            {{ $b->payment_method }}
                        </span>
                    </td>
                    <td>
                        <span style="background:{{ $b->payment_type === 'partial' ? '#FEF9C3' : '#D1FAE5' }}; color:{{ $b->payment_type === 'partial' ? '#854D0E' : '#166534' }}; font-size:.72rem;
                                     font-weight:700; padding:.25rem .6rem; border-radius:6px; white-space:nowrap;">
                            {{ $b->payment_type === 'partial' ? 'Partial' : 'Full' }}
                        </span>
                    </td>
                    <td>
                        <span style="font-size:.8rem; font-weight:700; color:{{ $statusColor }};">
                            {{ $b->payment_status }}
                        </span>
                    </td>
                    <td style="display:flex; align-items:center; gap:.85rem;">
                        {{-- Docs Button --}}
                        <button style="background:none; border:none; padding:0; font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:underline; text-underline-offset:3px; color:#64748B;"
                                data-bs-toggle="modal"
                                data-bs-target="#docsModal{{ $b->id }}">
                            <i class="bi bi-file-person me-1"></i>Docs
                        </button>

                        @if($b->balance_payment_status === 'pending_confirmation')
                            <form method="POST" action="{{ route('staff.tracking.confirmBalance', $b) }}" style="display:contents;">
                                @csrf
                                <button type="submit"
                                        style="background:none; border:none; padding:0; font-size:.8rem; font-weight:700; color:#5B21B6; cursor:pointer; text-decoration:underline; text-underline-offset:3px;">
                                    Confirm Balance
                                </button>
                            </form>
                        @endif

                        @if($b->payment_status === 'Pending Balance')
                            <form method="POST" action="{{ route('staff.tracking.settleBalance', $b) }}" style="display:contents;">
                                @csrf
                                <button type="submit"
                                        style="background:none; border:none; padding:0; font-size:.8rem; font-weight:700; color:#166534; cursor:pointer; text-decoration:underline; text-underline-offset:3px;">
                                    Settle Balance
                                </button>
                            </form>
                        @endif

                        @if($b->payment_status === 'Pending Payment')
                            <form method="POST" action="{{ route('staff.tracking.confirmPayment', $b) }}" style="display:contents;">
                                @csrf
                                <button type="submit" style="background:none; border:none; padding:0; font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:underline; text-underline-offset:3px; color:#166534;">
                                    <i class="bi bi-check-lg me-1"></i>Confirm Pay
                                </button>
                            </form>
                            <form method="POST" action="{{ route('staff.tracking.cancel', $b) }}"
                                  onsubmit="return confirm('Cancel this booking?')" style="display:contents;">
                                @csrf
                                <button type="submit" style="background:none; border:none; padding:0; font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:underline; text-underline-offset:3px; color:#991B1B;">
                                    <i class="bi bi-x-lg me-1"></i>Cancel
                                </button>
                            </form>

                        @elseif(in_array($b->payment_status, ['Payment Confirmed', 'Partial Payment'], true))
                            <form method="POST" action="{{ route('staff.tracking.markReturn', $b) }}" style="display:contents;">
                                @csrf
                                <button type="submit" style="background:none; border:none; padding:0; font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:underline; text-underline-offset:3px; color:#166534;">
                                    <i class="bi bi-arrow-return-left me-1"></i>Return
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>

                {{-- ══════════════════════════════════════════════ --}}
                {{-- FULL CUSTOMER INFO + DOCS MODAL               --}}
                {{-- ══════════════════════════════════════════════ --}}
                <div class="modal fade" id="docsModal{{ $b->id }}" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content" style="border-radius:16px; border:none;
                                                          box-shadow:0 24px 64px rgba(0,0,0,.15);">

                            {{-- Modal Header --}}
                            <div class="modal-header"
                                 style="border-bottom:1px solid #F1F5F9; padding:1.25rem 1.5rem;
                                        background:linear-gradient(135deg,#1E293B 0%,#334155 100%);
                                        border-radius:16px 16px 0 0;">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:42px; height:42px; background:#F59E0B; border-radius:10px;
                                                display:flex; align-items:center; justify-content:center;
                                                font-size:1.2rem;">
                                        📋
                                    </div>
                                    <div>
                                        <h5 class="modal-title fw-bold mb-0" style="color:#fff; font-size:1rem;">
                                            Booking Details & Documents
                                        </h5>
                                        <p class="mb-0 mt-1" style="font-size:.75rem; color:#94A3B8;">
                                            Booking #{{ str_pad($b->id, 5, '0', STR_PAD_LEFT) }}
                                            &nbsp;·&nbsp; {{ $b->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body" style="padding:0; background:#F8FAFC;">
                                <div class="row g-0">

                                    {{-- LEFT COLUMN: Info --}}
                                    <div class="col-lg-5"
                                         style="padding:1.5rem; border-right:1px solid #E2E8F0; background:#fff;">

                                        <div class="mb-4">
                                            <span style="font-size:.8rem; font-weight:700; color:
                                                @if($b->payment_status === 'Pending Payment')#D97706
                                                @elseif($b->payment_status === 'Partial Payment')#B45309
                                                @elseif($b->payment_status === 'Payment Confirmed')#1D4ED8
                                                @elseif($b->payment_status === 'Completed')#166534
                                                @else#991B1B @endif;">
                                            {{ $b->payment_status }}
                                            </span>
                                        </div>

                                        @if($b->payment_status === 'Pending Balance' && $b->remaining_balance > 0)
                                            <div class="mb-4" style="background:#FEE2E2; border:1px solid #FCA5A5; color:#991B1B; border-radius:12px; padding:1rem;">
                                                <strong>Customer has an outstanding balance of ₱{{ number_format($b->remaining_balance, 2) }}.</strong>
                                                Please collect before releasing.
                                            </div>
                                        @endif

                                        {{-- Customer --}}
                                        <div class="mb-4">
                                            <div style="font-size:.65rem; font-weight:800; letter-spacing:1.5px;
                                                        text-transform:uppercase; color:#94A3B8;
                                                        border-bottom:1px solid #F1F5F9; padding-bottom:.4rem;
                                                        margin-bottom:.85rem;">
                                                Customer Information
                                            </div>
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div style="width:44px; height:44px; background:#FEF3C7;
                                                            border-radius:50%; display:flex; align-items:center;
                                                            justify-content:center; font-weight:900;
                                                            color:#D97706; font-size:1rem; flex-shrink:0;">
                                                    {{ strtoupper(substr($b->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div style="font-weight:700; font-size:.95rem; color:#1E293B;">
                                                        {{ $b->user->name }}
                                                    </div>
                                                    <div style="font-size:.8rem; color:#64748B;">
                                                        {{ $b->user->email }}
                                                    </div>
                                                </div>
                                            </div>
                                            @php $row = fn($label,$val) => "<div class='d-flex justify-content-between py-2' style='border-bottom:1px solid #F8FAFC;'>
                                                <span style='font-size:.8rem;color:#94A3B8;font-weight:600;'>$label</span>
                                                <span style='font-size:.8rem;color:#1E293B;font-weight:700;'>$val</span>
                                            </div>"; @endphp
                                            {!! $row('Phone', $b->user->phone ?? '—') !!}
                                            {!! $row('Date of Birth', $b->user->date_of_birth ? $b->user->date_of_birth->format('M d, Y') : '—') !!}
                                        </div>

                                        {{-- Rental --}}
                                        <div class="mb-4">
                                            <div style="font-size:.65rem; font-weight:800; letter-spacing:1.5px;
                                                        text-transform:uppercase; color:#94A3B8;
                                                        border-bottom:1px solid #F1F5F9; padding-bottom:.4rem;
                                                        margin-bottom:.85rem;">
                                                Rental Details
                                            </div>
                                            {!! $row('Car', $b->car->name) !!}
                                            {!! $row('Type', $b->car->type) !!}
                                            {!! $row('Pick-up', $b->pickup_date->format('M d, Y')) !!}
                                            {!! $row('Return', $b->return_date->format('M d, Y')) !!}
                                            {!! $row('Duration', max(1, $b->pickup_date->diffInDays($b->return_date) + 1) . ' day(s)') !!}
                                            {!! $row('Daily Rate', '₱' . number_format($b->car->daily_rate, 2)) !!}
                                            <div class="d-flex justify-content-between"
                                                 style="background:#FEF3C7; border-radius:8px;
                                                        padding:.5rem .75rem; margin-top:.35rem;">
                                                <span style="font-size:.85rem; color:#92400E; font-weight:700;">
                                                    Total Amount
                                                </span>
                                                <span style="font-size:.95rem; color:#D97706; font-weight:900;">
                                                    ₱{{ number_format($b->total_amount, 2) }}
                                                </span>
                                            </div>
                                            <div class="mt-2">
                                                {!! $row('Payment Method', $b->payment_method) !!}
                                                {!! $row('Payment Type', ucfirst($b->payment_type ?? 'full')) !!}
                                                {!! $row('Amount Paid', '₱' . number_format($b->amount_paid, 2)) !!}
                                                {!! $row('Remaining Balance', '<span style="color:' . ($b->remaining_balance > 0 ? '#991B1B' : '#475569') . '; font-weight:700;">₱' . number_format($b->remaining_balance, 2) . '</span>') !!}
                                                {!! $row('GCash Reference', $b->gcash_reference_no ?? '—') !!}
                                            </div>
                                        </div>

                                        {{-- Identity --}}
                                        <div>
                                            <div style="font-size:.65rem; font-weight:800; letter-spacing:1.5px;
                                                        text-transform:uppercase; color:#94A3B8;
                                                        border-bottom:1px solid #F1F5F9; padding-bottom:.4rem;
                                                        margin-bottom:.85rem;">
                                                Identity & License
                                            </div>
                                            {!! $row("Driver's License No.", $b->driver_license_no) !!}
                                            {!! $row('License Expiry', optional($b->license_expiry)->format('M d, Y') ?: '—') !!}
                                            {!! $row('National ID No.', $b->national_id_no) !!}
                                            {!! $row('ID Type', $b->id_type) !!}
                                        </div>
                                    </div>

                                    {{-- RIGHT COLUMN: Documents --}}
                                    <div class="col-lg-7" style="padding:1.5rem; background:#F8FAFC;">
                                        <div style="font-size:.65rem; font-weight:800; letter-spacing:1.5px;
                                                    text-transform:uppercase; color:#94A3B8;
                                                    border-bottom:1px solid #E2E8F0; padding-bottom:.4rem;
                                                    margin-bottom:1.25rem;">
                                            Uploaded Documents
                                        </div>

                                        {{-- Valid ID --}}
                                        <div class="mb-4">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span style="background:#DBEAFE; color:#1D4ED8; font-size:.7rem;
                                                             font-weight:800; padding:.2rem .6rem;
                                                             border-radius:5px; text-transform:uppercase;">
                                                    Required
                                                </span>
                                                <span style="font-weight:700; font-size:.875rem; color:#1E293B;">
                                                    Valid ID
                                                    @if($b->id_type)
                                                        <span style="font-weight:500; color:#64748B; font-size:.8rem;">
                                                            ({{ $b->id_type }})
                                                        </span>
                                                    @endif
                                                </span>
                                            </div>

                                            @if($b->valid_id_path)
                                                @if(in_array(strtolower(pathinfo($b->valid_id_path, PATHINFO_EXTENSION)), ['pdf']))
                                                    <div style="background:#fff; border:1px solid #E2E8F0;
                                                                border-radius:12px; padding:1.5rem; text-align:center;">
                                                        <i class="bi bi-file-earmark-pdf"
                                                           style="font-size:2.5rem; color:#EF4444;"></i>
                                                        <div style="font-size:.85rem; color:#64748B; margin:.5rem 0;">
                                                            PDF Document
                                                        </div>
                                                        <a href="{{ asset('storage/' . $b->valid_id_path) }}"
                                                           target="_blank"
                                                           style="background:#EFF6FF; color:#1D4ED8; font-size:.8rem;
                                                                  font-weight:700; padding:.4rem 1rem;
                                                                  border-radius:8px; text-decoration:none;
                                                                  display:inline-flex; align-items:center; gap:.4rem;">
                                                            <i class="bi bi-box-arrow-up-right"></i> Open PDF
                                                        </a>
                                                    </div>
                                                @else
                                                    <div style="border:1px solid #E2E8F0; border-radius:12px;
                                                                overflow:hidden; background:#fff;">
                                                        <img src="{{ asset('storage/' . $b->valid_id_path) }}"
                                                             style="width:100%; max-height:260px;
                                                                    object-fit:contain; display:block; padding:.5rem;"
                                                             alt="Valid ID">
                                                        <div style="padding:.5rem .75rem; border-top:1px solid #F1F5F9;
                                                                    background:#F8FAFC;">
                                                            <a href="{{ asset('storage/' . $b->valid_id_path) }}"
                                                               target="_blank"
                                                               style="font-size:.75rem; color:#3B82F6;
                                                                      font-weight:600; text-decoration:none;">
                                                                <i class="bi bi-box-arrow-up-right me-1"></i>
                                                                Open full size
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div style="background:#FEF3C7; border:1px dashed #FDE68A;
                                                            border-radius:10px; padding:1rem; text-align:center;
                                                            color:#92400E; font-size:.85rem; font-weight:600;">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    No valid ID uploaded
                                                </div>
                                            @endif
                                        </div>

                                        {{-- GCash Receipt --}}
                                        <div class="mb-4">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span style="background:#FEF9C3; color:#854D0E; font-size:.7rem;
                                                             font-weight:800; padding:.2rem .6rem;
                                                             border-radius:5px; text-transform:uppercase;
                                                             letter-spacing:.5px;">
                                                    GCash Receipt
                                                </span>
                                                <span style="font-weight:700; font-size:.875rem; color:#1E293B;">
                                                    Transaction Proof
                                                </span>
                                            </div>

                                            @if($b->gcash_receipt_path)
                                                @if(in_array(strtolower(pathinfo($b->gcash_receipt_path, PATHINFO_EXTENSION)), ['pdf']))
                                                    <div style="background:#fff; border:1px solid #E2E8F0;
                                                                border-radius:12px; padding:1.5rem; text-align:center;">
                                                        <i class="bi bi-file-earmark-pdf"
                                                           style="font-size:2.5rem; color:#EF4444;"></i>
                                                        <div style="font-size:.85rem; color:#64748B; margin:.5rem 0;">
                                                            PDF Receipt
                                                        </div>
                                                        <a href="{{ asset('storage/' . $b->gcash_receipt_path) }}"
                                                           target="_blank"
                                                           style="background:#EFF6FF; color:#1D4ED8; font-size:.8rem;
                                                                  font-weight:700; padding:.4rem 1rem;
                                                                  border-radius:8px; text-decoration:none;
                                                                  display:inline-flex; align-items:center; gap:.4rem;">
                                                            <i class="bi bi-box-arrow-up-right"></i> Open PDF
                                                        </a>
                                                    </div>
                                                @else
                                                    <div style="border:1px solid #E2E8F0; border-radius:12px;
                                                                overflow:hidden; background:#fff;">
                                                        <img src="{{ asset('storage/' . $b->gcash_receipt_path) }}"
                                                             style="width:100%; max-height:260px;
                                                                    object-fit:contain; display:block;
                                                                    padding:.5rem;"
                                                             alt="GCash Receipt">
                                                        <div style="padding:.5rem .75rem; border-top:1px solid #F1F5F9;
                                                                    background:#F8FAFC;">
                                                            <a href="{{ asset('storage/' . $b->gcash_receipt_path) }}"
                                                               target="_blank"
                                                               style="font-size:.75rem; color:#3B82F6;
                                                                      font-weight:600; text-decoration:none;">
                                                                <i class="bi bi-box-arrow-up-right me-1"></i>
                                                                Open full size
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div style="background:#FEF3C7; border:1px dashed #FDE68A;
                                                            border-radius:10px; padding:1rem; text-align:center;
                                                            color:#92400E; font-size:.85rem; font-weight:600;">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    No GCash receipt uploaded
                                                </div>
                                            @endif
                                        </div>

                                        @if(in_array($b->balance_payment_status, ['pending_confirmation', 'confirmed']))
                                        <div style="margin-top:1.25rem;">
                                            <div style="font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#94A3B8; margin-bottom:.75rem;">
                                                Balance Payment Receipt
                                            </div>

                                            {{-- Reference number --}}
                                            <div style="font-size:.8rem; color:#64748B; margin-bottom:.75rem;">
                                                <span style="font-weight:700; color:#1E293B;">Ref No: </span>
                                                {{ $b->balance_gcash_reference_no ?? '—' }}
                                            </div>

                                            {{-- Receipt image --}}
                                            @if($b->balance_gcash_receipt_path)
                                                <img src="{{ Storage::url($b->balance_gcash_receipt_path) }}"
                                                     style="width:100%; max-height:280px; object-fit:contain; border-radius:10px; border:1px solid #E2E8F0; display:block;">
                                                <a href="{{ Storage::url($b->balance_gcash_receipt_path) }}"
                                                   target="_blank"
                                                   style="font-size:.75rem; color:#3B82F6; font-weight:600; text-decoration:none; display:block; margin-top:.5rem;">
                                                    Open full size
                                                </a>
                                            @endif

                                            {{-- Confirmation status --}}
                                            <div style="margin-top:.75rem; font-size:.78rem; font-weight:700;
                                                        color:{{ $b->balance_payment_status === 'confirmed' ? '#166534' : '#D97706' }};">
                                                {{ $b->balance_payment_status === 'confirmed' ? '✓ Balance payment confirmed' : '⏳ Awaiting confirmation' }}
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Birth Certificate --}}
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span style="background:#F0FDF4; color:#065F46; font-size:.7rem;
                                                             font-weight:800; padding:.2rem .6rem;
                                                             border-radius:5px; text-transform:uppercase;">
                                                    Optional
                                                </span>
                                                <span style="font-weight:700; font-size:.875rem; color:#1E293B;">
                                                    Birth Certificate
                                                </span>
                                            </div>

                                            @if($b->birth_cert_path)
                                                @if(in_array(strtolower(pathinfo($b->birth_cert_path, PATHINFO_EXTENSION)), ['pdf']))
                                                    <div style="background:#fff; border:1px solid #E2E8F0;
                                                                border-radius:12px; padding:1.5rem; text-align:center;">
                                                        <i class="bi bi-file-earmark-pdf"
                                                           style="font-size:2.5rem; color:#EF4444;"></i>
                                                        <div style="font-size:.85rem; color:#64748B; margin:.5rem 0;">
                                                            PDF Document
                                                        </div>
                                                        <a href="{{ asset('storage/' . $b->birth_cert_path) }}"
                                                           target="_blank"
                                                           style="background:#EFF6FF; color:#1D4ED8; font-size:.8rem;
                                                                  font-weight:700; padding:.4rem 1rem;
                                                                  border-radius:8px; text-decoration:none;
                                                                  display:inline-flex; align-items:center; gap:.4rem;">
                                                            <i class="bi bi-box-arrow-up-right"></i> Open PDF
                                                        </a>
                                                    </div>
                                                @else
                                                    <div style="border:1px solid #E2E8F0; border-radius:12px;
                                                                overflow:hidden; background:#fff;">
                                                        <img src="{{ asset('storage/' . $b->birth_cert_path) }}"
                                                             style="width:100%; max-height:260px;
                                                                    object-fit:contain; display:block; padding:.5rem;"
                                                             alt="Birth Certificate">
                                                        <div style="padding:.5rem .75rem; border-top:1px solid #F1F5F9;
                                                                    background:#F8FAFC;">
                                                            <a href="{{ asset('storage/' . $b->birth_cert_path) }}"
                                                               target="_blank"
                                                               style="font-size:.75rem; color:#3B82F6;
                                                                      font-weight:600; text-decoration:none;">
                                                                <i class="bi bi-box-arrow-up-right me-1"></i>
                                                                Open full size
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div style="background:#F8FAFC; border:1px dashed #E2E8F0;
                                                            border-radius:10px; padding:1rem; text-align:center;
                                                            color:#94A3B8; font-size:.85rem;">
                                                    <i class="bi bi-file-earmark-x me-1"></i>
                                                    No birth certificate uploaded
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="modal-footer"
                                 style="border-top:1px solid #E2E8F0; padding:.75rem 1.5rem;
                                        background:#fff; border-radius:0 0 16px 16px;
                                        justify-content:space-between;">
                                <span style="font-size:.8rem; color:#94A3B8;">
                                    Booked on {{ $b->created_at->format('M d, Y \a\t h:i A') }}
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                        style="border-radius:8px; font-weight:600; font-size:.8rem;"
                                        data-bs-dismiss="modal">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ══════════════════════════════════════════════ --}}

            @empty
                <tr>
                    <td colspan="8" class="text-center py-5" style="color:#94A3B8;">
                        <div style="font-size:2rem; margin-bottom:.5rem;">📋</div>
                        No bookings found.
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
        {{ $bookings->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@endsection
