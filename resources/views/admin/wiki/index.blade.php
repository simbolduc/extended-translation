@extends('admin.layouts.admin')

@section('title', trans('extended-translation::wiki.title'))

@push('styles')
    <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/style.css') }}">
@endpush

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <p class="text-body-secondary">{{ trans('extended-translation::wiki.subtitle') }}</p>

            @if($locales->isEmpty())
                <div class="alert alert-warning">
                    {{ trans('extended-translation::admin.no_locales') }}
                    @can('extended-translation.settings')
                        <a href="{{ route('extended-translation.admin.settings') }}" class="alert-link">
                            {{ trans('extended-translation::admin.nav.settings') }}
                        </a>
                    @endcan
                </div>
            @elseif(count($rows) === 0)
                <p class="mb-0">{{ trans('extended-translation::wiki.empty') }}</p>
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
                        @foreach($rows as $row)
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
                                    <td>
                                        @if($row['depth'] > 0)
                                            <span class="text-body-secondary">{{ str_repeat('↳ ', $row['depth']) }}</span>
                                        @endif
                                        {{ $category->name }}
                                    </td>
                                    <td>{{ trans('extended-translation::wiki.categories.type') }}</td>
                                    <td>
                                        @include('extended-translation::admin.wiki._status', [
                                            'model' => $category,
                                            'itemTranslations' => $itemTranslations,
                                        ])
                                    </td>
                                    <td>
                                        @if($target)
                                            <a href="{{ route('extended-translation.admin.wiki.categories.edit', [$category, $target]) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                                                <i class="bi bi-translate"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('wiki.admin.categories.edit', $category) }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @else
                                @php
                                    $page = $row['model'];
                                    $itemTranslations = $pageTranslations->get($page->id, collect());
                                    $target = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $itemTranslations->has($code))
                                        ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
                                        ?? $locales->keys()->first();
                                @endphp
                                <tr>
                                    <th scope="row">{{ $page->id }}</th>
                                    <td>
                                        @if($row['depth'] > 0)
                                            <span class="text-body-secondary">{{ str_repeat('↳ ', $row['depth']) }}</span>
                                        @endif
                                        {{ $page->title }}
                                    </td>
                                    <td>{{ trans('extended-translation::wiki.pages.type') }}</td>
                                    <td>
                                        @include('extended-translation::admin.wiki._status', [
                                            'model' => $page,
                                            'itemTranslations' => $itemTranslations,
                                        ])
                                    </td>
                                    <td>
                                        @if($target)
                                            <a href="{{ route('extended-translation.admin.wiki.pages.edit', [$page, $target]) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                                                <i class="bi bi-translate"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('wiki.admin.pages.edit', $page) }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
