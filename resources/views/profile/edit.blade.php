@extends('layouts.layouts_dashboard')

@section('title', 'Edit Profile')

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- vendor and user profile in one form --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Profile Information</h3>
                </div>
                <div class="card-body">
                    @if (session('status') === 'vendor-profile-updated' || session('status') === 'profile-updated')
                        <div class="alert alert-success">
                            Profile has been updated.
                        </div>
                    @endif

                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <h4 class="mb-3">Vendor Information</h4>
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="form-group">
                                    <label class="form-label">Current Logo</label>
                                    @if ($vendor->logo)
                                        <img src="{{ asset('vendors_logo/' . $vendor->logo) }}" alt="Vendor Logo"
                                            class="img-fluid rounded mb-2" style="max-height: 150px;">
                                    @else
                                        <div class="text-muted">No logo uploaded</div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="logo">Update Logo</label>
                                    <input type="file" class="form-control" id="logo" name="logo">
                                    @error('logo')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="vendor_name">Company Name</label>
                                    <input type="text" class="form-control" id="vendor_name" name="vendor_name"
                                        value="{{ old('vendor_name', $vendor->name) }}" required>
                                    @error('vendor_name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="vendor_email">Company Email</label>
                                    <input type="email" class="form-control" id="vendor_email" name="vendor_email"
                                        value="{{ old('vendor_email', $vendor->email) }}" required>
                                    @error('vendor_email')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="phone">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        value="{{ old('phone', $vendor->phone) }}" required>
                                    @error('phone')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="website">Website <span
                                            class="text-muted">(optional)</span></label>
                                    <input type="url" class="form-control" id="website" name="website"
                                        value="{{ old('website', $vendor->website) }}">
                                    @error('website')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address', $vendor->address) }}</textarea>
                                    @error('address')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <h4 class="mb-3">User Information</h4>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mb-4">
                @include('profile.partials.update-password-form')
            </div>

            <div class="mb-4">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
