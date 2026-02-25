@extends('layouts.admin')

@section('title', 'Configuración | StyleBox')
@section('page_title', 'Configuración del Sistema')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card card-custom border-0">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-cogs me-2"></i>Ajustes Generales</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                @foreach($settings as $group => $items)
                    <div class="mb-5">
                        <h6 class="text-uppercase fw-bold text-secondary mb-4 small letter-spacing-1">
                            {{ ucfirst($group) }}
                        </h6>
                        
                        <div class="row g-4">
                            @foreach($items as $setting)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-semibold mb-1">{{ $setting->label }}</label>
                                        
                                        @if($setting->type === 'text')
                                            <textarea name="settings[{{ $setting->key }}]" class="form-control" rows="3">{{ $setting->value }}</textarea>
                                        @else
                                            <input type="text" name="settings[{{ $setting->key }}]" class="form-control" value="{{ $setting->value }}">
                                        @endif
                                        
                                        @if($setting->description)
                                            <div class="form-text small opacity-75">{{ $setting->description }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="mt-4 pt-4 border-top text-end">
                    <button type="submit" class="btn btn-primary-custom px-5 shadow-sm">
                        <i class="fas fa-save me-2"></i>Actualizar Todo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .letter-spacing-1 { letter-spacing: 1px; }
</style>
@endsection
