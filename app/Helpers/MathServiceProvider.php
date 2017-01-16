<?php

namespace app\Helpers;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

class MathServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['math'] = $app->protect(function () {
            return new Math();
        }
        );
    }
}
?>