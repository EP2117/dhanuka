<?php

use Illuminate\Support\Facades\Route;
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



/*Route::get('/{any}', function () {
  return view('post');
})->where('any', '.*');*/

Auth::routes(['verify' => false, 'register' => false, 'reset' => true]);
Route::get('/', function () {
    return view('home');
})->middleware('auth');
Route::get('/master/', function () {
    return view('module_vw');
})->middleware('auth');
Route::get('/van/', function () {
    return view('module_vw');
})->middleware('auth');
Route::get('/office/', function () {
    return view('module_vw');
})->middleware('auth');
Route::get('/account/', function () {
    return view('module_vw');
})->middleware('auth');
Route::get('/purchase_office', function () {
    return view('module_vw');
})->middleware('auth');
Route::get('/inventory/', function () {
    return view('module_vw');
})->middleware('auth');
Route::get('/report/', function () {
    return view('module_vw');
})->middleware('auth');

Route::get('/test', 'ProductTransitionController@test');

Route::group(['prefix' => '',  'middleware' => 'auth'], function () {
    Route::resource('brand', 'BrandController');
    Route::get('/brands', 'BrandController@allBrands');
    Route::resource('uom', 'UomController');
    Route::get('/uoms', 'UomController@allUoms');
    Route::resource('category', 'CategoryController');
    Route::get('/categories', 'CategoryController@allCategories');
    Route::resource('product', 'ProductController');
    Route::resource('customer_type', 'CustomerTypeController');
    Route::resource('customer', 'CustomerController');
    Route::resource('country', 'CountryController');
    Route::resource('state', 'StateController');
    Route::get('/state_by_country/{id}', 'StateController@stateByCountry');
    Route::resource('township', 'TownshipController');
    Route::get('/township_by_state/{id}', 'TownshipController@townshipBystate');
    Route::get('/product/selling_uom/{product_id}', 'ProductController@getSellingUomByProductId');
    Route::get('/products', 'ProductController@allProducts');
    Route::post('/product/image/upload', 'ProductPhotoController@store');
    Route::get('/product_photo/{id}', 'ProductPhotoController@show');
    Route::delete('product_photo/{id}', 'ProductPhotoController@destroy');
    Route::resource('mainwarehouse_entry', 'MainwarehouseEntryController');
    Route::get('/transfer/maxid/', 'TransferController@getMaxId');
    Route::resource('transfer', 'TransferController');
    Route::get('/transfer_receive/', 'TransferController@getReceive');
    Route::post('/transfer_receive/{id}', 'TransferController@receiveTransfer');
    Route::get('/warehouses', 'WarehouseController@allWarehouses');
    Route::get('/customers', 'CustomerController@allCustomers');
    Route::post('/customer/image/upload', 'CustomerController@addPhoto');
    Route::post('/customer/image/delete/{name}/{id}', 'CustomerController@deletePhoto');
    Route::get('/productsByUserWarehouse', 'ProductTransitionController@getProductsByUserWarehouse');
    Route::get('/productsByUserWarehouse/{action}/{id}', 'ProductTransitionController@getProductsForSaleInvoice');
    Route::get('/get_product_for_purchase/{action}/{id}', 'ProductTransitionController@getProductsForPurchaseInvoice');
    Route::get('/filterProductsByUserWarehouse/{action}/{id}', 'ProductTransitionController@filterProductsForSaleInvoice');
    Route::get('/sale/maxid/', 'SaleController@getMaxId');
    Route::resource('sale', 'SaleController');
    Route::get('/sale/type/{sale_type}', 'SaleController@getBySaleType');
    Route::get('/order/products/', 'ProductController@getProducts');
    Route::resource('order', 'OrderController');
    Route::resource('order_approval', 'OrderApprovalController');
    Route::resource('collection', 'CollectionController');
    Route::resource('sale_delivery', 'SaleDeliveryController');
    Route::resource('user', 'UserController');
    Route::post('/user_offline/{id}', 'UserController@offUser');
    Route::get('/users/brands', 'UserController@getBrands');
    Route::get('/user/role/{id}', 'UserController@getByRole');
    Route::resource('role', 'RoleController');
    Route::get('/customer_sale_orders/{cus_id}', 'OrderController@getSaleOrders');
    Route::get('/customer_previous_balance/{cus_id}', 'SaleController@getCustomerPreviousBalance');
    Route::get('/sale_order_approval/{id}', 'OrderApprovalController@getApproval');
    Route::get('/customer_credit_sale/{cus_id}', 'SaleController@getCreditSaleByCustomer');
    Route::get('/customer_all_sale/{cus_id}', 'SaleController@getAllSaleByCustomer');
    Route::post('/product/search', 'ProductController@search');
    Route::get('/ara_brand', 'AraProductController@allBrands');
    Route::get('/ara_category', 'AraProductController@allCategories');
    Route::get('/report_brands', 'BrandController@filterBrands');
    Route::get('/sale_man', 'UserController@getSaleMan');
    Route::get('/all_users', 'UserController@getAllUsers');
    Route::get('/office_sale_man', 'UserController@getOfficeSaleMan');
    Route::get('/sale_delivery_approval/{sale_id}/{status}', 'SaleController@deliveryApproval');
    Route::get('/check_warehouse_uom/{product_id}', 'ProductTransitionController@checkWarehouseUom');
    Route::get('/check_selling_uom/{product_id}/{uom_id}', 'ProductTransitionController@checkSellingUom');
    Route::get('/product_status/{id}/{status}', 'ProductController@updateStatus');
    Route::get('/customer_status/{id}/{status}', 'CustomerController@updateStatus');
    Route::get('/brand_status/{id}/{status}', 'BrandController@updateStatus');
    Route::get('/uom_status/{id}/{status}', 'UOMController@updateStatus');
    Route::get('/category_status/{id}/{status}', 'CategoryController@updateStatus');
    Route::get('/categories_bybrand/{brand_id}', 'CategoryController@getCategoriesByBrand');
    Route::get('/order_products/', 'OrderController@getOrderProducts');
    Route::get('/order_products/{id}', 'OrderController@getEditOrderProducts');
    Route::get('/sale_revise/', 'SaleController@getReviseSales');
    Route::resource('branch', 'BranchController');
    Route::get('/branch_status/{id}/{status}', 'BranchController@updateStatus');
    Route::resource('warehouse', 'WarehouseController');
    Route::get('/warehouse_status/{id}/{status}', 'WarehouseController@updateStatus');
    Route::get('/branches', 'BranchController@allBranches');
    Route::get('/branches_byuser', 'BranchController@getBranchByUser');
    Route::get('/warehouses_bybranch/{id}', 'WarehouseController@warehouseByBranch');
    Route::resource('sale_man', 'SaleManController');

    Route::resource('sale_return', 'SaleReturnController');
    Route::resource('customer_return', 'CustomerReturnController');
    Route::resource('return_payment', 'ReturnPaymentController');
    Route::get('/customer_sale_return/{cus_id}', 'SaleReturnController@getSaleReturnByCustomer');
    // Route::get('get_sale_man',['SaleManController','getSaleMan']);
    Route::get('/saleman_status/{id}/{status}', 'SaleManController@updateStatus');
    Route::get('/sale_men', 'SaleManController@allSaleMen');
    //Route for report and excel export
    Route::get('/daily_sale_product_report/', 'SaleController@getDailySaleProductReport');
    Route::get('/daily_sale_product_export/', 'SaleController@exportDailySaleProductReport');

    Route::get('/daily_sale_report/', 'SaleController@getDailySaleReport');
    Route::get('/daily_sale_export/', 'SaleController@exportDailySaleReport');
    Route::get('/daily_sale_export_pdf/', 'SaleController@exportDailySaleReportPdf');

    Route::get('/inventory_report/', 'ProductTransitionController@getInventoryReport');
    Route::get('/inventory_export/', 'ProductTransitionController@exportInventoryReport');
    Route::get('/inventory_export_pdf/', 'ProductTransitionController@getInventoryReport')->name('inventory_export_pdf');; //Kamlesh

    Route::get('/so_product_report/', 'OrderController@getSOProductReport');
    Route::get('/so_product_export/', 'OrderController@exportSOProductReport');

    Route::get('/pending_approval_report/', 'OrderApprovalController@getPendingApprovalReport');
    Route::get('/pending_approval_export/', 'OrderApprovalController@exportPendingApprovalReport');

    Route::get('/blacklist_report/', 'CustomerLogController@getBlackListReport');
    Route::get('/blacklist_export/', 'CustomerLogController@exportBlackListReport');

    Route::get('/customer_wise_report/', 'CustomerController@getCustomerWiseReport');
    Route::get('/customer_wise_export/', 'CustomerController@exportCustomerWiseReport');
    Route::get('/customer_wise_export_pdf/', 'CustomerController@exportCustomerWiseReportPdf');

    Route::get('/product_costing_report/', 'LandedCostingController@getProductCostingReport');
    Route::get('/product_costing_export/', 'LandedCostingController@exportProductCostingReport');

    Route::get('/currency_gain_loss_report/', 'PurchaseCollectionController@getCurrencyGainLossReport');
    Route::get('/currency_gain_loss_export/', 'PurchaseCollectionController@exportCurrencyGainLossReport');

    Route::get('/sale_currency_gain_loss_report/', 'CollectionController@getCurrencyGainLossReport');
    Route::get('/sale_currency_gain_loss_export/', 'CollectionController@exportCurrencyGainLossReport');
    Route::get('/sale_return_report/', 'SaleReturnController@getSaleReturnReport');
    Route::get('/sale_return_export/', 'SaleReturnController@exportSaleReturnReport');

    Route::get('/sale_return_product_report/', 'SaleReturnController@getSaleReturnProductReport');
    Route::get('/sale_return_product_export/', 'SaleReturnController@exportSaleReturnProductReport');

    Route::get('/sale_return_payment_report/', 'ReturnPaymentController@getSaleReturnPaymentReport');
    Route::get('/sale_return_payment_export/', 'ReturnPaymentController@exportSaleReturnPaymentReport');

    Route::get('/sale_analyst_report/', 'SaleController@getSaleAnalystReport');
    Route::get('/sale_analyst_export/', 'SaleController@exportSaleAnalystReport');
    Route::get('/sale_analyst_export_pdf/', 'SaleController@exportSaleAnalystReportPdf');

    Route::get('/product_export/', 'ProductController@exportProduct');
    Route::get('/product_export_pdf/', 'ProductController@exportProductPdf');
    Route::get('/customer_export/', 'CustomerController@exportCustomer');

    Route::resource('sale_advance', 'SaleAdvanceController');
    Route::resource('purchase_advance', 'PurchaseAdvanceController');
    //Route for Import (Migration)
    Route::post('/import/uom', 'UomController@import');
    Route::post('/import/brand', 'BrandController@import');
    Route::post('/import/category', 'CategoryController@import');
    Route::post('/import/warehouse', 'WarehouseController@import');
    Route::post('/import/township', 'TownshipController@import');
    Route::post('/import/customer', 'CustomerController@import');
    Route::post('/import/product', 'ProductController@import');
    Route::post('/import/state', 'StateController@import');
    Route::post('/import/customer_type', 'CustomerTypeController@import');
    Route::post('/import/product_min_percentage_qty', 'ProductController@qtyImport');
    Route::get('/generate_purchase_invoice/{purchase_id}', 'PurchaseInvoiceController@generatePurchaseInvoicePDF');
    Route::get('/generate_purchase_invoice_currency/{purchase_id}', 'PurchaseInvoiceController@generatePurchaseInvoiceCurrencyPDF');
    Route::get('/generate_sale_return/{return_id}', 'SaleReturnController@generateReturnPDF');
    Route::get('/generate_invoice/{sale_id}', 'SaleController@generateInvoicePDF');
    Route::get('/generate_invoice_currency/{sale_id}', 'SaleController@generateInvoiceCurrencyPDF');
    Route::get('/generate_order/{order_id}', 'OrderController@generateOrderPDF');
    Route::group(['prefix' => 'supplier'], function () {
        Route::get('', 'SupplierController@index');
        Route::post('create', 'SupplierController@store');
        Route::get('get_suppliers', 'SupplierController@getSupplier');
        Route::get('/edit/{id}', 'SupplierController@edit');
        Route::patch('{id}/update', 'SupplierController@update');
        Route::get('supplier_status', 'SupplierController@changeStatus');
    });
    Route::group(['prefix' => 'purchase'], function () {
        Route::post('create', 'PurchaseInvoiceController@store');
        Route::get('get_purchase_list', 'PurchaseInvoiceController@index');
        Route::get('/{id}/edit', "PurchaseInvoiceController@edit");
        Route::get('/{id}/destroy', "PurchaseInvoiceController@destroy");
        Route::patch('/{id}/update', "PurchaseInvoiceController@update");
        Route::get('/{id}/get_previous_balance', 'PurchaseInvoiceController@getPreviousBalance');
    });
    Route::group(['prefix' => 'purchase_collection'], function () {
        Route::get('get_collection', 'PurchaseCollectionController@getPurchaseCollection');
        Route::get('supplier_credit_purchase/{id}', 'PurchaseCollectionController@getSupplierCreditPurchase');
        Route::post('store', "PurchaseCollectionController@store");
        Route::get('edit/{c_id}', 'PurchaseCollectionController@edit');
        Route::patch('update/{c_id}', 'PurchaseCollectionController@update');
        Route::delete('destroy/{id}', 'PurchaseCollectionController@destroy');
    });
    Route::group(['prefix' => 'account_head'], function () {
        Route::get('get_all', 'AccountHeadController@getAll');
        Route::get('get_financial_type/{type}', 'AccountHeadController@getFinancialType');
        Route::get('edit/{id}', 'AccountHeadController@edit');
        Route::post('store', 'AccountHeadController@store');
        Route::get('change_status/{id}/{active}', 'AccountHeadController@changeStatus');
        Route::patch('update/{id}', 'AccountHeadController@update');
        Route::delete('destroy/{id}', 'AccountHeadController@destroy');
    });
    Route::group(['prefix' => 'sub_account'], function () {
        Route::get('get_all', 'SubAccountController@getAll');
        Route::get('get_sub_account/{type}', 'SubAccountController@getSubAccount');
        Route::get('get_account_type', 'SubAccountController@getAccountType');
        Route::get('get_account_head', 'SubAccountController@getAccountHead');
        Route::get('get_all_sub_account', 'SubAccountController@getAllSubAccount');

        Route::get('change_status/{id}/{active}', 'SubAccountController@changeStatus');
        Route::get('edit/{id}', 'SubAccountController@edit');
        Route::post('store', 'SubAccountController@store');
        Route::patch('update/{id}', 'SubAccountController@update');
        Route::delete('destroy/{id}', 'SubAccountController@destroy');
        Route::get('get_sub_account_by_account_head/{id}', 'SubAccountController@getSubAccountByAccountHead');
    });
    Route::group(['prefix' => 'receipt'], function () {
        Route::get('get_all', 'ReceiptController@getAll');
        Route::get('edit/{id}', 'ReceiptController@edit');
        Route::post('store', 'ReceiptController@store');
        Route::patch('update/{id}', 'ReceiptController@update');
        Route::delete('destroy/{id}', 'ReceiptController@destroy');
    });
    Route::group(['prefix' => 'payment'], function () {
        Route::get('get_all', 'PaymentController@getAll');
        Route::get('edit/{id}', 'PaymentController@edit');
        Route::post('store', 'PaymentController@store');
        Route::patch('update/{id}', 'PaymentController@update');
        Route::delete('destroy/{id}', 'PaymentController@destroy');
    });
    Route::group(['prefix' => 'report'], function () {
/**HEAD
        Route::get('get_all_cashbook','AccountTransitionController@getAllCashbook');
        Route::get('get_all_ledger','AccountTransitionController@getAllLedger');
        Route::get('/daily_purchase_product_report/', ['App\Http\Controllers\PurchaseInvoiceController','getDailyPurchaseProductReport']);
        Route::get('daily_purchase_product_export',['App\Http\Controllers\PurchaseInvoiceController','getDailyPurchaseProductReport'])->name('daily_purchase_product_export');
        Route::get('get_credit_payment_report',['App\Http\Controllers\PurchaseCollectionController','getCreditPaymentReport']);
        Route::get('credit_payment_export',['App\Http\Controllers\PurchaseCollectionController','getCreditPaymentReport'])->name('credit_payment_export');
        Route::get('get_purchase_outstanding',['App\Http\Controllers\PurchaseCollectionController','getPurchaseOutStanding']);
        Route::get('purchase_outstanding_export',['App\Http\Controllers\PurchaseCollectionController','getPurchaseOutStanding'])->name('purchase_outstanding_export');
        Route::get('get_sale_outstanding',['App\Http\Controllers\CollectionController','getSaleOutstanding']);
        Route::get('sale_outstanding_export',['App\Http\Controllers\CollectionController','getSaleOutstanding'])->name('sale_outstanding_export');
        Route::get('get_credit_collection',['App\Http\Controllers\CollectionController','getCreditCollectionReport']);
        Route::get('credit_collection_export',['App\Http\Controllers\CollectionController','getCreditCollectionReport'])->name('credit_collection_export');
        Route::get('get_valuation',['App\Http\Controllers\ProductTransitionController','getValuationReport']);
        Route::get('profit_and_loss',['App\Http\Controllers\AccountTransitionController','getProfitAndLossReport']);
        Route::get('export_p_and_l_pdf',['App\Http\Controllers\AccountTransitionController','getProfitAndLossReport'])->name('export_p_and_l_pdf'); **/
        Route::get('get_all_cashbook', 'AccountTransitionController@getAllCashbook');
        Route::get('get_all_ledger', 'AccountTransitionController@getAllLedger');
        Route::get('/daily_purchase_product_report/', ['App\Http\Controllers\PurchaseInvoiceController', 'getDailyPurchaseProductReport']);
        Route::get('get_credit_payment_report', ['App\Http\Controllers\PurchaseCollectionController', 'getCreditPaymentReport']);
        Route::get('credit_payment_export', ['App\Http\Controllers\PurchaseCollectionController', 'getCreditPaymentReport'])->name('credit_payment_export');

        Route::get('get_purchase_outstanding', ['App\Http\Controllers\PurchaseCollectionController', 'getPurchaseOutStanding']);
        Route::get('purchase_outstanding_export', ['App\Http\Controllers\PurchaseCollectionController', 'getPurchaseOutStanding'])->name('purchase_outstanding_export');
        Route::get('purchase_outstanding_export_pdf', ['App\Http\Controllers\PurchaseCollectionController', 'getPurchaseOutstanding'])->name('purchase_outstanding_export_pdf'); //Kamlesh

        Route::get('get_sale_outstanding', ['App\Http\Controllers\CollectionController', 'getSaleOutstanding']);
        Route::get('sale_outstanding_export', ['App\Http\Controllers\CollectionController', 'getSaleOutstanding'])->name('sale_outstanding_export');
        Route::get('sale_outstanding_export_pdf', ['App\Http\Controllers\CollectionController', 'getSaleOutstanding'])->name('sale_outstanding_export_pdf'); //kamlesh
        Route::get('get_credit_collection', ['App\Http\Controllers\CollectionController', 'getCreditCollectionReport']);
        Route::get('credit_collection_export', ['App\Http\Controllers\CollectionController', 'getCreditCollectionReport'])->name('credit_collection_export');
        Route::get('get_valuation', ['App\Http\Controllers\ProductTransitionController', 'getValuationReport']);
        Route::get('get_valuation_export_pdf', ['App\Http\Controllers\ProductTransitionController', 'getValuationReport'])->name('get_valuation_export_pdf'); //kamlesh
        Route::get('profit_and_loss', ['App\Http\Controllers\AccountTransitionController', 'getProfitAndLossReport']);
        Route::get('export_p_and_l_pdf', ['App\Http\Controllers\AccountTransitionController', 'getProfitAndLossReport'])->name('export_p_and_l_pdf');
    });
    Route::group(['prefix' => 'supplier_ob'], function () {
        Route::get('', ['\App\Http\Controllers\SupplierOpeningBalanceController', 'index']);
        Route::get('edit/{id}', ['\App\Http\Controllers\SupplierOpeningBalanceController', 'edit']);
        Route::post('store', ['\App\Http\Controllers\SupplierOpeningBalanceController', 'store']);
        Route::patch('update/{id}', ['\App\Http\Controllers\SupplierOpeningBalanceController', 'update']);
        Route::delete('{id}/destroy', ['\App\Http\Controllers\SupplierOpeningBalanceController', 'destroy']);
    });
    Route::group(['prefix' => 'customer_ob'], function () {
        Route::get('', ['\App\Http\Controllers\CustomerOpeningBalanceController', 'index']);
        Route::get('edit/{id}', ['\App\Http\Controllers\CustomerOpeningBalanceController', 'edit']);
        Route::post('store', ['\App\Http\Controllers\CustomerOpeningBalanceController', 'store']);
        Route::patch('update/{id}', ['\App\Http\Controllers\CustomerOpeningBalanceController', 'update']);
        Route::delete('{id}/destroy', ['\App\Http\Controllers\CustomerOpeningBalanceController', 'destroy']);
    });
    Route::resource('inventory_adjustment', 'InventoryAdjustmentController');
    Route::post('landed_costing/create', 'LandedCostingController@store');
    Route::resource('landed_costing', 'LandedCostingController');

    Route::resource('currency', 'CurrencyController');
    Route::get('/currency_status/{id}/{status}', 'CurrencyController@updateStatus');
    Route::get('/all_currency', 'CurrencyController@allCurrency');
});
