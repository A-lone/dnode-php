<?php
namespace DNode;

class RemoteProxyTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->proxy = new RemoteProxy();
    }

    /** @test */
    public function getMethodsShouldDefaultToEmptyArray()
    {
        $this->assertSame(array(), $this->proxy->getMethods());
    }

    /** @test */
    public function setMethodShouldAddMethodToList()
    {
        $foo = function () {};
        $this->proxy->setMethod('foo', $foo);

        $this->assertSame(array('foo' => $foo), $this->proxy->getMethods());
    }

    /** @test */
    public function setMethodShouldAcceptMultipleCalls()
    {
        $foo = function () {};
        $bar = function () {};

        $this->proxy->setMethod('foo', $foo);
        $this->proxy->setMethod('bar', $bar);

        $this->assertSame(array('foo' => $foo, 'bar' => $bar), $this->proxy->getMethods());
    }

    /** @test */
    public function setMethodShouldOverrideExistingMethod()
    {
        $foo = function () {};
        $bar = function () {};

        $this->proxy->setMethod('foo', $foo);
        $this->proxy->setMethod('foo', $bar);

        $this->assertSame(array('foo' => $bar), $this->proxy->getMethods());
    }

    /** @test */
    public function proxyShouldDelegateMissingMethodsWithMagic()
    {
        $foo = $this->expectCallableOnce('a');
        $bar = $this->expectCallableOnce('b');

        $this->proxy->setMethod('foo', $foo);
        $this->proxy->setMethod('bar', $bar);

        $this->proxy->foo('a');
        $this->proxy->bar('b');
    }

    /**
     * @test
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Method baz not available
     */
    public function proxyShouldThrowExceptionOnNonExistentMethod()
    {
        $this->proxy->baz();
    }

    private function expectCallableOnce($arg)
    {
        $callable = $this->getMock('DNode\CallableStub');
        $callable
            ->expects($this->once())
            ->method('__invoke')
            ->with($arg);

        return $callable;
    }
}

class CallableStub
{
    public function __invoke()
    {
    }
}
