@extends('admin.layouts.admin')

@section('title', trans('extended-translation::changelog.title'))

@push('styles')
    <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/style.css') }}">
@endpush

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <p class="text-body-secondary">{{ trans('extended-translation::changelog.subtitle') }}</p>

            @if($locales->isEmpty())
                <div class="alert alert-warning">
                    {{ trans('extended-translation::admin.no_locales') }}
                    @can('extended-translation.settings')
                        <a href="{{ route('extended-translation.admin.settings') }}" class="alert-link">
                            {{ trans('extended-translation::admin.nav.settings') }}
                        </a>
                    @endcan
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ trans('messages.fields.name') }}</th>
                            <th scope="col">{{ trans('messages.fields.type') }}</th>
                            <th scope="col">{{ trans('extended-translation::admin.fields.status') }}</th>
                            <th scope="col">{{ trans('messages.fields.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $titleTarget = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $titleTranslations->has($code))
                                ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
                                ?? $locales->keys()->first();
                        @endphp
                        <tr>
                            <th scope="row">—</th>
                            <td>{{ $title }}</td>
                            <td>{{ trans('extended-translation::changelog.title_row.type') }}</td>
                            <td>
                                @include('extended-translation::admin.changelog._status', [
                                    'model' => null,
                                    'itemTranslations' => $titleTranslations,
                                ])
                            </td>
                            <td>
                                @if($titleTarget)
                                    <a href="{{ route('extended-translation.admin.changelog.title.edit', $titleTarget) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                                        <i class="bi bi-translate"></i>
                                    </a>
                                @endif
                                <a href="{{ route('changelog.admin.updates.index') }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                        @forelse($rows as $row)
                            @if($row['type'] === 'category')
                                @php
                                    $category = $row['model'];
                                    $itemTranslations = $categoryTranslations->get($category->id, collect());
                                    $target = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $itemTranslations->has($code))
                                        ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
                                        ?? $locales->keys()->first();
                                @endphp
                                <tr>
                                    <th scope="row">{{ $category->id }}</th>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ trans('extended-translation::changelog.categories.type') }}</td>
                                    <td>
                                        @include('extended-translation::admin.changelog._status', [
                                            'model' => $category,
                                            'itemTranslations' => $itemTranslations,
                                        ])
                                    </td>
                                    <td>
                                        @if($target)
                                            <a href="{{ route('extended-translation.admin.changelog.categories.edit', [$category, $target]) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                                                <i class="bi bi-translate"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('changelog.admin.categories.edit', $category) }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @else
                                @php
                                    $update = $row['model'];
                                    $itemTranslations = $updateTranslations->get($update->id, collect());
                                    $target = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $itemTranslations->has($code))
                                        ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
                                        ?? $locales->keys()->first();
                                @endphp
                                <tr>
                                    <th scope="row">{{ $update->id }}</th>
                                    <td>
                                        <span class="text-body-secondary">↳ </span>
                                        {{ $update->name }}
                                    </td>
                                    <td>{{ trans('extended-translation::changelog.updates.type') }}</td>
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
                            @endif
                        @empty
                            <tr>
                                <td colspan="5">{{ trans('extended-translation::changelog.empty') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
