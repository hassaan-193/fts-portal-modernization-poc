<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Homepage
Route::get('/', function () {
    return view('homepage.index');
});
//About Routes
Route::get('/about-us', function () { return view('about_us.about');});
Route::get('/our-mission', function () { return view('about_us.our_mission');});
Route::get('/quality-technology', function () { return view('about_us.quality_technology');});
Route::get('/quality-assurance', function () { return view('about_us.quality_assurance');});
Route::get('/policies/privacy-policy', function () { return view('about_us.privacy_policy');});

//Our Network Routes
Route::get('/collection-centres', function () { return view('our_network.collection_centers');});

//Join Us Routes
Route::get('/partner-with-us', function () { return view('join_us.partner_withus');});
Route::get('/current-vacancies', function () { return view('join_us.current_vacancies');});

//Services And Tests Routes
Route::group([ 'prefix' => 'services-and-tests' ], function () {
    Route::get('/test-services', function () {
        return view('services_tests.test_services');
    })->name('services-and-tests.test_services');

    // Route::get('/pathology-tests', function () {
    //     return view('services_tests.pathology_test');
    // })->name('services-and-tests.pathology_tests');

    Route::get('/home-sample-collection', function () {
        return view('services_tests.home_sample_collection');
    })->name('services-and-tests.sample_collection');

    Route::get('/book-an-appoinment', function () {
        return view('services_tests.book_appointment');
    })->name('services-and-tests.appoinment');
});

Route::get('/contact-us', function () { return view('contact_us.contact');});
