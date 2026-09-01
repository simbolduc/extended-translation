@extends('admin.layouts.admin')

@section('title', trans('extended-translation::admin.translate_to', ['locale' => $localeName]))

@push('styles')
    <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/style.css') }}">
@endpush

@section('content')
    <p class="mb-3">
        <a href="{{ route('extended-translation.admin.changelog.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ trans('messages.actions.back') }}
        </a>
    </p>

    <ul class="nav nav-pills ext-translation-langs mb-3">
        @foreach($locales as $code => $name)
            <li class="nav-item">
                <a class="nav-link @if($code === $locale) active @endif"
                   href="{{ route('extended-translation.admin.changelog.title.edit', $code) }}">
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

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>{{ trans('extended-translation::changelog.title_row.source') }}</span>
                    <a href="{{ route('changelog.admin.updates.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil-square"></i> {{ trans('extended-translation::admin.edit_original') }}
                    </a>
                </div>
                <div class="card-body">
                    <h2 class="h5">{{ $title }}</h2>
                    <p class="text-body-secondary mb-0">{{ trans('extended-translation::changelog.title_row.label') }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header">
                    {{ trans('extended-translation::admin.translate_to', ['locale' => $localeName]) }}
                </div>
                <div class="card-body">
                    <form action="{{ route('extended-translation.admin.changelog.title.update', $locale) }}" method="POST">
                        @method('PUT')
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="titleInput">{{ trans('messages.fields.title') }}</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="titleInput" name="title" value="{{ old('title', $translation->title ?? $title) }}" required maxlength="50">

                            @error('title')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ trans('extended-translation::admin.actions.save') }}
                        </button>

                        <a href="{{ route('extended-translation.admin.changelog.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> {{ trans('messages.actions.back') }}
                        </a>

                        @if($translation)
                            <a href="{{ route('extended-translation.admin.changelog.title.destroy', $locale) }}" class="btn btn-danger" data-confirm="delete">
                                <i class="bi bi-trash"></i> {{ trans('extended-translation::admin.delete_translation') }}
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
