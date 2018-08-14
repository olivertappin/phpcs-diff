<?php

namespace PhpcsDiff\Tests;

use PHPUnit\Framework\TestCase;

abstract class TestBase extends TestCase
{
    /**
     * @param string $expectClass
     * @param callable $callback
     */
    protected function assertException($expectClass, callable $callback)
    {
        try {
            $callback();
        } catch (\Throwable $exception) {
            $this->assertInstanceOf($expectClass, $exception, 'An invalid exception was thrown');
            return;
        }

        $this->fail('No exception was thrown');
    }
}
