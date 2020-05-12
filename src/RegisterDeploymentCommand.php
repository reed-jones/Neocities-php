<?php

namespace ReedJones\Neocities;

use TightenCo\Jigsaw\Console\BuildCommand;
use TightenCo\Jigsaw\Jigsaw;

class RegisterDeploymentCommand extends BuildCommand
{
    private $jigsaw;

    public function __construct($container)
    {
        $this->jigsaw = $container->get(Jigsaw::class);
        parent::__construct($container);
    }

    protected function configure()
    {
        parent::configure();

        // Override default name & description
        $this->setName('deploy')
            ->setDescription('Build & Deploy your site to neocities.');
    }

    protected function fire()
    {
        parent::fire();

        $startTime = microtime(true);

        $env = $this->input->getArgument('env');
        $info = $this->jigsaw->deployToNeocities();

        $this->writeDeployTime($startTime);
        $this->console->info("{$env} deployed to https://{$info->info->sitename}.neocities.org");
    }

    protected function writeDeployTime($time) {
        $this->console->write(
            '<fg=yellow>Deploy time: </><fg=white>' .
            round(microtime(true) - $time, 2) .
            ' seconds</> '
        );
    }
}
