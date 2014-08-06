<?php
namespace Phrappe;

class PhrappeTest extends \PHPUnit_Framework_TestCase
{
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
    public function test_non_existant_command()
    {
        Phrappe::xyzzyfooooobar();
    }
}
