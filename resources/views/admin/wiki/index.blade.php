@extends('admin.layouts.admin')

@section('title', trans('extended-translation::wiki.title'))

@push('styles')
    <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/style.css') }}">
@endpush

@section('content')
    @if($locales->isEmpty())
        <div class="card shadow mb-4">
            <div class="card-body">
                <p class="text-body-secondary">{{ trans('extended-translation::wiki.subtitle') }}</p>
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
        <p class="text-body-secondary">{{ trans('extended-translation::wiki.subtitle') }}</p>

        <h2 class="h5 mb-3">{{ trans('extended-translation::wiki.categories.section') }}</h2>

        @forelse($categories as $category)
            @include('extended-translation::admin.wiki._category', ['depth' => 0])
        @empty
            <div class="card shadow mb-4">
                <div class="card-body">
                    <p class="mb-0">{{ trans('extended-translation::wiki.empty') }}</p>
                </div>
            </div>
        @endforelse
    @endif
@endsection
