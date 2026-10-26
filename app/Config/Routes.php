<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->get('setup', 'Auth::setup');
$routes->post('setup', 'Auth::createAdministrator');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::authenticate');
$routes->post('logout', 'Auth::logout');

$routes->group('', ['filter' => 'auth'], static function ($routes): void {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('reports', 'Dashboard::reports');
    $routes->get('report-cards', 'ReportCards::index');
    $routes->post('report-cards/migrate', 'ReportCards::runMigration');
    $routes->get('report-cards/template', 'ReportCards::template');
    $routes->post('report-cards/import', 'ReportCards::importData');
    $routes->get('report-cards/subjects', 'ReportCards::subjectsPage');
    $routes->post('report-cards/subjects', 'ReportCards::updateSubjects');
    $routes->get('data/students/import', 'StudentImport::index');
    $routes->get('data/students/import/template', 'StudentImport::template');
    $routes->get('data/students/backup', 'StudentImport::backup');
    $routes->post('data/students/import', 'StudentImport::importData');
    $routes->post('data/students/delete-all', 'Registry::deleteAllStudents');
    $routes->get('data/students/(:num)', 'Registry::showStudent/$1');
    $routes->get('data/(:segment)', 'Registry::index/$1');
    $routes->get('data/(:segment)/new', 'Registry::new/$1');
    $routes->post('data/(:segment)', 'Registry::create/$1');
    $routes->get('data/(:segment)/(:num)/edit', 'Registry::edit/$1/$2');
    $routes->post('data/(:segment)/(:num)', 'Registry::update/$1/$2');
    $routes->post('data/(:segment)/(:num)/delete', 'Registry::delete/$1/$2');
});
