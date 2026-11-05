<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->match(['GET','POST'],'ping', 'Home::ping');

$routes->group('login', static function ($routes) {
    $routes->match(['GET', 'POST'], '(:segment)', 'Login::$1');
    $routes->match(['GET', 'POST'], '/', 'Login::index');
});

$routes->get('/logout', 'Login::logout');

$routes->match(['GET', 'POST'], '/changepassword', 'Login::changepassword', ['filter' => 'authenticatoinfilter:ANY_USER']);
$routes->match(['GET', 'POST'], '/updatepassword', 'Login::updatepassword', ['filter' => 'authenticatoinfilter:ANY_USER']);
$routes->match(['GET', 'POST'], '/changepin', 'Login::changepin', ['filter' => 'authenticatoinfilter:ANY_USER']);
$routes->match(['GET', 'POST'], '/updatepin', 'Login::updatepin', ['filter' => 'authenticatoinfilter:ANY_USER']);

$routes->get('/', 'Home::index', ['filter' => 'authenticatoinfilter:ANY_USER']);
$routes->get('/test2', 'Home::test');
$routes->get('/welcome', 'Home::welcome', ['filter' => 'authenticatoinfilter:ANY_USER']);
$routes->get('/display', 'Home::display');
$routes->post('/dental/store_communication', 'Home::store_communication');
$routes->get('/hospital/ajax/gettodatappointments', 'Hospital\Ajax::gettodatappointments');
$routes->get('/hospital/ajax/gettodaywaitings', 'Hospital\Ajax::gettodaywaitings');
$routes->get('/hospital/ajax/getdoctorqueues', 'Hospital\Ajax::getdoctorqueues');
$routes->get('/read_pos_response', 'Home::read_pos_response');
$routes->post('/pedsconfirmpayment', 'Home::pedsconfirmpayment');
$routes->get('/pedsconfirmpayment', 'Home::pedsconfirmpayment');
$routes->get('/run_shell', 'Home::run_shell');
$routes->get('/reboot', 'Home::reboot');

$routes->match(['GET', 'POST'], '/cashier/newdocumentupload_mobile', 'Cashier\Cashier::newdocumentupload_mobile');
$routes->match(['GET', 'POST'], '/cashier/addcashiersummary_mobile', 'Cashier\Cashier::addcashiersummary_mobile');
$routes->match(['GET', 'POST'], '/cashier/cashierdocument', 'Cashier\Cashier::cashierdocument');

$routes->get('/newappointment', 'Home::newappointment');
$routes->get('/savedapp', 'Home::savedapp');
$routes->post('/addappointment', 'Home::addappointment');
$routes->post('/getschedule', 'Home::getschedule');
$routes->post('/getpatientbymobile', 'Home::getpatientbymobile');
$routes->post('/getpatientbymobileforapp', 'Home::getpatientbymobileforapp');
$routes->get('/import_online_prescription', 'Home::import_online_prescription');

$routes->post('/hospital/ajax/getschedule', 'Hospital\Ajax::getschedule');
$routes->post('/hospital/ajax/getpatientbymobile', 'Hospital\Ajax::getpatientbymobile');


$routes->group('image', static function ($routes) {
    $routes->match(['GET', 'POST'], '/', 'Image::index');
    $routes->match(['GET', 'POST'], '(:segment)', 'Image::$1');
});

$routes->group('tools', static function ($routes) {
    $routes->match(['GET', 'POST'], '/', 'Tools\Index::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
    $routes->group('sms', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'Tools\SMS::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
        $routes->match(['GET', 'POST'], '(:segment)', 'Tools\SMS::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
    });
    $routes->match(['GET', 'POST'], '(:segment)', 'Tools\Index::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
});

$routes->group('api', static function ($routes) {
    $routes->match(['GET', 'POST'], '/', 'API\API::index', ['filter' => 'api_authenticationfilter:GRANT_ANY']);
    $routes->match(['GET', 'POST'], 'qrcode', 'API\API::qrcode');
    $routes->group('sms', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'API\SMS::index', ['filter' => 'api_authenticationfilter']);
        $routes->match(['GET', 'POST'], 'relay_send_sms', 'API\SMS::relay_send_sms', ['filter' => 'api_authenticationfilter:GRANT_ANY']);
        $routes->match(['GET', 'POST'], '(:segment)', 'API\SMS::$1', ['filter' => 'api_authenticationfilter']);
    });

    

});

$routes->group('api2', static function ($routes) {
    $routes->match(['GET', 'POST'], '/', 'API\API2\Api2::index');

    $routes->group('test/v1', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'API\API2\Test\V1::index', ['filter' => 'api2_authenticationfilter:GRANT_ANY']);
        $routes->match(['GET', 'POST'], '(:segment)', 'API\API2\Test\V1::$1', ['filter' => 'api2_authenticationfilter:GRANT_ANY']);
    });

    $routes->group('finance/v1', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'API\API2\Finance\V1::index', ['filter' => 'api2_authenticationfilter']);
        $routes->match(['GET', 'POST'], '(:segment)', 'API\API2\Finance\V1::$1', ['filter' => 'api2_authenticationfilter']);
    });

    $routes->group('appointment/v1', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'API\API2\Appointment\V1::index', ['filter' => 'api2_authenticationfilter']);
        $routes->match(['GET', 'POST'], '(:segment)', 'API\API2\Appointment\V1::$1', ['filter' => 'api2_authenticationfilter']);
    });

    $routes->group('pathology/v1', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'API\API2\Pathology\V1::index', ['filter' => 'api2_authenticationfilter']);
        $routes->match(['GET', 'POST'], '(:segment)', 'API\API2\Pathology\V1::$1', ['filter' => 'api2_authenticationfilter']);
    });

    $routes->group('image/v1', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'API\API2\Image\V1::index', ['filter' => 'api2_authenticationfilter']);
        $routes->match(['GET', 'POST'], '(:segment)', 'API\API2\Image\V1::$1', ['filter' => 'api2_authenticationfilter']);
    });

    $routes->group('paymentgateway/v1', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'API\API2\PaymentGateway\V1::index', ['filter' => 'api2_authenticationfilter']);
        $routes->match(['GET', 'POST'], '(:segment)', 'API\API2\PaymentGateway\V1::$1', ['filter' => 'api2_authenticationfilter']);
    });

    $routes->group('report/v1', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'API\API2\Report\V1::index', ['filter' => 'api2_authenticationfilter']);
        $routes->match(['GET', 'POST'], 'ping', 'API\API2\Report\V1::ping');
        $routes->match(['GET', 'POST'], '(:segment)', 'API\API2\Report\V1::$1', ['filter' => 'api2_authenticationfilter']);
    });
});

$routes->group('datacorrection', static function ($routes) {
    $routes->match(['GET', 'POST'], '/', 'DataCorrection::index', ['filter' => 'authenticatoinfilter:ANY_USER']);
    $routes->match(['GET', 'POST'], '(:segment)', 'DataCorrection::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
});

$routes->group('dental', static function ($routes) {
    $routes->group('reception', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Dental\Reception::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,RECEPTION']);
        $routes->match(['GET', 'POST'], '/', 'Dental\Reception::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,RECEPTION']);
    });
    $routes->group('doctor', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Dental\Doctor::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,DOCTOR']);
        $routes->match(['GET', 'POST'], '/', 'Dental\Doctor::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,DOCTOR']);
    });
    $routes->group('nurse', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Dental\Nurse::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,NURSE']);
        $routes->match(['GET', 'POST'], '/', 'Dental\Nurse::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,NURSE']);
    });
    
    $routes->group('ajax', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Dental\Ajax::$1', ['filter' => 'authenticatoinfilter:ANY_USER']);
        $routes->match(['GET', 'POST'], '/', 'Dental\Ajax::index', ['filter' => 'authenticatoinfilter:ANY_USER']);
    });
    $routes->group('report', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Dental\Report::$1', ['filter' => 'authenticatoinfilter:ANY_USER']);
        $routes->match(['GET', 'POST'], '/', 'Dental\Report::index', ['filter' => 'authenticatoinfilter:ANY_USER']);
    });
    $routes->group('admin', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Dental\Admin::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ADMIN']);
        $routes->match(['GET', 'POST'], '/', 'Dental\Admin::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ADMIN']);
    });
    $routes->group('super_admin', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Home::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
        $routes->match(['GET', 'POST'], '/', 'Home::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
    });
    $routes->group('store', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Inventory\Store::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,STORE']);
        $routes->match(['GET', 'POST'], '/', 'Inventory\Store::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,STORE']);
    });
});

$routes->group('hospital', static function ($routes) {
    $routes->group('reception', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'Hospital\Reception::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,RECEPTION']);
        $routes->match(['GET', 'POST'], 'newmanualcard_mobile', 'Hospital\Reception::newmanualcard_mobile');
        $routes->match(['GET', 'POST'], 'addmanualcard_mobile', 'Hospital\Reception::addmanualcard_mobile');
        $routes->match(['GET', 'POST'], 'manualcard', 'Hospital\Reception::manualcard');

        $routes->match(['GET', 'POST'], '(:segment)', 'Hospital\Reception::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,RECEPTION']);
        
    });
    $routes->group('callcenter', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Hospital\Callcenter::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,CALL_CENTER']);
        $routes->match(['GET', 'POST'], '/', 'Hospital\Callcenter::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,CALL_CENTER']);
    });
    $routes->group('pathology', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Hospital\Pathology::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,PATHOLOGY']);
        $routes->match(['GET', 'POST'], '/', 'Hospital\Pathology::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,PATHOLOGY']);
    });
    $routes->group('doctor', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'Hospital\Doctor::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,DOCTOR']);
        $routes->match(['GET', 'POST'], 'newprogresspicture_mobile', 'Hospital\Doctor::newprogresspicture_mobile');
        $routes->match(['GET', 'POST'], 'addprogresspicture_mobile', 'Hospital\Doctor::addprogresspicture_mobile');
        $routes->match(['GET', 'POST'], 'progresspicture', 'Hospital\Doctor::progresspicture');
        $routes->match(['GET', 'POST'], '(:segment)', 'Hospital\Doctor::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,DOCTOR']);
        
    });
    $routes->group('nurse', static function ($routes) {
        $routes->match(['GET', 'POST'], 'newprogresspicture_mobile', 'Hospital\Nurse::newprogresspicture_mobile');
        $routes->match(['GET', 'POST'], 'addprogresspicture_mobile', 'Hospital\Nurse::addprogresspicture_mobile');
        $routes->match(['GET', 'POST'], '(:segment)', 'Hospital\Nurse::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,NURSE']);
        $routes->match(['GET', 'POST'], '/', 'Hospital\Nurse::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,NURSE']);
        
    });
    $routes->group('laboratory', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Hospital\Laboratory::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,LABORATORY']);
        $routes->match(['GET', 'POST'], '/', 'Hospital\Laboratory::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,LABORATORY']);
    });
    $routes->group('store', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Inventory\Store::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,STORE']);
        $routes->match(['GET', 'POST'], '/', 'Inventory\Store::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,STORE']);
    });
    
    $routes->group('ajax', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Hospital\Ajax::$1', ['filter' => 'authenticatoinfilter:ANY_USER']);
        $routes->match(['GET', 'POST'], '/', 'Hospital\Ajax::index', ['filter' => 'authenticatoinfilter:ANY_USER']);
    });
    $routes->group('report', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Hospital\Report::$1', ['filter' => 'authenticatoinfilter:ANY_USER']);
        $routes->match(['GET', 'POST'], '/', 'Hospital\Report::index', ['filter' => 'authenticatoinfilter:ANY_USER']);
    });
    $routes->group('admin', static function ($routes) {
        $routes->match(['GET', 'POST'], 'clone_price_list', 'Hospital\Admin::clone_price_list', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ADMIN,FINANCE']);
        $routes->match(['GET', 'POST'], '(:segment)', 'Hospital\Admin::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ADMIN']);
        $routes->match(['GET', 'POST'], '/', 'Hospital\Admin::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ADMIN']);
    });
    $routes->group('MIS', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'Hospital\Report::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ADMIN,MIS']);
        $routes->match(['GET', 'POST'], '(:segment)', 'Hospital\Report::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ADMIN,MIS']);
    });
    $routes->group('super_admin', static function ($routes) {
        $routes->match(['GET', 'POST'], '(:segment)', 'Home::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
        $routes->match(['GET', 'POST'], '/', 'Home::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN']);
    });
});

$routes->group('inventory', static function ($routes) {
    $routes->group('ajax', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'Inventory\Ajax::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ANY_USER']);
        $routes->match(['GET', 'POST'], '(:segment)', 'Inventory\Ajax::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,ANY_USER']);
    });
    $routes->group('store', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'Inventory\Store::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,STORE']);
        $routes->match(['GET', 'POST'], '(:segment)', 'Inventory\Store::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,STORE']);
    });
    $routes->group('finance', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'Inventory\Finance::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,FINANCE']);
        $routes->match(['GET', 'POST'], '(:segment)', 'Inventory\Finance::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,FINANCE']);
    });
    $routes->group('dispensary', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'Inventory\Dispensary::index', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,'.BluelimeConfiguration::USER_ROLE::DISPENSARY.','.BluelimeConfiguration::USER_ROLE::COMPOUNDING]);
        $routes->match(['GET', 'POST'], '(:segment)', 'Inventory\Dispensary::$1', ['filter' => 'authenticatoinfilter:SUPER_ADMIN,'.BluelimeConfiguration::USER_ROLE::DISPENSARY.','.BluelimeConfiguration::USER_ROLE::COMPOUNDING]);
    });    
    $routes->group('compounding', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'Inventory\Compounding::index', ['filter' => 'authenticatoinfilter:'.BluelimeConfiguration::USER_ROLE::DISPENSARY.','.BluelimeConfiguration::USER_ROLE::COMPOUNDING]);
        $routes->match(['GET', 'POST'], '(:segment)', 'Inventory\Compounding::$1', ['filter' => 'authenticatoinfilter:'.BluelimeConfiguration::USER_ROLE::DISPENSARY.','.BluelimeConfiguration::USER_ROLE::COMPOUNDING]);
    });    
});

$routes->group('cashier', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'Cashier\Cashier::index', ['filter' => 'authenticatoinfilter:'.BluelimeConfiguration::USER_ROLE::CASHIER_HOSPITAL.','.BluelimeConfiguration::USER_ROLE::CASHIER_INVENTORY.','.BluelimeConfiguration::USER_ROLE::RECEPTION.','.BluelimeConfiguration::USER_ROLE::FINANCE]);
        $routes->match(['GET', 'POST'], '(:segment)', 'Cashier\Cashier::$1', ['filter' => 'authenticatoinfilter:'.BluelimeConfiguration::USER_ROLE::CASHIER_HOSPITAL.','.BluelimeConfiguration::USER_ROLE::CASHIER_INVENTORY.','.BluelimeConfiguration::USER_ROLE::RECEPTION.','.BluelimeConfiguration::USER_ROLE::FINANCE]);
        
    });

$routes->group('adjustment', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'Adjustment\Adjustment::index', ['filter' => 'authenticatoinfilter:'.BluelimeConfiguration::USER_ROLE::DISPENSARY.','.BluelimeConfiguration::USER_ROLE::STORE]);
        $routes->match(['GET', 'POST'], '(:segment)', 'Adjustment\Adjustment::$1', ['filter' => 'authenticatoinfilter:'.BluelimeConfiguration::USER_ROLE::DISPENSARY.','.BluelimeConfiguration::USER_ROLE::STORE]);
});

$routes->group('peachtreeidmap', static function ($routes) {
        $routes->match(['GET', 'POST'], '/', 'PeachtreeIdMap\PeachtreeIdMap::map_id', ['filter' => 'authenticatoinfilter:'.BluelimeConfiguration::USER_ROLE::FINANCE.','.BluelimeConfiguration::USER_ROLE::STORE.','.BluelimeConfiguration::USER_ROLE::DISPENSARY.','.BluelimeConfiguration::USER_ROLE::ADMIN]);
        $routes->match(['GET', 'POST'], '(:segment)', 'PeachtreeIdMap\PeachtreeIdMap::$1', ['filter' => 'authenticatoinfilter:'.BluelimeConfiguration::USER_ROLE::FINANCE.','.BluelimeConfiguration::USER_ROLE::STORE.','.BluelimeConfiguration::USER_ROLE::DISPENSARY.','.BluelimeConfiguration::USER_ROLE::ADMIN]);
});