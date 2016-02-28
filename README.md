# Laravel physical backup creation helper
Good morning, fellow developer! Have a great day and enjoy this neat little tool.

## Usage
In your migration simply use the `SebWas\Laravel\Migration\PhysicalBackup` trait like so (example file):
```
<?php

use Illuminate\Database\Schema\Blueprint;
use SebWas\Laravel\Migration\PhysicalBackup;
use Illuminate\Database\Migrations\Migration;

class TestMigration extends Migration
{
	use PhysicalBackup;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Do your stuff here
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	$this->runRestore();
		// Either delete this function or use the runRestore method from here
    }
}
```

## How it works
The trait simply sets a constructor that scans your file for table names and creates a backup of these using the `mysqldump` cli utility. It also overwrites your `Migration::down()` method for automatic recreation from the backup file it created.

## Limitations
Right now this tool relies on mysql usage and cli as well as filesystem access. Feel free to contribute to this project by providing other DBs or other ways of creating the backup and storing it. (E.g. create a table and use raw queries or whatever else comes to your mind.)
