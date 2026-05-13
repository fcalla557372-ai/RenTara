{{-- resources/views/customer/booking.blade.php --}}
@extends('layouts.customer')
@section('title', 'Book a Car')
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Book a Car</h1>
        <p class="page-subtitle">Select your preferred vehicle and complete your booking</p>
    </div>
</div>

<style>
    .sect-label {
        letter-spacing: 1.8px;
        text-transform: uppercase;
        color: var(--accent);
        border-bottom: 1px solid rgba(245,158,11,.2);
        padding-bottom: 8px;
        margin: 0 0 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sect-label i { font-size: 1rem; }

    /* Field styling from SignUp_page */
    .lux-field { margin-bottom: 18px; }

    .lux-field label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #64748B;
        margin-bottom: 7px;
        transition: color .25s;
    }

    html.dark .lux-field label { color: #6B6560; }
    .lux-field:focus-within label { color: var(--accent); }

    .lux-field input,
    .lux-field select {
        width: 100%;
        border-radius: 10px;
        border: 1px solid #E2E8F0;
        background: #F8FAFC;
        color: #1E293B;
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        padding: 12px 14px;
        outline: none;
        transition: border-color .25s, box-shadow .25s, background .25s;
    }

    html.dark .lux-field input,
    html.dark .lux-field select {
        background: #1A1A1A;
        border-color: #2A2A2A;
        color: #F0ECE0;
    }

    .lux-field input:focus,
    .lux-field select:focus {
        border-color: var(--accent);
        background: #fff;
        box-shadow: 0 0 0 3px var(--gold-glow);
    }

    html.dark .lux-field input:focus,
    html.dark .lux-field select:focus {
        background: #1A1810;
        box-shadow: 0 0 0 3px rgba(245,158,11,.12);
    }

    .lux-field input::placeholder { color: #CBD5E1; }
    html.dark .lux-field input::placeholder { color: #333; }
    html.dark .lux-field select option { background: #111; }

    .pt-toggle {
        display: grid;
        grid-template-columns: 1fr 1fr;
        background: #F1F5F9;
        border-radius: 12px;
        padding: 3px;
        gap: 3px;
        margin-bottom: 0;
    }
    html.dark .pt-toggle { background: #1A1A1A; }

    .pt-toggle label {
        cursor: pointer;
        display: block;
        width: 100%;
    }

    .pt-toggle input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .pt-tab {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #64748B;
        background: transparent;
        transition: background .18s, color .18s, box-shadow .18s;
        user-select: none;
        width: 100%;
        min-height: 44px;
        white-space: nowrap;
    }

    .pt-toggle input:checked + .pt-tab {
        background: #fff;
        color: #1E293B;
        box-shadow: 0 1px 4px rgba(0,0,0,.10);
    }
    html.dark .pt-toggle input:checked + .pt-tab {
        background: #2A2A2A;
        color: #F0ECE0;
    }

    .pt-tab i { font-size: 16px; color: #94A3B8; transition: color .18s; }
    .pt-toggle input:checked + .pt-tab i { color: #F59E0B; }

    .lux-field input[readonly] {
        opacity: .65;
        cursor: default;
        background: #F1F5F9;
    }
    html.dark .lux-field input[readonly] { background: #151515; }

    /* Info banner */
    .info-banner {
        background: rgba(245,158,11,.08);
        border: 1px solid rgba(245,158,11,.2);
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 13px;
        color: #92400E;
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }
    html.dark .info-banner { color: rgba(240,236,224,.7); }

    /* File upload */
    .file-wrapper { position: relative; }
    .file-wrapper input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 2; }
    .file-display {
        display: flex; align-items: center; gap: 10px;
        background: #F8FAFC;
        border: 1px dashed #CBD5E1;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 13px;
        color: #94A3B8;
        cursor: pointer;
        transition: border-color .25s, color .25s, background .25s;
    }
    html.dark .file-display { background: #1A1A1A; border-color: #333; color: #6B6560; }
    .file-wrapper:hover .file-display { border-color: var(--accent); color: var(--accent); }
    .file-name-tag { font-size: 12px; color: var(--accent); margin-top: 4px; display: none; }

    /* Total cost box */
    .total-box {
        background: linear-gradient(135deg, rgba(245,158,11,.08), rgba(245,158,11,.04));
        border: 2px dashed rgba(245,158,11,.3);
        border-radius: 14px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 24px 0;
    }
    html.dark .total-box {
        background: rgba(245,158,11,.06);
        border-color: rgba(245,158,11,.2);
    }

    .total-label { font-size: .8rem; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 1px; }
    .total-value { font-size: 2rem; font-weight: 900; color: var(--accent); font-family: 'Playfair Display', serif; }

    /* Submit button with shimmer (from SignUp_page) */
    .btn-checkout {
        position: relative;
        padding: 15px 32px;
        border: none;
        border-radius: 12px;
        background: var(--accent);
        color: #0A0800;
        font-family: 'DM Sans', sans-serif;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        overflow: hidden;
        transition: background .25s, transform .2s, box-shadow .25s;
    }
    .btn-checkout::after {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.25), transparent);
        transform: translateX(-100%);
        transition: transform .5s;
    }
    .btn-checkout:hover {
        background: var(--accent2);
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(245,158,11,.25);
    }
    .btn-checkout:hover::after { transform: translateX(100%); }
    .btn-checkout:active { transform: translateY(0); }

    .booking-card {
        background: #FFFFFF;
        border-radius: 24px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 20px 60px rgba(15, 23, 42, .08);
    }
    html.dark .booking-card {
        background: #0F172A;
        border-color: #1E293B;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .35);
    }

    .dark-divider { border-color: #F1F5F9; margin: 28px 0; }
    html.dark .dark-divider { border-color: #1E1E1E; }
</style>

@if($errors->any())
<div class="alert alert-danger mb-4" style="border-radius:12px; font-size:.875rem;">
    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('customer.booking.store') }}" enctype="multipart/form-data"
      @submit="updateAmountPaidFields()">
@csrf

<div class="booking-card p-4 p-md-5" x-data="bookingCalc()" x-init="init()">

    {{-- ── PERSONAL INFORMATION ── --}}
    <div class="sect-label"><i class='bx bx-user'></i> Personal Information</div>
    <div class="row g-3 mb-2">
        <div class="col-md-6">
            <div class="lux-field">
                <label>Full Name</label>
                <input type="text" value="{{ $user->name }}" readonly>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label>Date of Birth</label>
                <input type="date" name="date_of_birth" value="{{ $user->date_of_birth?->format('Y-m-d') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label>Phone Number</label>
                <input type="text" value="{{ $user->phone }}" readonly>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label>Email Address</label>
                <input type="email" value="{{ $user->email }}" readonly>
            </div>
        </div>
    </div>

    <hr class="dark-divider">

    {{-- ── IDENTITY & DOCUMENTS ── --}}
    <div class="sect-label"><i class='bx bx-id-card'></i> Identity & Documents</div>
    <div class="info-banner">
        <i class='bx bx-info-circle' style="font-size:1.1rem;flex-shrink:0;margin-top:1px;"></i>
        <span>A valid driver's license and a government-issued ID are required. Uploaded documents are reviewed by staff before your rental begins.</span>
    </div>
    <div class="row g-3 mb-2">
        <div class="col-md-6">
            <div class="lux-field">
                <label>Driver's License No. *</label>
                <input type="text" name="driver_license_no"
                       value="{{ old('driver_license_no', session('reg_driver_license_no')) }}"
                       placeholder="N01-23-456789" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label>License Expiry Date *</label>
                <input type="date" name="license_expiry"
                       value="{{ old('license_expiry', session('reg_license_expiry')) }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label x-text="getIdNumberLabel()"></label>
                <input type="text" name="national_id_no"
                       value="{{ old('national_id_no', session('reg_national_id_no')) }}"
                       x-bind:placeholder="getIdNumberPlaceholder()" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label>ID Type *</label>
                <select name="id_type" required x-model="selectedIdType">
                    <option value="">Select ID Type</option>
                    @foreach(['PhilSys National ID','Passport','SSS','UMID','Driver\'s License','Voter\'s ID','PRC ID'] as $t)
                    <option value="{{ $t }}" {{ old('id_type', session('reg_id_type')) === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label>Upload Driver's License Photo * <span style="text-transform:none;font-weight:400;color:#94A3B8;">(JPG, PNG, PDF — max 10MB)</span></label>
                <div class="file-wrapper">
                    <input type="file" name="license_photo" accept=".jpg,.jpeg,.png,.pdf" required
                           onchange="showFile(this,'lp')">
                    <div class="file-display"><i class='bx bx-paperclip'></i> Choose file or drag here</div>
                    <div class="file-name-tag" id="lp"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label x-text="getValidIdLabel()"></label>
                <div class="file-wrapper">
                    <input type="file" name="valid_id" accept=".jpg,.jpeg,.png,.pdf" required
                           onchange="showFile(this,'vid')">
                    <div class="file-display"><i class='bx bx-paperclip'></i> Choose file or drag here</div>
                    <div class="file-name-tag" id="vid"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label>Upload Birth Certificate <span style="text-transform:none;font-weight:400;color:#94A3B8;">(optional)</span></label>
                <div class="file-wrapper">
                    <input type="file" name="birth_cert" accept=".jpg,.jpeg,.png,.pdf"
                           onchange="showFile(this,'bc')">
                    <div class="file-display"><i class='bx bx-paperclip'></i> Choose file or drag here</div>
                    <div class="file-name-tag" id="bc"></div>
                </div>
            </div>
        </div>
    </div>

    <hr style="border-color:#F1F5F9;margin:28px 0;">

    {{-- ── RENTAL DETAILS ── --}}
    <div class="sect-label"><i class='bx bx-car'></i> Rental Details</div>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="lux-field">
                <label>Select Car *</label>
                <select name="car_id" required
                        @change="onCarChange($event)">
                    <option value="">Choose a car...</option>
                    @foreach($cars as $car)
                    <option value="{{ $car->id }}"
                            data-rate="{{ $car->daily_rate }}"
                            {{ old('car_id') == $car->id ? 'selected' : '' }}>
                        {{ $car->name }} ({{ $car->type }}) — ₱{{ number_format($car->daily_rate, 2) }}/day
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label>Pick-up Date *</label>
                <input type="date" name="pickup_date"
                       x-model="pickupDate"
                       @change="calcTotal()"
                       value="{{ old('pickup_date') }}"
                       min="{{ date('Y-m-d') }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label>Return Date *</label>
                <input type="date" name="return_date"
                       x-model="returnDate"
                       @change="calcTotal()"
                       value="{{ old('return_date') }}" required>
            </div>
        </div>
    </div>

    <div class="sect-label"><i class='bx bx-credit-card'></i> Payment Section</div>
    <div class="row g-3 mb-2">
        <div class="col-md-6">
           <div class="lux-field" style="margin-bottom: 4px;">
    <label>Payment Type *</label>
    <div class="pt-toggle">
                    <label>
                        <input type="radio" name="payment_type" value="full"
                               x-model="paymentType"
                               @change="setPaymentType('full')"
                               {{ old('payment_type', 'full') === 'full' ? 'checked' : '' }}>
                        <div class="pt-tab">
                            <i class='bx bx-check-circle'></i> Pay in Full
                        </div>
                    </label>
                    <label>
                        <input type="radio" name="payment_type" value="partial"
                               x-model="paymentType"
                               @change="setPaymentType('partial')"
                               {{ old('payment_type') === 'partial' ? 'checked' : '' }}>
                        <div class="pt-tab">
                            <i class='bx bx-slider-alt'></i> Partial Payment
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label>Payment Method</label>
                <input type="text" readonly value="GCash only" />
                <input type="hidden" name="payment_method" value="GCash">
            </div>
        </div>
        <div class="col-12" x-show="paymentType === 'partial'" style="display:none;">
            <div class="lux-field">
                <label>Amount to be Paid (30% upfront) *</label>
                <input type="text" readonly :value="'₱' + formatCurrency(partialAmount)" />
                <input type="hidden" name="amount_paid" x-ref="amountPaidPartial" :value="partialAmount">
            </div>
        </div>
        <div class="col-md-6" x-show="paymentType === 'full'" style="display:none;">
            <div class="lux-field">
                <label>Amount Paid *</label>
                <input type="text" readonly :value="'₱' + formatCurrency(totalNumeric)" />
                <input type="hidden" name="amount_paid" x-ref="amountPaidFull" :value="totalNumeric">
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label>GCash Reference Number *</label>
                <input type="text" name="gcash_reference_no"
                       value="{{ old('gcash_reference_no') }}"
                       placeholder="7040 658 290157" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="lux-field">
                <label>Upload GCash Receipt * <span style="text-transform:none;font-weight:400;color:#94A3B8;">(JPG, PNG, PDF — max 10MB)</span></label>
                <div class="file-wrapper">
                    <input type="file" name="gcash_receipt" accept=".jpg,.jpeg,.png,.pdf" required
                           onchange="showFile(this,'gr')">
                    <div class="file-display"><i class='bx bx-paperclip'></i> Choose file or drag here</div>
                    <div class="file-name-tag" id="gr"></div>
                </div>
            </div>
        </div>
        <div class="col-12" x-show="paymentType === 'partial'" style="display:none;">
            <div class="lux-field">
                <label>Remaining Balance</label>
                <input type="text" readonly :value="'₱' + formatCurrency(remainingBalance)"
                       style="font-weight:700;">
            </div>
        </div>
    </div>

    {{-- Total Cost --}}
    <div class="total-box">
        <div>
            <div class="total-label" x-text="paymentType === 'partial' ? 'Amount to Pay Now (30% Upfront)' : 'Estimated Total'"></div>
            <div style="font-size:.8rem;color:#94A3B8;margin-top:2px;" x-text="daysText"></div>
        </div>
        <div class="total-value" x-text="paymentType === 'partial' ? '₱' + formatCurrency(partialAmount) : '₱' + total"></div>
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn-checkout">
            Proceed to Checkout →
        </button>
    </div>

</div>
</form>

@push('scripts')
<script>
function bookingCalc() {
    return {
        pickupDate: '{{ old("pickup_date", "") }}',
        returnDate: '{{ old("return_date", "") }}',
        rate: 0,
        totalNumeric: 0,
        total: '0.00',
        daysText: '',
        paymentType: '{{ old("payment_type", "full") }}',
        partialAmount: 0,
        remainingBalance: 0,
        selectedIdType: '{{ old("id_type", "") }}',

        init() {
            const selectedCar = document.querySelector('select[name="car_id"] option:checked');
            this.rate = selectedCar ? parseFloat(selectedCar.dataset.rate || 0) : 0;
            this.calcTotal();
        },

        onCarChange(e) {
            const opt = e.target.options[e.target.selectedIndex];
            this.rate = parseFloat(opt.dataset.rate || 0);
            this.calcTotal();
        },

        setPaymentType(type) {
            this.paymentType = type;
            if (this.paymentType === 'full') {
                this.partialAmount = 0;
                this.remainingBalance = 0;
            } else if (this.paymentType === 'partial') {
                this.partialAmount = +(this.totalNumeric * 0.3).toFixed(2);
                this.remainingBalance = +(this.totalNumeric - this.partialAmount).toFixed(2);
            }
        },

        getIdNumberLabel() {
            if (!this.selectedIdType) {
                return 'ID Number *';
            }
            return this.selectedIdType + ' Number *';
        },

        getIdNumberPlaceholder() {
            switch (this.selectedIdType) {
                case 'PhilSys National ID': return '1234-5678-9012';
                case 'Passport': return 'AB1234567';
                case 'SSS': return '01-2345678-9';
                case 'UMID': return '1234-5678-9012';
                case 'Driver\'s License': return 'N01-23-456789';
                case 'Voter\'s ID': return '1234-5678-9012';
                case 'PRC ID': return '1234567';
                default: return 'Enter ID number';
            }
        },

        getValidIdLabel() {
            return this.selectedIdType ? 'Upload ' + this.selectedIdType + ' Photo *' : 'Upload Valid ID *';
        },

        calcTotal() {
            if (this.pickupDate && this.returnDate && this.rate > 0) {
                const p = new Date(this.pickupDate);
                const r = new Date(this.returnDate);
                const days = Math.max(1, Math.floor((r - p) / 86400000) + 1);
                this.totalNumeric = +(this.rate * days).toFixed(2);
                this.total = this.totalNumeric.toLocaleString('en-PH', { minimumFractionDigits: 2 });
                this.daysText = days + ' day' + (days !== 1 ? 's' : '') + ' × ₱' + this.rate.toLocaleString('en-PH') + '/day';
                if (this.paymentType === 'full') {
                    this.partialAmount = 0;
                    this.remainingBalance = 0;
                } else if (this.paymentType === 'partial') {
                    this.partialAmount = +(this.totalNumeric * 0.3).toFixed(2);
                    this.remainingBalance = +(this.totalNumeric - this.partialAmount).toFixed(2);
                } else {
                    this.partialAmount = 0;
                    this.remainingBalance = this.totalNumeric;
                }
            } else {
                this.totalNumeric = 0;
                this.total = '0.00';
                this.daysText = '';
                this.partialAmount = 0;
                this.remainingBalance = 0;
            }
        },

        formatCurrency(value) {
            return Number(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        updateAmountPaidFields() {
            // Update the hidden input value attributes before form submission
            if (this.paymentType === 'partial' && this.$refs.amountPaidPartial) {
                this.$refs.amountPaidPartial.value = this.partialAmount;
            } else if (this.paymentType === 'full' && this.$refs.amountPaidFull) {
                this.$refs.amountPaidFull.value = this.totalNumeric;
            }
        }
    }
}

function showFile(input, targetId) {
    const el = document.getElementById(targetId);
    if (!el || !input.files || !input.files.length) {
        return;
    }
    el.textContent = '✓ ' + input.files[0].name;
    el.style.display = 'block';
}
</script>
@endpush
@endsection