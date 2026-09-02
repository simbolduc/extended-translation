@foreach($locales as $code => $name)
    @php
        $translation = $itemTranslations->get($code);
        $isDefault = $code === $defaultLocale;
    @endphp
    @if($translation)
        <span class="badge text-bg-success" title="{{ $translation->isStale($model) ? trans('extended-translation::admin.stale') : trans('extended-translation::admin.done') }}">
            {{ $name }}
            @if($translation->isStale($model))
                <i class="bi bi-exclamation-triangle"></i>
            @endif
        </span>
    @elseif($isDefault)
        <span class="badge text-bg-secondary">{{ $name }} · {{ trans('extended-translation::admin.original') }}</span>
    @endif
@endforeach
