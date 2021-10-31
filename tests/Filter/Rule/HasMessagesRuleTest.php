<?php

namespace PhpcsDiff\Tests\Filter\Rule;

use PhpcsDiff\Filter\Rule\Exception\InvalidArgumentException;
use PhpcsDiff\Filter\Rule\Exception\RuleException;
use PhpcsDiff\Filter\Rule\FileRule;
use PhpcsDiff\Filter\Rule\HasMessagesRule;
use PhpcsDiff\Tests\TestBase;

class HasMessagesRuleTest extends TestBase
{
    /**
     * @covers \PhpcsDiff\Filter\Rule\HasMessagesRule::__invoke
     * @throws RuleException
     */
    public function testNoMessages(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectException(RuleException::class);
        $this->expectExceptionMessage("The data argument provided has no messages.");
        $rule = new HasMessagesRule();
        $rule([]);
    }

    /**
     * @covers \PhpcsDiff\Filter\Rule\HasMessagesRule::__invoke
     * @throws RuleException
     */
    public function testEmptyMessages(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectException(RuleException::class);
        $this->expectExceptionMessage("The data argument provided has no messages.");
        $rule = new HasMessagesRule();
        $rule(['messages' => []]);
    }

    /**
     * @covers \PhpcsDiff\Filter\Rule\HasMessagesRule::__invoke
     * @throws RuleException
     */
    public function testNullMessages(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectException(RuleException::class);
        $this->expectExceptionMessage("The data argument provided has no messages.");
        $rule = new HasMessagesRule();
        $rule(['messages' => null]);
    }

    /**
     * @covers \PhpcsDiff\Filter\Rule\HasMessagesRule::__invoke
     * @throws RuleException
     */
    public function testMessages(): void
    {
        $rule = new HasMessagesRule();
        $actual = $rule(['messages' => ['message']]);

        $this->assertNull($actual);
    }
}
