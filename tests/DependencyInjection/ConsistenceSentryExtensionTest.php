<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\DependencyInjection;

use Consistence\Sentry\SymfonyBundle\Annotation\Add;
use Consistence\Sentry\SymfonyBundle\Annotation\Contains;
use Consistence\Sentry\SymfonyBundle\Annotation\Get;
use Consistence\Sentry\SymfonyBundle\Annotation\Remove;
use Consistence\Sentry\SymfonyBundle\Annotation\Set;
use Generator;
use PHPUnit\Framework\Assert;

class ConsistenceSentryExtensionTest extends \Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase
{

	public function setUp(): void
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

	/**
	 * @return mixed[][]|\Generator
	 */
	public function configureContainerParameterDataProvider(): Generator
	{
		yield 'default generated.target_dir' => [
			'configuration' => [],
			'parameterName' => ConsistenceSentryExtension::CONTAINER_PARAMETER_GENERATED_TARGET_DIR,
			'expectedParameterValue' => $this->getCacheDir() . '/sentry',
		];

		yield 'default generated.class_map_target_file' => [
			'configuration' => [],
			'parameterName' => ConsistenceSentryExtension::CONTAINER_PARAMETER_GENERATED_CLASS_MAP_TARGET_FILE,
			'expectedParameterValue' => $this->getCacheDir() . '/sentry/_classMap.php',
		];

		yield 'default annotation.method_annotations_map' => [
			'configuration' => [],
			'parameterName' => ConsistenceSentryExtension::CONTAINER_PARAMETER_ANNOTATION_METHOD_ANNOTATIONS_MAP,
			'expectedParameterValue' => [
				Add::class => 'add',
				Contains::class => 'contains',
				Get::class => 'get',
				Remove::class => 'remove',
				Set::class => 'set',
			],
		];

		yield 'configure generated.target_dir' => [
			'configuration' => [
				'generated_files_dir' => __DIR__,
			],
			'parameterName' => ConsistenceSentryExtension::CONTAINER_PARAMETER_GENERATED_TARGET_DIR,
			'expectedParameterValue' => realpath(__DIR__),
		];

		yield 'configure annotation.method_annotations_map' => (static function (): array {
			$methodAnnotationsMap = [
				Get::class => 'get',
				Set::class => 'set',
			];

			return [
				'configuration' => [
					'method_annotations_map' => $methodAnnotationsMap,
				],
				'parameterName' => ConsistenceSentryExtension::CONTAINER_PARAMETER_ANNOTATION_METHOD_ANNOTATIONS_MAP,
				'expectedParameterValue' => $methodAnnotationsMap,
			];
		})();
	}

	/**
	 * @dataProvider configureContainerParameterDataProvider
	 *
	 * @param mixed[][] $configuration
	 * @param string $parameterName
	 * @param mixed $expectedParameterValue
	 */
	public function testConfigureContainerParameter(
		array $configuration,
		string $parameterName,
		$expectedParameterValue
	): void
	{
		$this->load($configuration);

		$this->assertContainerBuilderHasParameter(
			$parameterName,
			$expectedParameterValue
		);

		$this->compile();
	}

	public function testConfigureGeneratedFilesDirNonExistingDirectoryCreatesDir(): void
	{
		$dir = $this->getTempDir() . '/testConfigureGeneratedFilesDirNonExistingDirectoryCreatesDir';
		@rmdir($dir);
		Assert::assertFileNotExists($dir);

		$this->load([
			'generated_files_dir' => $dir,
		]);

		$this->assertContainerBuilderHasParameter(
			ConsistenceSentryExtension::CONTAINER_PARAMETER_GENERATED_TARGET_DIR,
			realpath($dir)
		);
		$this->assertContainerBuilderHasParameter(
			ConsistenceSentryExtension::CONTAINER_PARAMETER_GENERATED_CLASS_MAP_TARGET_FILE,
			realpath($dir) . '/_classMap.php'
		);
		Assert::assertFileExists($dir);

		$this->compile();
	}

}
