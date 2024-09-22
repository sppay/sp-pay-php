<?php

namespace Sppay\SpPayPhp\Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

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
