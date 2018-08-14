<?php

namespace PhpcsDiff\Tests\Filter\Rule;

use PhpcsDiff\Filter\Rule\FileRule;
use PhpcsDiff\Tests\TestBase;

class FileRuleTest extends TestBase
{
    public function setUp()
    {
        parent::setUp();

        // Create directory for testNonFile() test
        mkdir('test');

        // Create txt file for testFile() test
        fclose(fopen('test.txt', 'wb'));
    }

    public function tearDown()
    {
        parent::tearDown();

        // Remove directory for testNonFile() test
        rmdir('test');


        // Remove txt file for testFile() test
        unlink('test.txt');
    }

    /**
     * @covers FileRule::__invoke
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\InvalidArgumentException
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\RuleException
     * @expectedExceptionMessage The data argument provided is not a string.
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function testNonString()
    {
        $rule = new FileRule();
        $rule(null);
    }

    /**
     * @covers FileRule::__invoke
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\RuntimeException
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\RuleException
     * @expectedExceptionMessage The file provided does not exist.
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function testNonExistentFile()
    {
        $rule = new FileRule();
        $rule('');
    }

    /**
     * @covers FileRule::__invoke
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\RuntimeException
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\RuleException
     * @expectedExceptionMessage The file provided is not a regular file.
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function testNonFile()
    {
        $rule = new FileRule();
        $rule('test');
    }

    /**
     * @covers PhpFileRule::__invoke
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function testFile()
    {
        $rule = new FileRule();
        $actual = $rule('test.txt');

        $this->assertNull($actual);
    }
}
