<?php

namespace Sppay\SpPayPhp\Tests;

use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertStringContainsString;

class ExampleTest extends TestCase
{
    function test()
    {
        return  assertStringContainsString('SP Pay', 'SP Pay API works.');
    }
}
