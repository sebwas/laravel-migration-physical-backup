<?php

namespace SebWas\Laravel\Migration;

/**
 * Unites the creation and restoring of the physical backup
 *
 * @package SebWas\Laravel\Migration\PhysicalBackup
 */
trait PhysicalBackup {
	use PhysicalBackup\PhysicalBackupCreation;
	use PhysicalBackup\PhysicalBackupFileName;
	use PhysicalBackup\PhysicalBackupRestoring;
}
