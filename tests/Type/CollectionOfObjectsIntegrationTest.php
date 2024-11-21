<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Type;

use Closure;
use DateTimeImmutable;
use DateTimeInterface;
use Generator;
use PHPUnit\Framework\Assert;
use stdClass;

class CollectionOfObjectsIntegrationTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return \Consistence\Sentry\SymfonyBundle\Type\Foo[][]|\Generator
	 */
	public function fooDataProvider(): Generator
	{
		yield 'instance of generated class' => (function (): array {
			$generator = new SentryDataGenerator();
			$generator->generate('Foo');

			return [
				'foo' => new FooGenerated(),
			];
		})();
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testGetEmpty(Foo $foo): void
	{
		Assert::assertCount(0, $foo->getEventDates());
	}

	/**
	 * @dataProvider fooDataProvider
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
	 * @dataProvider fooDataProvider
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
	 * @dataProvider fooDataProvider
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
	 * @dataProvider fooDataProvider
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
	 * @return mixed[][]|\Generator
	 */
	public function invalidArgumentTypeDataProvider(): Generator
	{
		foreach ($this->fooDataProvider() as $caseName => $caseData) {
			yield $caseName . ' - setEventDates() with invalid collection type' => (function () use ($caseData): array {
				$invalidValue = new DateTimeImmutable();

				return [
					'callMethodCallback' => function () use ($caseData, $invalidValue): void {
						$caseData['foo']->setEventDates($invalidValue);
					},
					'expectedInvalidValue' => $invalidValue,
					'expectedInvalidValueType' => DateTimeImmutable::class,
					'expectedExpectedTypes' => 'array',
				];
			})();

			yield $caseName . ' - setEventDates() with invalid item type' => (function () use ($caseData): array {
				$invalidValue = new stdClass();

				return [
					'callMethodCallback' => function () use ($caseData, $invalidValue): void {
						$caseData['foo']->setEventDates([new DateTimeImmutable(), $invalidValue]);
					},
					'expectedInvalidValue' => $invalidValue,
					'expectedInvalidValueType' => stdClass::class,
					'expectedExpectedTypes' => DateTimeInterface::class,
				];
			})();

			yield $caseName . ' - setEventDates() with null value' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->setEventDates([new DateTimeImmutable(), null]);
				},
				'expectedInvalidValue' => null,
				'expectedInvalidValueType' => 'null',
				'expectedExpectedTypes' => DateTimeInterface::class,
			];

			yield $caseName . ' - addEventDate() with invalid item type' => (function () use ($caseData): array {
				$invalidValue = new stdClass();

				return [
					'callMethodCallback' => function () use ($caseData, $invalidValue): void {
						$caseData['foo']->addEventDate($invalidValue);
					},
					'expectedInvalidValue' => $invalidValue,
					'expectedInvalidValueType' => stdClass::class,
					'expectedExpectedTypes' => DateTimeInterface::class,
				];
			})();

			yield $caseName . ' - addEventDate() with null value' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->addEventDate(null);
				},
				'expectedInvalidValue' => null,
				'expectedInvalidValueType' => 'null',
				'expectedExpectedTypes' => DateTimeInterface::class,
			];

			yield $caseName . ' - containsEventDate() with invalid item type' => (function () use ($caseData): array {
				$invalidValue = new stdClass();

				return [
					'callMethodCallback' => function () use ($caseData, $invalidValue): void {
						$caseData['foo']->containsEventDate($invalidValue);
					},
					'expectedInvalidValue' => $invalidValue,
					'expectedInvalidValueType' => stdClass::class,
					'expectedExpectedTypes' => DateTimeInterface::class,
				];
			})();

			yield $caseName . ' - containsEventDate() with null value' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->containsEventDate(null);
				},
				'expectedInvalidValue' => null,
				'expectedInvalidValueType' => 'null',
				'expectedExpectedTypes' => DateTimeInterface::class,
			];

			yield $caseName . ' - removeEventDate() with invalid item type' => (function () use ($caseData): array {
				$invalidValue = new stdClass();

				return [
					'callMethodCallback' => function () use ($caseData, $invalidValue): void {
						$caseData['foo']->removeEventDate($invalidValue);
					},
					'expectedInvalidValue' => $invalidValue,
					'expectedInvalidValueType' => stdClass::class,
					'expectedExpectedTypes' => DateTimeInterface::class,
				];
			})();

			yield $caseName . ' - removeEventDate() with null value' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->removeEventDate(null);
				},
				'expectedInvalidValue' => null,
				'expectedInvalidValueType' => 'null',
				'expectedExpectedTypes' => DateTimeInterface::class,
			];
		}
	}

	/**
	 * @dataProvider invalidArgumentTypeDataProvider
	 *
	 * @param \Closure $callMethodCallback
	 * @param mixed $expectedInvalidValue
	 * @param string $expectedInvalidValueType
	 * @param string $expectedExpectedTypes
	 */
	public function testCallMethodWithInvalidArgumentType(
		Closure $callMethodCallback,
		$expectedInvalidValue,
		string $expectedInvalidValueType,
		string $expectedExpectedTypes
	): void
	{
		try {
			$callMethodCallback();
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($expectedInvalidValue, $e->getValue());
			Assert::assertSame($expectedInvalidValueType, $e->getValueType());
			Assert::assertSame($expectedExpectedTypes, $e->getExpectedTypes());
		}
	}

}
