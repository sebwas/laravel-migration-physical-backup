<?php

namespace SebWas\Testing\Helper;

use Illuminate\Database\Migrations\Migration;

class TableResolverTestClass extends Migration {
	public function up(){
		Schema::create('table_should_not_appear_create');

		Schema::table('changing_table', function($table){
			// Changing stuff here
		});

		Schema::rename('renaming_table', 'new_name_table');

		Schema::drop('dropping_table');

		Schema::dropIfExists('dropping_table_if_exists');

		// Schema::drop('table_should_not_appear_single_line_comment');

		/*
				Schema::drop('table_should_not_appear_multi_line_comment');
		 */
	}
}
