<?php

namespace PhpcsDiff\Filter;

use PhpcsDiff\Filter\Exception\FilterException;
use PhpcsDiff\Filter\Exception\InvalidRuleException;
use PhpcsDiff\Filter\Rule\Exception\RuleException;
use PhpcsDiff\Filter\Rule\RuleInterface;
use PhpcsDiff\Validator\Exception\ValidatorException;
use PhpcsDiff\Validator\RuleValidator;

class Filter
{
    /**
     * @var RuleInterface[]
     */
    protected $rules;

    /**
     * @var array
     */
    protected $unfilteredData;

    /**
     * @var array
     */
    protected $filteredData = [];

    /**
     * @var array
     */
    protected $contaminatedData = [];

    /**
     * @param array $rules
     * @param array $unfilteredData
     * @throws FilterException
     */
    public function __construct(array $rules, array $unfilteredData)
    {
        try {
            (new RuleValidator($rules))->validate();
        } catch (ValidatorException $exception) {
            throw new InvalidRuleException('', 0, $exception);
        }

        $this->rules = $rules;
        $this->unfilteredData = $unfilteredData;
    }

    /**
     * @return $this
     */
    public function filter()
    {
        foreach ($this->unfilteredData as $key => $item) {
            foreach ($this->rules as $rule) {
                try {
                    $rule($item);
                    $this->filteredData[$key] = $item;
                } catch (RuleException $exception) {
                    $this->contaminatedData[$key] = $item;
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFilteredData()
    {
        return $this->filteredData;
    }

    /**
     * @return array
     */
    public function getContaminatedData()
    {
        return $this->contaminatedData;
    }
}
