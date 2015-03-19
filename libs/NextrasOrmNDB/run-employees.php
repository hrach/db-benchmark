<?php

use Model\DepartmentsMapper;
use Model\DepartmentsRepository;
use Model\EmployeesMapper;
use Model\EmployeesRepository;
use Model\SalariesMapper;
use Model\SalariesRepository;
use Nette\Caching\Storages\FileStorage;
use Nette\Database\Connection;
use Nette\Database\Context;
use Nette\Database\Conventions\DiscoveredConventions;
use Nette\Database\Structure;
use Nette\Framework;
use Nextras\Orm\Model\SimpleModelFactory;

require_once __DIR__ . '/../../bootstrap.php';

Bootstrap::init();
Bootstrap::check(__DIR__);

$cacheStorage = new FileStorage(__DIR__ . '/temp');

$connection = new Connection(
    Bootstrap::$config['db']['driver'] . ':dbname=' . Bootstrap::$config['db']['dbname'],
    Bootstrap::$config['db']['user'],
    Bootstrap::$config['db']['password']
);
$structure = new Structure($connection, $cacheStorage);
$conventions = new DiscoveredConventions($structure);
$context = new Context($connection, $structure, $conventions, Bootstrap::$config['cache'] ? $cacheStorage : NULL);

$modelFactory = new SimpleModelFactory(
    $cacheStorage,
    [
        'employees' => new EmployeesRepository(new EmployeesMapper($context)),
        'salarieys' => new SalariesRepository(new SalariesMapper($context)),
        'departments' => new DepartmentsRepository(new DepartmentsMapper($context)),
    ]
);

$startTime = -microtime(TRUE);
ob_start();

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

Bootstrap::result('Nextras\OrmNDB', 'Nextras\Orm: 1.0.0-beta*', $startTime, $endTime);
