<?php

namespace App\Tests\Service\Factory;

use App\Service\Notifier\Factory\NotifierFactory;
use App\Service\Notifier\Notifier;
use PHPUnit\Framework\TestCase;

class NotifierFactoryTest extends TestCase
{
	/**
	 * @throws \ReflectionException
	 */
	public function testAdd()
	{
		$names = [
		    'mail',
            'sms',
            // 'browser'
        ];

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