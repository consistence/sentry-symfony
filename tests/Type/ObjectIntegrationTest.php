<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Type;

use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\Assert;

class ObjectIntegrationTest extends \PHPUnit\Framework\TestCase
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
	public function testGetUninitialized(Foo $foo): void
	{
		Assert::assertNull($foo->getPublishDate());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSet(Foo $foo): void
	{
		$publishDate = new DateTimeImmutable();
		$foo->setPublishDate($publishDate);
		Assert::assertSame($publishDate, $foo->getPublishDate());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetNullToNotNullable(Foo $foo): void
	{
		$invalidValue = null;

		try {
			$foo->setCreatedDate($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('null', $e->getValueType());
			Assert::assertSame(DateTimeImmutable::class, $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetScalarType(Foo $foo): void
	{
		$invalidValue = 1;

		try {
			$foo->setCreatedDate($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('int', $e->getValueType());
			Assert::assertSame(DateTimeImmutable::class, $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetInvalidType(Foo $foo): void
	{
		$invalidValue = new DateTime();

		try {
			$foo->setCreatedDate($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame(DateTime::class, $e->getValueType());
			Assert::assertSame(DateTimeImmutable::class, $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testNullableSetScalarType(Foo $foo): void
	{
		$invalidValue = 1;

		try {
			$foo->setPublishDate($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('int', $e->getValueType());
			Assert::assertSame(sprintf('%s|null', DateTimeImmutable::class), $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testNullableSetInvalidType(Foo $foo): void
	{
		$invalidValue = new DateTime();

		try {
			$foo->setPublishDate($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame(DateTime::class, $e->getValueType());
			Assert::assertSame(sprintf('%s|null', DateTimeImmutable::class), $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testAcceptSubtypes(Foo $foo): void
	{
		$immutable = new DateTimeImmutable();
		$foo->setDateTimeInterface($immutable);
		Assert::assertSame($immutable, $foo->getDateTimeInterface());

		$mutable = new DateTime();
		$foo->setDateTimeInterface($mutable);
		Assert::assertSame($mutable, $foo->getDateTimeInterface());
	}

}
