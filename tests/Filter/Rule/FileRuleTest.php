<?php

namespace PhpcsDiff\Tests\Filter\Rule;

use PhpcsDiff\Filter\Rule\Exception\InvalidArgumentException;
use PhpcsDiff\Filter\Rule\Exception\RuleException;
use PhpcsDiff\Filter\Rule\Exception\RuntimeException;
use PhpcsDiff\Filter\Rule\FileRule;
use PhpcsDiff\Tests\TestBase;

class FileRuleTest extends TestBase
{
    public function setUp(): void
    {
        parent::setUp();

        // Create directory for testNonFile() test
        mkdir('test');

        // Create txt file for testFile() test
        fclose(fopen('test.txt', 'wb'));
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // Remove directory for testNonFile() test
        rmdir('test');


        // Remove txt file for testFile() test
        unlink('test.txt');
    }

    /**
     * @covers \PhpcsDiff\Filter\Rule\FileRule::__invoke
     * @throws RuleException
     */
    public function testNonString()
    {
        $this->expectExceptionMessage('The data argument provided is not a string.');
        $this->expectException(RuleException::class);
        $this->expectException(InvalidArgumentException::class);
        $rule = new FileRule();
        $rule(null);
    }

    /**
     * @covers \PhpcsDiff\Filter\Rule\FileRule::__invoke
     * @throws RuleException
     */
    public function testNonExistentFile()
    {
        $this->expectExceptionMessage('The file provided does not exist.');
        $this->expectException(RuleException::class);
        $this->expectException(RuntimeException::class);
        $rule = new FileRule();
        $rule('');
    }

    /**
     * @covers \PhpcsDiff\Filter\Rule\FileRule::__invoke
     * @throws RuleException
     */
    public function testNonFile()
    {
        $this->expectException(RuntimeException::class);
        $this->expectException(RuleException::class);
        $this->expectExceptionMessage('The file provided is not a regular file.');
        $rule = new FileRule();
        $rule('test');
    }

    /**
     * @covers \PhpcsDiff\Filter\Rule\FileRule::__invoke
     * @throws RuleException
     */
    public function testFile()
    {
        $rule = new FileRule();
        $actual = $rule('test.txt');

        $this->assertNull($actual);
    }
}
