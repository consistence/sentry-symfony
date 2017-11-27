<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ConsistenceSentryExtension extends \Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension
{

	const ALIAS = 'consistence_sentry';

	const CONTAINER_PARAMETER_ANNOTATION_METHOD_ANNOTATIONS_MAP = 'consistence_sentry.annotation.method_annotations_map';
	const CONTAINER_PARAMETER_GENERATED_CLASS_MAP_TARGET_FILE = 'consistence_sentry.generated.class_map_target_file';
	const CONTAINER_PARAMETER_GENERATED_TARGET_DIR = 'consistence_sentry.generated.target_dir';

	const CONTAINER_SERVICE_GENERATED_AUTOLOADER = 'consistence_sentry.consistence.sentry.generated.sentry_autoloader';

	const DEFAULT_GENERATED_CLASS_MAP_TARGET_FILE_NAME = '_classMap.php';

	/**
	 * @param mixed[] $mergedConfig
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 */
	public function loadInternal(array $mergedConfig, ContainerBuilder $container)
	{
		$yamlFileLoader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/config'));

		$generatedFilesDir = $mergedConfig[Configuration::SECTION_GENERATED][Configuration::PARAMETER_GENERATED_FILES_DIR];

		$this->ensureTargetDirectoryExists($generatedFilesDir);

		$container->setParameter(self::CONTAINER_PARAMETER_GENERATED_TARGET_DIR, $generatedFilesDir);
		$container->setParameter(
			self::CONTAINER_PARAMETER_GENERATED_CLASS_MAP_TARGET_FILE,
			$generatedFilesDir . '/' . self::DEFAULT_GENERATED_CLASS_MAP_TARGET_FILE_NAME
		);

		$container->setParameter(
			self::CONTAINER_PARAMETER_ANNOTATION_METHOD_ANNOTATIONS_MAP,
			$mergedConfig[Configuration::PARAMETER_METHOD_ANNOTATIONS_MAP]
		);

		$yamlFileLoader->load('services.yml');
	}

	private function ensureTargetDirectoryExists(string $generatedFilesDir)
	{
		if (!file_exists($generatedFilesDir)) {
			if (!@mkdir($generatedFilesDir, 0775, true)) {
				// @codeCoverageIgnoreStart
				// should not happen and is filesystem dependent
				throw new \Exception(sprintf('Could not create generated files directory "%s".', $generatedFilesDir));
				// @codeCoverageIgnoreEnd
			}
		}
	}

	/**
	 * @param mixed[] $config
	 * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
	 * @return \Consistence\Sentry\SymfonyBundle\DependencyInjection\Configuration
	 */
	public function getConfiguration(array $config, ContainerBuilder $container): Configuration
	{
		return new Configuration(
			$this->getAlias(),
			$container->getParameter('kernel.cache_dir')
		);
	}

	public function getAlias(): string
	{
		return self::ALIAS;
	}

}
