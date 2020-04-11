<?php

namespace WeProvide\TranslationBundle\Event;

use Symfony\Component\Filesystem\Filesystem;

class TranslationUpdateListener
{
    protected $cacheDir;

    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    public function onTranslationUpdate()
    {
        // Very dirty, but it works. We place a file in the cache directoy which tells us that
        // we've updated a translation. Use the command 'weprovide:translation:recache' (or add
        // it to cron) to determine wether or not the cache must be cleared (it also clears it).
        $fs = new Filesystem();
        $fs->touch($this->cacheDir . '/wpclear');
    }
}