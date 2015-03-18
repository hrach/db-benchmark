<?php


require __DIR__ . '/../print_benchmark_result.php';
if (@!include __DIR__ . '/vendor/autoload.php') {
    echo 'Install Nette using `composer install`';
    exit(1);
}

require_once __DIR__ . '/loader.php';

date_default_timezone_set('Europe/Prague');

$cacheStorage = new Nette\Caching\Storages\FileStorage(__DIR__ . '/temp');
$connection = new Nette\Database\Connection('mysql:dbname=employees', 'employees', 'employees');
$structure = new Nette\Database\Structure($connection, $cacheStorage);
$conventions = new Nette\Database\Conventions\DiscoveredConventions($structure);
$context = new Nette\Database\Context($connection, $structure, $conventions, $cacheStorage);

$time = -microtime(TRUE);
ob_start();

$employees = new Model\Repository\EmployeeRepository($context);

foreach ($employees->findAll()->limit(500) as $employee) {
	echo $employee->getFirstName(), ' ', $employee->getLastName(), ' (', $employee->getEmpNo(), ")\n";

	echo 'Salaries:', "\n";
	foreach ($employee->getSalaries() as $salary) {
		echo $salary->getSalary(), "\n";
	}

	echo 'Departments:', "\n";
	foreach ($employee->getDepartments() as $department) {
		echo $department->getName(), "\n";
	}
}

ob_end_clean();

print_benchmark_result('YetORM');
