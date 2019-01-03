<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Type;

use DateTime;
use DateTimeImmutable;

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
		$this->assertNull($foo->getPublishDate());
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
		$this->assertSame($publishDate, $foo->getPublishDate());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetNullToNotNullable(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('DateTimeImmutable expected');

		$foo->setCreatedDate(null);
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetScalarType(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('DateTimeImmutable expected');

		$foo->setCreatedDate(1);
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetInvalidType(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('DateTimeImmutable expected');

		$foo->setCreatedDate(new DateTime());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testNullableSetScalarType(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('DateTimeImmutable|null expected');

		$foo->setPublishDate(1);
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testNullableSetInvalidType(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('DateTimeImmutable|null expected');

		$foo->setPublishDate(new DateTime());
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
		$this->assertSame($immutable, $foo->getDateTimeInterface());

		$mutable = new DateTime();
		$foo->setDateTimeInterface($mutable);
		$this->assertSame($mutable, $foo->getDateTimeInterface());
	}

}
