<?php

namespace Sebwas\Testing;

use SebWas\Laravel\Migration\PhysicalBackup\TableResolver;
use SebWas\Testing\Helper\TableResolverFailingTestClass;
use SebWas\Testing\Helper\TableResolverTestClass;

class TableResolverTest extends \PHPUnit_Framework_TestCase {
	/** @test */
	function it_returns_an_array_when_resolving(){
		$tableNames = (new TableResolver(TableResolverTestClass::class))->resolve();

		$this->assertInternalType('array', $tableNames);
	}

	/** @test */
	function it_returns_the_correct_array(){
		$tableNames = (new TableResolver(TableResolverTestClass::class))->resolve();

		$this->assertEquals([
			'changing_table',
			'renaming_table',
			'dropping_table',
			'dropping_table_if_exists',
		], $tableNames);
	}

	/**
	 * @test
	 * @expectedException \RuntimeException
	 */
	function it_fails_if_there_is_no_up_function(){
		(new TableResolver(TableResolverFailingTestClass::class))->resolve();
	}
}
