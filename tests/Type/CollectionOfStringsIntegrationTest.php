<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Type;

use Closure;
use Generator;
use PHPUnit\Framework\Assert;

class CollectionOfStringsIntegrationTest extends \PHPUnit\Framework\TestCase
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
		Assert::assertCount(0, $foo->getAuthors());
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetAndGet(Foo $foo): void
	{
		$authors = [
			'Me',
			'Myself',
		];
		$foo->setAuthors($authors);
		Assert::assertEquals($authors, $foo->getAuthors());
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testAdd(Foo $foo): void
	{
		$foo->addAuthor('Irene');
		Assert::assertContains('Irene', $foo->getAuthors());
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testContains(Foo $foo): void
	{
		$authors = [
			'Me',
			'Myself',
		];
		$foo->setAuthors($authors);

		Assert::assertTrue($foo->containsAuthor('Me'));
		Assert::assertTrue($foo->containsAuthor('Myself'));
		Assert::assertFalse($foo->containsAuthor('Irene'));
	}

	/**
	 * @dataProvider fooDataProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testRemove(Foo $foo): void
	{
		$authors = [
			'Me',
			'Myself',
		];
		$foo->setAuthors($authors);

		$foo->removeAuthor('Me');

		Assert::assertFalse($foo->containsAuthor('Me'));
		Assert::assertTrue($foo->containsAuthor('Myself'));
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function invalidArgumentTypeDataProvider(): Generator
	{
		foreach ($this->fooDataProvider() as $caseName => $caseData) {
			yield $caseName . ' - setAuthors() with invalid collection type' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->setAuthors('Me');
				},
				'expectedInvalidValue' => 'Me',
				'expectedInvalidValueType' => 'string',
				'expectedExpectedTypes' => 'array',
			];

			yield $caseName . ' - setAuthors() with invalid item type' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->setAuthors(['Me', 1]);
				},
				'expectedInvalidValue' => 1,
				'expectedInvalidValueType' => 'int',
				'expectedExpectedTypes' => 'string',
			];

			yield $caseName . ' - setAuthors() with null value' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->setAuthors(['Me', null]);
				},
				'expectedInvalidValue' => null,
				'expectedInvalidValueType' => 'null',
				'expectedExpectedTypes' => 'string',
			];

			yield $caseName . ' - addAuthor() with invalid item type' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->addAuthor(1);
				},
				'expectedInvalidValue' => 1,
				'expectedInvalidValueType' => 'int',
				'expectedExpectedTypes' => 'string',
			];

			yield $caseName . ' - addAuthor() with null value' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->addAuthor(null);
				},
				'expectedInvalidValue' => null,
				'expectedInvalidValueType' => 'null',
				'expectedExpectedTypes' => 'string',
			];

			yield $caseName . ' - containsAuthor() with invalid item type' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->containsAuthor(1);
				},
				'expectedInvalidValue' => 1,
				'expectedInvalidValueType' => 'int',
				'expectedExpectedTypes' => 'string',
			];

			yield $caseName . ' - containsAuthor() with null value' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->containsAuthor(null);
				},
				'expectedInvalidValue' => null,
				'expectedInvalidValueType' => 'null',
				'expectedExpectedTypes' => 'string',
			];

			yield $caseName . ' - removeAuthor() with invalid item type' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->removeAuthor(1);
				},
				'expectedInvalidValue' => 1,
				'expectedInvalidValueType' => 'int',
				'expectedExpectedTypes' => 'string',
			];

			yield $caseName . ' - removeAuthor() with null value' => [
				'callMethodCallback' => function () use ($caseData): void {
					$caseData['foo']->removeAuthor(null);
				},
				'expectedInvalidValue' => null,
				'expectedInvalidValueType' => 'null',
				'expectedExpectedTypes' => 'string',
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
