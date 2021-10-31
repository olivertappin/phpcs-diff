<?php

namespace PhpcsDiff\Tests\Mapper;

use PhpcsDiff\Mapper\MapperInterface;
use PhpcsDiff\Mapper\PhpcsViolationsMapper;
use PhpcsDiff\Tests\TestBase;

class PhpcsViolationsMapperTest extends TestBase
{
    private $mappedData1 = [
        'file.php' => [
            'messages' => [
                [
                    'line' => 100,
                    'message' => 'This is the message',
                    'type' => 'ERROR',
                ]
            ],
        ],
    ];

    private $mappedData2 = [
        'SomeImportantClass.php' => [
            'messages' => [
                [
                    'line' => 230,
                    'message' => 'This is the message',
                    'type' => 'ERROR',
                ],
            ],
        ],
        'index.php' => [
            'messages' => [
                [
                    'line' => 230,
                    'message' => 'This is the message',
                    'type' => 'ERROR',
                ],
                [
                    'line' => 250,
                    'message' => 'Some other warning',
                    'type' => 'WARNING',
                ]
            ],
        ],
    ];

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

    /**
     * @covers \PhpcsDiff\Mapper\PhpcsViolationsMapper::__construct
     * @covers \PhpcsDiff\Mapper\PhpcsViolationsMapper::map
     */
    public function testMapperLineMatch(): void
    {
        $changedLinesPerFile = [
            'file.php' => [
                100,
            ]
        ];

        $mappedData = (new PhpcsViolationsMapper($changedLinesPerFile, ''))->map($this->mappedData1);

        self::assertIsArray($mappedData);
        self::assertCount(1, $mappedData);
        self::assertIsString($mappedData[0]);
        self::assertSame($mappedData[0], 'file.php' . PHP_EOL . ' - Line 100 (ERROR) This is the message' . PHP_EOL);
    }

    /**
     * @covers \PhpcsDiff\Mapper\PhpcsViolationsMapper::__construct
     * @covers \PhpcsDiff\Mapper\PhpcsViolationsMapper::map
     */
    public function testMapperNoLineMatch(): void
    {
        $changedLinesPerFile = [
            'file.php' => [
                101,
            ]
        ];

        $mappedData = (new PhpcsViolationsMapper($changedLinesPerFile, ''))->map($this->mappedData1);

        self::assertIsArray($mappedData);
        self::assertCount(0, $mappedData);
    }

    /**
     * @covers \PhpcsDiff\Mapper\PhpcsViolationsMapper::__construct
     * @covers \PhpcsDiff\Mapper\PhpcsViolationsMapper::map
     */
    public function testMapperNoFileMatch(): void
    {
        $changedLinesPerFile = [
            'NonMatchedClass.php' => [
                101,
            ]
        ];

        $mappedData = (new PhpcsViolationsMapper($changedLinesPerFile, ''))->map($this->mappedData2);

        self::assertIsArray($mappedData);
        self::assertCount(0, $mappedData);
    }
}
