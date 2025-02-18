<?php

use Illuminate\Support\Facades\Route;
use Webkul\RestApi\Http\Controllers\V1\Admin\Setting\ChannelController;
use Webkul\RestApi\Http\Controllers\V1\Admin\Setting\CurrencyController;
use Webkul\RestApi\Http\Controllers\V1\Admin\Setting\ExchangeRateController;
use Webkul\RestApi\Http\Controllers\V1\Admin\Setting\InventorySourceController;
use Webkul\RestApi\Http\Controllers\V1\Admin\Setting\LocaleController;
use Webkul\RestApi\Http\Controllers\V1\Admin\Setting\RoleController;
use Webkul\RestApi\Http\Controllers\V1\Admin\Setting\SliderController;
use Webkul\RestApi\Http\Controllers\V1\Admin\Setting\TaxCategoryController;
use Webkul\RestApi\Http\Controllers\V1\Admin\Setting\TaxRateController;
use Webkul\RestApi\Http\Controllers\V1\Admin\Setting\UserController;

/**
 * Settings routes.
 */
Route::group([
    'middleware' => ['auth:sanctum', 'sanctum.admin'],
    'prefix'     => 'settings',
], function () {
    /**
     * Locale routes.
     */
    Route::controller(LocaleController::class)->prefix('locales')->group(function () {
        Route::get('', 'allResources');

        Route::post('', 'store');
    
        Route::get('{id}', 'getResource');
    
        Route::put('{id}', 'update');
    
        Route::delete('{id}', 'destroy');
    });

    /**
     * Currency routes.
     */
    Route::controller(CurrencyController::class)->prefix('currencies')->group(function () {
        Route::get('', 'allResources');

        Route::post('', 'store');
    
        Route::get('{id}', 'getResource');
    
        Route::put('{id}', 'update');
    
        Route::delete('{id}', 'destroy');
    
        Route::post('mass-destroy', 'massDestroy');
    
    });

    /**
     * Exchange rate routes.
     */
    Route::controller(ExchangeRateController::class)->prefix('exchange-rates')->group(function () {
        Route::get('', 'allResources');

        Route::post('', 'store');
    
        Route::get('{id}', 'getResource');
    
        Route::put('{id}', 'update');
    
        Route::delete('{id}', 'destroy');
    
        Route::post('update-rates', 'updateRates');
    });

    /**
     * Inventory source routes.
     */
    Route::controller(InventorySourceController::class)->prefix('inventory-sources')->group(function () {
        Route::get('', 'allResources');

        Route::post('', 'store');
    
        Route::get('{id}', 'getResource');
    
        Route::put('{id}', 'update');
    
        Route::delete('{id}', 'destroy');
    });

    /**
     * Channel routes.
     */
    Route::controller(ChannelController::class)->prefix('channels')->group(function () {
        Route::get('', 'allResources');

        Route::post('', 'store');
    
        Route::get('{id}', 'getResource');
    
        Route::put('{id}', 'update');
    
        Route::delete('{id}', 'destroy');
    });
  
    /**
     * User routes.
     */
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('', 'allResources');

        Route::post('', 'store');
    
        Route::get('{id}', 'getResource');
    
        Route::put('{id}', 'update');
    
        Route::delete('{id}', 'destroy');
    });

    /**
     * Role routes.
     */
    Route::controller(RoleController::class)->prefix('roles')->group(function () {
        Route::get('', 'allResources');

        Route::post('', 'store');

        Route::get('{id}', 'getResource');
    
        Route::put('{id}', 'update');
    
        Route::delete('{id}', 'destroy');
    });

    /**
     * Tax category routes.
     */
    Route::controller(TaxCategoryController::class)->prefix('tax-categories')->group(function () {
        Route::get('', 'allResources');

        Route::post('', 'store');
    
        Route::get('{id}', 'getResource');
    
        Route::put('{id}', 'update');
    
        Route::delete('{id}', 'destroy');
    });

    /**
     * Tax rate routes.
     */
    Route::controller(TaxRateController::class)->prefix('tax-rates')->group(function () {
        Route::get('', 'allResources');

        Route::post('', 'store');
    
        Route::get('{id}', 'getResource');
    
        Route::put('{id}', 'update');
    
        Route::delete('{id}', 'destroy');
    }); 
});
