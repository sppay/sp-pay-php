<?php

namespace Sppay\SpPayPhp\Tests;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

abstract class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize Dotenv
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    }
}
