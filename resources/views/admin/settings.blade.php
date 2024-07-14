@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Configuración de Tarifas</h2>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="gasoline_price">Precio de Gasolina (por 100 km)</label>
            <input type="number" step="0.01" id="gasoline_price" name="gasoline_price" value="{{ $settings->gasoline_price }}" class="form-control">
        </div>
        <div class="form-group">
            <label for="van_price">Precio de la Furgoneta</label>
            <input type="number" step="0.01" id="van_price" name="van_price" value="{{ $settings->van_price }}" class="form-control">
        </div>
        <div class="form-group">
            <label for="driver_price">Precio del Conductor (por hora)</label>
            <input type="number" step="0.01" id="driver_price" name="driver_price" value="{{ $settings->driver_price }}" class="form-control">
        </div>
        <div class="form-group">
            <label for="assistant_price">Precio del Ayudante (por hora)</label>
            <input type="number" step="0.01" id="assistant_price" name="assistant_price" value="{{ $settings->assistant_price }}" class="form-control">
        </div>
        <div class="form-group">
            <label for="commission_rate">Comisión (%)</label>
            <input type="number" step="0.01" id="commission_rate" name="commission_rate" value="{{ $settings->commission_rate }}" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
    </form>
</div>
@endsection
