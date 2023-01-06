<?php

namespace PhpcsDiff\Tests\Filter\Rule;

use PhpcsDiff\Filter\Rule\Exception\RuleException;
use PhpcsDiff\Filter\Rule\Exception\RuntimeException;
use PhpcsDiff\Filter\Rule\PhpFileRule;
use PhpcsDiff\Tests\TestBase;

class PhpFileRuleTest extends TestBase
{
    protected $handle;

    public function setUp(): void
    {
        parent::setUp();

        // Create txt file for testNonPhpFile() test
        fclose(fopen('test.txt', 'wb'));

        // Create txt file for testIncorrectMimeType() test
        fclose(fopen('image.php', 'wb'));

        // Create txt file for testPhpFile() test
        $handle = fopen('test.php', 'wb');
        fwrite($handle, '<?php echo "Hello World!";');
        fclose($handle);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // Remove txt file for testNonPhpFile() test
        unlink('test.txt');

        // Remove txt file for testIncorrectMimeType() test
        unlink('image.php');

        // Remove txt file for testPhpFile() test
        unlink('test.php');
    }

    /**
     * @covers \PhpcsDiff\Filter\Rule\PhpFileRule::__invoke
     * @covers \PhpcsDiff\Filter\Rule\FileRule::__invoke
     * @throws RuleException
     */
    public function testNonPhpFile(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectException(RuleException::class);
        $this->expectExceptionMessage('The file provided does not have a .php extension.');
        $rule = new PhpFileRule();
        $rule('test.txt');
    }

    /**
     * @covers \PhpcsDiff\Filter\Rule\PhpFileRule::__invoke
     * @covers \PhpcsDiff\Filter\Rule\FileRule::__invoke
     * @throws RuleException
     */
    public function testIncorrectMimeType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectException(RuleException::class);
        $this->expectExceptionMessage('The file provided does not have the text/x-php mime type.');
        $rule = new PhpFileRule();
        $rule('image.php');
    }

    /**
     * @covers \PhpcsDiff\Filter\Rule\PhpFileRule::__invoke
     * @covers \PhpcsDiff\Filter\Rule\FileRule::__invoke
     * @throws RuleException
     */
    public function testPhpFile(): void
    {
        $rule = new PhpFileRule();
        $actual = $rule('test.php');

        $this->assertNull($actual);
    }
}
