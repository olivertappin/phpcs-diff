<?php

namespace PhpcsDiff\Tests\Filter\Rule;

use PhpcsDiff\Filter\Rule\PhpFileRule;
use PhpcsDiff\Tests\TestBase;

class PhpFileRuleTest extends TestBase
{
    protected $handle;

    public function setUp()
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

    public function tearDown()
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
     * @covers PhpFileRule::__invoke
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\RuntimeException
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\RuleException
     * @expectedExceptionMessage The file provided does not have a .php extension.
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function testNonPhpFile()
    {
        $rule = new PhpFileRule();
        $rule('test.txt');
    }

    /**
     * @covers PhpFileRule::__invoke
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\RuntimeException
     * @expectedException \PhpcsDiff\Filter\Rule\Exception\RuleException
     * @expectedExceptionMessage The file provided does not have the text/x-php mime type.
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function testIncorrectMimeType()
    {
        $rule = new PhpFileRule();
        $rule('image.php');
    }

    /**
     * @covers PhpFileRule::__invoke
     * @throws \PhpcsDiff\Filter\Rule\Exception\RuleException
     */
    public function testPhpFile()
    {
        $rule = new PhpFileRule();
        $actual = $rule('test.php');

        $this->assertNull($actual);
    }
}
