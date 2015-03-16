<?php

/**
 * @see https://github.com/Majkl578/employees-doctrine2
 */

use Model\Entities\Employee;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Version;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Tools\Setup;

require_once __DIR__ . '/../../bootstrap.php';

Bootstrap::init();
Bootstrap::check(__DIR__);

$cache = new FilesystemCache(__DIR__ . '/temp');
$config = Setup::createAnnotationMetadataConfiguration(
    [__DIR__ . '/model/entities'],
    TRUE,
    __DIR__ . '/model/entities/proxies',
    Bootstrap::$config['cache'] ? $cache : NULL,
    FALSE
);
$config->setProxyNamespace('Model\Entities\Proxies');
$config->setAutoGenerateProxyClasses(TRUE);


// we need __toString on DateTime, since UoW converts composite primary keys to string
// (who the hell invented composite PKs :P)
Type::overrideType(Type::DATE, 'Model\Types\DateType');
Type::overrideType(Type::DATETIME, 'Model\Types\DateTimeType');


// TODO you may want to change this? ;)
$em = EntityManager::create(
    [
        'driver' => Bootstrap::$config['db']['driverpdo'],
        'user' => Bootstrap::$config['db']['user'],
        'password' => Bootstrap::$config['db']['password'],
        'dbname' => Bootstrap::$config['db']['dbname'],
    ],
    $config
);


$startTime = -microtime(TRUE);
ob_start();

$qb = $em->createQueryBuilder()
    ->from('Model\Entities\Employee', 'e')
    ->select('e')
    ->innerJoin('e.salaries', 's')
    ->addSelect('s')
    ->innerJoin('e.affiliatedDepartments', 'd')
    ->addSelect('d')
    ->innerJoin('d.department', 'dd')
    ->addSelect('dd')
    ->setMaxResults(Bootstrap::$config['limit'])
    ->getQuery();

$paginator = new Paginator($qb);

foreach ($paginator->getIterator() as $emp) {
    /** @var Employee $emp */

    // $output->writeln
    echo sprintf('%s %s (%d):', $emp->getFirstName(), $emp->getLastName(), $emp->getId()), PHP_EOL;

    // $output->writeln
    echo "\tSalaries:", PHP_EOL;
    foreach ($emp->getSalaries() as $salary) {
        // $output->writeln
        echo "\t\t", $salary->getAmount(), PHP_EOL;
    }

    // $output->writeln
    echo "\tDepartments:", PHP_EOL;
    foreach ($emp->getAffiliatedDepartments() as $department) {
        // $output->writeln
        echo "\t\t" . $department->getDepartment()->getName(), PHP_EOL;
    }
}

ob_end_clean();
$endTime = microtime(TRUE);

Bootstrap::result('Doctrine2', 'Doctrine: ' . Version::VERSION, $startTime, $endTime);
