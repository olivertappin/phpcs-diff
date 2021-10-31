<?php

namespace PhpcsDiff\Filter\Rule;

use PhpcsDiff\Filter\Rule\Exception\InvalidArgumentException;
use PhpcsDiff\Filter\Rule\Exception\RuleException;

class HasMessagesRule implements RuleInterface
{
    /**
     * @param mixed $data
     * @throws RuleException
     */
    public function __invoke($data)
    {
        if (empty($data['messages'])) {
            throw new InvalidArgumentException('The data argument provided has no messages.');
        }
    }
}
