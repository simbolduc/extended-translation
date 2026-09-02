@extends('admin.layouts.admin')

@section('title', trans('extended-translation::changelog.title'))

@push('styles')
    <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/style.css') }}">
@endpush

@section('content')
    @if($locales->isEmpty())
        <div class="card shadow mb-4">
            <div class="card-body">
                <p class="text-body-secondary">{{ trans('extended-translation::changelog.subtitle') }}</p>
                <div class="alert alert-warning mb-0">
                    {{ trans('extended-translation::admin.no_locales') }}
                    @can('extended-translation.settings')
                        <a href="{{ route('extended-translation.admin.settings') }}" class="alert-link">
                            {{ trans('extended-translation::admin.nav.settings') }}
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    @else
        <p class="text-body-secondary">{{ trans('extended-translation::changelog.subtitle') }}</p>

        @php
            $titleTarget = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $titleTranslations->has($code))
                ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
                ?? $locales->keys()->first();
        @endphp

        <div class="card shadow mb-4">
            <div class="card-header">
                <h2 class="h6 m-0 font-weight-bold text-primary">{{ trans('extended-translation::changelog.title_row.section') }}</h2>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <div class="fw-semibold">{{ $title }}</div>
                        <div class="small text-body-secondary">{{ trans('extended-translation::changelog.title_row.label') }}</div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @include('extended-translation::admin.changelog._status', [
                            'model' => null,
                            'itemTranslations' => $titleTranslations,
                        ])
                        <span class="text-nowrap">
                            @if($titleTarget)
                                <a href="{{ route('extended-translation.admin.changelog.title.edit', $titleTarget) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                                    <i class="bi bi-translate"></i>
                                </a>
                            @endif
                            <a href="{{ route('changelog.admin.updates.index') }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="h5 mb-3">{{ trans('extended-translation::changelog.categories.section') }}</h2>

        @forelse($categories as $category)
            @php
                $categoryItemTranslations = $categoryTranslations->get($category->id, collect());
                $categoryTarget = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $categoryItemTranslations->has($code))
                    ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
                    ?? $locales->keys()->first();
            @endphp

            <div class="card shadow mb-4">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h2 class="h6 m-0 font-weight-bold text-primary">{{ $category->name }}</h2>
                        <div class="small text-body-secondary">{{ trans('extended-translation::changelog.categories.type') }}</div>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        @include('extended-translation::admin.changelog._status', [
                            'model' => $category,
                            'itemTranslations' => $categoryItemTranslations,
                        ])
                        <span class="text-nowrap">
                            @if($categoryTarget)
                                <a href="{{ route('extended-translation.admin.changelog.categories.edit', [$category, $categoryTarget]) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                                    <i class="bi bi-translate"></i>
                                </a>
                            @endif
                            <a href="{{ route('changelog.admin.categories.edit', $category) }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if($category->updates->isEmpty())
                        <p class="mb-0">{{ trans('extended-translation::changelog.updates.empty') }}</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <caption class="visually-hidden">{{ trans('extended-translation::changelog.updates.type') }}</caption>
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ trans('messages.fields.name') }}</th>
                                    <th scope="col">{{ trans('extended-translation::admin.fields.status') }}</th>
                                    <th scope="col">{{ trans('messages.fields.action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($category->updates as $update)
                                    @php
                                        $itemTranslations = $updateTranslations->get($update->id, collect());
                                        $target = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $itemTranslations->has($code))
                                            ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
                                            ?? $locales->keys()->first();
                                    @endphp
                                    <tr>
                                        <th scope="row">{{ $update->id }}</th>
                                        <td>{{ $update->name }}</td>
                                        <td>
                                            @include('extended-translation::admin.changelog._status', [
                                                'model' => $update,
                                                'itemTranslations' => $itemTranslations,
                                            ])
                                        </td>
                                        <td>
                                            @if($target)
                                                <a href="{{ route('extended-translation.admin.changelog.updates.edit', [$update, $target]) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                                                    <i class="bi bi-translate"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('changelog.admin.updates.edit', $update) }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="card shadow mb-4">
                <div class="card-body">
                    <p class="mb-0">{{ trans('extended-translation::changelog.empty') }}</p>
                </div>
            </div>
        @endforelse
    @endif
@endsection
