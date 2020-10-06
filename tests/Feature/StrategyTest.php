<?php
namespace Feature;

use Strategy\ClientCode;
use PHPUnit\Framework\TestCase;

// -> vendor/bin/phpunit --colors tests

class StrategyTest extends TestCase
{
    public function testStrategy(){
        $strategy = new ClientCode();
        static::assertTrue($strategy->doAlgorithm());
    }
}