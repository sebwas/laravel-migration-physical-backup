<?php

namespace SebWas\Laravel\Migration\PhysicalBackup;

/**
 * Covers the naming of the files used for backup and restoring
 *
 * @package SebWas\Laravel\Migration\PhysicalBackup
 */
trait PhysicalBackupFileName {
	/**
	 * Returns the output file name
	 *
	 * @return string
	 */
	protected function getOutputFileName(): string {
		$class  = get_called_class();
		$folder = $this->createBackupFolder();

		return "$folder$class-backup.sql";
	}

	/**
	 * Attempts to create a backup folder
	 *
	 * @return string
	 */
	protected function createBackupFolder(): string {
		$directory = storage_path('migration-backup');

		if(!is_writable($directory) || !@mkdir($directory)){
			$directory = getcwd();
		}

		return $directory . DIRECTORY_SEPARATOR;
	}
}
