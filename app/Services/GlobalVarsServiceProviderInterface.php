<?php
/**
 * Created by PhpStorm.
 * User: rene
 * Date: 3/8/17
 * Time: 10:09 PM
 */

namespace app\Services;

interface GlobalVarsServiceProviderInterface{
    public function getBaseUrl(): string;
    public function getRequestUri(): string;
}
