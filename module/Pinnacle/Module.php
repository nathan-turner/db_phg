<?php
// module/Pinnacle/Module.php

namespace Pinnacle;

use Pinnacle\Model\GeozipTable;
use Pinnacle\Model\ClientsTable;
use Pinnacle\Model\ContractsTable;
use Pinnacle\Model\MidlevelsTable;
use Pinnacle\Model\PhysiciansTable;
use Pinnacle\Model\UsersTable;
use Pinnacle\Model\BookingTable; 
use Pinnacle\Model\CalendarTable; 
use Pinnacle\Model\Report\ReportingTable;
use Pinnacle\Model\Admin\UsermodTable;
use Pinnacle\Model\Admin\CleanupTable;
use Pinnacle\Model\Admin\GoalsTable;
use Pinnacle\Model\Mail\DescTable;
use Pinnacle\Model\Mail\ListsTable;
use Pinnacle\Model\MidcatTable;
use Pinnacle\Model\SpecialtyTable;
use Pinnacle\Model\Physician\SkillTable;
use Pinnacle\Model\Report\RetainedTable;
use Pinnacle\Model\Report\RetainedMacTable;
use Pinnacle\Model\Report\SpecdemoTable;
use Pinnacle\Model\Report\StatisticsTable;
use Pinnacle\Model\Report\MonmorTable;
use Pinnacle\Model\Report\PhoneTable;
use Pinnacle\Model\Report\PlacementTable as PlacementReport;
use Pinnacle\Model\Report\PlacemonthTable;
use Pinnacle\Model\Report\MarketlogTable;
use Pinnacle\Model\Report\InterviewTable as InterviewReport;
use Pinnacle\Model\Report\FulistTable;
use Pinnacle\Model\Report\LookupTable;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Pinnacle\Model\GeozipTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new GeozipTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\ClientsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new ClientsTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\ContractsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new ContractsTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\MidlevelsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new MidlevelsTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\PhysiciansTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new PhysiciansTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\UsersTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new UsersTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Admin\UsermodTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new UsermodTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Admin\CleanupTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new CleanupTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Admin\GoalsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new GoalsTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Mail\DescTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new DescTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Mail\ListsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new ListsTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Report\RetainedTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new RetainedTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Report\RetainedMacTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new RetainedMacTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Report\SpecdemoTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new SpecdemoTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Report\StatisticsTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new StatisticsTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Report\MonmorTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new MonmorTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Report\PhoneTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new PhoneTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\MidcatTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new MidcatTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\SpecialtyTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new SpecialtyTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Physician\SkillTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new SkillTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Report\PlacementTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new PlacementReport($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Report\PlacemonthTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new PlacemonthTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Report\MarketlogTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new MarketlogTable($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Report\InterviewTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new InterviewReport($dbAdapter);
                    return $table;
                },
                'Pinnacle\Model\Report\FulistTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new FulistTable($dbAdapter);
                    return $table;
                },
				'Pinnacle\Model\BookingTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new BookingTable($dbAdapter);
                    return $table;
                },
				'Pinnacle\Model\CalendarTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new CalendarTable($dbAdapter);
                    return $table;
                },
				'Pinnacle\Model\Report\ReportingTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new ReportingTable($dbAdapter);
                    return $table;
                },
				'Pinnacle\Model\Report\LookupTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new LookupTable($dbAdapter);
                    return $table;
                },
            ),
        );
    }
}

