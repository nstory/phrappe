<?php
namespace Phrappe;

/**
 * @backupStaticAttributes enabled
 */
class PhrappeTest extends \PHPUnit_Framework_TestCase
{
    private $phrappe;

    public function setUp()
    {
        $this->phrappe = new Phrappe;
    }

    public function test_static()
    {
        $calendar = Phrappe::cal('June', '1984');
        $this->assertRegExp('/June 1984/', $calendar);
    }

    /**
     * @expectedException Phrappe\PhrappeException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage cat: fooBARxyzzy42234242: No such file or directory
     */
    public function test_static_throws_exception()
    {
        Phrappe::cat('fooBARxyzzy42234242'); // cat a non-existant file
    }

    /**
     * @expectedException Phrappe\PhrappeException
     * @expectedExceptionMessage command not found
     */
    public function test_static_non_existant_command()
    {
        Phrappe::xyzzyfooooobar();
    }

    public function test_static_return_result()
    {
        Phrappe::$return_result = true;
        $this->assertRegExp(
            '/June 1984/',
            Phrappe::cal('June', '1984')->stdout
        );
        $this->assertRegExp(
            '/No such file or directory/',
            Phrappe::cat('fooBARxyzzy422369')->stderr
        );
        $this->assertEquals(1, Phrappe::false()->exit_code);
    }

    public function test_instance_call()
    {
        $calendar = $this->phrappe->cal('June', '1984');
        $this->assertRegExp('/June 1984/', $calendar);
    }

    /**
     * @expectedException Phrappe\PhrappeException
     * @expectedExceptionCode -1
     * @expectedExceptionMessage proc_open failed
     */
    public function test_instance_proc_open_fails()
    {
        $this->phrappe->proc_open = function() {
            return false;
        };
        $this->phrappe->true();
    }

    public function test_instance_return_result()
    {
        $this->phrappe->return_result = true;
        $this->assertRegExp(
            '/June 1984/',
            $this->phrappe->cal('June', '1984')->stdout
        );
        $this->assertRegExp(
            '/No such file or directory/',
            $this->phrappe->cat('fooBARxyzzy422369')->stderr
        );
        $this->assertEquals(1, $this->phrappe->false()->exit_code);
    }

    public function test_invoke()
    {
        $ph = $this->phrappe;
        $calendar = $ph('cal', 'June', '1984');
        $this->assertRegExp('/June 1984/', $calendar);
    }

    public function test_invoke_return_result()
    {
        $this->phrappe->return_result = true;
        $ph = $this->phrappe;
        $this->assertRegExp(
            '/June 1984/',
            $ph('cal', 'June', '1984')->stdout
        );
        $this->assertRegExp(
            '/No such file or directory/',
            $ph('cat', 'fooBARxyzzy422369')->stderr
        );
        $this->assertEquals(1, $ph('false')->exit_code);
    }

    public static function argument_examples()
    {
        return [
            [['foo', 'bar'], 'foo bar'],
            [['\'"!'], '\'"!'],
            [[['foo' => true]], '--foo'],
            [[['foo' => 'bar']], '--foo bar'],
            [['foo', ['bar' => 'baz']], 'foo --bar baz'],
            [[['b' => true]], '-b'],
            [[['foo', 'bar' => 'baz']], 'foo --bar baz'],
            [[['foo' => '\'']], '--foo \'']
        ];
    }

    /**
     * @dataProvider argument_examples
     */
    public function test_instance_arguments($input, $expected)
    {
        $actual = call_user_func_array([$this->phrappe, 'echo'], $input);
        $this->assertEquals("$expected\n", $actual);
    }

    /**
     * @dataProvider argument_examples
     */
    public function test_invoke_arguments($input, $expected)
    {
        $actual = call_user_func_array($this->phrappe, array_merge(['echo'], $input));
        $this->assertEquals("$expected\n", $actual);
    }
}
