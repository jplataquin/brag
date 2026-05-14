@extends('layouts.app')

@section('title', 'Sign Up')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="text-center mb-4">
            <h2 style="font-family: 'Orbitron', sans-serif; color: #ff00ff; text-shadow: 0 0 15px rgba(255,0,255,0.3);">
                <i class="bi bi-person-plus-fill"></i> JOIN BRAG
            </h2>
            <p style="color: #555577; font-size: 0.9rem;">Create your account and enter the arena</p>
        </div>

        <div class="neon-card p-4">
            <form method="POST" action="{{ route('register') }}" id="register-form">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="firstname" class="form-label">FIRST NAME</label>
                        <input id="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror"
                               name="firstname" value="{{ old('firstname') }}" required autocomplete="given-name" autofocus>
                        @error('firstname')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="lastname" class="form-label">LAST NAME</label>
                        <input id="lastname" type="text" class="form-control @error('lastname') is-invalid @enderror"
                               name="lastname" value="{{ old('lastname') }}" required autocomplete="family-name">
                        @error('lastname')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">USERNAME</label>
                    <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                           name="username" value="{{ old('username') }}" required autocomplete="username"
                           placeholder="your_gamer_tag" maxlength="30" pattern="[a-zA-Z0-9_]+">
                    <small style="color: #555577; font-size: 0.75rem;">Letters, numbers, and underscores only. This is your unique identity.</small>
                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autocomplete="email"
                           placeholder="your@email.com">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="birthdate" class="form-label">BIRTHDATE</label>
                    <input id="birthdate" type="date" class="form-control @error('birthdate') is-invalid @enderror"
                           name="birthdate" value="{{ old('birthdate') }}" required
                           max="{{ now()->subYears(13)->format('Y-m-d') }}">
                    <div class="form-text text-white-50" style="font-size: 0.75rem;">You must be at least 13 years old to register.</div>
                    @error('birthdate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="gender" class="form-label">GENDER</label>
                    <select id="gender" class="form-select @error('gender') is-invalid @enderror" name="gender">
                        <option value="None" {{ old('gender') === 'None' ? 'selected' : '' }}>None</option>
                        <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required autocomplete="new-password"
                           placeholder="••••••••">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" type="password" class="form-control"
                           name="password_confirmation" required autocomplete="new-password"
                           placeholder="••••••••">
                </div>

                <!-- Parental Consent Section -->
                <div id="parental-consent-section" class="mb-4" style="display: none; background: rgba(0,240,255,0.05); border: 1px solid rgba(0,240,255,0.3); border-radius: 8px; padding: 15px;">
                    <h5 style="color: #00f0ff; font-family: 'Orbitron', sans-serif;"><i class="bi bi-shield-lock"></i> Parental Consent Required</h5>
                    <p style="font-size: 0.8rem; color: #aaa;">Because you are under 18, a parent or legal guardian must provide their details and a valid government ID to approve your account creation.</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="parent_firstname" class="form-label">Parent First Name</label>
                            <input type="text" class="form-control @error('parent_firstname') is-invalid @enderror" id="parent_firstname" name="parent_firstname" value="{{ old('parent_firstname') }}">
                            @error('parent_firstname')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="parent_lastname" class="form-label">Parent Last Name</label>
                            <input type="text" class="form-control @error('parent_lastname') is-invalid @enderror" id="parent_lastname" name="parent_lastname" value="{{ old('parent_lastname') }}">
                            @error('parent_lastname')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="parent_email" class="form-label">Parent Email Address</label>
                        <input type="email" class="form-control @error('parent_email') is-invalid @enderror" id="parent_email" name="parent_email" value="{{ old('parent_email') }}" placeholder="parent@email.com">
                        <div class="form-text text-white-50" style="font-size: 0.75rem;">A confirmation link will be sent to this email address.</div>
                        @error('parent_email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="parent_birthdate" class="form-label">Parent Birthdate</label>
                        <input type="date" class="form-control @error('parent_birthdate') is-invalid @enderror" id="parent_birthdate" name="parent_birthdate" value="{{ old('parent_birthdate') }}" max="{{ now()->subYears(18)->format('Y-m-d') }}">
                        <div class="form-text text-white-50" style="font-size: 0.75rem;">Your parent or guardian must be at least 18 years old.</div>
                        @error('parent_birthdate')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Parent / Guardian Government ID</label>
                        <div class="position-relative" id="parent-id-upload-wrapper">
                            <input type="file" class="position-absolute w-100 h-100 opacity-0"
                                   style="z-index: 2; cursor: pointer; top: 0; left: 0;"
                                   id="parent_id_file" accept="image/jpeg,image/png">
                            <div id="parent-id-dropzone" class="d-flex flex-column align-items-center justify-content-center p-4 text-center neon-card @error('parent_id_base64') border-danger @enderror" style="border: 2px dashed rgba(0, 240, 255, 0.4); background: rgba(0, 240, 255, 0.02); transition: all 0.3s ease;">
                                <i class="bi bi-cloud-arrow-up-fill mb-2" style="font-size: 2.5rem; color: #00f0ff; text-shadow: 0 0 10px rgba(0,240,255,0.4);"></i>
                                <span style="font-family: 'Orbitron', sans-serif; color: #00f0ff; font-weight: 600; letter-spacing: 1px;" id="parent-id-dropzone-text">CLICK OR DRAG ID HERE</span>
                                <small class="mt-2" style="color: #8888aa; font-size: 0.75rem;">Supports JPEG, PNG only (Auto-resized)</small>
                            </div>
                        </div>
                        <input type="hidden" name="parent_id_base64" id="parent_id_base64" value="{{ old('parent_id_base64') }}">
                        @error('parent_id_base64')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-check">
                        <input class="form-check-input @error('parent_consent_agreed') is-invalid @enderror" type="checkbox" name="parent_consent_agreed" id="parent_consent_agreed" value="1" {{ old('parent_consent_agreed') ? 'checked' : '' }}>
                        <label class="form-check-label text-white-50" for="parent_consent_agreed" style="font-size: 0.85rem;">
                            I confirm that I am the parent or legal guardian of this user. I grant consent and accept responsibility for the gathering of the child's data and their participation in bragarena.com.
                        </label>
                        @error('parent_consent_agreed')
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 form-check d-flex align-items-center">
                    <input class="form-check-input mt-0 me-2 @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" {{ old('terms') ? 'checked' : '' }} required>
                    <label class="form-check-label text-white-50" for="terms" style="font-size: 0.85rem;">
                        I agree to the <a href="{{ route('terms.show') }}" target="_blank" style="color: var(--neon-cyan);">Terms of Service</a>
                    </label>
                    @error('terms')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4 form-check d-flex align-items-center">
                    <input class="form-check-input mt-0 me-2 @error('privacy') is-invalid @enderror" type="checkbox" name="privacy" id="privacy" {{ old('privacy') ? 'checked' : '' }} required>
                    <label class="form-check-label text-white-50" for="privacy" style="font-size: 0.85rem;">
                        I agree to the <a href="{{ route('privacy.show') }}" target="_blank" style="color: var(--neon-cyan);">Privacy Policy</a>
                    </label>
                    @error('privacy')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4 d-flex justify-content-center">
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.key') }}" data-theme="dark"></div>
                </div>
                @error('cf-turnstile-response')
                    <div class="text-center mb-3">
                        <span class="text-danger" style="font-size: 0.85rem;">
                            <strong>{{ $message }}</strong>
                        </span>
                    </div>
                @enderror

                <button type="submit" class="btn btn-neon-magenta w-100" id="btn-register">
                    <i class="bi bi-person-plus-fill"></i> {{ __('CREATE ACCOUNT') }}
                </button>

                <div class="d-flex align-items-center my-4">
                    <hr class="flex-grow-1" style="border-color: rgba(255,255,255,0.1);">
                    <span class="mx-3" style="color: #555577; font-size: 0.8rem; font-family: 'Orbitron', sans-serif;">OR</span>
                    <hr class="flex-grow-1" style="border-color: rgba(255,255,255,0.1);">
                </div>

                <a href="{{ route('auth.google') }}" class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center" style="border-color: rgba(255,255,255,0.2); font-family: 'Orbitron', sans-serif; font-size: 0.8rem; letter-spacing: 1px;">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google" style="width: 18px; margin-right: 10px;">
                    {{ __('SIGN UP WITH GOOGLE') }}
                </a>
            </form>
        </div>

        <div class="text-center mt-3">
            <span style="color: #555577; font-size: 0.85rem;">Already have an account?</span>
            <a href="{{ route('login') }}" style="color: #00f0ff; font-size: 0.85rem;">Login</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const birthdateInput = document.getElementById('birthdate');
    const consentSection = document.getElementById('parental-consent-section');
    const parentIdFile = document.getElementById('parent_id_file');
    const parentIdPath = document.getElementById('parent_id_path');
    const progressBar = document.getElementById('parent-id-progress');
    const progressContainer = document.getElementById('parent-id-progress-container');
    const statusText = document.getElementById('parent-id-status');
    const btnRegister = document.getElementById('btn-register');
    
    // Parent fields
    const parentFirstname = document.getElementById('parent_firstname');
    const parentLastname = document.getElementById('parent_lastname');
    const parentEmail = document.getElementById('parent_email');
    const parentBirthdate = document.getElementById('parent_birthdate');
    const parentAgreed = document.getElementById('parent_consent_agreed');

    function calculateAge(birthdate) {
        const today = new Date();
        const birthDate = new Date(birthdate);
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    }

    function toggleConsentSection() {
        const age = calculateAge(birthdateInput.value);
        if (age >= 13 && age < 18) {
            consentSection.style.display = 'block';
            parentFirstname.required = true;
            parentLastname.required = true;
            parentEmail.required = true;
            parentBirthdate.required = true;
            parentAgreed.required = true;
        } else {
            consentSection.style.display = 'none';
            parentFirstname.required = false;
            parentLastname.required = false;
            parentEmail.required = false;
            parentBirthdate.required = false;
            parentAgreed.required = false;
        }
    }

    if (birthdateInput) {
        birthdateInput.addEventListener('change', toggleConsentSection);
        if (birthdateInput.value) toggleConsentSection();
    }

    if (parentIdFile) {
        const dropzone = document.getElementById('parent-id-dropzone');
        const dropzoneText = document.getElementById('parent-id-dropzone-text');
        const hiddenBase64 = document.getElementById('parent_id_base64');

        parentIdFile.addEventListener('dragenter', () => {
            dropzone.style.borderColor = '#00f0ff';
            dropzone.style.background = 'rgba(0, 240, 255, 0.1)';
            dropzone.style.boxShadow = '0 0 20px rgba(0, 240, 255, 0.3)';
        });

        parentIdFile.addEventListener('dragleave', () => {
            dropzone.style.borderColor = 'rgba(0, 240, 255, 0.4)';
            dropzone.style.background = 'rgba(0, 240, 255, 0.02)';
            dropzone.style.boxShadow = 'none';
        });

        parentIdFile.addEventListener('drop', () => {
            dropzone.style.borderColor = 'rgba(0, 240, 255, 0.4)';
            dropzone.style.background = 'rgba(0, 240, 255, 0.02)';
            dropzone.style.boxShadow = 'none';
        });

        parentIdFile.addEventListener('mouseenter', () => {
            dropzone.style.borderColor = '#00f0ff';
            dropzone.style.background = 'rgba(0, 240, 255, 0.05)';
            dropzone.style.transform = 'translateY(-2px)';
        });

        parentIdFile.addEventListener('mouseleave', () => {
            dropzone.style.borderColor = 'rgba(0, 240, 255, 0.4)';
            dropzone.style.background = 'rgba(0, 240, 255, 0.02)';
            dropzone.style.transform = 'translateY(0)';
        });

        parentIdFile.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            // Simple validation
            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                window.neonAlert('Please select a JPEG or PNG image.', 'INVALID FILE');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;
                    const max_size = 1200;

                    if (width > height) {
                        if (width > max_size) {
                            height *= max_size / width;
                            width = max_size;
                        }
                    } else {
                        if (height > max_size) {
                            width *= max_size / height;
                            height = max_size;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                    hiddenBase64.value = dataUrl;

                    // Update UI
                    dropzone.innerHTML = `
                        <i class="bi bi-file-earmark-check-fill mb-2" style="font-size: 2.5rem; color: #39ff14; text-shadow: 0 0 10px rgba(57,255,20,0.4);"></i>
                        <span style="font-family: 'Orbitron', sans-serif; color: #39ff14; font-weight: 600; letter-spacing: 1px;">${file.name}</span>
                        <small class="mt-2" style="color: #8888aa; font-size: 0.75rem;">Resized & Ready</small>
                    `;
                    dropzone.style.borderColor = '#39ff14';
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // Handle form submission to ensure ID is uploaded if required
    const registerForm = document.getElementById('register-form');
    registerForm.addEventListener('submit', function(e) {
        const age = calculateAge(birthdateInput.value);
        if (age >= 13 && age < 18 && !hiddenBase64.value) {
            e.preventDefault();
            window.neonAlert('Please select and wait for the parent ID image to be processed.', 'ID REQUIRED');
        }
    });
});
</script>
@endsection
