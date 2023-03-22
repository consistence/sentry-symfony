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
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('array expected');

		$foo->setAuthors('Me');
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetInvalidItemType(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('string expected');

		$foo->setAuthors(['Me', 1]);
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testSetNullValue(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('string expected');

		$foo->setAuthors(['Me', null]);
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testAddInvalidItemType(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('string expected');

		$foo->addAuthor(1);
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testAddNull(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('string expected');

		$foo->addAuthor(null);
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testContainsInvalidItemType(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('string expected');

		$foo->containsAuthor(1);
	}

	/**
	 * @dataProvider fooProvider
	 *
	 * @param \Consistence\Sentry\SymfonyBundle\Type\Foo $foo
	 */
	public function testRemoveInvalidItemType(Foo $foo): void
	{
		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('string expected');

		$foo->removeAuthor(1);
	}

}
