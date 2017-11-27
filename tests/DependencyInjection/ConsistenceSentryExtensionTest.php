<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\DependencyInjection;

use Consistence\Sentry\SymfonyBundle\Annotation\Add;
use Consistence\Sentry\SymfonyBundle\Annotation\Contains;
use Consistence\Sentry\SymfonyBundle\Annotation\Get;
use Consistence\Sentry\SymfonyBundle\Annotation\Remove;
use Consistence\Sentry\SymfonyBundle\Annotation\Set;

class ConsistenceSentryExtensionTest extends \Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase
{

	public function setUp()
	{
		parent::setUp();
		$this->setParameter('kernel.root_dir', $this->getRootDir());
		$this->setParameter('kernel.cache_dir', $this->getCacheDir());
	}

	private function getTestsDir(): string
	{
		return realpath(__DIR__ . '/..');
	}

	private function getTempDir(): string
	{
		return $this->getTestsDir() . '/temp';
	}

	private function getRootDir(): string
	{
		return $this->getTestsDir();
	}

	private function getCacheDir(): string
	{
		return $this->getTempDir();
	}

	/**
	 * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface[]
	 */
	protected function getContainerExtensions(): array
	{
		return [
			new ConsistenceSentryExtension(),
		];
	}

	public function testDisabledByDefault()
	{
		$this->load();

		$this->assertContainerBuilderHasParameter(
			ConsistenceSentryExtension::CONTAINER_PARAMETER_MODE,
			'disabled'
		);

		$this->compile();
	}

	/**
	 * @return mixed[][]
	 */
	public function defaultGeneratedConfigurationValuesProvider(): array
	{
		return [
			[
				ConsistenceSentryExtension::CONTAINER_PARAMETER_MODE,
				'generated',
			],
			[
				ConsistenceSentryExtension::CONTAINER_PARAMETER_GENERATED_TARGET_DIR,
				$this->getCacheDir() . '/sentry',
			],
			[
				ConsistenceSentryExtension::CONTAINER_PARAMETER_GENERATED_CLASS_MAP_TARGET_FILE,
				$this->getCacheDir() . '/sentry/_classMap.php',
			],
			[
				ConsistenceSentryExtension::CONTAINER_PARAMETER_ANNOTATION_METHOD_ANNOTATIONS_MAP,
				[
					Add::class => 'add',
					Contains::class => 'contains',
					Get::class => 'get',
					Remove::class => 'remove',
					Set::class => 'set',
				],
			],
		];
	}

	/**
	 * @dataProvider defaultGeneratedConfigurationValuesProvider
	 *
	 * @param string $parameterName
	 * @param mixed $parameterValue
	 */
	public function testDefaultGeneratedConfigurationValues(string $parameterName, $parameterValue)
	{
		$this->load([
			'mode' => 'generated',
		]);

		$this->assertContainerBuilderHasParameter($parameterName, $parameterValue);

		$this->compile();
	}

	public function testConfigureGeneratedFilesDir()
	{
		$this->load([
			'mode' => 'generated',
			'generated' => [
				'generated_files_dir' => __DIR__,
			],
		]);

		$this->assertContainerBuilderHasParameter(
			ConsistenceSentryExtension::CONTAINER_PARAMETER_GENERATED_TARGET_DIR,
			realpath(__DIR__)
		);

		$this->compile();
	}

	public function testConfigureGeneratedFilesDirNonExistingDirectoryCreatesDir()
	{
		$dir = $this->getTempDir() . '/testConfigureGeneratedFilesDirNonExistingDirectoryCreatesDir';
		@rmdir($dir);
		$this->assertFileNotExists($dir);

		$this->load([
			'mode' => 'generated',
			'generated' => [
				'generated_files_dir' => $dir,
			],
		]);

		$this->assertContainerBuilderHasParameter(
			ConsistenceSentryExtension::CONTAINER_PARAMETER_GENERATED_TARGET_DIR,
			realpath($dir)
		);
		$this->assertContainerBuilderHasParameter(
			ConsistenceSentryExtension::CONTAINER_PARAMETER_GENERATED_CLASS_MAP_TARGET_FILE,
			realpath($dir) . '/_classMap.php'
		);
		$this->assertFileExists($dir);

		$this->compile();
	}

	public function testConfigureMethodAnnotationMap()
	{
		$methodAnnotationsMap = [
			Get::class => 'get',
			Set::class => 'set',
		];
		$this->load([
			'mode' => 'generated',
			'method_annotations_map' => $methodAnnotationsMap,
		]);

		$this->assertContainerBuilderHasParameter(
			ConsistenceSentryExtension::CONTAINER_PARAMETER_ANNOTATION_METHOD_ANNOTATIONS_MAP,
			$methodAnnotationsMap
		);

		$this->compile();
	}

}
