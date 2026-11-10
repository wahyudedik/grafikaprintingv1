<section>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Update Password') }}</h3>
            {{-- <p class="card-subtitle">
                {{ __('Ensure your account is using a long, random password to stay secure.') }}
            </p> --}}
        </div>
        <div class="card-body">
            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="mb-3">
                    <label class="form-label" for="update_password_current_password">{{ __('Current Password') }}</label>
                    <input id="update_password_current_password" name="current_password" type="password" 
                        class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                        autocomplete="current-password" />
                    @error('current_password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="update_password_password">{{ __('New Password') }}</label>
                    <input id="update_password_password" name="password" type="password" 
                        class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                        autocomplete="new-password" />
                    @error('password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                        class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                        autocomplete="new-password" />
                    @error('password_confirmation', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                    @if (session('status') === 'password-updated')
                        <span class="text-success ms-2">{{ __('Saved.') }}</span>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>
