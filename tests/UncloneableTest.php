<?php

declare(strict_types=1);

namespace Fleshgrinder\Core;

use PHPUnit\Framework\TestCase;

final class UncloneableTest extends TestCase {
	/**
	 * @testdox Magic __clone method is final.
	 * @covers \Fleshgrinder\Core\Uncloneable::__clone
	 */
	public static function testCloneFinalization() {
		static::assertTrue((new \ReflectionMethod(Uncloneable::class, '__clone'))->isFinal());
	}

	/**
	 * @testdox Magic __clone method has protected visibility.
	 * @covers \Fleshgrinder\Core\Uncloneable::__clone
	 */
	public static function testCloneVisibility() {
		static::assertTrue((new \ReflectionMethod(Uncloneable::class, '__clone'))->isProtected());
	}
}
