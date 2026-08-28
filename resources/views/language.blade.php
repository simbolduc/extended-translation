@extends('layouts.app')

@section('title', trans('extended-translation::messages.title'))

@push('styles')
    <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/style.css') }}">
@endpush

@section('content')
    <div class="et-language">
        <header class="et-language-header">
            <h1 class="et-language-title">{{ trans('extended-translation::messages.title') }}</h1>
            <p class="et-language-subtitle">{{ trans('extended-translation::messages.subtitle') }}</p>
        </header>

        <form action="{{ route('extended-translation.language.update') }}" method="POST">
            @csrf
            <input type="hidden" name="redirect" value="{{ $redirect }}">

            <div class="et-language-list" role="radiogroup" aria-label="{{ trans('extended-translation::messages.title') }}">
                @foreach($locales as $code => $name)
                    <label class="et-language-option" for="et-locale-{{ $code }}" lang="{{ str_replace('_', '-', $code) }}">
                        <input class="et-language-radio form-check-input"
                               type="radio"
                               name="locale"
                               id="et-locale-{{ $code }}"
                               value="{{ $code }}"
                               @checked($code === old('locale', $current))
                               required>
                        <span class="et-language-option-body">
                            <span class="et-language-name">{{ $name }}</span>
                            <span class="et-language-code">{{ strtoupper(str_replace('_', '-', $code)) }}</span>
                        </span>
                    </label>
                @endforeach
            </div>

            @error('locale')
                <div class="invalid-feedback d-block mb-3"><strong>{{ $message }}</strong></div>
            @enderror

            <button type="submit" class="btn btn-primary sl-btn sl-btn-red et-language-save">
                {{ trans('extended-translation::messages.save') }}
            </button>
        </form>
    </div>
@endsection
