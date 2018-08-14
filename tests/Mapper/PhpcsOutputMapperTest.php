<?php

namespace PhpcsDiff\Tests\Mapper;

use PhpcsDiff\Mapper\MapperInterface;
use PhpcsDiff\Mapper\PhpcsOutputMapper;
use PhpcsDiff\Tests\TestBase;

class PhpcsOutputMapperTest extends TestBase
{
    /**
     * @covers PhpcsOutputMapper::__construct
     */
    public function testMapperInstance()
    {
        $mapper = new PhpcsOutputMapper([], '');

        $this->assertInstanceOf(MapperInterface::class, $mapper);
    }

    /**
     * @covers PhpcsOutputMapper::map
     */
    public function testEmptyMapper()
    {
        $mappedData = (new PhpcsOutputMapper([], ''))->map([]);

        $this->assertEmpty($mappedData);
    }
}
