<?php

namespace app\Services;

interface EmailServiceInterface {
    public function send($callback): int ;
}
