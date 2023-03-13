<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Type;

use Closure;
use DateTime;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\Assert;

class ObjectIntegrationTest extends \PHPUnit\Framework\TestCase
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
	public function testGetUninitialized(Foo $foo): void
	{
		Assert::assertNull($foo->getPublishDate());
	}

	/**
	 * @dataProvider fooDataProvider
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
	 * @return mixed[][]|\Generator
	 */
	public function invalidArgumentTypeDataProvider(): Generator
	{
		foreach ($this->fooDataProvider() as $caseName => $caseData) {
			yield $caseName . ' - set not nullable with null value' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->setCreatedDate(null);
				},
				'expectedInvalidValue' => null,
				'expectedInvalidValueType' => 'null',
				'expectedExpectedTypes' => DateTimeImmutable::class,
			];

			yield $caseName . ' - set not nullable with scalar type' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->setCreatedDate(1);
				},
				'expectedInvalidValue' => 1,
				'expectedInvalidValueType' => 'int',
				'expectedExpectedTypes' => DateTimeImmutable::class,
			];

			yield $caseName . ' - set not nullable with invalid type' => (function () use ($caseData): array {
				$invalidValue = new DateTime();

				return [
					'callMethodCallback' => function () use ($caseData, $invalidValue): void {
						$caseData['foo']->setCreatedDate($invalidValue);
					},
					'expectedInvalidValue' => $invalidValue,
					'expectedInvalidValueType' => DateTime::class,
					'expectedExpectedTypes' => DateTimeImmutable::class,
				];
			})();

			yield $caseName . ' - set nullable with scalar type' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->setPublishDate(1);
				},
				'expectedInvalidValue' => 1,
				'expectedInvalidValueType' => 'int',
				'expectedExpectedTypes' => sprintf('%s|null', DateTimeImmutable::class),
			];

			yield $caseName . ' - set nullable with invalid type' => (function () use ($caseData): array {
				$invalidValue = new DateTime();

				return [
					'callMethodCallback' => function () use ($caseData, $invalidValue): void {
						$caseData['foo']->setPublishDate($invalidValue);
					},
					'expectedInvalidValue' => $invalidValue,
					'expectedInvalidValueType' => DateTime::class,
					'expectedExpectedTypes' => sprintf('%s|null', DateTimeImmutable::class),
				];
			})();
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

	/**
	 * @dataProvider fooDataProvider
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
