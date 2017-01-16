<?php

use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    // ...

    public function getCacheDir()
    {
        return PATH_ROOT .'/web/cache';
    }
}