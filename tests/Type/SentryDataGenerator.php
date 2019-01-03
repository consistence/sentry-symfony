<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Type;

use Consistence\ClassFinder\RobotLoader\LazyRobotLoaderClassFinder;
use Consistence\Sentry\Factory\Simple\SimpleSentryFactory;
use Consistence\Sentry\Generated\SentryGenerator;
use Consistence\Sentry\MetadataSource\Annotation\AnnotationMetadataSource;
use Consistence\Sentry\SentryIdentificatorParser\SentryIdentificatorParser;
use Consistence\Sentry\SymfonyBundle\Annotation\Add;
use Consistence\Sentry\SymfonyBundle\Annotation\Contains;
use Consistence\Sentry\SymfonyBundle\Annotation\DoctrineSentryAnnotationProvider;
use Consistence\Sentry\SymfonyBundle\Annotation\Get;
use Consistence\Sentry\SymfonyBundle\Annotation\Remove;
use Consistence\Sentry\SymfonyBundle\Annotation\Set;
use Consistence\Sentry\SymfonyBundle\Annotation\VarAnnotationProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Nette\Loaders\RobotLoader;

class SentryDataGenerator extends \Consistence\ObjectPrototype
{

	private const SUFFIX = 'Generated';

	private const GENERATED_FILENAME_NS_PART = 'Consistence_Sentry_SymfonyBundle_Type_';

	/** @var string */
	private $sourceDirectory;

	/** @var string */
	private $targetDirectory;

	public function __construct()
	{
		$this->sourceDirectory = __DIR__ . '/data';
		$this->targetDirectory = __DIR__ . '/data-generated';
	}

	public function generate(string $entity): void
	{
		$filePath = $this->targetDirectory . '/' . static::GENERATED_FILENAME_NS_PART . $entity . '.php';
		if (!file_exists($filePath)) {
			$generated = $this->getGenerator()->generateAll();
			foreach ($generated as $class => $file) {
				$entityName = substr($class, strrpos($class, '\\') + 1);
				$this->modifyGeneratedFile($entityName, $file);
			}
		}

		require_once $filePath;
	}

	private function getGenerator(): SentryGenerator
	{
		$robotLoader = new RobotLoader();
		$robotLoader->addDirectory($this->sourceDirectory);
		$robotLoader->rebuild();

		$classFinder = new LazyRobotLoaderClassFinder($robotLoader);

		$annotationProvider = new DoctrineSentryAnnotationProvider(
			new AnnotationReader(),
			[
				Add::class => 'add',
				Contains::class => 'contains',
				Get::class => 'get',
				Remove::class => 'remove',
				Set::class => 'set',
			],
			new VarAnnotationProvider()
		);

		$sentryIdentificatorParser = new SentryIdentificatorParser();

		$sentryFactory = new SimpleSentryFactory($sentryIdentificatorParser);

		$metadataSource = new AnnotationMetadataSource(
			$sentryFactory,
			$sentryIdentificatorParser,
			$annotationProvider
		);

		return new SentryGenerator(
			$classFinder,
			$metadataSource,
			$sentryFactory,
			$this->targetDirectory
		);
	}

	private function modifyGeneratedFile(string $entity, string $file): void
	{
		$classContent = file_get_contents($file);
		$classContent = str_replace('class ' . $entity, 'class ' . $entity . self::SUFFIX, $classContent);
		$classContent = preg_replace('~extends [a-zA-Z_\x7f-\xff\\\\]+~', 'extends ' . $entity, $classContent);
		file_put_contents($file, $classContent);
	}

}
