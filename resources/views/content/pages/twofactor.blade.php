@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Two Steps Verifications')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/pages/twofactor.js'])
@endsection

@section('content')
    <!-- Toast -->
    @include('_partials.message')
    <!-- /Toast -->

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!--  Two Steps Verification -->
                <div class="card px-sm-6 px-0">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <h4 class="mb-1">{{ config('app.name') }}</h4>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-1">Two Step Verification 💬</h4>
                        <p class="mb-0">Type your 6 digit security code</p>
                        <form id="twoStepsForm" action="{{ route('twofactor.store') }}" method="POST">
                            @csrf
                            <div class="mb-6 form-control-validation">
                                <div
                                    class="auth-input-wrapper d-flex align-items-center justify-content-between numeral-mask-wrapper">
                                    <input type="tel"
                                        class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 mt-2"
                                        maxlength="1" autofocus />
                                    <input type="tel"
                                        class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 mt-2"
                                        maxlength="1" />
                                    <input type="tel"
                                        class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 mt-2"
                                        maxlength="1" />
                                    <input type="tel"
                                        class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 mt-2"
                                        maxlength="1" />
                                    <input type="tel"
                                        class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 mt-2"
                                        maxlength="1" />
                                    <input type="tel"
                                        class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 mt-2"
                                        maxlength="1" />
                                </div>
                                <input type="hidden" name="otp" />
                            </div>
                            <button class="btn btn-primary d-grid w-100 mb-6">Verify my account</button>
                        </form>
                    </div>
                </div>
                <!-- / Two Steps Verification -->
            </div>
        </div>
    </div>
@endsection
