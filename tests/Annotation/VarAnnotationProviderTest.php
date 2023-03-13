<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Annotation;

use Generator;
use PHPUnit\Framework\Assert;
use ReflectionClass;
use ReflectionProperty;

class VarAnnotationProviderTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return string[][]|\Generator
	 */
	public function varAnnotationDataProvider(): Generator
	{
		yield [
			'string',
		];
		yield [
			'integer',
		];
		yield [
			'null',
		];
		yield [
			'string|null',
		];
		yield [
			'int',
		];
		yield [
			'string|integer',
		];
		yield [
			'\Foo',
		];
		yield [
			'\Foo\Bar',
		];
		yield [
			'Foo',
		];
		yield [
			'\Foo|integer',
		];
		yield [
			'string[]',
		];
		yield [
			'\Foo[]',
		];
		yield [
			'Template<Foo>',
		];
		yield [
			'integer:string',
		];
	}

	/**
	 * @dataProvider varAnnotationDataProvider
	 *
	 * @param string $value
	 */
	public function testGetVarAnnotationValue(string $value): void
	{
		$docComment = sprintf('/**
			 * @var %s
			 */', $value);

		$property = $this
			->getMockBuilder(ReflectionProperty::class)
			->disableOriginalConstructor()
			->getMock();

		$property
			->expects(self::once())
			->method('getDocComment')
			->willReturn($docComment);

		$varAnnotationProvider = new VarAnnotationProvider();

		$annotation = $varAnnotationProvider->getPropertyAnnotation($property, 'var');

		Assert::assertSame('var', $annotation->getName());
		Assert::assertSame($value, $annotation->getValue());
		Assert::assertCount(0, $annotation->getFields());
	}

	/**
	 * @dataProvider varAnnotationDataProvider
	 *
	 * @param string $value
	 */
	public function testGetVarAnnotationValueInline(string $value): void
	{
		$docComment = sprintf('/** @var %s */', $value);

		$property = $this
			->getMockBuilder(ReflectionProperty::class)
			->disableOriginalConstructor()
			->getMock();

		$property
			->expects(self::once())
			->method('getDocComment')
			->willReturn($docComment);

		$varAnnotationProvider = new VarAnnotationProvider();

		$annotation = $varAnnotationProvider->getPropertyAnnotation($property, 'var');

		Assert::assertSame('var', $annotation->getName());
		Assert::assertSame($value, $annotation->getValue());
		Assert::assertCount(0, $annotation->getFields());
	}

	public function testVarAnnotationDoesNotExist(): void
	{
		try {
			$docComment = '/**
				 * @author
				 */';

			$property = $this
				->getMockBuilder(ReflectionProperty::class)
				->disableOriginalConstructor()
				->getMock();

			$property
				->expects(self::once())
				->method('getDocComment')
				->willReturn($docComment);

			$property
				->expects(self::any())
				->method('getDeclaringClass')
				->willReturn(new ReflectionClass(Foo::class));

			$property
				->expects(self::any())
				->method('getName')
				->willReturn('test');

			$varAnnotationProvider = new VarAnnotationProvider();

			$varAnnotationProvider->getPropertyAnnotation($property, 'var');

			Assert::fail('Exception expected');

		} catch (\Consistence\Annotation\AnnotationNotFoundException $e) {
			Assert::assertSame($property, $e->getProperty());
			Assert::assertSame('var', $e->getAnnotationName());
		}
	}

	public function testMalformedVarAnnotation(): void
	{
		try {
			$docComment = '/**
				 * @var
				 */';

			$property = $this
				->getMockBuilder(ReflectionProperty::class)
				->disableOriginalConstructor()
				->getMock();

			$property
				->expects(self::once())
				->method('getDocComment')
				->willReturn($docComment);

			$property
				->expects(self::any())
				->method('getDeclaringClass')
				->willReturn(new ReflectionClass(Foo::class));

			$property
				->expects(self::any())
				->method('getName')
				->willReturn('test');

			$varAnnotationProvider = new VarAnnotationProvider();

			$varAnnotationProvider->getPropertyAnnotation($property, 'var');

			Assert::fail('Exception expected');

		} catch (\Consistence\Annotation\AnnotationNotFoundException $e) {
			Assert::assertSame($property, $e->getProperty());
			Assert::assertSame('var', $e->getAnnotationName());
		}
	}

	public function testSupportsOnlyVarAnnotation(): void
	{
		try {
			$property = $this
				->getMockBuilder(ReflectionProperty::class)
				->disableOriginalConstructor()
				->getMock();

			$property
				->expects(self::any())
				->method('getDeclaringClass')
				->willReturn(new ReflectionClass(Foo::class));

			$property
				->expects(self::any())
				->method('getName')
				->willReturn('test');

			$varAnnotationProvider = new VarAnnotationProvider();

			$varAnnotationProvider->getPropertyAnnotation($property, 'author');

			Assert::fail('Exception expected');

		} catch (\Consistence\Annotation\AnnotationNotFoundException $e) {
			Assert::assertSame($property, $e->getProperty());
			Assert::assertSame('author', $e->getAnnotationName());
		}
	}

	public function testDoesNotSupportGetAnnotations(): void
	{
		$property = $this
			->getMockBuilder(ReflectionProperty::class)
			->disableOriginalConstructor()
			->getMock();

		$property
			->expects(self::never())
			->method('getDocComment');

		$varAnnotationProvider = new VarAnnotationProvider();

		Assert::assertCount(0, $varAnnotationProvider->getPropertyAnnotations($property, 'var'));
	}

}
