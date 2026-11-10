@extends('dev.layouts.app')

@section('title', 'Vendor Details')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Vendor Details</h3>
                    </div>
                    <div class="card-body">
                        <!-- Company Information Section -->
                        <h4>Company Information</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Company Name</label>
                                    <div class="form-control-plaintext">{{ $vendor->name }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="form-control-plaintext">{{ $vendor->email }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <div class="form-control-plaintext">{{ $vendor->phone }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <div class="form-control-plaintext">{{ $vendor->address }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Website</label>
                                    <div class="form-control-plaintext">{{ $vendor->website ?? 'Not provided' }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div>
                                        @if ($vendor->is_active)
                                            <span class="badge bg-green text-white">Active</span>
                                        @else
                                            <span class="badge bg-red text-white">Inactive</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($vendor->logo)
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Company Logo</label>
                                    <div>
                                        <img src="{{ asset('vendors_logo/' . $vendor->logo) }}" 
                                             alt="{{ $vendor->name }} Logo" 
                                             class="img-fluid" 
                                             style="max-height: 100px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <hr class="my-4">

                        <!-- Account Manager Section -->
                        <h4>Account Manager</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <div class="form-control-plaintext">{{ $users->name }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="form-control-plaintext">{{ $users->email }}</div>
                                </div>

                                <div class="mb-3"> 
                                    <label class="form-label">User Type</label>
                                    <div>
                                        @if ($users->usertype == 'dev')
                                            <span class="badge bg-blue text-white">Developer</span>
                                        @else
                                            <span class="badge bg-green text-white">Vendor</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email Verified</label>
                                    <div class="form-control-plaintext">
                                        @if ($users->email_verified_at)
                                            <span class="badge bg-green text-white">
                                                Verified ({{ $users->email_verified_at->format('d M Y H:i') }})
                                            </span>
                                        @else
                                            <span class="badge bg-red text-white">Not Verified</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Account Created</label>
                                    <div class="form-control-plaintext">{{ $users->created_at->format('d M Y H:i') }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Last Updated</label>
                                    <div class="form-control-plaintext">{{ $users->updated_at->format('d M Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('vendors.edit', $vendor->id) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                <path d="M16 5l3 3"></path>
                            </svg>
                            Edit
                        </a>

                        <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 12l14 0"></path>
                                <path d="M5 12l6 6"></path>
                                <path d="M5 12l6 -6"></path>
                            </svg>
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
