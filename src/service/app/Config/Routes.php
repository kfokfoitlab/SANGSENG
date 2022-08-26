<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

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
# $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get ('/',                          'Home::index');
$routes->get ('/Image/(:any)',              'Image::getImage/$1');
$routes->get ('/Image/(:any)/(:any)',       'Image::getImage/$1');


$routes->group('Chat', function($routes){
    $routes->get ('Room/(:any)',                            'Chat::Room/$1');
    $routes->post('Send',                                   'Chat::Send');
    $routes->get ('CreateChannel/(:any)/(:any)',            'Chat::CreateChannel/$1/$2');
});


$routes->group('Auth', function($routes){
    $routes->get ('/',                  'Auth::Index');
    $routes->get ('SignIn',             'Auth::SignIn');
    $routes->post('SignInSubmit',       'Auth::SignInSubmit');
    $routes->get ('SignUp',             'Auth::SignUp');
    $routes->get ('SignInCompanyReport/(:any)', 'Auth::SignInCompanyReport/$1');
    $routes->get ('ForgotMyId',         'Auth::ForgotMyId');
    $routes->get ('ForgotMyPass',       'Auth::ForgotMyPass');
    $routes->post ('ForgotSubmit',      'Auth::ForgotSubmit');

    $routes->get ('SignUpBuyerSLA',      'Auth::SignUpBuyerSLA');
    $routes->post('SignUpBuyer',         'Auth::SignUpBuyer');
    $routes->post('SignUpBuyerStep2',    'Auth::SignUpBuyerStep2');
    $routes->post('SignUpBuyerSubmit',    'Auth::SignUpBuyerSubmit');
    $routes->get ('SignUpBuyerComplete/(:any)', 'Auth::SignUpBuyerComplete/$1');

    $routes->get ('SignUpSellerSLA',      'Auth::SignUpSellerSLA');
    $routes->post('SignUpSeller',         'Auth::SignUpSeller');
    $routes->post('SignUpSellerSubmit',    'Auth::SignUpSellerSubmit');
    $routes->get ('SignUpSellerComplete/(:any)', 'Auth::SignUpSellerComplete/$1');


    $routes->get('SignOut', 'Auth::SignOut');
});

$routes->group('Recruit', function($routes){
    $routes->get ('/', 'Recruit::index');
    $routes->get ('Detail/(:any)', 'Recruit::Detail/$1');
    $routes->get ('Application/(:any)', 'Recruit::Application/$1');
    $routes->post('ApplicationSubmit', 'Recruit::ApplicationSubmit');
    $routes->get ('ApplicationComplete', 'Recruit::ApplicationComplete');
    $routes->post('Bookmark',           'Recruit::Bookmark');
});

$routes->group('Person', function($routes){
    $routes->get ('/', 'Person::index');
    $routes->get ('Detail/(:any)', 'Person::Detail/$1');
    $routes->post('Bookmark',      'Person::Bookmark');
});

$routes->group('Company', function($routes){
    $routes->get ('/',              'Company::index');
    $routes->get ('Detail/(:any)',  'Company::Detail/$1');
    $routes->post('Bookmark',       'Company::Bookmark');
});


$routes->group('Management', ['namespace' => 'App\Controllers\Management'], static function ($routes) {
    $group_name = "User";
    $routes->get ($group_name.'/Profile/',                      $group_name.'\Profile::Index');
    $routes->post($group_name.'/Profile/UpdateSubmit/(:any)',   $group_name.'\Profile::UpdateSubmit/$1');

    $routes->get ($group_name.'/Resume',                        $group_name.'\Resume::Index');
    $routes->get ($group_name.'/Resume/Create',                 $group_name.'\Resume::Create');
    $routes->POST($group_name.'/Resume/CreateSubmit',           $group_name.'\Resume::CreateSubmit');
    $routes->get ($group_name.'/Resume/Update/(:any)',          $group_name.'\Resume::Update/$1');
    $routes->POST($group_name.'/Resume/UpdateSubmit',           $group_name.'\Resume::UpdateSubmit');
    $routes->get ($group_name.'/Resume/Detail/(:any)',          $group_name.'\Resume::Detail/$1');
    $routes->get ($group_name.'/Resume/DetailPreview/(:any)',   $group_name.'\Resume::DetailPreview/$1');
    $routes->get ($group_name.'/Resume/DeleteSubmit/(:any)',    $group_name.'\Resume::DeleteSubmit/$1');

    $routes->get ($group_name.'/ManageJob',                     $group_name.'\ManageJob::Index');
    $routes->get ($group_name.'/ManageJob/Create',              $group_name.'\ManageJob::Create');
    $routes->POST($group_name.'/ManageJob/CreateSubmit',        $group_name.'\ManageJob::CreateSubmit');
    $routes->get ($group_name.'/ManageJob/Detail',              $group_name.'\ManageJob::Detail');
    $routes->POST($group_name.'/ManageJob/UpdateSubmit',        $group_name.'\ManageJob::UpdateSubmit');
    $routes->get ($group_name.'/ManageJob/DeleteSubmit/(:num)', $group_name.'\ManageJob::DeleteSubmit/$1');

    $routes->get ($group_name.'/Application',                   $group_name.'\Application::Index');
    $routes->get ($group_name.'/Application/Detail/(:any)',     $group_name.'\Application::Detail/$1');

    $routes->get ($group_name.'/BookmarkApplication',           $group_name.'\BookmarkApplication::Index');
    $routes->get ($group_name.'/BookmarkCompany',               $group_name.'\BookmarkCompany::Index');

    $routes->get ($group_name.'/Chat',        $group_name.'\Chat::Index');

    /*
    $routes->get ($group_name.'/Application',       $group_name.'::Application');
    $routes->get ($group_name.'/BookmarkApplication',   $group_name.'::BookmarkApplication');
    $routes->get ($group_name.'/BookmarkCompany',   $group_name.'::BookmarkCompany');
    $routes->get ($group_name.'/Message',           $group_name.'::Message');
     */


    $group_name = "Company";
    $routes->get ($group_name.'/Profile/',                      $group_name.'\Profile::Index');
    $routes->post($group_name.'/Profile/UpdateSubmit/(:any)',   $group_name.'\Profile::UpdateSubmit/$1');

    $routes->get ($group_name.'/PublicSettings/',               $group_name.'\PublicSettings::Index');
    $routes->post($group_name.'/PublicSettings/UpdateSubmit',   $group_name.'\PublicSettings::UpdateSubmit');

    $routes->get ($group_name.'/Application',                        $group_name.'\Application::Index');
    $routes->get ($group_name.'/Application/Create',                 $group_name.'\Application::Create');
    $routes->POST($group_name.'/Application/CreateSubmit',           $group_name.'\Application::CreateSubmit');
    $routes->get ($group_name.'/Application/Update/(:any)',          $group_name.'\Application::Update/$1');
    $routes->POST($group_name.'/Application/UpdateSubmit',           $group_name.'\Application::UpdateSubmit');
    $routes->get ($group_name.'/Application/Detail/(:any)',          $group_name.'\Application::Detail/$1');
    $routes->get ($group_name.'/Application/DetailPreview/(:any)',   $group_name.'\Application::DetailPreview/$1');
    $routes->get ($group_name.'/Application/DetailReceipt/(:any)/(:any)/(:any)',   $group_name.'\Application::DetailReceipt/$1/$2/$3');
    $routes->get ($group_name.'/Application/Result/(:any)/(:any)/(:any)',   $group_name.'\Application::Result/$1/$2/$3');
    $routes->get ($group_name.'/Application/CloseSubmit/(:any)',          $group_name.'\Application::CloseSubmit/$1');
    $routes->get ($group_name.'/Application/DeleteSubmit/(:any)',          $group_name.'\Application::DeleteSubmit/$1');

    $routes->get ($group_name.'/BookmarkCandidate',             $group_name.'\BookmarkCandidate::Index');

    $routes->get ($group_name.'/Chat',        $group_name.'\Chat::Index');

});

$routes->group('Buyer', function($routes){
    $routes->get ('/',                      'Buyer::index');
    $routes->get ('Shop/List',              'Buyer::List');
    $routes->get ('Shop/Detail/(:any)',     'Buyer::Detail/$1');
    $routes->post ('Contract',        'Buyer\Contract::Contract');
    $routes->get ('MyPage/Info',            'Buyer::Info');
    $routes->post ('Mypage/Cart',           'Buyer\Mypage::Cart');
});

$routes->group('Seller',  function ($routes){
    $group_name = "Item";
    $routes->get ( '/',                     'Seller::index');
    $routes->get ('Item/ItemUpdate',        'Seller::ItemUpdate');
    $routes->get ($group_name.'/ItemRegist','Seller\Item::ItemRegist');
    $routes->get ($group_name.'/ItemList',   'Seller\Item::ItemList');
    $routes->post ($group_name.'ItemSubmit', 'Seller\Item::ItemSubmit');
    $routes->get ('Contract',               'Seller::Contract');

    $group_name = "IMJOB";
    $routes->get ($group_name.'/List',             'Seller::List');
    $routes->get ($group_name.'/Manage',           'Seller\IMJOB::Manage');
    $routes->post ($group_name.'/reg_worker',           'Seller\IMJOB::reg_worker');
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