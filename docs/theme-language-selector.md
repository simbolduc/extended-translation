# Language dropdown for themes

Extended Translation ships a Bootstrap 5 dropdown that lets visitors change the site language **without leaving the current page**. Themes can include it anywhere (navbar, footer, sidebar) instead of linking to the plugin’s language page.

The dedicated language page (`extended-translation.language`) still exists. You can add it to the Azuriom navbar if you prefer a full-page switcher. Most themes should use the dropdown.

## Requirements

- The **Extended Translation** plugin must be enabled.
- At least **two languages** must be selected in **Admin → Translations → Settings**. With fewer than two languages, the dropdown renders nothing.
- The layout must load Bootstrap 5’s JavaScript bundle (`bootstrap.bundle.min.js`), which Azuriom already does.

## Basic usage

Wrap the include with Azuriom’s `@plugin` directive so the theme still works when the plugin is disabled:

```blade
@plugin('extended-translation')
    @include('extended-translation::dropdown')
@else
    {{-- Optional fallback when the plugin is off --}}
@endplugin
```

`extended-translation::selector` is an alias of the same view (kept for older themes).

## Navbar example

Place the dropdown in a right-side utility list, next to login / register:

```blade
<ul class="navbar-nav ms-auto align-items-center">
    <li class="nav-item">
        @plugin('extended-translation')
            @include('extended-translation::dropdown')
        @endplugin
    </li>

    {{-- authentication links… --}}
</ul>
```

Choosing a language stores the locale in a cookie/session and redirects back to the current URL.

## Optional parameters

Pass an array as the second argument of `@include`:

| Parameter     | Default                         | Description |
|---------------|---------------------------------|-------------|
| `align`       | `'end'`                         | `'end'` adds `dropdown-menu-end` (right-aligned). Use `'start'` to left-align. |
| `toggleClass` | `''`                            | Extra CSS classes on the toggle button (merged with `et-lang-toggle dropdown-toggle`). |
| `menuClass`   | `''`                            | Extra CSS classes on the menu form (merged with `dropdown-menu et-lang-menu`). |
| `redirect`    | Current full URL                | URL to return to after switching. Must be on the same site. |
| `etRedirect`  | Same as `redirect`              | Alternate name for `redirect`. |

Example with theme-specific classes:

```blade
@include('extended-translation::dropdown', [
    'align' => 'end',
    'toggleClass' => 'my-lang-toggle',
    'menuClass' => 'my-lang-menu',
])
```

## Markup and CSS classes

Default styles load automatically from `plugin_asset('extended-translation', 'css/dropdown.css')`. Override them in your theme CSS.

| Class              | Element |
|--------------------|---------|
| `.et-lang-dropdown` | Wrapper (`div.dropdown`) |
| `.et-lang-toggle`   | Button that shows the current short code (`EN`, `FR`, …) |
| `.et-lang-menu`     | `<form>` that is also the Bootstrap `.dropdown-menu` |
| `.et-lang-option`   | Link for each locale (also `.dropdown-item`) |
| `.et-lang-code`     | Short locale code |
| `.et-lang-name`     | Localized language name |
| `.active`           | Current locale option (`aria-current="true"`) |

The toggle uses `data-bs-toggle="dropdown"`. Each option is a link to `extended-translation.language.switch`.

Minimal override example:

```css
.et-lang-toggle {
    border-color: #c4a24a;
    letter-spacing: 0.15em;
}

.et-lang-code {
    color: #c4a24a;
}
```

## Language page (optional)

If you still want a full page, add the **Language** route in **Admin → Navbar**. The route name is `extended-translation.language`. Visitors who land there can pick a language and return to the previous page.

Themes that use the dropdown do not need that navbar item.

## Translations

User-facing strings live in the plugin:

- `extended-translation::messages.switch` — toggle `aria-label`
- `extended-translation::messages.title` — menu `aria-label`

Language names come from Azuriom’s `messages.lang` translation in each locale.
