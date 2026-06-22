<?php namespace Golem15\Backend;

use Backend\Controllers\Auth as AuthController;
use Backend\Models\BrandSetting;
use System\Classes\PluginBase;
use Illuminate\Support\Facades\Cache;
use Winter\Storm\Support\Facades\File;

class Plugin extends PluginBase
{
    public $elevated = true;

    public function pluginDetails(): array
    {
        return [
            'name'        => 'golem15.backend::lang.plugin.name',
            'description' => 'golem15.backend::lang.plugin.description',
            'author'      => 'Golem15',
            'icon'        => 'icon-paint-brush',
        ];
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/brand.php',
            'brand'
        );
    }

    public function boot(): void
    {
        if (!$this->app->runningInBackend()) {
            return;
        }

        $this->extendAuthController();
        $this->syncBrandStyles();
    }

    protected function extendAuthController(): void
    {
        AuthController::extend(function ($controller) {
            $controller->bindEvent('page.beforeDisplay', function () use ($controller) {
                $controller->layout = '~/plugins/golem15/backend/views/layouts/auth';
            });
        });
    }

    /**
     * Keep the brand custom_css in sync with our LESS file.
     * Compares file mtime against a cached timestamp to avoid
     * unnecessary DB writes on every request.
     */
    protected function syncBrandStyles(): void
    {
        $lessPath = __DIR__ . '/assets/less/backend.less';

        if (!File::exists($lessPath)) {
            return;
        }

        $mtime = File::lastModified($lessPath);
        $cacheKey = 'golem15.backend::less_mtime';

        if (Cache::get($cacheKey) === $mtime) {
            return;
        }

        $settings = BrandSetting::instance();
        $fileContent = File::get($lessPath);

        if ($settings->custom_css !== $fileContent) {
            $settings->custom_css = $fileContent;
            $settings->save();
        }

        Cache::put($cacheKey, $mtime, now()->addDays(30));
    }
}
