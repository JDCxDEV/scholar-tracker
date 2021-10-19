<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

Route::namespace('Auth')->middleware('guest:admin')->group(function() {

    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login')->name('login');

    Route::get('reset-password/{token}/{email}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('reset-password/change', 'ResetPasswordController@reset')->name('password.change');

    Route::get('forgot-password', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('forgot-password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');

});

Route::middleware('auth:admin')->group(function() {

    Route::namespace('Auth')->group(function() {

        Route::get('logout', 'LoginController@logout')->name('logout');

    });

    Route::get('', 'DashboardController@index')->name('dashboard');

    /**
     * @Count Fetch Controller
     */
    Route::post('count/notifications', 'CountFetchController@fetchNotificationCount')->name('counts.fetch.notifications');
    Route::post('count/sample-items', 'CountFetchController@fetchSampleItemCount')->name('counts.fetch.sample-items.pending');

    /**
     * @Analytics
     */
    Route::namespace('Analytics')->group(function() {
        Route::post('analytics/slp-line/{id}', 'AxieAnalyticsController@fetchUserGraphLine')->name('axie-analytics.fetch-slp-line.user');
        Route::post('analytics/slp-line', 'AxieAnalyticsController@fetchLine')->name('axie-analytics.fetch-graph-line.user');
        Route::post('analytics/slp-pie', 'AxieAnalyticsController@fetchPie')->name('axie-analytics.fetch-graph.user');
        Route::get('analytics/battle-logs/{id}', 'AxieAnalyticsController@fetchBattleLogs')->name('axie-analytics.fetch-battle-logs.user');
        Route::get('analytics/battle-logs-user/{id}', 'AxieAnalyticsController@fetchUserBattleLogs')->name('axie-analytics.fetch-battle-logs.top-user');
    });

    Route::namespace('Profiles')->group(function() {

        /**
         * @Admin Profiles
         */
        Route::get('profile', 'ProfileController@show')->name('profiles.show');
        Route::post('profile/update', 'ProfileController@update')->name('profiles.update');
        Route::post('profile/change-password', 'ProfileController@changePassword')->name('profiles.change-password');

        Route::post('profile/fetch', 'ProfileController@fetch')->name('profiles.fetch');

    });

    /**
     * @AdminUsers
     */
    Route::namespace('AdminUsers')->group(function() {

        /**
         * @AdminUsers
         */
        Route::get('admin-users', 'AdminUserController@index')->name('admin-users.index');
        Route::get('admin-users/create', 'AdminUserController@create')->name('admin-users.create');
        Route::post('admin-users/store', 'AdminUserController@store')->name('admin-users.store');
        Route::get('admin-users/show/{id}', 'AdminUserController@show')->name('admin-users.show');
        Route::post('admin-users/update/{id}', 'AdminUserController@update')->name('admin-users.update');
        Route::post('admin-users/{id}/archive', 'AdminUserController@archive')->name('admin-users.archive');
        Route::post('admin-users/{id}/restore', 'AdminUserController@restore')->name('admin-users.restore');

        Route::post('admin-users/fetch', 'AdminUserFetchController@fetch')->name('admin-users.fetch');
        Route::post('admin-users/fetch?archived=1', 'AdminUserFetchController@fetch')->name('admin-users.fetch-archive');
        Route::post('admin-users/fetch-item/{id?}', 'AdminUserFetchController@fetchView')->name('admin-users.fetch-item');
        Route::post('admin-users/fetch-pagination/{id}', 'AdminUserFetchController@fetchPagePagination')->name('admin-users.fetch-pagination');

    });

    /**
     * CMS Pages
     */
    Route::namespace('Pages')->group(function() {

        Route::get('pages', 'PageController@index')->name('pages.index');
        Route::get('pages/create', 'PageController@create')->name('pages.create');
        Route::post('pages/store', 'PageController@store')->name('pages.store');
        Route::get('pages/show/{id}', 'PageController@show')->name('pages.show');
        Route::post('pages/update/{id}', 'PageController@update')->name('pages.update');
        Route::post('pages/{id}/archive', 'PageController@archive')->name('pages.archive');
        Route::post('pages/{id}/restore', 'PageController@restore')->name('pages.restore');

        Route::post('pages/fetch', 'PageFetchController@fetch')->name('pages.fetch');
        Route::post('pages/fetch?archived=1', 'PageFetchController@fetch')->name('pages.fetch-archive');
        Route::post('pages/fetch-item/{id?}', 'PageFetchController@fetchView')->name('pages.fetch-item');
        Route::post('pages/fetch-pagination/{id}', 'PageFetchController@fetchPagePagination')->name('pages.fetch-pagination');

        Route::get('page-items', 'PageItemController@index')->name('page-items.index');
        Route::get('page-items/create', 'PageItemController@create')->name('page-items.create');
        Route::post('page-items/store', 'PageItemController@store')->name('page-items.store');
        Route::get('page-items/show/{id}', 'PageItemController@show')->name('page-items.show');
        Route::post('page-items/update/{id}', 'PageItemController@update')->name('page-items.update');
        Route::post('page-items/{id}/archive', 'PageItemController@archive')->name('page-items.archive');
        Route::post('page-items/{id}/restore', 'PageItemController@restore')->name('page-items.restore');

        Route::post('page-items/fetch', 'PageItemFetchController@fetch')->name('page-items.fetch');
        Route::post('page-items/fetch?archived=1', 'PageItemFetchController@fetch')->name('page-items.fetch-archive');
        Route::post('page-items/fetch?page_id={id}', 'PageItemFetchController@fetch')->name('page-items.fetch-page-items');
        Route::post('page-items/fetch-item/{id?}', 'PageItemFetchController@fetchView')->name('page-items.fetch-item');
        Route::post('page-items/fetch-pagination/{id}', 'PageItemFetchController@fetchPagePagination')->name('page-items.fetch-pagination');

    });

    /**
     * @Roles
     */
    Route::namespace('Roles')->group(function() {

        Route::get('roles', 'RoleController@index')->name('roles.index');
        Route::get('roles/create', 'RoleController@create')->name('roles.create');
        Route::post('roles/store', 'RoleController@store')->name('roles.store');
        Route::get('roles/{id}', 'RoleController@show')->name('roles.show');
        Route::post('roles/{id}/update', 'RoleController@update')->name('roles.update');
        Route::post('roles/{id}/archive', 'RoleController@archive')->name('roles.archive');
        Route::post('roles/{id}/restore', 'RoleController@restore')->name('roles.restore');

        Route::post('roles/{id}/update-permission', 'RoleController@updatePermissions')->name('roles.update-permissions');

        Route::post('roles/fetch', 'RoleFetchController@fetch')->name('roles.fetch');
        Route::post('roles/fetch?archived=1', 'RoleFetchController@fetch')->name('roles.fetch-archive');
        Route::post('roles/fetch-item/{id?}', 'RoleFetchController@fetchView')->name('roles.fetch-item');
        Route::post('role/fetch-pagination/{id}', 'RoleFetchController@fetchPagePagination')->name('roles.fetch-pagination');

    });

    /**
     * @Permissions
     */
    Route::namespace('Permissions')->group(function() {

        Route::post('permissions-fetch/{id?}', 'PermissionFetchController@fetch')->name('permissions.fetch');

    });

    Route::namespace('Notifications')->group(function() {

        Route::get('notifications', 'NotificationController@index')->name('notifications.index');
        Route::post('notifications/all/mark-as-read', 'NotificationController@readAll')->name('notifications.read-all');
        Route::post('notifications/{id}/read', 'NotificationController@read')->name('notifications.read');
        Route::post('notifications/{id}/unread', 'NotificationController@unread')->name('notifications.unread');

        Route::post('notifications-fetch', 'NotificationFetchController@fetch')->name('notifications.fetch');
        Route::post('notifications-fetch?read=1', 'NotificationFetchController@fetch')->name('notifications.fetch-read');
        Route::post('notifications-fetch?unread=1', 'NotificationFetchController@fetch')->name('notifications.fetch-unread');

    });

    Route::namespace('ActivityLogs')->group(function() {

        Route::get('activity-logs', 'ActivityLogController@index')->name('activity-logs.index');
        Route::post('activity-logs/fetch', 'ActivityLogFetchController@fetch')->name('activity-logs.fetch');

        Route::post('activity-logs/fetch?id={id?}&sample=1', 'ActivityLogFetchController@fetch')->name('activity-logs.fetch.sample-items');

        Route::post('activity-logs/fetch?id={id?}&admin=1', 'ActivityLogFetchController@fetch')->name('activity-logs.fetch.admin-users');
        Route::post('activity-logs/fetch?id={id?}&user=1', 'ActivityLogFetchController@fetch')->name('activity-logs.fetch.users');

        Route::post('activity-logs/fetch?profile=1', 'ActivityLogFetchController@fetch')->name('activity-logs.fetch.profiles');

        Route::post('activity-logs/fetch?id={id?}&roles=1', 'ActivityLogFetchController@fetch')->name('activity-logs.fetch.roles');

        Route::post('activity-logs/fetch?id={id?}&pagecontents=1', 'ActivityLogFetchController@fetch')->name('activity-logs.fetch.pages');
        Route::post('activity-logs/fetch?id={id?}&pageitems=1', 'ActivityLogFetchController@fetch')->name('activity-logs.fetch.page-items');

        Route::post('activity-logs/fetch?id={id?}&articles=1', 'ActivityLogFetchController@fetch')->name('activity-logs.fetch.articles');

        Route::post('activity-logs/fetch?id={id?}&faqs=1', 'ActivityLogFetchController@fetch')->name('activity-logs.fetch.faqs');
        Route::post('activity-logs/fetch?id={id?}&announcements=1', 'ActivityLogFetchController@fetch')->name('activity-logs.fetch.announcements');
        Route::post('activity-logs/fetch?id={id?}&branches=1', 'ActivityLogFetchController@fetch')->name('activity-logs.fetch.branches');
        Route::post('activity-logs/fetch?id={id?}&scholar-types=1', 'ActivityLogFetchController@fetch')->name('activity-logs.fetch.scholar-types');

    });

    Route::namespace('Faqs')->group(function() {
        Route::get('faqs', 'FaqController@index')->name('faqs.index');
        Route::get('faqs/create', 'FaqController@create')->name('faqs.create');
        Route::post('faqs/store', 'FaqController@store')->name('faqs.store');
        Route::get('faqs/show/{id}', 'FaqController@show')->name('faqs.show');
        Route::post('faqs/update/{id}', 'FaqController@update')->name('faqs.update');
        Route::post('faqs/{id}/archive', 'FaqController@archive')->name('faqs.archive');
        Route::post('faqs/{id}/restore', 'FaqController@restore')->name('faqs.restore');

        Route::post('faqs/fetch', 'FaqFetchController@fetch')->name('faqs.fetch');
        Route::post('faqs/fetch?archived=1', 'FaqFetchController@fetch')->name('faqs.fetch-archive');
        Route::post('faqs/fetch-item/{id?}', 'FaqFetchController@fetchView')->name('faqs.fetch-item');
        Route::post('faqs/fetch-pagination/{id}', 'FaqFetchController@fetchPagePagination')->name('faqs.fetch-pagination');
    });

    Route::namespace('Announcements')->group(function() {
        Route::get('announcements', 'AnnouncementController@index')->name('announcements.index');
        Route::get('announcements/create', 'AnnouncementController@create')->name('announcements.create');
        Route::post('announcements/store', 'AnnouncementController@store')->name('announcements.store');
        Route::get('announcements/show/{id}', 'AnnouncementController@show')->name('announcements.show');
        Route::post('announcements/update/{id}', 'AnnouncementController@update')->name('announcements.update');
        Route::post('announcements/{id}/archive', 'AnnouncementController@archive')->name('announcements.archive');
        Route::post('announcements/{id}/restore', 'AnnouncementController@restore')->name('announcements.restore');

        Route::post('announcements/fetch', 'AnnouncementFetchController@fetch')->name('announcements.fetch');
        Route::post('announcements/fetch?archived=1', 'AnnouncementFetchController@fetch')->name('announcements.fetch-archive');
        Route::post('announcements/fetch-item/{id?}', 'AnnouncementFetchController@fetchView')->name('announcements.fetch-item');
        Route::post('announcements/fetch-pagination/{id}', 'AnnouncementFetchController@fetchPagePagination')->name('announcements.fetch-pagination');
    });

        Route::namespace('AboutUs')->group(function() {
        Route::get('about-us', 'AboutUsController@index')->name('about-us.index');
        Route::post('about-us/store', 'AboutUsController@store')->name('about-us.store');
        Route::post('about-us/update/{id}', 'AboutUsController@update')->name('about-us.update');
        Route::post('about-us/{id}/remove-image', 'AboutUsController@removeImage')->name('about-us.remove-image');

        Route::post('about-us/fetch', 'AboutUsFetchController@fetch')->name('about-us.fetch');
        Route::post('about-us/fetch?archived=1', 'AboutUsFetchController@fetch')->name('about-us.fetch-archive');
        Route::post('about-us/fetch-item/{id?}', 'AboutUsFetchController@fetchView')->name('about-us.fetch-item');
        Route::post('about-us/fetch-pagination/{id}', 'AboutUsFetchController@fetchPagePagination')->name('about-us.fetch-pagination');
    });

    Route::namespace('Inquiries')->group(function() {
        Route::get('inquiries', 'InquiryController@index')->name('inquiries.index');
        Route::get('inquiries/show/{id}', 'InquiryController@show')->name('inquiries.show');
        Route::post('inquiries/{id}/archive', 'InquiryController@archive')->name('inquiries.archive');
        Route::post('inquiries/{id}/restore', 'InquiryController@restore')->name('inquiries.restore');

        Route::post('inquiries/fetch', 'InquiryFetchController@fetch')->name('inquiries.fetch');
        Route::post('inquiries/fetch?archived=1', 'InquiryFetchController@fetch')->name('inquiries.fetch-archive');
        Route::post('inquiries/fetch-item/{id?}', 'InquiryFetchController@fetchView')->name('inquiries.fetch-item');
        Route::post('inquiries/fetch-pagination/{id}', 'InquiryFetchController@fetchPagePagination')->name('inquiries.fetch-pagination');
    });

    /**
     * @Users
     */
    Route::namespace('Users')->group(function() {

        /**
         * @AdminUsers
         */
        Route::get('scholars', 'UserController@index')->name('users.index');
        Route::get('scholars/create', 'UserController@create')->name('users.create');
        Route::post('scholars/store', 'UserController@store')->name('users.store');
        Route::get('scholars/show/{id}', 'UserController@show')->name('users.show');
        Route::post('scholars/update/{id}', 'UserController@update')->name('users.update');
        Route::post('scholars/{id}/archive', 'UserController@archive')->name('users.archive');
        Route::post('scholars/{id}/restore', 'UserController@restore')->name('users.restore');
        Route::post('scholars/export', 'UserController@export')->name('users.export');

        Route::post('scholars/fetch', 'UserFetchController@fetch')->name('users.fetch');
        Route::post('scholars/fetch?archived=1', 'UserFetchController@fetch')->name('users.fetch-archive');
        Route::post('scholars/fetch-item/{id?}', 'UserFetchController@fetchView')->name('users.fetch-item');
        Route::post('scholars/fetch-pagination/{id}', 'UserFetchController@fetchPagePagination')->name('users.fetch-pagination');

        Route::get('scholars/slp-data/{id}', 'UserController@parseUserSlpData')->name('users.slp-data');
        Route::get('scholars/cards-data', 'UserController@getCardsData')->name('users.cards-data');
        Route::get('scholars/axie-count-data', 'UserController@getTotalAxies')->name('users.axie-count-data');
        Route::get('scholars/list', 'UserController@getScholars')->name('users.lists');
        Route::get('scholars/update-battle-logs', 'UserController@updateScholarBattleLogs')->name('users.update-battle-logs');
    });

    /**
     * @Scholars
     */
    Route::namespace('Scholars')->group(function() {

        Route::namespace('DailyRecords')->group(function() {

            /**
             * @DailyRecords
             */
            Route::post('daily-records/fetch', 'ScholarDailyRecordFetchController@fetch')->name('scholar-daily-records.fetch');;
            Route::post('daily-records/fetch-pagination/{id}', 'ScholarDailyRecordFetchController@fetchPagePagination')->name('scholar-daily-records.fetch-pagination');
        });
        Route::namespace('Types')->group(function() {

            /**
             * @Types
             */
            Route::get('scholar-types', 'ScholarTypeController@index')->name('scholar-types.index');
            Route::get('scholar-types/create', 'ScholarTypeController@create')->name('scholar-types.create');
            Route::post('scholar-types/store', 'ScholarTypeController@store')->name('scholar-types.store');
            Route::get('scholar-types/show/{id}', 'ScholarTypeController@show')->name('scholar-types.show');
            Route::post('scholar-types/update/{id}', 'ScholarTypeController@update')->name('scholar-types.update');
            Route::post('scholar-types/{id}/archive', 'ScholarTypeController@archive')->name('scholar-types.archive');
            Route::post('scholar-types/{id}/restore', 'ScholarTypeController@restore')->name('scholar-types.restore');
            Route::post('scholar-types/export', 'ScholarTypeController@export')->name('scholar-types.export');

            Route::post('scholar-types/fetch', 'ScholarTypeFetchController@fetch')->name('scholar-types.fetch');
            Route::post('scholar-types/fetch?archived=1', 'ScholarTypeFetchController@fetch')->name('scholar-types.fetch-archive');
            Route::post('scholar-types/fetch-item/{id?}', 'ScholarTypeFetchController@fetchView')->name('scholar-types.fetch-item');
            Route::post('scholar-types/fetch-pagination/{id}', 'ScholarTypeFetchController@fetchPagePagination')->name('scholar-types.fetch-pagination');
        });
    });

});
