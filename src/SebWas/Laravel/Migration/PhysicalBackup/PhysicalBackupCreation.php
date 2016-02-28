<?php

namespace SebWas\Laravel\Migration\PhysicalBackup;

/**
 * Handles the physical backup creation of a migration using the `mysqldump` cli tool
 *
 * @package SebWas\Laravel\Migration\PhysicalBackup
 */
trait PhysicalBackupCreation {
	use PhysicalBackupFileName;

	/**
	 * The constructor gets everything going
	 */
	public function __construct(){
		$this->run();
	}

	/**
	 * Runs the physical backup creation
	 */
	public function run(){
		$tableResolver = $this->getTableResolver();

		$this->runBackup(
			$tableResolver->resolve());
	}

	/**
	 * Runs the actual backup backing up the given columns
	 *
	 * @param  array  $tableNames
	 */
	protected function runBackup(array $tableNames){
		$command = $this->getBackupCommand($this->getBaseBackupCommand(), $tableNames);

		exec($command, $output, $exitCode);

		if($exitCode !== 0){
			throw new \RuntimeException('Could not dump mysql because:' . PHP_EOL . $output);
		}
	}

	/**
	 * Gets the whole command to be run
	 *
	 * @param  string $baseCommand
	 * @param  array  $tableNames
	 * @return string
	 */
	protected function getBackupCommand(string $baseCommand, array $tableNames): string {
		return sprintf($baseCommand,
			implode(' ', $tableNames));
	}

	/**
	 * Returns the base command name, excluding the table names
	 *
	 * @return string
	 */
	protected function getBaseBackupCommand(): string {
		return sprintf('mysqldump -u %s -p%s %s %%s -r %s',
			str_replace('%', '%%', config('database.connections.mysql.username')),
			str_replace('%', '%%', config('database.connections.mysql.password')),
			config('database.connections.mysql.database'),
			$this->getOutputFileName()
		);
	}

	/**
	 * Gets the TableResolver used for getting table names to be backed up
	 *
	 * @return TableResolver
	 */
	protected function getTableResolver(): TableResolver {
		$class = get_called_class();

		return new TableResolver($class);
	}
}
