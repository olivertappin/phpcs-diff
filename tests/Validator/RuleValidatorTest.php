<?php

namespace PhpcsDiff\Tests\Validator;

use PhpcsDiff\Filter\Rule\FileRule;
use PhpcsDiff\Filter\Rule\HasMessagesRule;
use PhpcsDiff\Filter\Rule\PhpFileRule;
use PhpcsDiff\Tests\TestBase;
use PhpcsDiff\Validator\Exception\InvalidArgumentException;
use PhpcsDiff\Validator\Exception\ValidatorException;
use PhpcsDiff\Validator\RuleValidator;

class RuleValidatorTest extends TestBase
{
    /**
     * @covers RuleValidator::__construct
     */
    public function testRuleValidatorInstance(): void
    {
        $actual = new RuleValidator([]);

        $this->assertInstanceOf(RuleValidator::class, $actual);
    }

    /**
     * @covers RuleValidator::validate
     */
    public function testEmptyRuleValidator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage("The data provided is empty.");
        (new RuleValidator([]))->validate();
    }

    /**
     * @covers RuleValidator::validate
     */
    public function testNonArrayRuleValidator(): void
    {
        $this->expectException(ValidatorException::class);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The data provided is not an array.');
        (new RuleValidator('string'))->validate();
    }

    /**
     * @covers RuleValidator::validate
     */
    public function testRuleValidator(): void
    {
        $actual = (new RuleValidator([
            new FileRule(),
            new PhpFileRule(),
            new HasMessagesRule(),
        ]))->validate();

        $this->assertNull($actual);
    }
}
