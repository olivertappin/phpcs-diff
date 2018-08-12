<?php

namespace PhpcsDiff\Filter\Rule;

use PhpcsDiff\Filter\Rule\Exception\RuntimeException;

class PhpFileRule extends FileRule
{
    /**
     * @param mixed $data
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function __invoke($data)
    {
        parent::__invoke($data);

        if ('.php' !== substr($data, -4)) {
            throw new RuntimeException('The file provided does not have a .php extension.');
        }

        if ('text/x-php' !== mime_content_type($data)) {
            throw new RuntimeException('The file provided does not have the text/x-php mime type.');
        }
    }
}
