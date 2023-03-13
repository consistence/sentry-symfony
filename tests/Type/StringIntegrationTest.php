<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Type;

use Closure;
use Generator;
use PHPUnit\Framework\Assert;

class StringIntegrationTest extends \PHPUnit\Framework\TestCase
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
	 * @return mixed[][]|\Generator
	 */
	public function setDataProvider(): Generator
	{
		foreach ($this->fooDataProvider() as $caseName => $caseData) {
			yield $caseName . ' - non-empty string' => [
				'foo' => $caseData['foo'],
				'value' => 'fooBar',
			];

			yield $caseName . ' - empty string' => [
				'foo' => $caseData['foo'],
				'value' => '',
			];

			yield $caseName . ' - string zero' => [
				'foo' => $caseData['foo'],
				'value' => '0',
			];

			yield $caseName . ' - whitespace string' => [
				'foo' => $caseData['foo'],
				'value' => '    ',
			];
		}
	}

	/**
	 * @dataProvider setDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 * @param string $value
	 */
	public function testSet(
		Foo $foo,
		string $value
	): void
	{
		$foo->setName($value);
		Assert::assertSame($value, $foo->getName());
	}

	/**
	 * @dataProvider setDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 * @param string $value
	 */
	public function testSetNullable(
		Foo $foo,
		string $value
	): void
	{
		$foo->setDescription($value);
		Assert::assertSame($value, $foo->getDescription());
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function invalidArgumentTypeDataProvider(): Generator
	{
		foreach ($this->fooDataProvider() as $caseName => $caseData) {
			yield $caseName . ' - set not nullable with null value' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->setName(null);
				},
				'expectedInvalidValue' => null,
				'expectedInvalidValueType' => 'null',
				'expectedExpectedTypes' => 'string',
			];

			yield $caseName . ' - set not nullable with invalid type' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->setName(1);
				},
				'expectedInvalidValue' => 1,
				'expectedInvalidValueType' => 'int',
				'expectedExpectedTypes' => 'string',
			];

			yield $caseName . ' - set nullable with invalid type' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->setDescription(1);
				},
				'expectedInvalidValue' => 1,
				'expectedInvalidValueType' => 'int',
				'expectedExpectedTypes' => 'string|null',
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
