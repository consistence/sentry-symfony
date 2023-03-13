<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Type;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\Assert;
use stdClass;

class CollectionOfObjectsIntegrationTest extends \PHPUnit\Framework\TestCase
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
	public function testGetEmpty(Foo $foo): void
	{
		Assert::assertCount(0, $foo->getEventDates());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetAndGet(Foo $foo): void
	{
		$eventDates = [
			new DateTimeImmutable(),
			new DateTimeImmutable('tomorrow'),
		];
		$foo->setEventDates($eventDates);
		Assert::assertEquals($eventDates, $foo->getEventDates());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testAdd(Foo $foo): void
	{
		$date = new DateTimeImmutable();
		$foo->addEventDate($date);
		Assert::assertContains($date, $foo->getEventDates());
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testContains(Foo $foo): void
	{
		$today = new DateTimeImmutable();
		$tomorrow = new DateTimeImmutable('tomorrow');
		$dates = [
			$today,
			$tomorrow,
		];
		$foo->setEventDates($dates);

		Assert::assertTrue($foo->containsEventDate($today));
		Assert::assertTrue($foo->containsEventDate($tomorrow));
		Assert::assertFalse($foo->containsEventDate(new DateTimeImmutable()));
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testRemove(Foo $foo): void
	{
		$today = new DateTimeImmutable();
		$tomorrow = new DateTimeImmutable('tomorrow');
		$dates = [
			$today,
			$tomorrow,
		];
		$foo->setEventDates($dates);

		$foo->removeEventDate($today);

		Assert::assertFalse($foo->containsEventDate($today));
		Assert::assertTrue($foo->containsEventDate($tomorrow));
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetInvalidCollectionType(Foo $foo): void
	{
		$invalidValue = new DateTimeImmutable();
		$eventDates = $invalidValue;

		try {
			$foo->setEventDates($eventDates);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame(DateTimeImmutable::class, $e->getValueType());
			Assert::assertSame('array', $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetInvalidItemType(Foo $foo): void
	{
		$invalidValue = new stdClass();
		$eventDates = [new DateTimeImmutable(), $invalidValue];

		try {
			$foo->setEventDates($eventDates);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame(stdClass::class, $e->getValueType());
			Assert::assertSame(DateTimeInterface::class, $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetNullValue(Foo $foo): void
	{
		$invalidValue = null;
		$eventDates = [new DateTimeImmutable(), $invalidValue];

		try {
			$foo->setEventDates($eventDates);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('null', $e->getValueType());
			Assert::assertSame(DateTimeInterface::class, $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testAddInvalidItemType(Foo $foo): void
	{
		$invalidValue = new stdClass();

		try {
			$foo->addEventDate($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame(stdClass::class, $e->getValueType());
			Assert::assertSame(DateTimeInterface::class, $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testAddNull(Foo $foo): void
	{
		$invalidValue = null;

		try {
			$foo->addEventDate($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('null', $e->getValueType());
			Assert::assertSame(DateTimeInterface::class, $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testContainsInvalidItemType(Foo $foo): void
	{
		$invalidValue = new stdClass();

		try {
			$foo->containsEventDate($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame(stdClass::class, $e->getValueType());
			Assert::assertSame(DateTimeInterface::class, $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testRemoveInvalidItemType(Foo $foo): void
	{
		$invalidValue = new stdClass();

		try {
			$foo->removeEventDate($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame(stdClass::class, $e->getValueType());
			Assert::assertSame(DateTimeInterface::class, $e->getExpectedTypes());
		}
	}

}
