<?php

namespace SebWas\Laravel\Migration\PhysicalBackup;

/**
 * Handles the physical backup restoring of a migration using the `mysql` cli tool
 *
 * @package SebWas\Laravel\Migration\PhysicalBackup
 */
trait PhysicalBackupRestoring {
	abstract public function getOutputFileName(): string;

	/**
	 * Runs the restore process
	 */
	public function down(){
		$this->runRestore();
	}

	/**
	 * Runs the physical backup restoring
	 */
	protected function runRestore(){
		$this->restoreFrom(
			$this->getOutputFileName());
	}

	/**
	 * Runs the restore command via cli
	 *
	 * @param  string $fileName
	 */
	protected function restoreFrom(string $fileName){
		$command = $this->getRestoreCommand($this->getBaseRestoreCommand(), $fileName);

		exec($command, $output, $exitCode);

		if($exitCode !== 0){
			throw new \RuntimeException('Could not restore mysql because:' . PHP_EOL . $output);
		}
	}

	/**
	 * Gets the whole command to be run
	 *
	 * @param  string $baseCommand
	 * @param  string $fileName
	 * @return string
	 */
	protected function getRestoreCommand(string $baseCommand, string $fileName): string {
		return sprintf($baseCommand,
			$this->getMysqlQueryFromFileName($fileName));
	}

	/**
	 * Returns the mysql commands for importing the restore file
	 *
	 * @param  string $fileName
	 * @return string
	 */
	protected function getMysqlQueryFromFileName(string $fileName): string {
		return sprintf('SET NAMES \'utf8\';SOURCE %s;', $fileName);
	}

	/**
	 * Returns the base command name, excluding the table names
	 *
	 * @return string
	 */
	protected function getBaseRestoreCommand(): string {
		return sprintf('mysql -u %s -p%s --default-character-set=utf8 %s -e "%%s"',
			str_replace('%', '%%', config('database.connections.mysql.username')),
			str_replace('%', '%%', config('database.connections.mysql.password')),
			config('database.connections.mysql.database')
		);
	}
}
