@extends('admin.layouts.admin')

@section('title', trans('extended-translation::admin.translate_to', ['locale' => $localeName]))

@push('styles')
    <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/style.css') }}">
@endpush

@section('content')
    <p class="mb-3">
        <a href="{{ route('extended-translation.admin.faq.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ trans('messages.actions.back') }}
        </a>
    </p>

    <ul class="nav nav-pills ext-translation-langs mb-3">
        @foreach($locales as $code => $name)
            <li class="nav-item">
                <a class="nav-link @if($code === $locale) active @endif"
                   href="{{ route('extended-translation.admin.faq.edit', [$question, $code]) }}">
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
                    <span>{{ trans('extended-translation::faq.source') }}</span>
                    <a href="{{ route('faq.admin.questions.edit', $question) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-pencil-square"></i> {{ trans('extended-translation::admin.edit_original') }}
                    </a>
                </div>
                <div class="card-body">
                    <h2 class="h5">{{ $question->name }}</h2>
                    <div class="ext-translation-original content-body">
                        {!! $question->answer !!}
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
                    <form action="{{ route('extended-translation.admin.faq.update', [$question, $locale]) }}" method="POST">
                        @method('PUT')

                        @include('admin.elements.editor', ['imagesUploadUrl' => route('faq.admin.questions.attachments.store', $question)])

                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="nameInput">{{ trans('messages.fields.name') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="nameInput" name="name" value="{{ old('name', $translation->name ?? $question->name) }}" required>

                            @error('name')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="answerInput">{{ trans('faq::messages.fields.answer') }}</label>
                            <textarea class="form-control html-editor @error('answer') is-invalid @enderror" id="answerInput" name="answer" rows="5">{{ old('answer', $translation->answer ?? $question->answer) }}</textarea>

                            @error('answer')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ trans('extended-translation::admin.actions.save') }}
                        </button>

                        <a href="{{ route('extended-translation.admin.faq.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> {{ trans('messages.actions.back') }}
                        </a>

                        @if($translation)
                            <a href="{{ route('extended-translation.admin.faq.destroy', [$question, $locale]) }}" class="btn btn-danger" data-confirm="delete">
                                <i class="bi bi-trash"></i> {{ trans('extended-translation::admin.delete_translation') }}
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
