<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle;

use Consistence\Sentry\SymfonyBundle\DependencyInjection\ConsistenceSentryExtension;
use Consistence\Sentry\SymfonyBundle\DependencyInjection\SentryIntegrationMode;

class ConsistenceSentryBundle extends \Symfony\Component\HttpKernel\Bundle\Bundle
{

	/**
	 * @codeCoverageIgnore changes global state
	 */
	public function boot()
	{
		switch ($this->container->getParameter(ConsistenceSentryExtension::CONTAINER_PARAMETER_MODE)) {
			case SentryIntegrationMode::GENERATED:
				$sentryAutoloader = $this->container->get(ConsistenceSentryExtension::CONTAINER_SERVICE_GENERATED_AUTOLOADER);
				if ($sentryAutoloader->isClassMapReady()) {
					$sentryAutoloader->register();
				}
				break;
			case SentryIntegrationMode::DISABLED:
				return;
			default:
				// @codeCoverageIgnoreStart
				// should be unreachable
				throw new \Exception('Unexpected mode');
				// @codeCoverageIgnoreEnd
		}
	}

}
