<?php

namespace Azuriom\Plugin\ExtendedTranslation\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Setting;
use Azuriom\Plugin\ExtendedTranslation\Http\Requests\Admin\SettingsRequest;
use Azuriom\Plugin\ExtendedTranslation\Support\LocaleCatalog;
use Azuriom\Plugin\ExtendedTranslation\Support\PluginOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private LocaleCatalog $locales,
    ) {
        //
    }

    /**
     * Show the plugin settings.
     */
    public function index(): View
    {
        return view('extended-translation::admin.settings', [
            'installed' => $this->locales->installed(),
            'enabled' => $this->locales->enabled()->keys(),
            'defaultLocale' => $this->locales->defaultLocale(),
            'injectCoreAdmin' => PluginOptions::injectCoreAdmin(),
        ]);
    }

    /**
     * Update the plugin settings.
     */
    public function update(SettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Setting::updateSettings([
            'extended-translation.locales' => array_values($validated['locales']),
            'extended-translation.inject_core_admin' => $validated['inject_core_admin'],
        ]);

        return to_route('extended-translation.admin.settings')
            ->with('success', trans('messages.status.success'));
    }
}
