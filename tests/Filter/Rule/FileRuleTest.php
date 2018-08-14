<?php

namespace PhpcsDiff\Tests\Filter\Rule;

use PhpcsDiff\Filter\Rule\Exception\InvalidArgumentException;
use PhpcsDiff\Filter\Rule\Exception\RuntimeException;
use PhpcsDiff\Filter\Rule\FileRule;
use PhpcsDiff\Tests\TestBase;

class FileRuleTest extends TestBase
{
    /**
     * @covers FileRule::__invoke
     */
    public function testInvoke()
    {
        $rule = new FileRule();

        $this->assertException(InvalidArgumentException::class, function () use ($rule) {
            $rule(null);
        });

        $this->assertException(RuntimeException::class, function () use ($rule) {
            $rule('');
        });

        mkdir('test');

        $this->assertException(RuntimeException::class, function () use ($rule) {
            $rule('test');
        });

        rmdir('test');



//
//        $handle = fopen('test', 'wb');
//
//        $this->assertException(RuntimeException::class, function() use ($rule) {
//            $rule('');
//        });
//
//        fclose($handle);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The data argument provided is not a string.
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function testNonString()
    {
        $rule = new FileRule();
        $rule(null);
    }
}
