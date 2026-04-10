<?php

namespace App\Translations;

use LaravelLang\Publisher\Plugins\Provider;

class PluginManager extends Provider
{
    protected string $base_path = __DIR__.'/../../resources/translations/';

    protected array $plugins = [
        AppPlugin::class,
    ];
}
