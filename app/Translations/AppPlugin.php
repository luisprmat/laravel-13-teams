<?php

namespace App\Translations;

use LaravelLang\Publisher\Plugins\Plugin;

class AppPlugin extends Plugin
{
    public function files(): array
    {
        return [
            // path from $base_path/source for PluginManager
            'app.json' => '{locale}.json',
        ];
    }
}
