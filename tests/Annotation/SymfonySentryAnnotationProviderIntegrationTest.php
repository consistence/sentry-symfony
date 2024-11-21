<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Annotation;

use Consistence\Annotation\Annotation;
use Consistence\Annotation\AnnotationField;
use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\Assert;
use ReflectionProperty;

class SymfonySentryAnnotationProviderIntegrationTest extends \PHPUnit\Framework\TestCase
{

	public function testGetAnnotationWithNoParams(): void
	{
		$annotationProvider = $this->createAnnotationProvider();
		$property = new ReflectionProperty(Foo::class, 'noParams');

		$annotation = $annotationProvider->getPropertyAnnotation($property, 'get');
		Assert::assertInstanceOf(Annotation::class, $annotation);
		Assert::assertSame('get', $annotation->getName());
		Assert::assertNull($annotation->getValue());
		Assert::assertCount(0, $annotation->getFields());
	}

	public function testGetAnnotationWithFields(): void
	{
		$annotationProvider = $this->createAnnotationProvider();
		$property = new ReflectionProperty(Foo::class, 'withFields');

		$annotation = $annotationProvider->getPropertyAnnotation($property, 'get');
		Assert::assertInstanceOf(Annotation::class, $annotation);
		Assert::assertSame('get', $annotation->getName());
		Assert::assertNull($annotation->getValue());
		$fields = $annotation->getFields();
		Assert::assertCount(2, $fields);
		Assert::assertInstanceOf(AnnotationField::class, $fields[0]);
		Assert::assertSame('name', $fields[0]->getName());
		Assert::assertSame('fooName', $fields[0]->getValue());
		Assert::assertInstanceOf(AnnotationField::class, $fields[1]);
		Assert::assertSame('visibility', $fields[1]->getName());
		Assert::assertSame('private', $fields[1]->getValue());
	}

	public function testGetAnnotationNotFound(): void
	{
		$annotationProvider = $this->createAnnotationProvider();
		$property = new ReflectionProperty(Foo::class, 'noParams');

		try {
			$annotationProvider->getPropertyAnnotation($property, 'foo');
			Assert::fail('Exception expected');
		} catch (\Consistence\Annotation\AnnotationNotFoundException $e) {
			Assert::assertSame($property, $e->getProperty());
			Assert::assertSame('foo', $e->getAnnotationName());
		}
	}

	public function testGetAnnotations(): void
	{
		$annotationProvider = $this->createAnnotationProvider();
		$property = new ReflectionProperty(Foo::class, 'multiple');

		$annotations = $annotationProvider->getPropertyAnnotations($property, 'get');
		Assert::assertCount(2, $annotations);
		Assert::assertInstanceOf(Annotation::class, $annotations[0]);
		Assert::assertSame('get', $annotations[0]->getName());
		Assert::assertNull($annotations[0]->getValue());
		Assert::assertCount(0, $annotations[0]->getFields());
		Assert::assertInstanceOf(Annotation::class, $annotations[1]);
		Assert::assertSame('get', $annotations[1]->getName());
		Assert::assertNull($annotations[1]->getValue());
		$fields = $annotations[1]->getFields();
		Assert::assertCount(1, $fields);
		Assert::assertInstanceOf(AnnotationField::class, $fields[0]);
		Assert::assertSame('name', $fields[0]->getName());
		Assert::assertSame('fooName', $fields[0]->getValue());
	}

	public function testGetAnnotationsNotFound(): void
	{
		$annotationProvider = $this->createAnnotationProvider();
		$property = new ReflectionProperty(Foo::class, 'noParams');

		Assert::assertCount(0, $annotationProvider->getPropertyAnnotations($property, 'foo'));
	}

	public function testUnstructuredAnnotation(): void
	{
		$annotationProvider = $this->createAnnotationProvider();
		$property = new ReflectionProperty(Foo::class, 'noParams');

		$annotation = $annotationProvider->getPropertyAnnotation($property, 'var');
		Assert::assertInstanceOf(Annotation::class, $annotation);
		Assert::assertSame('var', $annotation->getName());
		Assert::assertSame('string', $annotation->getValue());
		Assert::assertCount(0, $annotation->getFields());
	}

	private function createAnnotationProvider(): DoctrineSentryAnnotationProvider
	{
		return new DoctrineSentryAnnotationProvider(
			new AnnotationReader(),
			[
				Get::class => 'get',
			],
			new VarAnnotationProvider()
		);
	}

}
