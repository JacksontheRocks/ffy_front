@extends('layouts.app')

@section('content')
<div id="app">
    <home-map-component :settings="{{ $settings }}"></home-map-component>
</div>

<div class="loading-spinner" style="display: none;">
    <div class="text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <h3 class="mt-3">Estamos buscando tu furgoneta...</h3>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
@vite(['resources/js/app.js', 'resources/css/app.css'])
@endsection

