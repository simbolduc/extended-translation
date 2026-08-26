<?php

namespace Azuriom\Plugin\ExtendedTranslation\Support;

use Illuminate\Support\Facades\Route;

trait SkipsAdminOverlay
{
    public function shouldApply(): bool
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return false;
        }

        $request = request();

        if ($request->is('admin') || $request->is('admin/*')) {
            return false;
        }

        $route = Route::currentRouteName();

        return ! is_string($route) || ! str_starts_with($route, 'admin.');
    }
}
