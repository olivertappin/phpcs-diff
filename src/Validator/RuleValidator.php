<?php

namespace PhpcsDiff\Validator;

use PhpcsDiff\Filter\Rule\RuleInterface;
use PhpcsDiff\Validator\Exception\InvalidArgumentException;

class RuleValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * @return mixed|void
     * @throws \PhpcsDiff\Validator\Exception\ValidatorException
     */
    public function validate()
    {
        if (empty($this->data)) {
            throw new InvalidArgumentException('The data provided is empty.');
        }

        if (!is_array($this->data)) {
            throw new InvalidArgumentException('The data provided is not an array.');
        }

        foreach (array_values($this->data) as $i => $rule) {
            if (!$rule instanceof RuleInterface) {
                throw new InvalidArgumentException('Rule ' . ++$i . ' is not a valid rule class');
            }
        }
    }
}
