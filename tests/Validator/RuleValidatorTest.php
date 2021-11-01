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
     * @covers \PhpcsDiff\Validator\RuleValidator::__construct
     */
    public function testRuleValidatorInstance(): void
    {
        $actual = new RuleValidator([]);

        $this->assertInstanceOf(RuleValidator::class, $actual);
    }

    /**
     * @covers \PhpcsDiff\Validator\RuleValidator::__construct
     * @covers \PhpcsDiff\Validator\RuleValidator::validate
     */
    public function testEmptyRuleValidator(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('The data provided is empty.');
        (new RuleValidator([]))->validate();
    }

    /**
     * @covers \PhpcsDiff\Validator\RuleValidator::__construct
     * @covers \PhpcsDiff\Validator\RuleValidator::validate
     */
    public function testNonArrayRuleValidator(): void
    {
        $this->expectException(ValidatorException::class);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The data provided is not an array.');
        (new RuleValidator('string'))->validate();
    }

    /**
     * @covers \PhpcsDiff\Validator\RuleValidator::__construct
     * @covers \PhpcsDiff\Validator\RuleValidator::validate
     * @throws ValidatorException
     */
    public function testRuleValidator(): void
    {
        $this->expectNotToPerformAssertions();

        // If this throws an exception, the test will fail
        (new RuleValidator([
            new FileRule(),
            new PhpFileRule(),
            new HasMessagesRule(),
        ]))->validate();
    }
}
