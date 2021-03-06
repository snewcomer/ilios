<?php

declare(strict_types=1);

namespace App\Monitor;

use App\Command\UpdateFrontendCommand;
use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;

class Frontend implements CheckInterface
{
    private $cacheDir;

    public function __construct($kernelCacheDir)
    {
        $this->cacheDir = $kernelCacheDir;
    }

    /**
     * @inheritdoc
     */
    public function check()
    {
        $assetsPath = $this->cacheDir . UpdateFrontendCommand::FRONTEND_DIRECTORY;
        $path = $assetsPath . 'index.json';

        if (!file_exists($path)) {
            return new Failure("has not been loaded. Run bin/console ilios:maintenance:update-frontend");
        }

        return new Success('has been loaded');
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return 'Ilios Frontend';
    }
}
