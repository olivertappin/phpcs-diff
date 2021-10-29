<?php

namespace PhpcsDiff\Validator;

use PhpcsDiff\Filter\Rule\RuleInterface;
use PhpcsDiff\Validator\Exception\InvalidArgumentException;
use PhpcsDiff\Validator\Exception\ValidatorException;

class RuleValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * @throws ValidatorException
     */
    public function validate(): void
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
