{{-- resources/views/admin/car-inventory.blade.php --}}
@extends('layouts.admin')
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
        <p class="page-subtitle">Manage your fleet of rental vehicles</p>
    </div>
    <button class="btn btn-brand d-flex align-items-center gap-2"
            data-bs-toggle="modal" data-bs-target="#addCarModal">
        <i class="bi bi-plus-lg"></i> Add Car
    </button>
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
        Cars currently rented out cannot be removed. A car must be returned and marked <strong>Completed</strong> before it can be deleted.
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger py-2 mb-4" style="border-radius:10px;">
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Car Grid --}}
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
            <p class="text-center fw-bold mb-3" style="color: #F59E0B;">₱{{ number_format($car->daily_rate, 2) }}/day</p>
            <div class="d-flex gap-2 mt-auto">
                <button class="btn btn-sm btn-outline-warning flex-grow-1"
                    data-bs-toggle="modal" data-bs-target="#editCarModal{{ $car->id }}">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <form method="POST" action="{{ route('admin.car-inventory.destroy', $car) }}"
                      onsubmit="return confirm('Remove this car?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger {{ $car->status === 'rented' ? 'disabled' : '' }}"
                        type="submit" {{ $car->status === 'rented' ? 'disabled' : '' }}>
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editCarModal{{ $car->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Car — {{ $car->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.car-inventory.update', $car) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body row g-3">
                        <div class="col-12">
                            <label class="form-label">Car Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $car->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                @foreach(['Sedan','SUV','Luxury','Electric Car','Van','MPV','Hatchback','Crossover'] as $t)
                                    <option value="{{ $t }}" {{ $car->type === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Daily Rate (₱)</label>
                            <input type="number" name="daily_rate" class="form-control" value="{{ $car->daily_rate }}" required min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Brand</label>
                            <input type="text" name="brand" class="form-control" value="{{ $car->brand }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" class="form-control" value="{{ $car->year }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Upload New Image (optional)</label>
                            <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning fw-bold">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
        <div style="grid-column:1/-1; text-align:center; padding:4rem 2rem; color:#94A3B8;">
            <div style="font-size:2.5rem; margin-bottom:.75rem;">🚗</div>
            <div style="font-weight:700; font-size:1rem; color:#64748B; margin-bottom:.25rem;">No vehicles found</div>
            <div style="font-size:.875rem;">No cars match the selected filters. <a href="{{ route('admin.car-inventory') }}" style="color:#F59E0B; text-decoration:none; font-weight:600;">Clear filters</a></div>
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

{{-- Add Car Modal --}}
<div class="modal fade" id="addCarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Car</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.car-inventory.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Car Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Toyota Vios 2024">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type *</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            @foreach(['Sedan','SUV','Luxury','Electric Car','Van','MPV','Hatchback','Crossover'] as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Daily Rate (₱) *</label>
                        <input type="number" name="daily_rate" class="form-control" required min="1" placeholder="2500">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control" placeholder="Toyota">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control" placeholder="Vios">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control" placeholder="2024" min="2000" max="2030">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Upload Image</label>
                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning fw-bold">Add Car</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
