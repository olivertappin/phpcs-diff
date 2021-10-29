<?php

namespace PhpcsDiff\Tests\Mapper;

use PhpcsDiff\Mapper\MapperInterface;
use PhpcsDiff\Mapper\PhpcsViolationsMapper;
use PhpcsDiff\Tests\TestBase;

class PhpcsViolationsMapperTest extends TestBase
{
    /**
     * @covers \PhpcsDiff\Mapper\PhpcsViolationsMapper::__construct
     */
    public function testMapperInstance(): void
    {
        $mapper = new PhpcsViolationsMapper([], '');

        $this->assertInstanceOf(MapperInterface::class, $mapper);
    }

    /**
     * @covers \PhpcsDiff\Mapper\PhpcsViolationsMapper::__construct
     * @covers \PhpcsDiff\Mapper\PhpcsViolationsMapper::map
     */
    public function testEmptyMapper(): void
    {
        $mappedData = (new PhpcsViolationsMapper([], ''))->map([]);

        $this->assertEmpty($mappedData);
    }
}
