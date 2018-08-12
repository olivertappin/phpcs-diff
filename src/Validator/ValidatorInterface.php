<?php

namespace PhpcsDiff\Validator;

interface ValidatorInterface
{
    /**
     * @return mixed
     * @throws \PhpcsDiff\Validator\Exception\ValidatorException
     */
    public function validate();
}
