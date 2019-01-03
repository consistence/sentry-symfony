<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Type;

class StringIntegrationTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return \Consistence\Sentry\SymfonyBundle\Type\Foo[][]
	 */
	public function fooProvider(): array
	{
		$generator = new SentryDataGenerator();
		$generator->generate('Foo');

		return [
			[new FooGenerated()],
		];
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testGet(Foo $foo): void
	{
		$this->assertSame('testName', $foo->getName());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testGetUninitializedString(Foo $foo): void
	{
		$this->assertNull($foo->getDescription());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSet(Foo $foo): void
	{
		$foo->setName('fooBar');
		$this->assertSame('fooBar', $foo->getName());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetNullToNotNullable(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);

		$foo->setName(null);
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetEmptyString(Foo $foo): void
	{
		$foo->setName('');
		$this->assertSame('', $foo->getName());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetStringZero(Foo $foo): void
	{
		$foo->setName('0');
		$this->assertSame('0', $foo->getName());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetWhitespaceString(Foo $foo): void
	{
		$foo->setName('    ');
		$this->assertSame('    ', $foo->getName());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetInvalidType(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('string expected');

		$foo->setName(1);
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testNullableSetEmptyString(Foo $foo): void
	{
		$foo->setDescription('');
		$this->assertSame('', $foo->getDescription());
	}

	/**
	 * @dataProvider fooProvider
	 * @depends testGet
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testNullableSetWhitespaceString(Foo $foo): void
	{
		$foo->setDescription('    ');
		$this->assertSame('    ', $foo->getDescription());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testNullableSetInvalidType(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('string|null expected');

		$foo->setDescription(1);
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testCustomNameGetAndSet(Foo $foo): void
	{
		$foo->setMy('foo');
		$this->assertSame('foo', $foo->getMy());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testCallPrivateFromPublic(Foo $foo): void
	{
		$foo->setPublic('foo');
		$this->assertSame('foo', $foo->getPrivate());
	}

}
