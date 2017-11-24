<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Annotation;

use ReflectionProperty;

class VarAnnotationProviderIntegrationTest extends \PHPUnit\Framework\TestCase
{

	public function testGetVarAnnotationValue()
	{
		$varAnnotationProvider = new VarAnnotationProvider();

		$annotation = $varAnnotationProvider->getPropertyAnnotation(new ReflectionProperty(
			Foo::class,
			'noParams'
		), 'var');

		$this->assertSame('var', $annotation->getName());
		$this->assertSame('string', $annotation->getValue());
		$this->assertEmpty($annotation->getFields());
	}

	public function testVarAnnotationDoesNotExist()
	{
		try {
			$varAnnotationProvider = new VarAnnotationProvider();

			$varAnnotationProvider->getPropertyAnnotation(new ReflectionProperty(
				Foo::class,
				'withFields'
			), 'var');

			$this->fail();

		} catch (\Consistence\Annotation\AnnotationNotFoundException $e) {
			$this->assertSame(Foo::class, $e->getProperty()->getDeclaringClass()->getName());
			$this->assertSame('withFields', $e->getProperty()->getName());
			$this->assertSame('var', $e->getAnnotationName());
		}
	}

}
