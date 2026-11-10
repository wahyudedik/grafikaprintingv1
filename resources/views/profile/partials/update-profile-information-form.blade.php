<section>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Profile Information') }}</h3>
            {{-- <p class="card-subtitle">
                {{ __("Update your account's profile information and email address.") }}
            </p> --}}
        </div>
        <div class="card-body">
            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf 
            </form>

            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="mb-3">
                    <label class="form-label" for="name">{{ __('Name') }}</label>
                    <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">{{ __('Email') }}</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="alert alert-warning mt-2">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v2m0 4v.01"></path><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"></path></svg>
                                </div>
                                <div>
                                    {{ __('Your email address is unverified.') }}
                                    <button form="send-verification" class="btn btn-link btn-sm p-0 m-0">
                                        {{ __('Click here to re-send the verification email.') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <div class="alert alert-success mt-2">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </div>
                        @endif
                    @endif
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                    @if (session('status') === 'profile-updated')
                        <span class="text-success ms-2">{{ __('Saved.') }}</span>
                    @endif
                </div>
            </form>
        </div>
    </div>
</section>
