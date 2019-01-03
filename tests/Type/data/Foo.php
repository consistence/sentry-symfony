<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SymfonyBundle\Type;

use Consistence\Sentry\SymfonyBundle\Annotation as Sentry;
use DateTimeImmutable;

class Foo extends \Consistence\Sentry\SentryObject
{

	/**
	 * @Sentry\Get()
	 * @Sentry\Set()
	 * @var string
	 */
	private $name;

	/**
	 * @Sentry\Get()
	 * @Sentry\Set()
	 * @var string|null
	 */
	private $description;

	/**
	 * @Sentry\Get(name="getMy")
	 * @Sentry\Set(name="setMy")
	 * @var string
	 */
	private $myName;

	/**
	 * See self::setPublic which calls setPrivate
	 *
	 * @Sentry\Get()
	 * @Sentry\Set(visibility="private")
	 * @var string
	 */
	private $private;

	/**
	 * @Sentry\Get()
	 * @Sentry\Set()
	 * @var \DateTimeImmutable
	 */
	private $createdDate;

	/**
	 * @Sentry\Get()
	 * @Sentry\Set()
	 * @var \DateTimeImmutable|null
	 */
	private $publishDate;

	/**
	 * @Sentry\Get()
	 * @Sentry\Set()
	 * @var \DateTimeInterface|null
	 */
	private $dateTimeInterface;

	/**
	 * @Sentry\Get()
	 * @Sentry\Set()
	 * @Sentry\Add()
	 * @Sentry\Remove()
	 * @Sentry\Contains()
	 * @var string[]
	 */
	private $authors;

	/**
	 * @Sentry\Get()
	 * @Sentry\Set()
	 * @Sentry\Add()
	 * @Sentry\Remove()
	 * @Sentry\Contains()
	 * @var \DateTimeInterface[]
	 */
	private $eventDates;

	public function __construct()
	{
		$this->name = 'testName';
		$this->myName = 'testMyName';
		$this->createdDate = new DateTimeImmutable();
		$this->authors = [];
		$this->eventDates = [];
	}

	public function setPublic(string $string): void
	{
		$this->setPrivate($string);
	}

}
