@extends('admin.layouts.admin')

@section('title', trans('extended-translation::shop.title'))

@push('styles')
    <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/style.css') }}">
@endpush

@section('content')
    @if($locales->isEmpty())
        <div class="card shadow mb-4">
            <div class="card-body">
                <p class="text-body-secondary">{{ trans('extended-translation::shop.subtitle') }}</p>
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
        <p class="text-body-secondary">{{ trans('extended-translation::shop.subtitle') }}</p>

        <h2 class="h5 mb-3">{{ trans('extended-translation::shop.packages.section') }}</h2>

        @forelse($categories as $category)
            @include('extended-translation::admin.shop._category', ['depth' => 0])
        @empty
            <div class="card shadow mb-4">
                <div class="card-body">
                    <p class="mb-0">{{ trans('extended-translation::shop.packages.empty') }}</p>
                </div>
            </div>
        @endforelse

        <div class="card shadow mb-4">
            <div class="card-header">
                <h2 class="h6 m-0 font-weight-bold text-primary">{{ trans('extended-translation::shop.offers.section') }}</h2>
            </div>
            <div class="card-body">
                @if($offers->isEmpty())
                    <p class="mb-0">{{ trans('extended-translation::shop.offers.empty') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <caption class="visually-hidden">{{ trans('extended-translation::shop.offers.type') }}</caption>
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ trans('messages.fields.name') }}</th>
                                <th scope="col">{{ trans('extended-translation::admin.fields.status') }}</th>
                                <th scope="col">{{ trans('messages.fields.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($offers as $offer)
                                @php
                                    $itemTranslations = $offerTranslations->get($offer->id, collect());
                                    $target = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $itemTranslations->has($code))
                                        ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
                                        ?? $locales->keys()->first();
                                @endphp
                                <tr>
                                    <th scope="row">{{ $offer->id }}</th>
                                    <td>{{ $offer->name }}</td>
                                    <td>
                                        @include('extended-translation::admin.shop._status', [
                                            'model' => $offer,
                                            'itemTranslations' => $itemTranslations,
                                        ])
                                    </td>
                                    <td>
                                        @if($target)
                                            <a href="{{ route('extended-translation.admin.shop.offers.edit', [$offer, $target]) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                                                <i class="bi bi-translate"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('shop.admin.offers.edit', $offer) }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
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

        <div class="card shadow mb-4">
            <div class="card-header">
                <h2 class="h6 m-0 font-weight-bold text-primary">{{ trans('extended-translation::shop.variables.section') }}</h2>
            </div>
            <div class="card-body">
                @if($variables->isEmpty())
                    <p class="mb-0">{{ trans('extended-translation::shop.variables.empty') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <caption class="visually-hidden">{{ trans('extended-translation::shop.variables.type') }}</caption>
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ trans('messages.fields.name') }}</th>
                                <th scope="col">{{ trans('extended-translation::admin.fields.status') }}</th>
                                <th scope="col">{{ trans('messages.fields.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($variables as $variable)
                                @php
                                    $itemTranslations = $variableTranslations->get($variable->id, collect());
                                    $target = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $itemTranslations->has($code))
                                        ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
                                        ?? $locales->keys()->first();
                                @endphp
                                <tr>
                                    <th scope="row">{{ $variable->id }}</th>
                                    <td>{{ $variable->name }}</td>
                                    <td>
                                        @include('extended-translation::admin.shop._status', [
                                            'model' => $variable,
                                            'itemTranslations' => $itemTranslations,
                                        ])
                                    </td>
                                    <td>
                                        @if($target)
                                            <a href="{{ route('extended-translation.admin.shop.variables.edit', [$variable, $target]) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                                                <i class="bi bi-translate"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('shop.admin.variables.edit', $variable) }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
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
    @endif
@endsection
