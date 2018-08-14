<?php

namespace PhpcsDiff\Tests\Filter\Rule;

use PhpcsDiff\Filter\Rule\FileRule;
use PhpcsDiff\Filter\Rule\HasMessagesRule;
use PhpcsDiff\Tests\TestBase;

class HasMessagesRuleTest extends TestBase
{
    /**
     * @covers HasMessagesRule::__invoke
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\InvalidArgumentException
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\RuleException
     * @expectedExceptionMessage The data argument provided has no messages.
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function testNoMessages()
    {
        $rule = new HasMessagesRule();
        $rule([]);
    }

    /**
     * @covers HasMessagesRule::__invoke
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\InvalidArgumentException
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\RuleException
     * @expectedExceptionMessage The data argument provided has no messages.
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function testEmptyMessages()
    {
        $rule = new HasMessagesRule();
        $rule(['messages' => []]);
    }

    /**
     * @covers HasMessagesRule::__invoke
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\InvalidArgumentException
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\RuleException
     * @expectedExceptionMessage The data argument provided has no messages.
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function testNullMessages()
    {
        $rule = new HasMessagesRule();
        $rule(['messages' => null]);
    }

    /**
     * @covers FileRule::__invoke
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function testMessages()
    {
        $rule = new HasMessagesRule();
        $actual = $rule(['messages' => ['message']]);

        $this->assertNull($actual);
    }
}
