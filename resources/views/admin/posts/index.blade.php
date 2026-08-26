@extends('admin.layouts.admin')

@section('title', trans('extended-translation::admin.title'))

@push('styles')
    <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/style.css') }}">
@endpush

@section('content')
    <div class="card shadow mb-4">
        <div class="card-body">
            <p class="text-body-secondary">{{ trans('extended-translation::admin.subtitle') }}</p>

            @if($locales->isEmpty())
                <div class="alert alert-warning">
                    {{ trans('extended-translation::admin.no_locales') }}
                    <a href="{{ route('extended-translation.admin.settings') }}" class="alert-link">
                        {{ trans('extended-translation::admin.nav.settings') }}
                    </a>
                </div>
            @elseif($posts->isEmpty())
                <p class="mb-0">{{ trans('extended-translation::admin.empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ trans('messages.fields.title') }}</th>
                            <th scope="col">{{ trans('messages.fields.author') }}</th>
                            <th scope="col">{{ trans('extended-translation::admin.fields.status') }}</th>
                            <th scope="col">{{ trans('messages.fields.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($posts as $post)
                            @php
                                $postTranslations = $translations->get($post->id, collect());
                            @endphp
                            <tr>
                                <th scope="row">{{ $post->id }}</th>
                                <td>{{ $post->title }}</td>
                                <td>
                                    @if($post->author)
                                        {{ $post->author->name }}
                                    @endif
                                </td>
                                <td>
                                    @foreach($locales as $code => $name)
                                        @php
                                            $translation = $postTranslations->get($code);
                                            $isDefault = $code === $defaultLocale;
                                        @endphp
                                        @if($translation)
                                            <span class="badge text-bg-success" title="{{ $translation->isStale($post) ? trans('extended-translation::admin.stale') : trans('extended-translation::admin.done') }}">
                                                {{ $name }}
                                                @if($translation->isStale($post))
                                                    <i class="bi bi-exclamation-triangle"></i>
                                                @endif
                                            </span>
                                        @elseif($isDefault)
                                            <span class="badge text-bg-secondary">{{ $name }} · {{ trans('extended-translation::admin.original') }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @php
                                        $target = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $postTranslations->has($code))
                                            ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
                                            ?? $locales->keys()->first();
                                    @endphp
                                    @if($target)
                                        <a href="{{ route('extended-translation.admin.posts.edit', [$post, $target]) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                                            <i class="bi bi-translate"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $posts->links() }}
            @endif
        </div>
    </div>
@endsection
