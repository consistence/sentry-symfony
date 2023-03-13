<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Annotation;

use PHPUnit\Framework\Assert;
use ReflectionProperty;

class VarAnnotationProviderIntegrationTest extends \PHPUnit\Framework\TestCase
{

	public function testGetVarAnnotationValue(): void
	{
		$varAnnotationProvider = new VarAnnotationProvider();

		$annotation = $varAnnotationProvider->getPropertyAnnotation(new ReflectionProperty(
			Foo::class,
			'noParams'
		), 'var');

		Assert::assertSame('var', $annotation->getName());
		Assert::assertSame('string', $annotation->getValue());
		Assert::assertCount(0, $annotation->getFields());
	}

	public function testVarAnnotationDoesNotExist(): void
	{
		try {
			$varAnnotationProvider = new VarAnnotationProvider();

			$varAnnotationProvider->getPropertyAnnotation(new ReflectionProperty(
				Foo::class,
				'withoutVar'
			), 'var');

			Assert::fail('Exception expected');

		} catch (\Consistence\Annotation\AnnotationNotFoundException $e) {
			Assert::assertSame(Foo::class, $e->getProperty()->getDeclaringClass()->getName());
			Assert::assertSame('withoutVar', $e->getProperty()->getName());
			Assert::assertSame('var', $e->getAnnotationName());
		}
	}

}
