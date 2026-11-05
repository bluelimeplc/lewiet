<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();
//$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->group('login', static function ($routes) {
    $routes->match(['get', 'post'], '(:segment)', 'Login::$1');
    $routes->match(['get', 'post'], '/', 'Login::index');
});




$routes->get('/logout', 'Login::logout');

$routes->get('/', 'Home::index', ['filter' => 'authenticatoinfilter:ANY_USER']);
$routes->get('/test2', 'Home::test');
$routes->get('/welcome', 'Home::welcome', ['filter' => 'authenticatoinfilter:ANY_USER']);
$routes->get('/display', 'Home::display');
$routes->post('/dental/store_communication', 'Home::store_communication');
$routes->get('/dental/ajax/gettodatappointments', 'Dental\Ajax::gettodatappointments');


$routes->group('tools', static function ($routes) {
    $routes->match(['get', 'post'], '/', 'Tools\Index::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
    $routes->group('sms', static function ($routes) {
        $routes->match(['get', 'post'], '/', 'Tools\SMS::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
        $routes->match(['get', 'post'], '(:segment)', 'Tools\SMS::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
    });
    $routes->match(['get', 'post'], '(:segment)', 'Tools\Index::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
});

$routes->group('api', static function ($routes) {
    $routes->match(['get', 'post'], '/', 'API\API::index', ['filter' => 'api_authenticationfilter']);
    $routes->group('sms', static function ($routes) {
        $routes->match(['get', 'post'], '/', 'API\SMS::index', ['filter' => 'api_authenticationfilter']);
        $routes->match(['get', 'post'], '(:segment)', 'API\SMS::$1', ['filter' => 'api_authenticationfilter']);
    });
});

$routes->group('datacorrection', static function ($routes) {
    $routes->match(['get', 'post'], '/', 'DataCorrection::index', ['filter' => 'authenticatoinfilter:ANY_USER']);
    $routes->match(['get', 'post'], '(:segment)', 'DataCorrection::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
});

$routes->group('dental', static function ($routes) {
    $routes->group('reception', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Dental\Reception::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,RECEPTION']);
        $routes->match(['get', 'post'], '/', 'Dental\Reception::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,RECEPTION']);
    });
    $routes->group('doctor', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Dental\Doctor::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,DOCTOR']);
        $routes->match(['get', 'post'], '/', 'Dental\Doctor::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,DOCTOR']);
    });
    $routes->group('nurse', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Dental\Nurse::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,NURSE']);
        $routes->match(['get', 'post'], '/', 'Dental\Nurse::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,NURSE']);
    });
    
    $routes->group('ajax', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Dental\Ajax::$1', ['filter' => 'authenticatoinfilter:ANY_USER']);
        $routes->match(['get', 'post'], '/', 'Dental\Ajax::index', ['filter' => 'authenticatoinfilter:ANY_USER']);
    });
    $routes->group('report', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Dental\Report::$1', ['filter' => 'authenticatoinfilter:ANY_USER']);
        $routes->match(['get', 'post'], '/', 'Dental\Report::index', ['filter' => 'authenticatoinfilter:ANY_USER']);
    });
    $routes->group('admin', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Dental\Admin::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ADMIN']);
        $routes->match(['get', 'post'], '/', 'Dental\Admin::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ADMIN']);
    });
    $routes->group('super_admin', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Home::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
        $routes->match(['get', 'post'], '/', 'Home::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
    });
    $routes->group('store', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Inventory\Store::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,STORE']);
        $routes->match(['get', 'post'], '/', 'Inventory\Store::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,STORE']);
    });
});

$routes->group('hospital', static function ($routes) {
    $routes->group('reception', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Hospital\Reception::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,RECEPTION']);
        $routes->match(['get', 'post'], '/', 'Hospital\Reception::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,RECEPTION']);
    });
    $routes->group('doctor', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Hospital\Doctor::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,DOCTOR']);
        $routes->match(['get', 'post'], '/', 'Hospital\Doctor::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,DOCTOR']);
    });
    $routes->group('nurse', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Hospital\Nurse::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,NURSE']);
        $routes->match(['get', 'post'], '/', 'Hospital\Nurse::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,NURSE']);
    });
    $routes->group('laboratory', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Hospital\Laboratory::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,LABORATORY']);
        $routes->match(['get', 'post'], '/', 'Hospital\Laboratory::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,LABORATORY']);
    });
    $routes->group('store', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Inventory\Store::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,STORE']);
        $routes->match(['get', 'post'], '/', 'Inventory\Store::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,STORE']);
    });
    
    $routes->group('ajax', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Hospital\Ajax::$1', ['filter' => 'authenticatoinfilter:ANY_USER']);
        $routes->match(['get', 'post'], '/', 'Hospital\Ajax::index', ['filter' => 'authenticatoinfilter:ANY_USER']);
    });
    $routes->group('report', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Hospital\Report::$1', ['filter' => 'authenticatoinfilter:ANY_USER']);
        $routes->match(['get', 'post'], '/', 'Hospital\Report::index', ['filter' => 'authenticatoinfilter:ANY_USER']);
    });
    $routes->group('admin', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Hospital\Admin::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ADMIN']);
        $routes->match(['get', 'post'], '/', 'Hospital\Admin::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ADMIN']);
    });
    $routes->group('super_admin', static function ($routes) {
        $routes->match(['get', 'post'], '(:segment)', 'Home::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
        $routes->match(['get', 'post'], '/', 'Home::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
    });
});

$routes->group('inventory', static function ($routes) {
    $routes->group('ajax', static function ($routes) {
        $routes->match(['get', 'post'], '/', 'Inventory\Ajax::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ANY_USER']);
        $routes->match(['get', 'post'], '(:segment)', 'Inventory\Ajax::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ANY_USER']);
    });
    $routes->group('store', static function ($routes) {
        $routes->match(['get', 'post'], '/', 'Inventory\Store::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,STORE']);
        $routes->match(['get', 'post'], '(:segment)', 'Inventory\Store::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,STORE']);
    });
    $routes->group('finance', static function ($routes) {
        $routes->match(['get', 'post'], '/', 'Inventory\Finance::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,FINANCE']);
        $routes->match(['get', 'post'], '(:segment)', 'Inventory\Finance::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,FINANCE']);
    });
    $routes->group('dispensary', static function ($routes) {
        $routes->match(['get', 'post'], '/', 'Inventory\Dispensary::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,DISPENSARY']);
        $routes->match(['get', 'post'], '(:segment)', 'Inventory\Dispensary::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,DISPENSARY']);
    });
    
    
});
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
