<?php

namespace SebWas\Testing\Helper;

use SebWas\Laravel\Migration\PhysicalBackup\PhysicalBackupRestoring;

class PhysicalBackupRestoringImpl {
	use PhysicalBackupRestoring {
		restoreFrom as public;
		getRestoreCommand as public;
		getMysqlQueryFromFileName as public;
		getBaseRestoreCommand as public;
	};
}
