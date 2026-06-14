@extends('layouts/layoutMaster')

@section('title', 'Profile ' . $user->name)

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/notyf/notyf.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/notyf/notyf.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/notiflix/notiflix.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/app/select2-utils.js', 'resources/assets/js/pages/profile.js'])
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card card-action mb-6">
                <div class="card-header align-items-center">
                    <h5 class="card-action-title mb-0">Update Account</h5>
                </div>
                <div class="card-body">
                    <form id="formProfile" class="modal-content">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label" for="name">Name</label>
                                <input type="text" id="name" name="name" class="form-control"
                                    placeholder="johndoe" value="{{ old('name', $user->name) }}" autofocus />
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="username">Username</label>
                                <input type="text" id="username" name="username" class="form-control"
                                    placeholder="johndoe" value="{{ old('username', $user->username) }}" />
                            </div>
                            <div class="col-12 col-md-6 mb-3 form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i
                                            class="icon-base bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3 form-password-toggle">
                                <label class="form-label" for="password_confirmation">Password Confirmation</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password_confirmation" class="form-control"
                                        name="password_confirmation"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password_confirmation" />
                                    <span class="input-group-text cursor-pointer"><i
                                            class="icon-base bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="two_factor_enabled">2FA</label>
                                <select id="two_factor_enabled" name="two_factor_enabled" class="select2 form-select"
                                    data-allow-clear="true">
                                    <option value="0" {{ !$user->two_factor_enabled ? 'selected' : '' }}>
                                        Disabled
                                    </option>
                                    <option value="1" {{ $user->two_factor_enabled ? 'selected' : '' }}>
                                        Enabled
                                    </option>
                                </select>
                            </div>
                            <div id="twoFactorSection" class="col-12 mb-3 d-none">
                                <div id="qrContainer"></div>
                                <div class="mt-3">
                                    <label class="form-label" for="otp">Verification Code</label>
                                    <input type="text" id="otp" name="otp"
                                        data-username="{{ $user->username }}" class="form-control" maxlength="6"
                                        placeholder="123456">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary me-4">Submit</button>
                            <button type="reset" class="btn btn-label-danger" data-bs-dismiss="modal"
                                aria-label="Close">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
