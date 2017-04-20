<?php

use Studio\Models\Visitor;

class VisitorTest
{
    public function __construct(Helper $helper, RecursiveValidator $validator, BladeProxy $blade){
        $this->validator = $validator;
        $this->blade = $blade;
        $this->helper = $helper;
    }
}