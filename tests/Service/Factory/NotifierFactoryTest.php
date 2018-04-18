<?php

namespace App\Tests\Service\Factory;

use App\Service\Factory\NotifierFactory;
use App\Service\Notifier;
use PHPUnit\Framework\TestCase;

/**
 * Class NotifierFactoryTest
 * @package App\Tests\Service\Factory
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotifierFactoryTest extends TestCase
{
	/**
	 * @throws \ReflectionException
	 */
	public function testAdd()
	{
		$names = ['mail', 'sms', 'browser'];

		$notifierFactory = new NotifierFactory();

		$reflector = new \ReflectionClass($notifierFactory);

		foreach ($names as $name) {
			$createMethod = 'create' . $name . 'Notifier';

			$this->assertEquals($reflector->hasMethod($createMethod), true);

			$returnTypeReflector = new \ReflectionClass($reflector->getMethod($createMethod)->getReturnType()->getName());

			$this->assertEquals($returnTypeReflector->implementsInterface(Notifier::class), true);
		}
	}
}