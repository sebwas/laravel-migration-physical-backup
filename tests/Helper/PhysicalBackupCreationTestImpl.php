<?php

namespace SebWas\Testing\Helper;

use SebWas\Laravel\Migration\PhysicalBackup\PhysicalBackupCreation;

class PhysicalBackupCreationTestImpl {
	use PhysicalBackupCreation {
		runBackup as public;
		getBackupCommand as public;
		getBaseBackupCommand as public;
		getTableResolver as public;
	};
}
