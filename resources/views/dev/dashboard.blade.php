@extends('dev.layouts.app')

@section('title', 'Dashboard')
@section('content')
    <!-- User and Vendor Count Widgets -->
    <div class="row row-deck row-cards mb-4">
        <!-- Users Count Widget -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Users</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ \App\Models\User::count() }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-green d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('users.index') }}" class="text-decoration-none">Manage Users</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendors Count Widget -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Vendors</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ \App\Models\Vendor::count() }}</div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="status-dot status-dot-animated bg-blue d-block"></span>
                        </div>
                        <div class="col">
                            <a href="{{ route('vendors.index') }}" class="text-decoration-none">Manage Vendors</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
