<?php

namespace PhpcsDiff\Tests;

use League\CLImate\CLImate;
use PhpcsDiff\PhpcsDiff;
use PHPUnit\Framework\TestCase;

class PhpcsDiffTest extends TestCase
{
    /**
     * @var CLImate
     */
    private $cliMate;

    protected function setUp(): void
    {
        $this->cliMate = $this->createMock(CLImate::class);
    }

    /**
     * @covers \PhpcsDiff\PhpcsDiff::__construct
     * @covers \PhpcsDiff\PhpcsDiff::error
     * @covers \PhpcsDiff\PhpcsDiff::getExitCode
     * @covers \PhpcsDiff\PhpcsDiff::isFlagSet
     * @covers \PhpcsDiff\PhpcsDiff::setExitCode
     */
    public function testExitCodeBeforeRun()
    {
        $phpcsDiff = new PhpcsDiff([], $this->cliMate);

        self::assertSame(1, $phpcsDiff->getExitCode());
    }

    /**
     * @covers \PhpcsDiff\PhpcsDiff::__construct
     * @covers \PhpcsDiff\PhpcsDiff::error
     * @covers \PhpcsDiff\PhpcsDiff::getExitCode
     * @covers \PhpcsDiff\PhpcsDiff::isFlagSet
     * @covers \PhpcsDiff\PhpcsDiff::setExitCode
     */
    public function testPhpcsDiffNoCurrent()
    {
        $phpcsDiff = new PhpcsDiff(['fakeBranch'], $this->cliMate);

        self::assertSame(1, $phpcsDiff->getExitCode());
    }

    /**
     * @covers \PhpcsDiff\PhpcsDiff::__construct
     * @covers \PhpcsDiff\PhpcsDiff::error
     * @covers \PhpcsDiff\PhpcsDiff::getExitCode
     * @covers \PhpcsDiff\PhpcsDiff::isFlagSet
     * @covers \PhpcsDiff\PhpcsDiff::setExitCode
     */
    public function testVerboseNoCurrent()
    {
        $this->cliMate
            ->expects(self::exactly(2))
            ->method('__call')
            ->withConsecutive(
                ['comment', ['Running in verbose mode.']],
                ['error', ['Please provide a <bold>base branch</bold> as the first argument.']]
            );
        ;

        $phpcsDiff = new PhpcsDiff(['-v', 'fakeBranch'], $this->cliMate);

        self::assertSame(1, $phpcsDiff->getExitCode());
    }
}
