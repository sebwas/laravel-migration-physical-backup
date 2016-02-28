<?php

namespace SebWas\Laravel\Migration\PhysicalBackup;

class TableResolver {
	/**
	 * The subject that is being inspected
	 *
	 * @var string
	 */
	private $subject = '';

	/**
	 * Holds the tables names found in the subject
	 *
	 * @var array
	 */
	private $tableNames = [];

	/**
	 * Default regexes for stripping comments out
	 *
	 * @var array
	 */
	private $defaultComments = [
		'/\/\*.*?\*\//s',
		'/\/\/.*/'
	];

	private $defaultSurroundings = [
		'prefix' => '#Schema\s*::\s*',
		'suffix' => '\s*\(\s*(?:\'|")(?P<tablename>[\x01-\xff]+?)(?:\'|")#'
	];

	/**
	 * Sets the subject
	 *
	 * @param string $className
	 */
	public function __construct(string $className){
		$this->subject = $className;
	}

	/**
	 * Runs the resolver and returns a list with the table names
	 *
	 * @return array
	 */
	public function resolve(): array {
		$this->run();

		return $this->tableNames;
	}

	/**
	 * Runs the table resolver
	 */
	protected function run(){
		$method = $this->getReflectedMethod();

		$this->extractTableNames($this->getMethodCode($method));
	}

	/**
	 * Returns a list of distinct table names to be backed up
	 *
	 * @param  string $methodCode
	 */
	protected function extractTableNames(string $methodCode){
		$patterns = $this->getTableNamePatterns();

		preg_match_all($patterns, $methodCode, $tableNames);

		$this->tableNames = array_unique($tableNames['tablename']);
	}

	/**
	 * Returns all table name patterns that will be used to get tables to be backed up
	 *
	 * @return array
	 */
	protected function getTableNamePatterns(): string {
		$methods = $this->getAllowedMethods();

		return $this->createTableNamePatterns($methods);
	}

	/**
	 * Returns an array of ready-to-use table name patterns from the given methods array
	 *
	 * @param  array  $methods
	 * @return array
	 */
	protected function createTableNamePatterns(array $methods): string {
		$methods = implode('|', $methods);

		return $this->createTableNamePattern('(?:'.$methods.')');
	}

	/**
	 * Creates a pattern to for getting the table name out of a call to a \Schema function
	 *
	 * @param  string $method
	 * @param  array  $surroundings
	 * @return string
	 */
	protected function createTableNamePattern(string $method, array $surroundings = []): string {
		if(empty($surroundings)){
			$surroundings = $this->defaultSurroundings;
		}

		return $surroundings['prefix'] . $method . $surroundings['suffix'];
	}

	/**
	 * Returns the allowed methods that will be used to find table names
	 *
	 * @return array
	 */
	protected function getAllowedMethods(): array {
		return ['table', 'drop', 'dropIfExists', 'rename'];
	}

	/**
	 * Gets the specified method's code without any comments
	 *
	 * @param  \ReflectionMethod $method
	 * @return string
	 */
	protected function getMethodCode(\ReflectionMethod $method): string {
		$content = $this->getFileContentFromMethod($method);

		return $this->stripCommentsFromCode(
					$this->extractMethodCode($content, $method));
	}

	/**
	 * Returns the given code without any comments in it
	 *
	 * @param  string $code
	 * @param  array  $comments
	 * @return string
	 */
	protected function stripCommentsFromCode(string $code, array $comments = []): string {
		if(empty($comments)){
			$comments = $this->defaultComments;
		}

		return preg_replace($comments, '', $code);
	}

	/**
	 * Extracts the method's code from the given file in array representation
	 *
	 * @param  array             $file
	 * @param  \ReflectionMethod $method
	 * @return string
	 */
	protected function extractMethodCode(array $file, \ReflectionMethod $method): string {
		$length = $method->getEndLine() - $method->getStartLine();

		return implode('',
					array_slice($file, $method->getStartLine() - 1, $length));
	}

	/**
	 * Returns the method's file's content as a per line array representation
	 *
	 * @param  \ReflectionMethod $method
	 * @return array
	 */
	protected function getFileContentFromMethod(\ReflectionMethod $method): array {
		return file($method->getFileName());
	}

	/**
	 * Gets the reflection of the up method of the specified subject
	 *
	 * @return ReflectionMethod
	 */
	protected function getReflectedMethod(): \ReflectionMethod {
		$class = new \ReflectionClass($this->subject);

		try {
			return $class->getMethod('up');
		} catch (\ReflectionException $e){
			throw new \RuntimeException("Can't find method 'up'. Not running the table resolver from a migration context?");
		}
	}
}
