<?php

namespace PhpcsDiff\Mapper;

interface MapperInterface
{
    /**
     * @param array $data
     * @return array
     */
    public function map(array $data);
}
