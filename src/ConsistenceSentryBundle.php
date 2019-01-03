<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle;

use Consistence\Sentry\SymfonyBundle\DependencyInjection\ConsistenceSentryExtension;

class ConsistenceSentryBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle
{

	/**
	 * @codeCoverageIgnore changes global state
	 */
	public function boot(): void
	{
		$sentryAutoloader = $this->container->get(ConsistenceSentryExtension::CONTAINER_SERVICE_GENERATED_AUTOLOADER);
		if ($sentryAutoloader->isClassMapReady()) {
			$sentryAutoloader->register();
		}
	}

}
