<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Type;

use PHPUnit\Framework\Assert;

class CollectionOfStringsIntegrationTest extends \PHPUnit\Framework\TestCase
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
		Assert::assertCount(0, $foo->getAuthors());
	}

	/**
	 * @dataProvider fooProvider
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
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testAdd(Foo $foo): void
	{
		$foo->addAuthor('Irene');
		Assert::assertContains('Irene', $foo->getAuthors());
	}

	/**
	 * @dataProvider fooProvider
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
	 * @dataProvider fooProvider
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
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetInvalidCollectionType(Foo $foo): void
	{
		$invalidValue = 'Me';
		$newValues = $invalidValue;

		try {
			$foo->setAuthors($newValues);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('string', $e->getValueType());
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
		$invalidValue = 1;
		$newValues = ['Me', $invalidValue];

		try {
			$foo->setAuthors($newValues);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('int', $e->getValueType());
			Assert::assertSame('string', $e->getExpectedTypes());
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
		$newValues = ['Me', $invalidValue];

		try {
			$foo->setAuthors($newValues);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('null', $e->getValueType());
			Assert::assertSame('string', $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testAddInvalidItemType(Foo $foo): void
	{
		$invalidValue = 1;

		try {
			$foo->addAuthor($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('int', $e->getValueType());
			Assert::assertSame('string', $e->getExpectedTypes());
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
			$foo->addAuthor($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('null', $e->getValueType());
			Assert::assertSame('string', $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testContainsInvalidItemType(Foo $foo): void
	{
		$invalidValue = 1;

		try {
			$foo->containsAuthor($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('int', $e->getValueType());
			Assert::assertSame('string', $e->getExpectedTypes());
		}
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testRemoveInvalidItemType(Foo $foo): void
	{
		$invalidValue = 1;

		try {
			$foo->removeAuthor($invalidValue);
			Assert::fail('Exception expected');
		} catch (\Consistence\InvalidArgumentTypeException $e) {
			Assert::assertSame($invalidValue, $e->getValue());
			Assert::assertSame('int', $e->getValueType());
			Assert::assertSame('string', $e->getExpectedTypes());
		}
	}

}
