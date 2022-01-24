<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;







Route::post('core/state','API\CoreController@state')->name('core.state');
// General routes
Route::group([ 'namespace' => 'API', 'prefix' => '{app_access_token}' ], function() {

    // Core routes
    Route::get('core/datalists','CoreController@datalists')->name('core.datalists');
    Route::get('core/check/{check_type}/{resource_name}/{test_value}','CoreController@check')->name('core.check');

    Route::get('core/search/all/{query_string}','CoreController@index')->name('core.search.all');
    Route::get('core/search/stores/{query_string}',['uses'=>'StoreController@index','action_type'=>'search'])->name('core.search.stores');
    Route::get('core/search/users/{query_string}',['uses'=>'UserController@index','action_type'=>'search'])->name('core.search.users'); // returns all for admin, otherwise sellers only
    Route::get('core/search/products/{query_string}',['uses'=>'ProductController@index','action_type'=>'search'])->name('core.search.products');
    Route::get('core/search/events/{query_string}',['uses'=>'EventController@index','action_type'=>'search'])->name('core.search.events');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('core/logs/on_thing/{thing_table}/{thing_id}/{thing_column?}','UserController@index_on_thing')->name('core.logs.index_on_thing');
        Route::get('core/logs/by_user/{action_user_id}','UserController@index_by_user')->name('core.logs.index_by_user');
        Route::get('core/logs/by_user_on_thing/{action_user_id}/{thing_table}/{thing_id}/{thing_column?}','UserController@index_by_user_on_thing')->name('core.logs.index_by_user_on_thing');
    });








    // User Specific routes


    // Base
    // store, show, index, update, destroy
    // signin, signout
    Route::get('users/all',['uses'=>'UserController@index','action_type'=>'all'])->name('users.index'); // returns all for admin, otherwise sellers only
    Route::get('users/{username}','UserController@show')->name('users.show'); // "show" takes string (username) not int

    Route::post('users/signup','UserController@store')->name('users.signup');
    Route::post('users/signin','UserController@signin')->name('users.signin');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('users/signout','UserController@signout')->name('users.signout');
        Route::apiResource('users', 'UserController')->only(['update','delete'])->parameter('users','id');
        Route::post('users/{id}/merge_pins','UserController@merge_pins')->name('users.merge_pins');
    });

    // User Phones
    // store, update, destroy
    Route::apiResource('users/{owner_id}/phones','PhoneController',['middleware'=>'auth:api'])->only(['store','update','destroy'])->parameter('phones','id')->names('users.phones');

    // User Address
    // store, update, destroy
    Route::apiResource('users/{owner_id}/addresses','AddressController',['middleware'=>'auth:api'])->only(['store','update','destroy'])->parameter('addresses','id')->names('users.addresses');
    
    // User Admin Extension and attributes 
    Route::group(['middleware'=>'auth:api'], function () { // Self / Admin
        // User Admin Extension
        // store, update, destroy
        Route::apiResource('users/{user_id}/admins','AdminController')->only(['store','update','destroy'])->parameter('admins','id')->names('users.admins');

        // Admin Extension Permission Instances
        // store, update, destroy
        Route::apiResource('users/{user_id}/admins/{admin_id}/permissions','PermissionInstanceController')->only(['store','update','destroy'])->parameter('permissions','id')->names('users.admins.permissions');

        // Events
        // store, update, destroy
        Route::apiResource('users/{user_id}/admins/{adder_admin_id}/events_added','EventController')->only(['store','update','destroy'])->parameter('events_added','id')->names('users.admins.events_added');

        // Notifications
        // update, destroy
        Route::apiResource('admins/{entity_id}/notifications','EntityNotificationController')->only(['update','destroy'])->parameter('notifications','id')->names('users.admins.notifications');

        // Admin Preferences
        // store, update
        Route::apiResource('admins/{entity_id}/preferences','EntityPreferenceController')->only(['store','update'])->parameter('preferences','id')->names('users.admins.preferences');


        // Admin actions
        Route::get('admins/products/pending_action',['uses'=>'ProductController@index','action_type'=>'pending_action'])->name('admins.products.index');
        Route::get('admins/orders',['uses'=>'OrderController@index','action_type'=>'all'])->name('admins.orders.index');
    
    });

    // User Pinned Items
    // store, update, destroy
    Route::apiResource('users/{adder_user_id}/pins','PinController',['middleware'=>'auth:api'])->only(['store','update','destroy'])->parameter('pins','id')->names('users.pins');
    Route::get('users/{adder_user_id}/pins/{list}',['middleware'=>'auth:api','uses'=>'PinController@index'])->name('users.pins.index');

    // User Search Items
    // destroy, destroy_all
    Route::group(['middleware' => 'auth:api'], function () { // Self
        Route::delete('users/{entity_id}/search_history/{id}','SearchTermController@destroy')->name('users.search_history.destroy');
        Route::delete('users/{entity_id}/search_history','SearchTermController@destroy_all')->name('users.search_history.destroy_all');
    });

    // User Follow Instances
    // store, destroy
    Route::apiResource('users/{follower_user_id}/follow_instances','FollowInstanceController',['middleware'=>'auth:api'])->only(['store','destroy'])->parameter('follow_instances','id')->names('users.follow_instances');

    // User Store Managed
    // store, update, destroy
    Route::apiResource('users/{owner_user_id}/stores','StoreController',['middleware'=>'auth:api'])->only(['store','update','destroy'])->parameter('stores','id')->names('users.stores');

    // User Market Profile Reviews
    // index, store, update, destroy
    Route::get('users/{reviewed_thing_id}/seller_reviews','ReviewController@index')->name('users.seller_reviews.index');
    Route::apiResource('users/{reviewed_thing_id}/seller_reviews','ReviewController',['middleware'=>'auth:api'])->only(['store','update','destroy'])->parameter('seller_reviews','id')->names('users.seller_reviews');

    // User Market Products
    // index, store, update, destroy
    Route::get('users/{seller_id}/seller_products',['uses'=>'ProductController@index','action_type'=>'from_seller'])->name('users.seller_products.index');
    Route::apiResource('users/{seller_id}/seller_products','ProductController',['middleware'=>'auth:api'])->only(['store','update','destroy'])->parameter('seller_products','id')->names('users.seller_products');
    Route::post('users/{seller_id}/seller_products/upload_images',['middleware'=>'auth:api','uses'=>'ImageController@upload','upload_dir_name'=>'products-images'])->name('users.seller_products.upload_images');

    // User Orders: seller_received
    // index, store, update
    Route::get('users/{seller_id}/seller_received_orders',['middleware'=>'auth:api','uses'=>'OrderController@index','action_type'=>'received'])->name('users.seller_received_orders.index');
    Route::apiResource('users/{seller_id}/seller_received_orders','OrderController',['middleware'=>'auth:api'])->only(['store','update'])->parameter('seller_received_orders','id')->names('users.seller_received_orders');
    Route::post('users/{seller_id}/seller_received_orders/upload_images',['middleware'=>'auth:api','uses'=>'ImageController@upload','upload_dir_name'=>'orders-images'])->name('users.seller_received_orders.upload_images');

    // User Orders: placed
    // index
    Route::get('users/{placer_user_id}/orders',['middleware'=>'auth:api','uses'=>'OrderController@index','action_type'=>'placed'])->name('users.placed_orders.index');
    
    // User Notifications
    // update, destroy
    Route::apiResource('users/{entity_id}/notifications','EntityNotificationController',['middleware'=>'auth:api'])->only(['update','destroy'])->parameter('notifications','id')->names('users.notifications');

    // User Preferences
    // store, update
    Route::apiResource('users/{entity_id}/preferences','EntityPreferenceController',['middleware'=>'auth:api'])->only(['store','update'])->parameter('preferences','id')->names('users.preferences');

    // User Connect Instances
    // index, destroy
    // Delete used to remote signout
    Route::apiResource('users/{user_id}/connect_instances','ConnectInstanceController',['middleware'=>'auth:api'])->only(['index','destroy'])->parameter('connect_instances','id')->names('users.connect_instances');










    // Store Routes


    Route::get('stores/all',['uses'=>'StoreController@index','action_type'=>'all'])->name('stores.index');
    Route::get('stores/{username}','StoreController@show')->name('stores.show'); // "show" takes string (username) not int
    
    // Store Phones
    // store, update, destroy
    Route::apiResource('stores/{owner_id}/phones','PhoneController',['middleware'=>'auth:api'])->only(['store','update','destroy'])->parameter('phones','id')->names('stores.phones');

    // Store Followers
    // store, destroy
    Route::apiResource('stores/{follower_user_id}/follow_instances','FollowInstanceController',['middleware'=>'auth:api'])->only(['store','destroy'])->parameter('follow_instances','id')->names('stores.follow_instances');

    // Store Reviews
    // index, store, update, destroy
    Route::get('stores/{reviewed_thing_id}/reviews','ReviewController@index')->name('stores.reviews.index');
    Route::apiResource('stores/{reviewed_thing_id}/reviews','ReviewController',['middleware'=>'auth:api'])->only(['store','update','destroy'])->parameter('reviews','id')->names('stores.reviews');

    // Store Social Links
    // store, update, destroy
    Route::apiResource('stores/{owner_id}/social_links','SocialLinkController',['middleware'=>'auth:api'])->only(['store','update','destroy'])->parameter('social_links','id')->names('stores.social_links');

    // Store Products
    // index, store, update, destroy
    Route::get('stores/{seller_id}/products',['uses'=>'ProductController@index','action_type'=>'from_seller'])->name('stores.products.index');
    Route::apiResource('stores/{seller_id}/products','ProductController',['middleware'=>'auth:api'])->only(['store','update','destroy'])->parameter('products','id')->names('stores.products');
    Route::post('stores/{seller_id}/products/upload_images',['middleware'=>'auth:api','uses'=>'ImageController@upload','upload_dir_name'=>'products-images'])->name('stores.products.upload_images');

    
    // Store Orders: received
    // index, store, update
    Route::get('stores/{seller_id}/orders',['middleware'=>'auth:api','uses'=>'OrderController@index','action_type'=>'received'])->name('stores.orders.index');
    Route::apiResource('stores/{seller_id}/orders','OrderController',['middleware'=>'auth:api'])->only(['store','update'])->parameter('orders','id')->names('stores.orders');
    Route::post('stores/{seller_id}/orders/upload_images',['middleware'=>'auth:api','uses'=>'ImageController@upload','upload_dir_name'=>'orders-images'])->name('stores.orders.upload_images');

    // Store Notifications
    // update, destroy
    Route::apiResource('stores/{entity_id}/notifications','EntityNotificationController',['middleware'=>'auth:api'])->only(['update','destroy'])->parameter('notifications','id')->names('stores.notifications');

    // Store Preferences
    // store, update
    Route::apiResource('stores/{entity_id}/preferences','EntityPreferenceController',['middleware'=>'auth:api'])->only(['store','update'])->parameter('preferences','id')->names('stores.preferences');










    // Other Routes

    // Permissions
    // store, update
    Route::apiResource('permissions','PermissionController',['middleware'=>'auth:api'])->parameter('permissions','id')->only(['store','update']); // All routes protected by admin middleware

    // Product Categories
    // store, update
    Route::apiResource('product_categories','ProductCategoryController',['middleware'=>'auth:api'])->only(['store','update'])->parameter('product_categories','id');









    // Order Routes
    Route::group([ 'middleware' => 'auth:api' ], function() {

        Route::get('orders/{reference}','OrderController@show')->name('orders.show'); // "show" takes string (reference) not int
        
        // Orders received address & phone
        // update
        Route::put('orders/{owner_id}/phones/{id}','PhoneController@update')->name('orders.phones.update');
        Route::put('orders/{owner_id}/addresses/{id}','AddressController@update')->name('orders.addresses.update');

        // order payment receipt
        // store
        Route::post('orders/{owner_id}/post_payment_receipt/{id}','ImageController@update')->name('orders.post_payment_receipt.update');
        
    });








    // Product & Product Category Routes

    // Product List routes
    Route::get('products/all',['uses'=>'ProductController@index','action_type'=>'all'])->name('products.index');
    Route::get('products/from_location/{location_name}',['uses'=>'ProductController@index','action_type'=>'from_location'])->name('products.index');
    Route::get('products/structured_collection/{user_id?}','ProductController@structured_collection')->name('products.structured_collection');
    Route::get('products/category/{category_id}','ProductController@category')->name('products.category');

    // show
    Route::get('products/{reference}','ProductController@show')->name('products.show'); // "show" takes string (reference) not int
    Route::get('products/{reference}/with_related', ['uses'=>'ProductController@show','action_type'=>'with_related'])->name('products.show'); // "show" takes string (reference) not int

    // Product Reviews
    // index, store, update, destroy
    Route::get('products/{reviewed_thing_id}/reviews','ReviewController@index')->name('products.reviews.index');
    Route::apiResource('products/{reviewed_thing_id}/reviews','ReviewController',['middleware'=>'auth:api'])->only(['store','update','destroy'])->parameter('reviews','id')->names('products.reviews');
    











    // Event Routes


    // Event List routes
    Route::get('events/all',['uses'=>'EventController@index','action_type'=>'all'])->name('events.index');
    Route::get('events/{reference}','EventController@show')->name('events.show'); // "show" takes string (reference) not int
    Route::apiResource('events','EventController',['middleware'=>'auth:api'])->parameter('events','id')->only(['store','update','destroy']); // All routes protected by admin middleware
    Route::post('events/upload_images',['middleware'=>'auth:api','uses'=>'ImageController@upload','upload_dir_name'=>'events-images'])->name('events.upload_images');

    // Store Social Links
    // store, update, destroy
    Route::apiResource('events/{owner_id}/social_links','SocialLinkController',['middleware'=>'auth:api'])->only(['store','update','destroy'])->parameter('social_links','id')->names('events.social_links');

    // Event Reviews
    // index, store, update, destroy
    Route::get('events/{reviewed_thing_id}/reviews','ReviewController@index')->name('events.reviews.index');
    Route::apiResource('events/{reviewed_thing_id}/reviews','ReviewController',['middleware'=>'auth:api'])->only(['store','update','destroy'])->parameter('reviews','id')->names('events.reviews');

});