@extends('dev.layouts.app')

@section('title', 'Edit Vendor')
@section('content')
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <form action="{{ route('vendors.update', $vendor->id) }}" method="POST" class="card"
                    onsubmit="showLoading('Updating vendor...')" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-header">
                        <h3 class="card-title">Edit Vendor</h3>
                    </div>
                    <div class="card-body">
                        <!-- Company Information Section -->
                        <h4>Company Information</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Company Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name', $vendor->name) }}"
                                        placeholder="Enter company name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email', $vendor->email) }}"
                                        placeholder="Enter company email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" value="{{ old('phone', $vendor->phone) }}"
                                        placeholder="Enter company phone">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="5"
                                        placeholder="Enter company address">{{ old('address', $vendor->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Website</label>
                                    <input type="text" class="form-control @error('website') is-invalid @enderror"
                                        name="website" value="{{ old('website', $vendor->website) }}"
                                        placeholder="Enter company website">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            {{ old('is_active', $vendor->is_active) ? 'checked' : '' }}>
                                        <span class="form-check-label">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Company Logo</label>
                            @if ($vendor->logo)
                                <div class="mb-2">
                                    <img src="{{ asset('vendors_logo/' . $vendor->logo) }}" alt="{{ $vendor->name }} Logo"
                                        class="img-fluid" style="max-height: 100px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" name="logo">
                            <small class="form-hint">Leave empty to keep current logo. Upload new image to change.</small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Account Manager Section -->
                        <h4>Account Manager</h4>
                        <div class="mb-3">
                            <label class="form-label required">Associated User</label>
                            <input type="text" class="form-control" value="{{ $users->name }} ({{ $users->email }})"
                                readonly>
                            <input type="hidden" name="user_id" value="{{ $users->id }}">
                            <small class="form-hint">The user who manages this vendor account</small>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                                <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                <path d="M14 4l0 4l-6 0l0 -4"></path>
                            </svg>
                            Update
                        </button>

                        <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M18 6l-12 12"></path>
                                <path d="M6 6l12 12"></path>
                            </svg>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
