<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Annotation;

use Consistence\Annotation\Annotation;
use Consistence\Annotation\AnnotationField;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionProperty;

class SymfonySentryAnnotationProviderIntegrationTest extends \PHPUnit\Framework\TestCase
{

	public function testGetAnnotationWithNoParams(): void
	{
		$annotationProvider = $this->createAnnotationProvider();
		$property = new ReflectionProperty(Foo::class, 'noParams');

		$annotation = $annotationProvider->getPropertyAnnotation($property, 'get');
		$this->assertInstanceOf(Annotation::class, $annotation);
		$this->assertSame('get', $annotation->getName());
		$this->assertNull($annotation->getValue());
		$this->assertEmpty($annotation->getFields());
	}

	public function testGetAnnotationWithFields(): void
	{
		$annotationProvider = $this->createAnnotationProvider();
		$property = new ReflectionProperty(Foo::class, 'withFields');

		$annotation = $annotationProvider->getPropertyAnnotation($property, 'get');
		$this->assertInstanceOf(Annotation::class, $annotation);
		$this->assertSame('get', $annotation->getName());
		$this->assertNull($annotation->getValue());
		$fields = $annotation->getFields();
		$this->assertCount(2, $fields);
		$this->assertInstanceOf(AnnotationField::class, $fields[0]);
		$this->assertSame('name', $fields[0]->getName());
		$this->assertSame('fooName', $fields[0]->getValue());
		$this->assertInstanceOf(AnnotationField::class, $fields[1]);
		$this->assertSame('visibility', $fields[1]->getName());
		$this->assertSame('private', $fields[1]->getValue());
	}

	public function testGetAnnotationNotFound(): void
	{
		$annotationProvider = $this->createAnnotationProvider();
		$property = new ReflectionProperty(Foo::class, 'noParams');

		$this->expectException(\Consistence\Annotation\AnnotationNotFoundException::class);

		$annotationProvider->getPropertyAnnotation($property, 'foo');
	}

	public function testGetAnnotations(): void
	{
		$annotationProvider = $this->createAnnotationProvider();
		$property = new ReflectionProperty(Foo::class, 'multiple');

		$annotations = $annotationProvider->getPropertyAnnotations($property, 'get');
		$this->assertCount(2, $annotations);
		$this->assertInstanceOf(Annotation::class, $annotations[0]);
		$this->assertSame('get', $annotations[0]->getName());
		$this->assertNull($annotations[0]->getValue());
		$this->assertEmpty($annotations[0]->getFields());
		$this->assertInstanceOf(Annotation::class, $annotations[1]);
		$this->assertSame('get', $annotations[1]->getName());
		$this->assertNull($annotations[1]->getValue());
		$fields = $annotations[1]->getFields();
		$this->assertCount(1, $fields);
		$this->assertInstanceOf(AnnotationField::class, $fields[0]);
		$this->assertSame('name', $fields[0]->getName());
		$this->assertSame('fooName', $fields[0]->getValue());
	}

	public function testGetAnnotationsNotFound(): void
	{
		$annotationProvider = $this->createAnnotationProvider();
		$property = new ReflectionProperty(Foo::class, 'noParams');

		$this->assertEmpty($annotationProvider->getPropertyAnnotations($property, 'foo'));
	}

	public function testUnstructuredAnnotation(): void
	{
		$annotationProvider = $this->createAnnotationProvider();
		$property = new ReflectionProperty(Foo::class, 'noParams');

		$annotation = $annotationProvider->getPropertyAnnotation($property, 'var');
		$this->assertInstanceOf(Annotation::class, $annotation);
		$this->assertSame('var', $annotation->getName());
		$this->assertSame('string', $annotation->getValue());
		$this->assertEmpty($annotation->getFields());
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
