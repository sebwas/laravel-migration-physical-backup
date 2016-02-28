<?php

namespace SebWas\Testing\Helper;

use SebWas\Laravel\Migration\PhysicalBackup\PhysicalBackupFileName;

class PhysicalBackupFileNameImpl {
	use PhysicalBackupFileName {
		getOutputFileName as public;
		createBackupFolder as public;
	};
}
