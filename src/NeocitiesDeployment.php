<?php

namespace ReedJones\Neocities;

use TightenCo\Jigsaw\Jigsaw;

class NeocitiesDeployment
{
    public function __construct($container = null)
    {
        if ($container) {
            // Register 'deploy' command
            // jigsaw deploy
            $container->get(Jigsaw::class)->registerCommand(RegisterDeploymentCommand::class);
        }
    }

    public function deployToNeocities()
    {
        return function () {
            $this->app->singleton(Neocities::class, function ($c) {
                return new Neocities(['apiKey' => env('NEO_CITIES_API_KEY')]);
            });

            $buildDir = str_replace(__DIR__, '', $this->getDestinationPath());

            $files = collect($this->getFilesystem()->allFiles(__DIR__.$buildDir))
                ->flatMap(function ($file) use ($buildDir) {
                    $file = $file->getRelativePathname();

                    return [$file => ".{$buildDir}/$file"];
                })
                ->toArray();

            $this->app->get(Neocities::class)->upload($files);

            return $this->app->get(Neocities::class)->info();
        };
    }
}
