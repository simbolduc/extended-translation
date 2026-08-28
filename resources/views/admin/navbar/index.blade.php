@extends('admin.layouts.admin')

@section('title', trans('extended-translation::admin.navbar.title'))

@push('styles')
    <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/style.css') }}">
@endpush

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <p class="text-body-secondary">{{ trans('extended-translation::admin.navbar.subtitle') }}</p>

            @if($locales->isEmpty())
                <div class="alert alert-warning">
                    {{ trans('extended-translation::admin.no_locales') }}
                    @can('extended-translation.settings')
                        <a href="{{ route('extended-translation.admin.settings') }}" class="alert-link">
                            {{ trans('extended-translation::admin.nav.settings') }}
                        </a>
                    @endcan
                </div>
            @elseif($elements->isEmpty())
                <p class="mb-0">{{ trans('extended-translation::admin.navbar.empty') }}</p>
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
                        @foreach($elements as $element)
                            @php
                                $elementTranslations = $translations->get($element->id, collect());
                            @endphp
                            <tr>
                                <th scope="row">{{ $element->id }}</th>
                                <td>
                                    @if($element->hasParent())
                                        <span class="text-body-secondary">↳</span>
                                    @endif
                                    {{ $element->raw_name }}
                                </td>
                                <td>{{ trans('admin.navbar_elements.fields.'.$element->type) }}</td>
                                <td>
                                    @foreach($locales as $code => $name)
                                        @php
                                            $translation = $elementTranslations->get($code);
                                            $isDefault = $code === $defaultLocale;
                                        @endphp
                                        @if($translation)
                                            <span class="badge text-bg-success">{{ $name }}</span>
                                        @elseif($isDefault)
                                            <span class="badge text-bg-secondary">{{ $name }} · {{ trans('extended-translation::admin.original') }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @php
                                        $target = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $elementTranslations->has($code))
                                            ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
                                            ?? $locales->keys()->first();
                                    @endphp
                                    @if($target)
                                        <a href="{{ route('extended-translation.admin.navbar.edit', [$element, $target]) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                                            <i class="bi bi-translate"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.navbar-elements.edit', $element) }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
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
@endsection
