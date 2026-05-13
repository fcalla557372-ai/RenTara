{{-- resources/views/staff/car-inventory.blade.php --}}
@extends('layouts.staff')
@section('title', 'Car Inventory')
@section('content')

<style>
    .car-card {
        min-height: 460px;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .car-photo-frame {
        min-height: 240px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #F8FAFC;
    }

    .car-photo {
        max-height: 230px;
        width: auto;
        object-fit: contain;
    }

    .car-icon-fallback {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 240px;
        width: 100%;
        font-size: 3rem;
    }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Car Inventory</h1>
        <p class="page-subtitle">View and manage rental vehicles</p>
    </div>
</div>

{{-- Filter Bar --}}
<div style="background:#fff; border-radius:14px; border:1px solid #E2E8F0; padding:1rem 1.25rem; margin-bottom:1.25rem; box-shadow:0 1px 2px rgba(0,0,0,.04);">
    <form method="GET" id="filterForm" style="display:flex; gap:1rem; align-items:flex-end; flex-wrap:wrap;">
        {{-- Car Type Filter --}}
        <div>
            <label for="typeFilter" style="display:block; font-weight:600; font-size:.85rem; color:#475569; margin-bottom:.5rem;">Car Type</label>
            <select id="typeFilter" name="type" class="form-select" style="width:200px; font-size:.875rem;" onchange="this.form.submit();">
                <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>All Types</option>
                @foreach($carTypes as $type)
                <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        {{-- Status Filter --}}
        <input type="hidden" name="per_page" value="{{ request('per_page', 6) }}">
        <div>
            <label for="statusFilter" style="display:block; font-weight:600; font-size:.85rem; color:#475569; margin-bottom:.5rem;">Status</label>
            <select id="statusFilter" name="status" class="form-select" style="width:160px; font-size:.875rem;" onchange="this.form.submit();">
                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                <option value="rented" {{ request('status') === 'rented' ? 'selected' : '' }}>Rented</option>
            </select>
        </div>

        {{-- Results Count --}}
        <div style="margin-left:auto; font-size:.8rem; color:#94A3B8; font-weight:600;">
            Showing {{ $cars->count() }} vehicle(s)
        </div>
    </form>
</div>

<div class="alert border-0 mb-4 d-flex align-items-start gap-3"
     style="background:#EFF6FF; border-radius:12px; color:#1E40AF;">
    <i class="bi bi-info-circle-fill mt-1" style="color:#3B82F6; flex-shrink:0;"></i>
    <div style="font-size:.875rem; font-weight:500;">
        Current fleet status — which cars are available and which are rented out.
        Contact an admin to add, edit, or remove vehicles.
    </div>
</div>

<div class="row g-3">
    @forelse($cars as $car)
    <div class="col-md-6 col-lg-4 d-flex">
        <div class="car-card bg-white rounded-3 shadow-sm p-4 position-relative w-100 d-flex flex-column" style="border-top: 4px solid #F59E0B;">
            <span class="badge bg-secondary position-absolute top-0 start-0 m-2" style="font-size:.7rem;">{{ $car->type }}</span>
            <span class="status-badge position-absolute top-0 end-0 m-2
                {{ $car->status === 'available' ? 'badge-available' : 'badge-rented' }}">
                {{ ucfirst($car->status) }}
            </span>
            <div class="car-photo-frame">
                @if($car->image_path)
                    <img src="{{ asset('storage/' . $car->image_path) }}"
                         alt="{{ $car->name }}"
                         class="car-photo rounded-3"
                         onerror="this.classList.add('d-none');this.nextElementSibling?.classList.remove('d-none');"
                         loading="lazy">
                    <div class="car-icon-fallback d-none">
                        {{ match($car->type) {
                            'Sedan' => '🚗',
                            'SUV' => '🚙',
                            'Luxury' => '🏎️',
                            'Electric Car' => '⚡',
                            'Van' => '🚐',
                            'MPV' => '🚐',
                            'Hatchback' => '🚘',
                            'Crossover' => '🚙',
                            default => '🚘',
                        } }}
                    </div>
                @else
                    <div class="car-icon-fallback">
                        {{ match($car->type) {
                            'Sedan' => '🚗',
                            'SUV' => '🚙',
                            'Luxury' => '🏎️',
                            'Electric Car' => '⚡',
                            'Van' => '🚐',
                            'MPV' => '🚐',
                            'Hatchback' => '🚘',
                            'Crossover' => '🚙',
                            default => '🚘',
                        } }}
                    </div>
                @endif
            </div>
            <h6 class="fw-bold text-center">{{ $car->name }}</h6>
            <p class="text-center fw-bold mb-0" style="color: #F59E0B;">₱{{ number_format($car->daily_rate, 2) }}/day</p>
        </div>
    </div>
    @empty
        <div style="grid-column:1/-1; text-align:center; padding:4rem 2rem; color:#94A3B8;">
            <div style="font-size:2.5rem; margin-bottom:.75rem;">🚗</div>
            <div style="font-weight:700; font-size:1rem; color:#64748B; margin-bottom:.25rem;">No vehicles found</div>
            <div style="font-size:.875rem;">No cars match the selected filters. <a href="{{ route('staff.car-inventory') }}" style="color:#F59E0B; text-decoration:none; font-weight:600;">Clear filters</a></div>
        </div>
    @endforelse
</div>

@if($cars->total())
    <div class="table-card mt-4 p-3 d-flex flex-column flex-md-row flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-3 flex-shrink-0">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted" style="font-size:.88rem;">Per page</span>
                <form method="GET" class="d-flex align-items-center gap-2 mb-0" style="min-width:auto;">
                    <input type="hidden" name="type" value="{{ request('type', 'all') }}">
                    <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                    <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto; min-width:90px; max-width:120px;">
                        @foreach([6, 9, 12] as $size)
                            <option value="{{ $size }}" {{ $cars->perPage() == $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <span class="text-muted" style="font-size:.88rem;">
                Showing {{ $cars->firstItem() }}–{{ $cars->lastItem() }} of {{ $cars->total() }} vehicles
            </span>
        </div>

        @if($cars->hasPages())
            <div class="pagination-wrapper flex-shrink-0">
                {{ $cars->withQueryString()->links('pagination::bootstrap-5-no-summary') }}
            </div>
        @endif
    </div>
@endif

@endsection
