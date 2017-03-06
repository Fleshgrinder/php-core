<?php

declare(strict_types=1);

namespace Fleshgrinder\Core;

use PHPUnit\Framework\TestCase;

final class UnconstructableTest extends TestCase {
	/**
	 * @testdox Constructor is final.
	 * @covers \Fleshgrinder\Core\Unconstructable::__construct
	 */
	public static function testConstructFinalization() {
		static::assertTrue((new \ReflectionMethod(Unconstructable::class, '__construct'))->isFinal());
	}

	/**
	 * @testdox Constructor has protected visibility.
	 * @covers \Fleshgrinder\Core\Unconstructable::__construct
	 */
	public function testConstructVisibility() {
		static::assertTrue((new \ReflectionMethod(Unconstructable::class, '__construct'))->isProtected());
	}
}
