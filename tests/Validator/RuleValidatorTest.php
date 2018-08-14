<?php

namespace PhpcsDiff\Tests\Validator;

use PhpcsDiff\Filter\Rule\FileRule;
use PhpcsDiff\Filter\Rule\HasMessagesRule;
use PhpcsDiff\Filter\Rule\PhpFileRule;
use PhpcsDiff\Tests\TestBase;
use PhpcsDiff\Validator\RuleValidator;

class RuleValidatorTest extends TestBase
{
    /**
     * @covers RuleValidator::__construct
     */
    public function testRuleValidatorInstance()
    {
        $actual = new RuleValidator([]);

        $this->assertInstanceOf(RuleValidator::class, $actual);
    }

    /**
     * @covers RuleValidator::validate
     * @expectedException \PhpcsDiff\Validator\Exception\InvalidArgumentException
     * @expectedException \PhpcsDiff\Validator\Exception\ValidatorException
     * @expectedExceptionMessage The data provided is empty.
     */
    public function testEmptyRuleValidator()
    {
        (new RuleValidator([]))->validate();
    }

    /**
     * @covers RuleValidator::validate
     * @expectedException \PhpcsDiff\Validator\Exception\InvalidArgumentException
     * @expectedException \PhpcsDiff\Validator\Exception\ValidatorException
     * @expectedExceptionMessage The data provided is not an array.
     */
    public function testNonArrayRuleValidator()
    {
        (new RuleValidator('string'))->validate();
    }

    /**
     * @covers RuleValidator::validate
     */
    public function testRuleValidator()
    {
        $actual = (new RuleValidator([
            new FileRule(),
            new PhpFileRule(),
            new HasMessagesRule(),
        ]))->validate();

        $this->assertNull($actual);
    }
}
