<?php
namespace Phrappe;

use Mockery as m;

class PhrappeTest extends \PHPUnit_Framework_TestCase
{
    private $phrappe;

    public function setUp()
    {
        $this->phrappe = new Phrappe;
    }

    public function test_static_call()
    {
        $calendar = Phrappe::cal('June', '1984');
        $this->assertRegExp('/June 1984/', $calendar);
    }

    /**
     * @expectedException Phrappe\PhrappeException
     * @expectedExceptionCode 1
     * @expectedExceptionMessage cat: fooBARxyzzy42234242: No such file or directory
     */
    public function test_static_call_throws_exception()
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

    public function test_instance_call()
    {
        $calendar = $this->phrappe->cal('June', '1984');
        $this->assertRegExp('/June 1984/', $calendar);
    }

    // public function test_instance_proc_open_fails()
    // {
    //     $ph = m::mock('Phrappe\Phrappe[proc_open]');
    //     $ph->true();
    // }

    public function test_invoke()
    {
        $ph = $this->phrappe;
        $calendar = $ph('cal', 'June', '1984');
        $this->assertRegExp('/June 1984/', $calendar);
    }

    public function test_invoke_escapes_arguments()
    {
        // need to use invoke b/c echo is a PHP keyword
        $mess_of_chars = '\'"!';
        $ph = $this->phrappe;
        $this->assertEquals("$mess_of_chars\n", $ph('echo', $mess_of_chars));
    }
}
