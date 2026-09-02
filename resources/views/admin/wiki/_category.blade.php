@php
    $categoryItemTranslations = $categoryTranslations->get($category->id, collect());
    $categoryTarget = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $categoryItemTranslations->has($code))
        ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
        ?? $locales->keys()->first();
@endphp

<div @if($depth > 0) class="ms-4" @endif>
    <div class="card shadow mb-4">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h2 class="h6 m-0 font-weight-bold text-primary">{{ $category->name }}</h2>
                <div class="small text-body-secondary">{{ trans('extended-translation::wiki.categories.type') }}</div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                @include('extended-translation::admin.wiki._status', [
                    'model' => $category,
                    'itemTranslations' => $categoryItemTranslations,
                ])
                <span class="text-nowrap">
                    @if($categoryTarget)
                        <a href="{{ route('extended-translation.admin.wiki.categories.edit', [$category, $categoryTarget]) }}" class="mx-1" title="{{ trans('extended-translation::admin.actions.translate') }}" data-bs-toggle="tooltip">
                            <i class="bi bi-translate"></i>
                        </a>
                    @endif
                    <a href="{{ route('wiki.admin.categories.edit', $category) }}" class="mx-1" title="{{ trans('extended-translation::admin.edit_original') }}" data-bs-toggle="tooltip">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="card-body">
            @if($category->pages->isEmpty())
                <p class="mb-0">{{ trans('extended-translation::wiki.pages.empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <caption class="visually-hidden">{{ trans('extended-translation::wiki.pages.type') }}</caption>
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ trans('messages.fields.name') }}</th>
                            <th scope="col">{{ trans('extended-translation::admin.fields.status') }}</th>
                            <th scope="col">{{ trans('messages.fields.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($category->pages as $page)
                            @php
                                $itemTranslations = $pageTranslations->get($page->id, collect());
                                $target = $locales->keys()->first(fn ($code) => $code !== $defaultLocale && ! $itemTranslations->has($code))
                                    ?? $locales->keys()->first(fn ($code) => $code !== $defaultLocale)
                                    ?? $locales->keys()->first();
                            @endphp
                            <tr>
                                <th scope="row">{{ $page->id }}</th>
                                <td>{{ $page->title }}</td>
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
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @if($category->relationLoaded('categories'))
        @foreach($category->categories as $child)
            @include('extended-translation::admin.wiki._category', ['category' => $child, 'depth' => $depth + 1])
        @endforeach
    @endif
</div>
