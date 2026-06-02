@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title')

@section('vendor-style')

@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-landing.scss'])
@endsection

@section('vendor-script')

@endsection

@section('page-script')

@endsection

@section('content')
    @include('content.landing.sections.hero')

    @include('content.landing.sections.features')

    @include('content.landing.sections.products')

    @include('content.landing.sections.banner')

    @include('content.landing.sections.articles')

    @include('content.landing.sections.brands')
@endsection
