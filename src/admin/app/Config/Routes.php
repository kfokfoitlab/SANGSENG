<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
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
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('/Image/(:any)', 'Image::getImage/$1');
$routes->get('/Image/(:any)/(:any)', 'Image::getImage/$1');

// 회원
$routes->group('Member', ['namespace' => 'App\Controllers\Member'], static function ($routes) {
    // 인재
    $group_name = "User";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/getList',           $group_name.'::getList');
    $routes->get ($group_name.'/Detail/(:any)',     $group_name.'::Detail/$1');
    $routes->get ($group_name.'/Update/(:any)',     $group_name.'::Update/$1');
    $routes->post($group_name.'/UpdateSubmit',      $group_name.'::UpdateSubmit');
    $routes->get ($group_name.'/DeleteSubmit/(:any)', $group_name.'::DeleteSubmit/$1');

    // 기업
    $group_name = "Company";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/getList',           $group_name.'::getList');
    $routes->get ($group_name.'/Detail/(:any)',     $group_name.'::Detail/$1');
    $routes->get ($group_name.'/Confirm/(:any)/(:num)',     $group_name.'::Confirm/$1/$2');
    $routes->get ($group_name.'/Update/(:any)',     $group_name.'::Update/$1');
    $routes->post($group_name.'/UpdateSubmit',      $group_name.'::UpdateSubmit');
    $routes->get ($group_name.'/DeleteSubmit/(:any)', $group_name.'::DeleteSubmit/$1');
	
	// ID/PW 찾기
	$group_name = "ForgotInfo";
	$routes->get ($group_name.'/',                  $group_name.'::Index');
	$routes->post($group_name.'/getList',           $group_name.'::getList');
	$routes->get ($group_name.'/Detail/(:any)',     $group_name.'::Detail/$1');
	$routes->get ($group_name.'/Update/(:any)',     $group_name.'::Update/$1');
	$routes->post($group_name.'/UpdateSubmit',      $group_name.'::UpdateSubmit');
	$routes->get ($group_name.'/DeleteSubmit/(:any)', $group_name.'::DeleteSubmit/$1');
	$routes->get ($group_name.'/statusUpdate', $group_name.'::statusUpdate');
	$routes->get ($group_name.'/searchId', $group_name.'::searchId');
	$routes->post ($group_name.'/resetPw', $group_name.'::resetPw');
});

// 채용
$routes->group('Application', ['namespace' => 'App\Controllers\Application'], static function ($routes) {
    // 전체 목록
    $group_name = "Lists";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/getList',           $group_name.'::getList');
    $routes->get ($group_name.'/Detail/(:any)',     $group_name.'::Detail/$1');
    $routes->get ($group_name.'/RecommendSubmit/(:any)/(:any)',     $group_name.'::RecommendSubmit/$1/$2');
    $routes->get ($group_name.'/Update',            $group_name.'::Update');

});

// 구직
$routes->group('Job', ['namespace' => 'App\Controllers\Job'], static function ($routes) {
    // 전체 목록
    $group_name = "Lists";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/getList',           $group_name.'::getList');
    $routes->get ($group_name.'/Detail/(:any)',     $group_name.'::Detail/$1');
    $routes->get ($group_name.'/Update',            $group_name.'::Update');
});

// 인재
$routes->group('IMJOB', ['namespace' => 'App\Controllers\IMJOB'], static function ($routes) {
	// 전체 목록
	$group_name = "Lists";
	$routes->get ($group_name.'/',                  $group_name.'::Index');
	$routes->post($group_name.'/getList',           $group_name.'::getList');
	$routes->get ($group_name.'/Detail/(:any)',     $group_name.'::Detail/$1');
	$routes->get ($group_name.'/Update',            $group_name.'::Update');
});

// 이력서
$routes->group('Resume', ['namespace' => 'App\Controllers\Resume'], static function ($routes) {
    // 전체 목록
    $group_name = "Lists";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/getList',           $group_name.'::getList');
    $routes->get ($group_name.'/Detail/(:any)',     $group_name.'::Detail/$1');
});

// chat
$routes->group('Chat', ['namespace' => 'App\Controllers\Chat'], static function ($routes) {
    // 전체 목록
    $group_name = "Lists";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/getList',           $group_name.'::getList');
    $routes->get ($group_name.'/Detail/(:any)',     $group_name.'::Detail/$1');
});

// 데이터베이스
$routes->group('Database/Impairment', ['namespace' => 'App\Controllers\Database\Impairment'], static function ($routes) {
    // 장애 유형
    $group_name = "Type";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->get ($group_name.'/Create',            $group_name.'::Create');
    $routes->post($group_name.'/getList',           $group_name.'::getList');
    $routes->post($group_name.'/Request/(:any)',    $group_name.'::Request/$1');

    // 장애 정도
    $group_name = "Degree";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',      $group_name.'::UpdateSubmit');

    // 보장구
    $group_name = "AssistiveDevice";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',      $group_name.'::UpdateSubmit');

    // 운동능력
    $group_name = "PhysicalAbility";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',      $group_name.'::UpdateSubmit');
});

$routes->group('Database/Job', ['namespace' => 'App\Controllers\Database\Job'], static function ($routes) {
    // 직무 유형
    $group_name = "Profession";
    $routes->get($group_name.'/',                $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',   $group_name.'::UpdateSubmit');

    // 고용 형태
    $group_name = "EmploymentType";
    $routes->get($group_name.'/',                $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',   $group_name.'::UpdateSubmit');

    // 근무 형태
    $group_name = "WorkType";
    $routes->get($group_name.'/',                $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',   $group_name.'::UpdateSubmit');

    // 경력 사항
    $group_name = "Career";
    $routes->get($group_name.'/',                $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',   $group_name.'::UpdateSubmit');

    // 복리 후생
    $group_name = "Welfare";
    $routes->get($group_name.'/',                $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',   $group_name.'::UpdateSubmit');

});


// 환경 설정
$routes->group('Configuration', ['namespace' => 'App\Controllers\Configuration'], static function ($routes) {
    // 승인 설정
    $group_name = "Approve";
    $routes->get ($group_name.'/',                $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',    $group_name.'::UpdateSubmit');

});


// 약관 관리
$routes->group('Terms', ['namespace' => 'App\Controllers\Terms'], static function ($routes) {
    $group_name = "ServiceLevelAgreement";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',      $group_name.'::UpdateSubmit');

    $group_name = "TermsOfService";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',      $group_name.'::UpdateSubmit');

    $group_name = "PrivacyPolicy";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',      $group_name.'::UpdateSubmit');

    $group_name = "Subscribe";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',      $group_name.'::UpdateSubmit');

    $group_name = "AdditionalService";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->post($group_name.'/UpdateSubmit',      $group_name.'::UpdateSubmit');
});

// 약관히스토리
$routes->group('TermsHistory', ['namespace' => 'App\Controllers\TermsHistory'], static function ($routes) {
    // 전체 목록
    $group_name = "Lists";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->get ($group_name.'/Detail',            $group_name.'::Detail');

});

// 근로자관리
$routes->group('Employees', ['namespace' => 'App\Controllers\Employees'], static function ($routes) {
    // 전체 목록
    $group_name = "Lists";
    $routes->get ($group_name.'/',                  $group_name.'::Index');
    $routes->get ($group_name.'/Detail',            $group_name.'::Detail');
    $routes->get ($group_name.'/Update',            $group_name.'::Update');

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
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
