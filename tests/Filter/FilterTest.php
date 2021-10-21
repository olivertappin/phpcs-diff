<?php

namespace PhpcsDiff\Tests\Filter;

use PhpcsDiff\Filter\Exception\FilterException;
use PhpcsDiff\Filter\Exception\InvalidRuleException;
use PhpcsDiff\Filter\Filter;
use PhpcsDiff\Filter\Rule\FileRule;
use PhpcsDiff\Tests\TestBase;

class FilterTest extends TestBase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create directory for testFileFilter() test
        mkdir('test');
        fclose(fopen('test.txt', 'wb'));
        fclose(fopen('image.php', 'wb'));
        $handle = fopen('test.php', 'wb');
        fwrite($handle, '<?php echo "Hello World!";');
        fclose($handle);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Remove directory and files for testFileFilter() test
        rmdir('test');
        unlink('test.txt');
        unlink('image.php');
        unlink('test.php');
    }

    /**
     * @return array
     */
    public function unfilteredDataProvider(): array
    {
        return [
            [
                [
                    null,
                ],
                [],
                [
                    null,
                ],
            ],
            [
                [
                    '',
                    null,
                    0,
                    1,
                    'test.txt',
                ],
                [
                    'test.txt'
                ],
                [
                    '',
                    null,
                    0,
                    1,
                ],
            ],
            [
                [
                    'test.txt',
                ],
                [
                    'test.txt'
                ],
                [],
            ],
            [
                [
                    null,
                    '',
                    'nonExistentFile',
                    'test',
                    'test.txt',
                    'image.php',
                    'test.php',
                ],
                [
                    'test.txt',
                    'image.php',
                    'test.php',
                ],
                [
                    null,
                    '',
                    'nonExistentFile',
                    'test',
                ],
            ]
        ];
    }

    /**
     * @covers Filter::__construct
     * @throws FilterException
     */
    public function testInvalidRule(): void
    {
        $this->expectException(FilterException::class);
        $this->expectException(InvalidRuleException::class);
        new Filter(
            [
                new \stdClass(),
            ],
            [
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ]
        );
    }

    /**
     * @covers Filter::filter
     * @throws FilterException
     */
    public function testFilterInstance(): void
    {
        $filter = (new Filter(
            [
                new FileRule(),
            ],
            [
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ]
        ))->filter();

        $this->assertInstanceOf(Filter::class, $filter);
    }

    /**
     * @covers Filter::__construct
     * @covers Filter::filter
     * @covers Filter::getFilteredData
     * @covers Filter::getContaminatedData
     * @dataProvider unfilteredDataProvider
     * @param array $unfilteredData
     * @param array $filteredData
     * @param array $contaminatedData
     * @throws FilterException
     */
    public function testFileFilter(array $unfilteredData, array $filteredData, array $contaminatedData): void
    {
        $filter = (new Filter(
            [
                new FileRule(),
            ],
            $unfilteredData
        ))->filter();

        $this->assertSame($filteredData, array_values($filter->getFilteredData()));
        $this->assertSame($contaminatedData, array_values($filter->getContaminatedData()));
    }
}
