<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\DependencyInjection;

class SentryIntegrationMode extends \Consistence\Enum\Enum
{

	const GENERATED = 'generated';
	const DISABLED = 'disabled';

}
