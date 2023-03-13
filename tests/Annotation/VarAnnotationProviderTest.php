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
		yield 'string' => [
			'value' => 'string',
		];
		yield 'integer' => [
			'value' => 'integer',
		];
		yield 'null' => [
			'value' => 'null',
		];
		yield 'string|null' => [
			'value' => 'string|null',
		];
		yield 'int' => [
			'value' => 'int',
		];
		yield 'string|integer' => [
			'value' => 'string|integer',
		];
		yield '\Foo' => [
			'value' => '\Foo',
		];
		yield '\Foo\Bar' => [
			'value' => '\Foo\Bar',
		];
		yield 'Foo' => [
			'value' => 'Foo',
		];
		yield '\Foo|integer' => [
			'value' => '\Foo|integer',
		];
		yield 'string[]' => [
			'value' => 'string[]',
		];
		yield '\Foo[]' => [
			'value' => '\Foo[]',
		];
		yield 'Template<Foo>' => [
			'value' => 'Template<Foo>',
		];
		yield 'integer:string' => [
			'value' => 'integer:string',
		];
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function getVarAnnotationValueDataProvider(): Generator
	{
		foreach ($this->varAnnotationDataProvider() as $caseName => $caseData) {
			yield $caseName . ' - inline annotation' => [
				'value' => $caseData['value'],
				'docComment' => sprintf('/** @var %s */', $caseData['value']),
			];

			yield $caseName . ' - multiline annotation' => [
				'value' => $caseData['value'],
				'docComment' => sprintf('/**
					 * @var %s
					 */', $caseData['value']),
			];
		}
	}

	/**
	 * @dataProvider getVarAnnotationValueDataProvider
	 *
	 * @param string $value
	 * @param string $docComment
	 */
	public function testGetVarAnnotationValue(
		string $value,
		string $docComment
	): void
	{
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
	 * @return mixed[][]|\Generator
	 */
	public function annotationNotFoundDataProvider(): Generator
	{
		yield '@var annotation does not exist' => [
			'docComment' => '/**
				 * @author
				 */',
			'annotationName' => 'var',
		];

		yield 'malformed @var annotation' => [
			'docComment' => '/**
				 * @var
				 */',
			'annotationName' => 'var',
		];

		yield 'supports only @var annotation' => [
			'docComment' => '/**
				 * @author
				 */',
			'annotationName' => 'author',
		];
	}

	/**
	 * @dataProvider annotationNotFoundDataProvider
	 *
	 * @param string|null $docComment
	 * @param string $annotationName
	 */
	public function testAnnotationNotFound(
		?string $docComment,
		string $annotationName
	): void
	{
		try {
			$property = $this
				->getMockBuilder(ReflectionProperty::class)
				->disableOriginalConstructor()
				->getMock();

			$property
				->expects(self::atMost(1))
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

			$varAnnotationProvider->getPropertyAnnotation($property, $annotationName);

			Assert::fail('Exception expected');

		} catch (\Consistence\Annotation\AnnotationNotFoundException $e) {
			Assert::assertSame($property, $e->getProperty());
			Assert::assertSame($annotationName, $e->getAnnotationName());
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
