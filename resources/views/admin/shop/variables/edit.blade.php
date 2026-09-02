@extends('admin.layouts.admin')

@section('title', trans('extended-translation::admin.translate_to', ['locale' => $localeName]))

@push('styles')
    <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/style.css') }}">
@endpush

@section('content')
    <p class="mb-3">
        <a href="{{ route('extended-translation.admin.shop.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ trans('messages.actions.back') }}
        </a>
    </p>

    <ul class="nav nav-pills ext-translation-langs mb-3">
        @foreach($locales as $code => $name)
            <li class="nav-item">
                <a class="nav-link @if($code === $locale) active @endif"
                   href="{{ route('extended-translation.admin.shop.variables.edit', [$variable, $code]) }}">
                    {{ $name }}
                    @if($existing->contains($code))
                        <i class="bi bi-check2"></i>
                    @elseif($code === $defaultLocale)
                        <span class="small">{{ trans('extended-translation::admin.original') }}</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>

    @if($translation?->isStale($variable))
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            {{ trans('extended-translation::admin.stale') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>{{ trans('extended-translation::shop.variables.source') }}</span>
                    <a href="{{ route('shop.admin.variables.edit', $variable) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil-square"></i> {{ trans('extended-translation::admin.edit_original') }}
                    </a>
                </div>
                <div class="card-body">
                    <h2 class="h5">{{ $variable->name }}</h2>
                    <p>{{ $variable->description }}</p>
                    @if($variable->type === 'dropdown' && ! empty($variable->options))
                        <ul class="mb-0">
                            @foreach($variable->options as $option)
                                <li>
                                    {{ $option['name'] ?? '' }}
                                    <span class="text-body-secondary">({{ $option['value'] ?? '' }})</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header">
                    {{ trans('extended-translation::admin.translate_to', ['locale' => $localeName]) }}
                </div>
                <div class="card-body">
                    <form action="{{ route('extended-translation.admin.shop.variables.update', [$variable, $locale]) }}" method="POST">
                        @method('PUT')
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="descriptionInput">{{ trans('messages.fields.description') }}</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="descriptionInput" name="description" value="{{ old('description', $translation->description ?? $variable->description) }}" required maxlength="200">

                            @error('description')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        @if($variable->type === 'dropdown' && ! empty($variable->options))
                            <fieldset class="mb-3">
                                <legend class="form-label fs-6">{{ trans('extended-translation::shop.variables.options') }}</legend>

                                @foreach($variable->options as $i => $option)
                                    @php
                                        $optionValue = (string) ($option['value'] ?? '');
                                    @endphp
                                    <div class="mb-2">
                                        <label class="form-label" for="optionName{{ $i }}">{{ $optionValue }}</label>
                                        <input type="hidden" name="options[{{ $i }}][value]" value="{{ $optionValue }}">
                                        <input type="text" class="form-control @error('options.'.$i.'.name') is-invalid @enderror" id="optionName{{ $i }}" name="options[{{ $i }}][name]" value="{{ old('options.'.$i.'.name', $optionNames->get($optionValue, $option['name'] ?? '')) }}" required maxlength="100">

                                        @error('options.'.$i.'.name')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                @endforeach
                            </fieldset>
                        @endif

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ trans('extended-translation::admin.actions.save') }}
                        </button>

                        <a href="{{ route('extended-translation.admin.shop.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> {{ trans('messages.actions.back') }}
                        </a>

                        @if($translation)
                            <a href="{{ route('extended-translation.admin.shop.variables.destroy', [$variable, $locale]) }}" class="btn btn-danger" data-confirm="delete">
                                <i class="bi bi-trash"></i> {{ trans('extended-translation::admin.delete_translation') }}
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
