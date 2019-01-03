<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Generated;

use Consistence\Sentry\Generated\SentryAutoloader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore does not contain any logic and depends on global state
 */
class GenerateSentryCommand extends \Symfony\Component\Console\Command\Command
{

	public const NAME = 'consistence:sentry:generate';

	/** @var \Consistence\Sentry\Generated\SentryAutoloader */
	private $sentryAutoloader;

	/** @var string */
	private $generatedTargetDir;

	public function __construct(SentryAutoloader $sentryAutoloader, string $generatedTargetDir)
	{
		parent::__construct();
		$this->sentryAutoloader = $sentryAutoloader;
		$this->generatedTargetDir = $generatedTargetDir;
	}

	protected function configure(): void
	{
		$this->setName(self::NAME);
		$description = 'Generate classes with Sentry methods';
		$this->setDescription($description);
		$this->setHelp($description);
	}

	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		if ($this->sentryAutoloader->isClassMapReady()) {
			throw new \Exception('<error>Class map already exists, remove it manually or call bin/console cache:clear</error>');
		}
		if (!is_dir($this->generatedTargetDir) || !is_writable($this->generatedTargetDir)) {
			throw new \Exception(sprintf('<error>Directory %s must exist and be writable</error>', $this->generatedTargetDir));
		}
		$this->sentryAutoloader->rebuild();
	}

}
