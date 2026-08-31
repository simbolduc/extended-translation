<?php

namespace Azuriom\Plugin\ExtendedTranslation\Core;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as IlluminateView;

trait RegistersAdminInjectComposer
{
    /**
     * @param  array<int, string>  $views
     * @param  class-string  $composer
     */
    protected function registerAdminInjectComposer(array $views, string $composer, string $permission): void
    {
        View::composer($views, function (IlluminateView $view) use ($composer, $permission): void {
            if (! Gate::allows($permission)) {
                return;
            }

            app($composer)->compose($view);
        });
    }
}
