<?php

namespace Azuriom\Plugin\ExtendedTranslation\Integrations\Faq;

use Azuriom\Models\ActionLog;
use Azuriom\Plugin\ExtendedTranslation\Core\PluginIntegration;
use Azuriom\Plugin\ExtendedTranslation\Core\RegistersAdminInjectComposer;
use Azuriom\Plugin\FAQ\Models\Question;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Route;

final class FaqIntegration implements PluginIntegration
{
    use RegistersAdminInjectComposer;

    public const PLUGIN_ID = 'faq';

    public const QUESTIONS = 'extended-translation.faq';

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
        $app->singleton(QuestionTranslator::class);
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
            self::QUESTIONS => 'extended-translation::faq.permissions.questions',
        ];
    }

    public function adminNavPermissions(): array
    {
        return [self::QUESTIONS];
    }

    public function adminNavItems(): array
    {
        return [
            'extended-translation.admin.faq.index' => [
                'name' => trans('extended-translation::faq.nav'),
                'permission' => self::QUESTIONS,
            ],
        ];
    }

    protected function registerRuntime(Application $app): void
    {
        if (! self::available() || ! class_exists(Question::class)) {
            return;
        }

        Route::model('question', Question::class);

        Question::retrieved(function (Question $question) use ($app) {
            $app->make(QuestionTranslator::class)->apply($question);
        });

        $this->registerAdminInjectComposer(
            ['faq::admin.questions.index', 'faq::admin.questions.edit'],
            AdminQuestionsComposer::class,
            self::QUESTIONS,
        );

        ActionLog::registerLogs([
            'extended-translation.faq.updated' => [
                'icon' => 'translate',
                'color' => 'info',
                'message' => 'extended-translation::faq.logs.updated',
                'model' => Question::class,
            ],
            'extended-translation.faq.deleted' => [
                'icon' => 'translate',
                'color' => 'danger',
                'message' => 'extended-translation::faq.logs.deleted',
                'model' => Question::class,
            ],
        ]);
    }
}
