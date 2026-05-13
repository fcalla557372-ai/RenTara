@extends('layouts.admin')
@section('title', 'Manage Users')
@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Manage Users</h1>
        <p class="page-subtitle">View and manage all system users</p>
    </div>
    <button class="btn btn-brand d-flex align-items-center gap-2"
            data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus"></i> Add Staff
    </button>
</div>

{{-- Info Banner --}}
<div class="alert border-0 mb-4 d-flex align-items-start gap-3"
     style="background:#EFF6FF; border-radius:12px; color:#1E40AF;">
    <i class="bi bi-info-circle-fill mt-1" style="color:#3B82F6; flex-shrink:0;"></i>
    <div style="font-size:.875rem; font-weight:500;">
        New customer registrations default to the <strong>Customer</strong> role.
        Only admins can create <strong>Staff</strong> accounts from this page.
        Admin accounts cannot be created here.
    </div>
</div>

{{-- Users Table --}}
<div class="table-card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="
                                width:34px; height:34px; border-radius:50%; flex-shrink:0;
                                background:{{ $user->role === 'admin' ? '#FEE2E2' : ($user->role === 'staff' ? '#DBEAFE' : '#F0FDF4') }};
                                color:{{ $user->role === 'admin' ? '#991B1B' : ($user->role === 'staff' ? '#1E40AF' : '#065F46') }};
                                display:flex; align-items:center; justify-content:center;
                                font-weight:800; font-size:.8rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:.875rem; color:#1E293B;">
                                    {{ $user->name }}
                                    @if($user->id === auth()->id())
                                        <span style="background:#F1F5F9; color:#64748B; font-size:.65rem;
                                                     font-weight:700; padding:.1rem .4rem; border-radius:4px;
                                                     margin-left:.25rem;">You</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <code style="background:#F1F5F9; padding:.2rem .5rem; border-radius:5px;
                                     font-size:.8rem; color:#475569;">
                            {{ $user->username }}
                        </code>
                    </td>
                    <td style="color:#64748B; font-size:.85rem;">{{ $user->email }}</td>
                    <td style="color:#64748B; font-size:.85rem;">{{ $user->phone ?? '—' }}</td>
                    <td>
                        @if($user->role === 'admin')
                            <span style="font-size:.8rem; font-weight:700; color:#991B1B;">
                                <i class="bi bi-shield-fill me-1" style="font-size:.6rem;"></i> Admin
                            </span>
                        @elseif($user->role === 'staff')
                            <span style="font-size:.8rem; font-weight:700; color:#1D4ED8;">
                                <i class="bi bi-person-badge me-1" style="font-size:.6rem;"></i> Staff
                            </span>
                        @else
                            <span style="font-size:.8rem; font-weight:700; color:#166534;">
                                <i class="bi bi-person me-1" style="font-size:.6rem;"></i> Customer
                            </span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size:.8rem; font-weight:700; color:{{ $user->status === 'active' ? '#166534' : '#991B1B' }};">
                            <span style="width:6px; height:6px; border-radius:50%; background:currentColor; display:inline-block; margin-right:5px;"></span>
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td style="display:flex; align-items:center; gap:.85rem;">
                        @if($user->id !== auth()->id())
                        <div class="d-flex gap-2" style="display:contents;">
                            <form method="POST"
                                  action="{{ route('admin.manage-users.toggleStatus', $user) }}" style="display:contents;">
                                @csrf
                                <button type="submit" style="background:none; border:none; padding:0; font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:underline; text-underline-offset:3px; color:{{ $user->status === 'active' ? '#D97706' : '#166534' }};">
                                    {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            @if($user->role !== 'admin')
                            <form method="POST"
                                  action="{{ route('admin.manage-users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')" style="display:contents;">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none; border:none; padding:0; font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:underline; text-underline-offset:3px; color:#991B1B;">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                            @endif
                        </div>
                        @else
                            <span style="color:#CBD5E1; font-size:.8rem;">—</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="p-3 d-flex justify-content-between align-items-center"
         style="border-top:1px solid #F1F5F9;">
        <div style="font-size:.8rem; color:#94A3B8;">
            Showing {{ $users->firstItem() }}–{{ $users->lastItem() }}
            of {{ $users->total() }} users
        </div>
        {{ $users->links() }}
    </div>
    @endif
</div>

{{-- Add Staff Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; border:none;
                                          box-shadow:0 20px 60px rgba(0,0,0,.15);">
            <div class="modal-header" style="border-bottom:1px solid #E2E8F0; padding:1.25rem 1.5rem;">
                <div>
                    <h5 class="modal-title fw-bold mb-0" style="color:#1E293B;">Add New Staff</h5>
                    <p class="mb-0 mt-1" style="font-size:.8rem; color:#64748B;">
                        Staff can access Tracking and Car Inventory.
                    </p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.manage-users.store') }}">
                @csrf
                <input type="hidden" name="role" value="staff">
                <div class="modal-body" style="padding:1.5rem;">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-size:.85rem; font-weight:600; color:#374151;">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control"
                                   style="border-radius:8px; border-color:#D1D5DB;"
                                   placeholder="e.g. Maria Santos" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.85rem; font-weight:600; color:#374151;">
                                Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="username" class="form-control"
                                   style="border-radius:8px; border-color:#D1D5DB;"
                                   placeholder="maria.santos" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.85rem; font-weight:600; color:#374151;">
                                Phone
                            </label>
                            <input type="text" name="phone" class="form-control"
                                   style="border-radius:8px; border-color:#D1D5DB;"
                                   placeholder="09XXXXXXXXX">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:.85rem; font-weight:600; color:#374151;">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" class="form-control"
                                   style="border-radius:8px; border-color:#D1D5DB;"
                                   placeholder="maria@rentara.com" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:.85rem; font-weight:600; color:#374151;">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" name="password" class="form-control"
                                   style="border-radius:8px; border-color:#D1D5DB;"
                                   placeholder="Minimum 6 characters" required>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-2 p-3"
                                 style="background:#EFF6FF; border-radius:8px; border:1px solid #BFDBFE;">
                                <i class="bi bi-person-badge" style="color:#3B82F6;"></i>
                                <span style="font-size:.85rem; font-weight:600; color:#1E40AF;">
                                    Role: Staff — Tracking & Car Inventory access only
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #E2E8F0; padding:1rem 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary"
                            style="border-radius:8px; font-weight:600;"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand"
                            style="border-radius:8px; padding:.5rem 1.5rem;">
                        <i class="bi bi-person-plus me-1"></i> Create Staff
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection