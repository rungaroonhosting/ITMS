@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <h1 class="display-4 fw-bold text-primary">Welcome Back, {{ auth()->user()->name }}! 👋</h1>
                <p class="lead text-muted">You're logged in as {{ auth()->user()->role_display_name }}</p>
                <p class="text-muted">Last login: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'First time login' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x mb-3"></i>
                <h4>150</h4>
                <p class="mb-0">Total Employees</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-laptop fa-3x mb-3"></i>
                <h4>342</h4>
                <p class="mb-0">IT Assets</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <h4>12</h4>
                <p class="mb-0">Open Incidents</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                <h4>28</h4>
                <p class="mb-0">Service Requests</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @if(auth()->user()->hasPermission('incidents.create'))
                    <div class="col-md-3 mb-3">
                        <a href="#" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                            Report Incident
                        </a>
                    </div>
                    @endif
                    
                    @if(auth()->user()->hasPermission('service_requests.create'))
                    <div class="col-md-3 mb-3">
                        <a href="#" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-ticket-alt fa-2x d-block mb-2"></i>
                            Service Request
                        </a>
                    </div>
                    @endif
                    
                    @if(auth()->user()->hasPermission('assets.view'))
                    <div class="col-md-3 mb-3">
                        <a href="#" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-search fa-2x d-block mb-2"></i>
                            Search Assets
                        </a>
                    </div>
                    @endif
                    
                    @if(auth()->user()->hasPermission('employees.view'))
                    <div class="col-md-3 mb-3">
                        <a href="#" class="btn btn-outline-primary w-100 py-3">
                            <i class="fas fa-address-book fa-2x d-block mb-2"></i>
                            Employee Directory
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Your Permissions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Your Permissions</h5>
            </div>
            <div class="card-body">
                @if(auth()->user()->isSuperAdmin())
                    <div class="alert alert-success">
                        <i class="fas fa-crown me-2"></i>
                        You have <strong>Super Administrator</strong> privileges with full system access.
                    </div>
                @else
                    <div class="row">
                        @foreach(auth()->user()->permissions as $permission)
                            <div class="col-md-4 mb-2">
                                <span class="badge bg-primary me-2">
                                    <i class="fas fa-check me-1"></i>{{ $permission }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
