<?php

namespace Sppay\SpPayPhp\Tests;

use function PHPUnit\Framework\assertStringContainsString;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test()
    {
        return  assertStringContainsString('SP Pay', 'SP Pay API works.');
    }
}
