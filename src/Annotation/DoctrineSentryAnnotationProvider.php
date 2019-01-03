<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Annotation;

use Consistence\Annotation\Annotation;
use Consistence\Annotation\AnnotationField;
use Consistence\Annotation\AnnotationProvider as AnnotationProviderInterface;
use Doctrine\Common\Annotations\Reader;
use ReflectionProperty;

class DoctrineSentryAnnotationProvider extends \Consistence\ObjectPrototype implements \Consistence\Annotation\AnnotationProvider
{

	/** @var \Doctrine\Common\Annotations\Reader */
	private $annotationReader;

	/** @var string[] format: annotation class (string) => sentry annotation name (string) */
	private $annotationMap;

	/** @var \Consistence\Annotation\AnnotationProvider */
	private $fallbackAnnotationProvider;

	/**
	 * @param \Doctrine\Common\Annotations\Reader $annotationReader
	 * @param string[] $annotationMap
	 * @param \Consistence\Annotation\AnnotationProvider $fallbackAnnotationProvider
	 */
	public function __construct(
		Reader $annotationReader,
		array $annotationMap,
		AnnotationProviderInterface $fallbackAnnotationProvider
	)
	{
		$this->annotationReader = $annotationReader;
		$this->annotationMap = $annotationMap;
		$this->fallbackAnnotationProvider = $fallbackAnnotationProvider;
	}

	public function getPropertyAnnotation(ReflectionProperty $property, string $annotationName): Annotation
	{
		try {
			$annotation = $this->findAnnotation($property, $annotationName);
			return $this->convertAnnotation($annotationName, $annotation);
		} catch (\Consistence\Annotation\AnnotationNotFoundException $e) {
			return $this->fallbackAnnotationProvider->getPropertyAnnotation($property, $annotationName);
		}
	}

	/**
	 * @param \ReflectionProperty $property
	 * @param string $annotationName
	 * @return \Consistence\Annotation\Annotation[]
	 */
	public function getPropertyAnnotations(ReflectionProperty $property, string $annotationName): array
	{
		$annotations = $this->annotationReader->getPropertyAnnotations($property);

		$convertedAnnotations = [];
		foreach ($annotations as $annotation) {
			$class = get_class($annotation);
			if (!isset($this->annotationMap[$class]) || $this->annotationMap[$class] !== $annotationName) {
				continue;
			}
			$convertedAnnotations[] = $this->convertAnnotation($annotationName, $annotation);
		}

		return array_merge(
			$convertedAnnotations,
			array_values($this->fallbackAnnotationProvider->getPropertyAnnotations($property, $annotationName))
		);
	}

	private function findAnnotation(ReflectionProperty $property, string $annotationName): object
	{
		foreach ($this->annotationMap as $class => $name) {
			if ($name !== $annotationName) {
				continue;
			}
			$annotation = $this->annotationReader->getPropertyAnnotation($property, $class);
			if ($annotation !== null) {
				return $annotation;
			}
		}

		throw new \Consistence\Annotation\AnnotationNotFoundException($annotationName, $property);
	}

	private function convertAnnotation(string $annotationName, object $annotation): Annotation
	{
		$fields = [];
		foreach ($annotation as $name => $value) {
			if ($value === null) {
				continue;
			}
			$fields[] = new AnnotationField($name, $value);
		}

		return Annotation::createAnnotationWithFields($annotationName, $fields);
	}

}
