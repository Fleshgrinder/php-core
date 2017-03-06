<?php

declare(strict_types=1);

namespace Fleshgrinder\Core;

use PHPUnit\Framework\TestCase;

final class DisenchantTest extends TestCase {
	private $cut;
	private $cut_class;

	protected function setUp() {
		$this->cut = new class { use Disenchant; };
		$this->cut_class = \get_class($this->cut);
	}

	/**
	 * @testdox Magic __get method throws \Error.
	 * @covers \Fleshgrinder\Core\Disenchant::__get
	 */
	public function testGet() {
		$this->expectException(\Error::class);
		$this->expectExceptionMessage("Cannot get dynamic properties from immutable class {$this->cut_class}");

		$this->cut->prop;
	}

	/**
	 * @testdox Magic __isset method throws \Error.
	 * @covers \Fleshgrinder\Core\Disenchant::__isset
	 */
	public function testIsset() {
		$this->expectException(\Error::class);
		$this->expectExceptionMessage("Cannot check if dynamic properties are set on immutable class {$this->cut_class}");

		isset($this->cut->prop);
	}

	/**
	 * @testdox Magic __set method throws \Error.
	 * @covers \Fleshgrinder\Core\Disenchant::__set
	 */
	public function testSet() {
		$this->expectException(\Error::class);
		$this->expectExceptionMessage("Cannot set dynamic properties on immutable class {$this->cut_class}");

		$this->cut->prop = '';
	}

	/**
	 * @testdox Magic __unset method throws \Error.
	 * @covers \Fleshgrinder\Core\Disenchant::__unset
	 */
	public function testUnset() {
		$this->expectException(\Error::class);
		$this->expectExceptionMessage("Cannot remove dynamic properties from immutable class {$this->cut_class}");

		unset($this->cut->prop);
	}
}
