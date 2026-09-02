@extends('admin.layouts.admin')

@section('title', trans('extended-translation::shop.title'))

@push('styles')
    <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/style.css') }}">
@endpush

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <p class="text-body-secondary">{{ trans('extended-translation::shop.subtitle') }}</p>

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
                <h2 class="h5">{{ trans('extended-translation::shop.packages.section') }}</h2>

                @if(count($rows) === 0)
                    <p>{{ trans('extended-translation::shop.packages.empty') }}</p>
                @else
                    <div class="table-responsive mb-4">
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
                                        <td>{{ trans('extended-translation::shop.categories.type') }}</td>
                                        <td>
                                            @include('extended-translation::admin.shop._status', [
                                                'model' => $category,
                                                'itemTranslations' => $itemTranslations,
                                            ])
                                        </td>
                                        <td>
                                            @if($target)
                                                <a href="{{ route('extended-translation.admin.shop.categories.edit', [$category, $target]) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                                                    <i class="bi bi-translate"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('shop.admin.categories.edit', $category) }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @else
                                    @php
                                        $package = $row['model'];
                                        $itemTranslations = $packageTranslations->get($package->id, collect());
                                        $target = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $itemTranslations->has($code))
                                            ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
                                            ?? $locales->keys()->first();
                                    @endphp
                                    <tr>
                                        <th scope="row">{{ $package->id }}</th>
                                        <td>
                                            @if($row['depth'] > 0)
                                                <span class="text-body-secondary">{{ str_repeat('↳ ', $row['depth']) }}</span>
                                            @endif
                                            {{ $package->name }}
                                        </td>
                                        <td>{{ trans('extended-translation::shop.packages.type') }}</td>
                                        <td>
                                            @include('extended-translation::admin.shop._status', [
                                                'model' => $package,
                                                'itemTranslations' => $itemTranslations,
                                            ])
                                        </td>
                                        <td>
                                            @if($target)
                                                <a href="{{ route('extended-translation.admin.shop.packages.edit', [$package, $target]) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                                                    <i class="bi bi-translate"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('shop.admin.packages.edit', $package) }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
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

                <h2 class="h5">{{ trans('extended-translation::shop.offers.section') }}</h2>

                @if($offers->isEmpty())
                    <p>{{ trans('extended-translation::shop.offers.empty') }}</p>
                @else
                    <div class="table-responsive mb-4">
                        <table class="table table-striped">
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

                <h2 class="h5">{{ trans('extended-translation::shop.variables.section') }}</h2>

                @if($variables->isEmpty())
                    <p class="mb-0">{{ trans('extended-translation::shop.variables.empty') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
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
            @endif
        </div>
    </div>
@endsection
