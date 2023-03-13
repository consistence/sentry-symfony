<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Type;

use PHPUnit\Framework\Assert;

class StringIntegrationTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return \Consistence\Sentry\SymfonyBundle\Type\Foo[][]
	 */
	public function fooDataProvider(): array
	{
		$generator = new SentryDataGenerator();
		$generator->generate('Foo');

		return [
			[new FooGenerated()],
		];
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testGet(Foo $foo): void
	{
		Assert::assertSame('testName', $foo->getName());
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testGetUninitializedString(Foo $foo): void
	{
		Assert::assertNull($foo->getDescription());
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSet(Foo $foo): void
	{
		$foo->setName('fooBar');
		Assert::assertSame('fooBar', $foo->getName());
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetNullToNotNullable(Foo $foo): void
	{
		$invalidValue = null;

		try {
			$foo->setName($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('null', $e->getValueType());
			Assert::assertSame('string', $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetEmptyString(Foo $foo): void
	{
		$foo->setName('');
		Assert::assertSame('', $foo->getName());
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetStringZero(Foo $foo): void
	{
		$foo->setName('0');
		Assert::assertSame('0', $foo->getName());
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetWhitespaceString(Foo $foo): void
	{
		$foo->setName('    ');
		Assert::assertSame('    ', $foo->getName());
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetInvalidType(Foo $foo): void
	{
		$invalidValue = 1;

		try {
			$foo->setName($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('int', $e->getValueType());
			Assert::assertSame('string', $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testNullableSetEmptyString(Foo $foo): void
	{
		$foo->setDescription('');
		Assert::assertSame('', $foo->getDescription());
	}

	/**
	 * @dataProvider fooDataProvider
	 * @depends testGet
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testNullableSetWhitespaceString(Foo $foo): void
	{
		$foo->setDescription('    ');
		Assert::assertSame('    ', $foo->getDescription());
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testNullableSetInvalidType(Foo $foo): void
	{
		$invalidValue = 1;

		try {
			$foo->setDescription($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('int', $e->getValueType());
			Assert::assertSame('string|null', $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testCustomNameGetAndSet(Foo $foo): void
	{
		$foo->setMy('foo');
		Assert::assertSame('foo', $foo->getMy());
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testCallPrivateFromPublic(Foo $foo): void
	{
		$foo->setPublic('foo');
		Assert::assertSame('foo', $foo->getPrivate());
	}

}
