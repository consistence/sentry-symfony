<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Annotation;

use Consistence\Annotation\Annotation;
use Consistence\RegExp\RegExp;

use ReflectionProperty;

/**
 * This class is suited only for finding a particular var annotation on a property, not as a general AnnotationProvider
 */
class VarAnnotationProvider extends \Consistence\ObjectPrototype implements \Consistence\Annotation\AnnotationProvider
{

	public function getPropertyAnnotation(ReflectionProperty $property, string $annotationName): Annotation
	{
		if ($annotationName !== 'var') {
			throw new \Consistence\Annotation\AnnotationNotFoundException($annotationName, $property);
		}
		$docComment = $property->getDocComment();
		if (strpos($docComment, '@' . $annotationName) === false) {
			throw new \Consistence\Annotation\AnnotationNotFoundException($annotationName, $property);
		}

		$matches = RegExp::match($docComment, '~@var[ \t]+([^\s]+)~');
		if (count($matches) === 0) {
			throw new \Consistence\Annotation\AnnotationNotFoundException($annotationName, $property);
		}

		return Annotation::createAnnotationWithValue($annotationName, $matches[1]);
	}

	/**
	 * @param \ReflectionProperty $property
	 * @param string $annotationName
	 * @return \Consistence\Annotation\Annotation[]
	 */
	public function getPropertyAnnotations(ReflectionProperty $property, string $annotationName): array
	{
		return [];
	}

}
