@extends('admin.layouts.admin')

@section('title', trans('extended-translation::admin.translate_to', ['locale' => $localeName]))

@push('styles')
    <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/style.css') }}">
@endpush

@section('content')
    <p class="mb-3">
        <a href="{{ route('extended-translation.admin.posts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ trans('messages.actions.back') }}
        </a>
    </p>

    <ul class="nav nav-pills ext-translation-langs mb-3">
        @foreach($locales as $code => $name)
            <li class="nav-item">
                <a class="nav-link @if($code === $locale) active @endif"
                   href="{{ route('extended-translation.admin.posts.edit', [$post, $code]) }}">
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

    @if($translation?->isStale())
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            {{ trans('extended-translation::admin.stale') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>{{ trans('extended-translation::admin.source') }}</span>
                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil-square"></i> {{ trans('extended-translation::admin.edit_original') }}
                    </a>
                </div>
                <div class="card-body">
                    <h2 class="h5">{{ $post->title }}</h2>
                    <p class="text-body-secondary">{{ $post->description }}</p>
                    <div class="ext-translation-original content-body">
                        {!! $post->content !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header">
                    {{ trans('extended-translation::admin.translate_to', ['locale' => $localeName]) }}
                </div>
                <div class="card-body">
                    <form action="{{ route('extended-translation.admin.posts.update', [$post, $locale]) }}" method="POST">
                        @method('PUT')

                        @include('admin.elements.editor', ['imagesUploadUrl' => route('admin.posts.attachments.store', $post)])

                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="titleInput">{{ trans('messages.fields.title') }}</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="titleInput" name="title" value="{{ old('title', $translation->title ?? $post->title) }}" required>

                            @error('title')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="descriptionInput">{{ trans('messages.fields.description') }}</label>
                            <input type="text" class="form-control @error('description') is-invalid @enderror" id="descriptionInput" name="description" value="{{ old('description', $translation->description ?? $post->description) }}" required>

                            @error('description')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="textArea">{{ trans('messages.fields.content') }}</label>
                            <textarea class="form-control html-editor @error('content') is-invalid @enderror" id="textArea" name="content" rows="5">{{ old('content', $translation->content ?? $post->content) }}</textarea>

                            @error('content')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ trans('extended-translation::admin.actions.save') }}
                        </button>

                        <a href="{{ route('extended-translation.admin.posts.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> {{ trans('messages.actions.back') }}
                        </a>

                        @if($translation)
                            <a href="{{ route('extended-translation.admin.posts.destroy', [$post, $locale]) }}" class="btn btn-danger" data-confirm="delete">
                                <i class="bi bi-trash"></i> {{ trans('extended-translation::admin.delete_translation') }}
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
