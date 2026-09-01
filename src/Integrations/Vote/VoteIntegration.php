<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Vote;

use Azuriom\Models\ActionLog;
use Azuriom\Plugin\ExtendedTranslation\Core\PluginIntegration;
use Azuriom\Plugin\ExtendedTranslation\Core\RegistersAdminInjectComposer;
use Azuriom\Plugin\Vote\Models\Reward;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Route;

final class VoteIntegration implements PluginIntegration
{
    use RegistersAdminInjectComposer;

    public const PLUGIN_ID = 'vote';

    public const REWARDS = 'extended-translation.vote';

    public static function pluginId(): string
    {
        return self::PLUGIN_ID;
    }

    public static function available(): bool
    {
        return plugins()->isEnabled(self::PLUGIN_ID);
    }

    public function register(Application $app): void
    {
        $app->singleton(RewardTranslator::class);
    }

    public function boot(Application $app): void
    {
        $app->booted(function () use ($app) {
            $this->registerRuntime($app);
        });
    }

    public function permissions(): array
    {
        return [
            self::REWARDS => 'extended-translation::vote.permissions.rewards',
        ];
    }

    public function adminNavPermissions(): array
    {
        return [self::REWARDS];
    }

    public function adminNavItems(): array
    {
        return [
            'extended-translation.admin.vote.index' => [
                'name' => trans('extended-translation::vote.nav'),
                'permission' => self::REWARDS,
            ],
        ];
    }

    protected function registerRuntime(Application $app): void
    {
        if (! self::available() || ! class_exists(Reward::class)) {
            return;
        }

        Route::model('voteReward', Reward::class);

        Reward::retrieved(function (Reward $reward) use ($app) {
            $app->make(RewardTranslator::class)->apply($reward);
        });

        $this->registerAdminInjectComposer(
            ['vote::admin.rewards.index', 'vote::admin.rewards.edit'],
            AdminRewardsComposer::class,
            self::REWARDS,
        );

        ActionLog::registerLogs([
            'extended-translation.vote.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::vote.logs.updated',
                'model' => Reward::class,
            ],
            'extended-translation.vote.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::vote.logs.deleted',
                'model' => Reward::class,
            ],
        ]);
    }
}
