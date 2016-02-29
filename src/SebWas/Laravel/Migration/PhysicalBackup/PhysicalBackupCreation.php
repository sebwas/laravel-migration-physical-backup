<?php

namespace SebWas\Laravel\Migration\PhysicalBackup;

/**
 * Handles the physical backup creation of a migration using the `mysqldump` cli tool
 *
 * @package SebWas\Laravel\Migration\PhysicalBackup
 */
trait PhysicalBackupCreation {
	abstract public function getOutputFileName(): string;

	/**
	 * The constructor gets everything going
	 */
	public function __construct(){
		if($this->isValidUseCase()){
			$this->runBackupCreation();
		}
	}

	/**
	 * Runs the physical backup creation
	 */
	protected function runBackupCreation(){
		$tableResolver = $this->getTableResolver();

		$tableNames = $this->checkTables(
						$tableResolver->resolve());

		$this->runBackup($tableNames);
	}

	/**
	 * Returns only tables that are actually existing
	 *
	 * @param  array  $tableNames
	 * @return array
	 */
	protected function checkTables(array $tableNames): array {
		$tables = \DB::select("SHOW TABLES");

		return array_intersect(
					array_column($tables, 0), $tableNames);
	}

	/**
	 * Tells if it is a valid use case for the creation
	 *
	 * @return boolean
	 */
	protected function isValidUseCase(){
		global $argv;

		switch(strtolower($argv[1])){
			case 'migrate':
				return true;
			default:
				return false;
		}
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
			throw new \RuntimeException('Could not dump mysql');
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
