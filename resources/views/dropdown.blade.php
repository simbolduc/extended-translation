@php
    $etLocales = collect($etLocales ?? []);
    $etCurrentLocale = $etCurrentLocale ?? app()->getLocale();
    $etLocaleShort = $etLocaleShort ?? strtoupper(preg_split('/[-_]/', (string) $etCurrentLocale)[0] ?? '');
    $etRedirect = $etRedirect ?? $redirect ?? url()->full();
    $etAlign = $align ?? 'end';
    $etToggleClass = trim('et-lang-toggle dropdown-toggle '.($toggleClass ?? ''));
    $etMenuAlignClass = $etAlign === 'start' ? '' : 'dropdown-menu-end';
    $etMenuClass = trim('dropdown-menu et-lang-menu '.$etMenuAlignClass.' '.($menuClass ?? ''));
@endphp

@if($etLocales->count() >= 2)
    @once
        <link rel="stylesheet" href="{{ plugin_asset('extended-translation', 'css/dropdown.css') }}">
    @endonce

    <div class="dropdown et-lang-dropdown">
        <button class="{{ $etToggleClass }}"
                type="button"
                data-bs-toggle="dropdown"
                data-bs-auto-close="true"
                aria-expanded="false"
                aria-haspopup="true"
                aria-label="{{ trans('extended-translation::messages.switch') }}">
            {{ $etLocaleShort }}
        </button>

        <ul class="{{ $etMenuClass }}" aria-label="{{ trans('extended-translation::messages.title') }}">
            @foreach($etLocales as $code => $name)
                <li>
                    <a class="dropdown-item et-lang-option @if($code === $etCurrentLocale) active @endif"
                       href="{{ route('extended-translation.language.switch', ['locale' => $code, 'redirect' => $etRedirect]) }}"
                       hreflang="{{ str_replace('_', '-', $code) }}"
                       lang="{{ str_replace('_', '-', $code) }}"
                       @if($code === $etCurrentLocale) aria-current="true" @endif>
                        <span class="et-lang-code">{{ strtoupper(preg_split('/[-_]/', $code)[0]) }}</span>
                        <span class="et-lang-name">{{ $name }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
