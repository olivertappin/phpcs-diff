<?php

namespace PhpcsDiff\Filter\Rule;

use PhpcsDiff\Filter\Rule\Exception\InvalidArgumentException;

class HasMessagesRule implements RuleInterface
{
    /**
     * @param mixed $data
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function __invoke($data)
    {
        if (empty($data['messages'])) {
            throw new InvalidArgumentException('The data argument provided has no messages.');
        }
    }
}
