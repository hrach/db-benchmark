<?php

use Nextras\Dbal\Connection;
use Nextras\Orm\Model\SimpleModelFactory;

require_once __DIR__ . '/../../bootstrap.php';

Bootstrap::init();
Bootstrap::check(__DIR__);

$connection = new Connection(
    Bootstrap::$config['db']['driver'] . ':dbname=' . Bootstrap::$config['db']['dbname'],
    Bootstrap::$config['db']['user'],
    Bootstrap::$config['db']['password']
);
$structure = new Structure($connection, $cacheStorage);
$conventions = new DiscoveredConventions($structure);
$context = new Context($connection, $structure, $conventions, Bootstrap::$config['cache'] ? $cacheStorage : NULL);


$startTime = -microtime(TRUE);
ob_start();

$modelFactory = new SimpleModelFactory(
    $cacheStorage,
    [
        'employees' => new Model\EmployeesRepository(new Model\EmployeesMapper($context)),
        'salarieys' => new Model\SalariesRepository(new Model\SalariesMapper($context)),
        'departments' => new Model\DepartmentsRepository(new Model\DepartmentsMapper($context)),
    ]
);
$model = $modelFactory->create();

$employees = $model->employees->findOverview(Bootstrap::$config['limit']);

foreach ($employees as $employee) {
    echo "$employee->firstName $employee->lastName ($employee->id)\n";
    echo "Salaries:\n";
    foreach ($employee->salaries as $salary) {
        echo "-", $salary->salary, "\n";
    }
    echo "Departments:\n";
    foreach ($employee->departments as $department) {
        echo "-", $department->name, "\n";
    }
}

ob_end_clean();
$endTime = microtime(TRUE);

Bootstrap::result('Nextras\OrmDbal', 'Nextras\Orm: dev-master', $startTime, $endTime);
