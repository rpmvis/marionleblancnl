<?php

namespace Studio\Models;

use \Mockery as m;
use Studio\Models\Visitor;

class VisitorMock{
    function __construct()
    {
    }

    public function getVisitor() : Visitor{
        // mock expected result
        return $this->_mockVisitor();
    }

    protected function _mockVisitor(): \Mockery\MockInterface
    {
        // mock visitor
        $mock = m::mock('Studio\Models\Visitor');
        $mock->bezDatum = '01-01-1980';
        $mock->bezAanhef = "De heer";
        $mock->bezNaam = "Joe Dalton";
        $mock->bezTelefoon = "06 12345678";
        $mock->bezBelVanaf = "09:00 uur";
        $mock->bezBelTot = "17:00 uur";
        $mock->bezEmail = "rpmvis@gmail.com";
        $mock->bezOpmerking = "No Comment";

        $mock->shouldReceive('getVisitor')->once()->andReturn( $mock );

        return $mock;
    }
}
