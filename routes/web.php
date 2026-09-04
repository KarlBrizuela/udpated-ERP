<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\Marketing\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\Production\InventoryController;
use App\Http\Controllers\Production\SiteController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AdminFinanceController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
  // Dashboard
  Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
  Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
  Route::post('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change-password');
  Route::post('/profile/test-email', [App\Http\Controllers\ProfileController::class, 'testEmail'])->name('profile.test-email');
  Route::get('/session-keep-alive', function () {
      return response()->json(['status' => 'alive']);
  })->name('session-keep-alive');

  // Payment Requests Common Routes
  Route::get('/payment-requests/{id}', [App\Http\Controllers\Accounting\PaymentRequestController::class, 'show'])->name('payment-requests.show')->where('id', '[0-9]+');
  Route::post('/payment-requests/{id}/approve', [App\Http\Controllers\Accounting\PaymentRequestController::class, 'approve'])->name('payment-requests.approve')->where('id', '[0-9]+');
  Route::post('/payment-requests/{id}/reject', [App\Http\Controllers\Accounting\PaymentRequestController::class, 'reject'])->name('payment-requests.reject')->where('id', '[0-9]+');

  // Auto Debit Letters Common Routes (Accessible across divisions: Director, Admin & Finance Managers, Production)
  Route::get('/production/ford/auto-debit/{id}', [App\Http\Controllers\Production\FORDController::class, 'autoDebitShow'])->name('production.ford.auto-debit.show')->where('id', '[0-9]+');
  Route::post('/production/ford/auto-debit/{id}/approve-director', [App\Http\Controllers\Production\FORDController::class, 'autoDebitApproveDirector'])->name('production.ford.auto-debit.approve-director')->where('id', '[0-9]+');
  Route::post('/production/ford/auto-debit/{id}/approve-finance', [App\Http\Controllers\Production\FORDController::class, 'autoDebitApproveFinance'])->name('production.ford.auto-debit.approve-finance')->where('id', '[0-9]+');
  Route::post('/production/ford/auto-debit/{id}/reject', [App\Http\Controllers\Production\FORDController::class, 'autoDebitReject'])->name('production.ford.auto-debit.reject')->where('id', '[0-9]+');

  // Universal Storage Fallback (Fix for servers without symlink support)
  Route::get('/storage/{path}', [FileController::class, 'serve'])->where('path', '.*');

  // Division Dashboards
  Route::get('/marketing', [MarketingController::class, 'dashboard'])->name('marketing.dashboard')->middleware('division.access:marketing');
  Route::get('/production', [DashboardController::class, 'production'])->name('production.dashboard')->middleware('division.access:production');

  // Production Division Routes - Protected by division.access:production middleware
  Route::middleware(['division.access:production'])->group(function () {
    Route::get('/production/approval-queue', [App\Http\Controllers\ProductionController::class, 'approvalQueue'])->name('production.approval-queue');
    Route::get('/production/my-requests', [App\Http\Controllers\ProductionController::class, 'myRequests'])->name('production.my-requests');
    Route::get('/production/executive-dashboard', [App\Http\Controllers\ProductionController::class, 'executiveDashboard'])->name('production.executive-dashboard.index');
    Route::get('/production/sales-order/{id}/review', [App\Http\Controllers\ProductionController::class, 'reviewSalesOrder'])->name('production.sales-order.detail');
    Route::post('/production/sales-order/{id}/approve', [App\Http\Controllers\ProductionController::class, 'approveSalesOrder'])->name('production.sales-order.approve');
    Route::post('/production/sales-order/{id}/reject', [App\Http\Controllers\ProductionController::class, 'rejectSalesOrder'])->name('production.sales-order.reject');
    Route::match(['get', 'post'], '/production/team-stock-transfer/{id}/approve', [App\Http\Controllers\ProductionController::class, 'approveTeamStockTransfer'])->name('production.team-stock-transfer.approve');
    Route::post('/production/team-stock-transfer/{id}/reject', [App\Http\Controllers\ProductionController::class, 'rejectTeamStockTransfer'])->name('production.team-stock-transfer.reject');
    
    // Production Inventory Management
    Route::prefix('production/inventory')->name('production.inventory.')->group(function () {
      Route::get('/master', [InventoryController::class, 'masterInventory'])->name('master');
      Route::post('/items/store', [InventoryController::class, 'storeInventoryCategoryItem'])->name('items.store');
      Route::post('/stock/transfer', [InventoryController::class, 'transferWarehouseStock'])->name('stock.transfer');
      Route::post('/stock/update', [InventoryController::class, 'updateWarehouseStockDirectly'])->name('stock.update');
      Route::get('/overview', [InventoryController::class, 'overview'])->name('overview');
      Route::get('/add-stock', [InventoryController::class, 'addStock'])->name('add-stock');
      Route::post('/store-stock', [InventoryController::class, 'storeStock'])->name('store-stock');
      Route::get('/received', [InventoryController::class, 'received'])->name('received');
      Route::get('/get-product-details/{id}', [InventoryController::class, 'getProductDetails'])->name('get-product-details');
      Route::put('/update-transaction/{id}', [InventoryController::class, 'updateTransaction'])->name('update-transaction');
      Route::delete('/destroy-transaction/{id}', [InventoryController::class, 'destroyTransaction'])->name('destroy-transaction');
      Route::delete('/destroy-product/{id}', [InventoryController::class, 'destroyProduct'])->name('destroy-product');
      Route::post('/process-add-stock', [InventoryController::class, 'processAddStock'])->name('process-add-stock');
      Route::post('/update-stock/{bookId}', [InventoryController::class, 'updateStockDirectly'])->name('update-stock');
      Route::post('/update-index-stock/{indexId}', [InventoryController::class, 'updateIndexStockDirectly'])->name('update-index-stock');
      Route::post('/update-bundle-stock/{bundleId}', [InventoryController::class, 'updateBundleStockDirectly'])->name('update-bundle-stock');
      Route::post('/reconcile-stock', [InventoryController::class, 'reconcileStock'])->name('reconcile-stock');
      Route::post('/mark-as-lost', [InventoryController::class, 'markAsLost'])->name('mark-as-lost');
    });

    // Production Costing
    Route::prefix('production/costing')->name('production.costing.')->group(function () {
      Route::get('/', [App\Http\Controllers\Production\ProductionCostingController::class, 'index'])->name('index');
      Route::get('/{id}', [App\Http\Controllers\Production\ProductionCostingController::class, 'show'])->name('show');
      Route::post('/calculate', [App\Http\Controllers\Production\ProductionCostingController::class, 'calculate'])->name('calculate');
      Route::post('/store', [App\Http\Controllers\Production\ProductionCostingController::class, 'store'])->name('store');
      Route::post('/sync', [App\Http\Controllers\Production\ProductionCostingController::class, 'syncFromProductionErp'])->name('sync');
    });

    // Production Fixed Assets
    Route::prefix('production/assets')->name('production.assets.')->group(function () {
      Route::get('/', [App\Http\Controllers\Production\ProductionFixedAssetController::class, 'index'])->name('index');
      Route::get('/{id}', [App\Http\Controllers\Production\ProductionFixedAssetController::class, 'show'])->name('show');
      Route::post('/store', [App\Http\Controllers\Production\ProductionFixedAssetController::class, 'store'])->name('store');
      Route::put('/{id}', [App\Http\Controllers\Production\ProductionFixedAssetController::class, 'update'])->name('update');
      Route::delete('/{id}', [App\Http\Controllers\Production\ProductionFixedAssetController::class, 'destroy'])->name('destroy');
      Route::post('/maintenance/store', [App\Http\Controllers\Production\ProductionFixedAssetController::class, 'storeMaintenanceLog'])->name('maintenance.store');
    });

    // Site Management Routes
    Route::prefix('production/sites')->name('production.sites.')->group(function () {
      Route::post('/store', [App\Http\Controllers\Production\SiteController::class, 'store'])->name('store');
      Route::post('/add-stock', [App\Http\Controllers\Production\SiteController::class, 'addStock'])->name('add-stock');
      Route::post('/update/{id}', [App\Http\Controllers\Production\SiteController::class, 'updateSite'])->name('update');
      Route::post('/delete/{id}', [App\Http\Controllers\Production\SiteController::class, 'deleteSite'])->name('delete');
      Route::get('/{siteId}/inventory', [App\Http\Controllers\Production\SiteController::class, 'getInventory'])->name('inventory');
      Route::post('/transfer', [App\Http\Controllers\Production\SiteController::class, 'transfer'])->name('transfer');
      Route::post('/transfer-batch', [App\Http\Controllers\Production\SiteController::class, 'transferBatch'])->name('transfer-batch');
      Route::post('/approve-transfer/{id}', [App\Http\Controllers\Production\SiteController::class, 'approveTransfer'])->name('approve-transfer');
      Route::post('/reject-transfer/{id}', [App\Http\Controllers\Production\SiteController::class, 'rejectTransfer'])->name('reject-transfer');
    });

    // Production Logistic Management
    Route::prefix('production/logistic')->name('production.logistic.')->group(function () {
      Route::get('/pick-lists', [App\Http\Controllers\Production\LogisticController::class, 'pickListList'])->name('pick-list-list');
      Route::get('/pick-lists/{id}', [App\Http\Controllers\Production\LogisticController::class, 'showPickList'])->name('pick-list-details');
      Route::get('/pick-list-management', [App\Http\Controllers\Production\LogisticController::class, 'pickListManagement'])->name('pick-list-management'); // Keep for form
      Route::post('/pick-list/save', [App\Http\Controllers\Production\LogisticController::class, 'savePickedItems'])->name('pick-list.save');
      Route::post('/mark-as-gathered/{id?}', [App\Http\Controllers\Production\LogisticController::class, 'markAsGathered'])->name('mark-as-gathered');
      Route::get('/pick-list/{id}/delete', [App\Http\Controllers\Production\LogisticController::class, 'deletePickList'])->name('pick-list-delete');
      
      // Pick-up Requests CRUD
      Route::get('/pickup-requests', [App\Http\Controllers\Production\LogisticController::class, 'pickupRequestsIndex'])->name('pickup-requests.index');
      Route::get('/pickup-requests/create', [App\Http\Controllers\Production\LogisticController::class, 'pickupRequestsCreate'])->name('pickup-requests.create');
      Route::post('/pickup-requests', [App\Http\Controllers\Production\LogisticController::class, 'pickupRequestsStore'])->name('pickup-requests.store');
      Route::get('/pickup-requests/{id}/edit', [App\Http\Controllers\Production\LogisticController::class, 'pickupRequestsEdit'])->name('pickup-requests.edit');
      Route::put('/pickup-requests/{id}', [App\Http\Controllers\Production\LogisticController::class, 'pickupRequestsUpdate'])->name('pickup-requests.update');
      Route::delete('/pickup-requests/{id}', [App\Http\Controllers\Production\LogisticController::class, 'pickupRequestsDestroy'])->name('pickup-requests.destroy');
      Route::post('/pickup-requests/{id}/approve', [App\Http\Controllers\Production\LogisticController::class, 'pickupRequestsApprove'])->name('pickup-requests.approve');
      Route::post('/pickup-requests/{id}/reject', [App\Http\Controllers\Production\LogisticController::class, 'pickupRequestsReject'])->name('pickup-requests.reject');
      Route::post('/pickup-requests/{id}/complete', [App\Http\Controllers\Production\LogisticController::class, 'pickupRequestsComplete'])->name('pickup-requests.complete');
      Route::post('/pickup-requests/{id}/assign-driver', [App\Http\Controllers\Production\LogisticController::class, 'pickupRequestsAssignDriver'])->name('pickup-requests.assign-driver');
      
      Route::get('/shipping-label/{id}', [App\Http\Controllers\Production\LogisticController::class, 'shippingLabel'])->name('shipping-label');

      // Packing Management
      Route::get('/packing-management', [App\Http\Controllers\Production\LogisticController::class, 'packingManagement'])->name('packing-management');
      Route::get('/packing/{id}/data', [App\Http\Controllers\Production\LogisticController::class, 'getPackingOrderData'])->name('packing-order-data');
      Route::post('/packing/save', [App\Http\Controllers\Production\LogisticController::class, 'savePackingData'])->name('packing.save');
      Route::post('/packing/save-remarks', [App\Http\Controllers\Production\LogisticController::class, 'savePackingRemarks'])->name('packing.save-remarks');
      Route::post('/packing/set-ready-for-pickup', [App\Http\Controllers\Production\LogisticController::class, 'setReadyForPickup'])->name('packing.set-ready-for-pickup');
      Route::post('/packing/mark-as-gathered', [App\Http\Controllers\Production\LogisticController::class, 'markPackedOrdersAsGathered'])->name('packing.mark-as-gathered');
      Route::match(['post', 'delete'], '/packing/delete-order/{id}', [App\Http\Controllers\Production\LogisticController::class, 'deletePackingOrder'])->name('packing.delete-order');

      // Delivery & Fleet management
      Route::get('/delivery-scheduling', [App\Http\Controllers\Production\LogisticController::class, 'deliveryScheduling'])->name('delivery-scheduling');
      Route::post('/delivery-scheduling/set-as-pickup', [App\Http\Controllers\Production\LogisticController::class, 'setAsPickup'])->name('set-as-pickup');
      Route::post('/delivery-scheduling/{id}/delivered', [App\Http\Controllers\Production\LogisticController::class, 'markAsDelivered'])->name('mark-as-delivered');
      Route::post('/delivery-scheduling/{id}/picked-up', [App\Http\Controllers\Production\LogisticController::class, 'markAsPickedUp'])->name('mark-as-picked-up');
      Route::post('/delivery-scheduling/{id}/move-back-to-delivery', [App\Http\Controllers\Production\LogisticController::class, 'moveBackToDelivery'])->name('move-back-to-delivery');
      Route::post('/delivery-scheduling/{id}/assign-driver', [App\Http\Controllers\Production\LogisticController::class, 'assignDriver'])->name('assign-driver');
      Route::post('/delivery-scheduling/{id}/approve-driver', [App\Http\Controllers\Production\LogisticController::class, 'approveDriverAssignment'])->name('approve-driver');
      Route::post('/delivery-scheduling/{id}/reject-driver', [App\Http\Controllers\Production\LogisticController::class, 'rejectDriverAssignment'])->name('reject-driver');
      Route::get('/delivery-scheduling/{id}/transmittal', [App\Http\Controllers\Production\LogisticController::class, 'printTransmittal'])->name('print-transmittal');
      Route::get('/driver-dashboard', [App\Http\Controllers\Production\LogisticController::class, 'driverDashboard'])->name('driver-dashboard');
      Route::get('/delivery-tracking', [App\Http\Controllers\Production\LogisticController::class, 'deliveryTracking'])->name('delivery-tracking');
      
      // Delivery Receipts (Restored)
      Route::get('/delivery-receipt-list', [App\Http\Controllers\Production\LogisticController::class, 'deliveryReceiptList'])->name('delivery-receipt-list');
      Route::match(['get', 'post'], '/delivery-receipt-bulk-print', [App\Http\Controllers\Production\LogisticController::class, 'bulkPrintDR'])->name('delivery-receipt.bulk-print');
      Route::get('/delivery-receipt/{id?}', [App\Http\Controllers\Production\LogisticController::class, 'deliveryReceipt'])->name('delivery-receipt');
      Route::post('/delivery-receipt/import-excel', [App\Http\Controllers\Production\LogisticController::class, 'importDeliveryReceiptFromExcel'])->name('delivery-receipt.import-excel');
      Route::post('/mark-as-dr-prepared/{id}', [App\Http\Controllers\Production\LogisticController::class, 'markAsDRPrepared'])->name('mark-as-dr-prepared');
      Route::post('/approve-dr/{id}', [App\Http\Controllers\Production\LogisticController::class, 'approveDR'])->name('approve-dr');
      Route::post('/complete-dr/{id}', [App\Http\Controllers\Production\LogisticController::class, 'completeDR'])->name('complete-dr');
      Route::post('/link-consignment-to-si/{id}', [App\Http\Controllers\Production\LogisticController::class, 'linkConsignmentToSI'])->name('link-consignment-to-si');
      Route::post('/request-reconsignment/{id}', [App\Http\Controllers\Production\LogisticController::class, 'requestReconsignment'])->name('request-reconsignment');
      Route::post('/return-consignment/{id}', [App\Http\Controllers\Production\LogisticController::class, 'returnConsignment'])->name('return-consignment');
      Route::post('/move-to-ar/{id}', [App\Http\Controllers\Production\LogisticController::class, 'moveToAR'])->name('move-to-ar');
      Route::post('/move-to-cr/{id}', [App\Http\Controllers\Production\LogisticController::class, 'moveToCR'])->name('move-to-cr');
      Route::post('/move-to-si/{id}', [App\Http\Controllers\Production\LogisticController::class, 'fastMoveToSI'])->name('move-to-si');
      Route::post('/delivery-receipt/{id}/update-pick-qty', [App\Http\Controllers\Production\LogisticController::class, 'updateDrPickQty'])->name('delivery-receipt.update-pick-qty');
      Route::post('/upload-dr-pop/{id}', [App\Http\Controllers\Production\LogisticController::class, 'uploadDRProofOfPayment'])->name('upload-dr-pop');

      // Area Consignment (Logistics)
      Route::get('/area-consignment', [App\Http\Controllers\Production\LogisticController::class, 'areaConsignment'])->name('area-consignment');

      // Purchase Orders
      Route::get('/purchase-order-list', [App\Http\Controllers\Production\LogisticController::class, 'purchaseOrderList'])->name('purchase-order-list');
      Route::get('/purchase-order', [App\Http\Controllers\Production\LogisticController::class, 'purchaseOrder'])->name('purchase-order');
      Route::get('/purchase-order/{id}', [App\Http\Controllers\Production\LogisticController::class, 'showPurchaseOrder'])->name('purchase-order.show');
      Route::post('/purchase-order', [App\Http\Controllers\Production\LogisticController::class, 'storePurchaseOrder'])->name('purchase-order.store');
      Route::delete('/purchase-order/{id}', [App\Http\Controllers\Production\LogisticController::class, 'destroyPurchaseOrder'])->name('purchase-order.destroy');

      // Receiving Reports
      Route::get('/receiving-report-list', [App\Http\Controllers\Production\LogisticController::class, 'receivingReportList'])->name('receiving-report-list');
      Route::get('/receiving-report/create/{po_id?}', [App\Http\Controllers\Production\LogisticController::class, 'createReceivingReport'])->name('receiving-report.create');
      Route::get('/receiving-report/{id}', [App\Http\Controllers\Production\LogisticController::class, 'showReceivingReport'])->name('receiving-report.show');
      Route::post('/receiving-report', [App\Http\Controllers\Production\LogisticController::class, 'storeReceivingReport'])->name('receiving-report.store');
      
      // Freight Quotation
      Route::get('/freight-quotation', [App\Http\Controllers\Production\LogisticController::class, 'freightQuotation'])->name('freight-quotation');
      Route::post('/freight-quotation', [App\Http\Controllers\Production\LogisticController::class, 'storeFreightQuotation'])->name('freight-quotation.store');
      
      // Pending Freight Quotations from Marketing
      Route::get('/freight-quotations', [App\Http\Controllers\Production\LogisticController::class, 'pendingFreightQuotations'])->name('pending-freight-quotations');
      Route::get('/freight-quotations/{freightQuotation}', [App\Http\Controllers\Production\LogisticController::class, 'showFreightQuotation'])->name('show-freight-quotation');
      Route::post('/freight-quotations/{freightQuotation}/approve', [App\Http\Controllers\Production\LogisticController::class, 'approveFreightQuotation'])->name('approve-freight-quotation');
      Route::post('/freight-quotations/{freightQuotation}/reject', [App\Http\Controllers\Production\LogisticController::class, 'rejectFreightQuotation'])->name('reject-freight-quotation');
      Route::put('/freight-quotations/{freightQuotation}/cargo-items', [App\Http\Controllers\Production\LogisticController::class, 'updateCargoItems'])->name('update-cargo-items');
      
      // Delivery Form View
      Route::get('/delivery-form/{id}', [App\Http\Controllers\Production\LogisticController::class, 'viewDeliveryForm'])->name('view-delivery-form');

      // Team Stock Transfer Fulfillment Routes
      Route::post('/team-stock-transfer/{id}/save-pick-items', [App\Http\Controllers\Production\LogisticController::class, 'saveTeamStockPickItems'])->name('team-stock-transfer.save-pick-items');
      Route::post('/team-stock-transfer/{id}/save-pack-items', [App\Http\Controllers\Production\LogisticController::class, 'saveTeamStockPackItems'])->name('team-stock-transfer.save-pack-items');
      Route::post('/team-stock-transfer/{id}/update', [App\Http\Controllers\Production\LogisticController::class, 'updateTeamStockTransfer'])->name('team-stock-transfer.update');
      Route::match(['get', 'post'], '/team-stock-transfer/{id}/complete-pick', [App\Http\Controllers\Production\LogisticController::class, 'completeTeamStockPickList'])->name('team-stock-transfer.complete-pick');
      Route::match(['get', 'post'], '/team-stock-transfer/{id}/complete-pack', [App\Http\Controllers\Production\LogisticController::class, 'completeTeamStockPacking'])->name('team-stock-transfer.complete-pack');
      Route::match(['get', 'post', 'delete'], '/team-stock-transfer/{id}/delete', [App\Http\Controllers\Production\LogisticController::class, 'deleteTeamStockTransfer'])->name('team-stock-transfer.delete');

      // Acknowledgement Receipt (Area Sales Consignment import)
      Route::get('/acknowledgement-receipt', [App\Http\Controllers\Production\LogisticController::class, 'acknowledgementReceipt'])->name('acknowledgement-receipt');
      Route::post('/acknowledgement-receipt/import', [App\Http\Controllers\Production\LogisticController::class, 'importAcknowledgementReceipt'])->name('acknowledgement-receipt.import');
      Route::post('/acknowledgement-receipt/import-excel', [App\Http\Controllers\Production\LogisticController::class, 'importAcknowledgementReceiptFromExcel'])->name('acknowledgement-receipt.import-excel');
    });

    // Production DTO Management
    Route::prefix('production/dto')->name('production.dto.')->group(function () {
      Route::get('/job-request-form', [App\Http\Controllers\Production\DTOController::class, 'jobRequestForm'])->name('job-request-form');
      Route::post('/job-request-form', [App\Http\Controllers\Production\DTOController::class, 'storeJobRequest'])->name('job-request-form.store');
      Route::put('/job-request-form/{id}', [App\Http\Controllers\Production\DTOController::class, 'updateJobRequest'])->name('job-request-form.update');
      Route::delete('/job-request-form/{id}', [App\Http\Controllers\Production\DTOController::class, 'destroyJobRequest'])->name('job-request-form.destroy');
    });

    Route::prefix('production/ford')->name('production.ford.')->group(function () {
      Route::get('/auto-debit', [App\Http\Controllers\Production\FORDController::class, 'autoDebitIndex'])->name('auto-debit');
      Route::get('/auto-debit/create', [App\Http\Controllers\Production\FORDController::class, 'autoDebitCreate'])->name('auto-debit.create');
      Route::post('/auto-debit/store', [App\Http\Controllers\Production\FORDController::class, 'autoDebitStore'])->name('auto-debit.store');
      Route::get('/client-payment-posting', [App\Http\Controllers\Production\FORDController::class, 'clientPaymentPosting'])->name('client-payment-posting');
      Route::post('/client-payment-posting', [App\Http\Controllers\Production\FORDController::class, 'storeClientPaymentPosting'])->name('client-payment-posting.store');
      Route::get('/eford-payout', [App\Http\Controllers\Production\FORDController::class, 'eFordPayout'])->name('eford-payout');
      Route::post('/eford-payout', [App\Http\Controllers\Production\FORDController::class, 'storeEfordPayout'])->name('eford-payout.store');
      Route::get('/eford-payout/customers/{id}/unpaid-invoices', [App\Http\Controllers\Production\FORDController::class, 'getUnpaidInvoices'])->name('eford-payout.unpaid-invoices');
      Route::get('/payment-request', [App\Http\Controllers\Production\FORDController::class, 'paymentRequest'])->name('payment-request');
      Route::post('/payment-request', [App\Http\Controllers\Accounting\PaymentRequestController::class, 'store'])->name('payment-request.store');
      Route::get('/purchase-order', [App\Http\Controllers\Production\FORDController::class, 'purchaseOrder'])->name('purchase-order');
      Route::post('/purchase-order', [App\Http\Controllers\Production\FORDController::class, 'storeFordPurchaseOrder'])->name('purchase-order.store');
      Route::get('/request-for-quotation', [App\Http\Controllers\Production\FORDController::class, 'requestForQuotation'])->name('request-for-quotation');
      Route::get('/freight-quotation', [App\Http\Controllers\Production\FORDController::class, 'freightQuotationIndex'])->name('freight-quotation.index');
      Route::get('/freight-quotation/create', [App\Http\Controllers\Production\FORDController::class, 'freightQuotationCreate'])->name('freight-quotation.create');
      Route::post('/freight-quotation/store', [App\Http\Controllers\Production\FORDController::class, 'freightQuotationStore'])->name('freight-quotation.store');
      Route::get('/freight-quotation/{id}', [App\Http\Controllers\Production\FORDController::class, 'freightQuotationShow'])->name('freight-quotation.show');
      Route::get('/sales-order', [App\Http\Controllers\Production\FORDController::class, 'salesOrder'])->name('sales-order');
      Route::get('/sales-order/create', [App\Http\Controllers\Production\FORDController::class, 'salesOrderCreate'])->name('sales-order.create');
      Route::get('/sales-order/products/search', [App\Http\Controllers\Production\FORDController::class, 'searchProducts'])->name('sales-order.products-search');
      Route::post('/sales-order/store', [App\Http\Controllers\Production\FORDController::class, 'storeSalesOrder'])->name('sales-order.store');
      Route::get('/sales-order/{id}/review', [App\Http\Controllers\Production\FORDController::class, 'reviewSalesOrder'])->name('sales-order.review');
      Route::post('/sales-order/{id}/approve', [App\Http\Controllers\Production\FORDController::class, 'approveSalesOrder'])->name('sales-order.approve');
      Route::post('/sales-order/{id}/reject', [App\Http\Controllers\Production\FORDController::class, 'rejectSalesOrder'])->name('sales-order.reject');
      Route::get('/transmittal', [App\Http\Controllers\Production\FORDController::class, 'transmittal'])->name('transmittal');
    });

    // Top-level aliases for Production Sales Order review from approval queue
    Route::get('/production/sales-order/{id}/review', [App\Http\Controllers\Production\FORDController::class, 'reviewSalesOrder'])->name('production.sales-order.review');
    Route::get('/production/sales-order/{id}/detail', [App\Http\Controllers\Production\FORDController::class, 'reviewSalesOrder'])->name('production.sales-order.detail');
    Route::post('/production/sales-order/{id}/approve', [App\Http\Controllers\Production\FORDController::class, 'approveSalesOrder'])->name('production.sales-order.approve');
    Route::post('/production/sales-order/{id}/reject', [App\Http\Controllers\Production\FORDController::class, 'rejectSalesOrder'])->name('production.sales-order.reject');
    Route::get('/production/sales-order-redirect', function() {
        return redirect()->route('production.ford.sales-order');
    })->name('sales-order');

    // Production Printing Services
    Route::prefix('production/printing')->name('production.printing.')->group(function () {
      Route::get('/request-payment-to-printer', [App\Http\Controllers\Production\PrintingServicesController::class, 'requestPaymentToPrinter'])->name('request-payment-to-printer');
    });
  });

  // User Management - Accessible to all authenticated users
  Route::get('/super-admin/users', [SuperAdminController::class, 'users'])->name('users.index');
  Route::post('/super-admin/users', [SuperAdminController::class, 'store'])->name('users.store');
  Route::put('/super-admin/users/{id}', [SuperAdminController::class, 'update'])->name('users.update');
  Route::delete('/super-admin/users/{id}', [SuperAdminController::class, 'destroy'])->name('users.destroy');

  // Super Admin Pages - Protected by super.admin middleware (Roles, Divisions, Settings only)
  Route::middleware(['super.admin'])->group(function () {
    Route::get('/super-admin/roles', [SuperAdminController::class, 'roles'])->name('roles.index');
    Route::put('/super-admin/roles/{id}', [SuperAdminController::class, 'updateRole'])->name('roles.update');
    Route::put('/super-admin/roles/{id}/permissions', [SuperAdminController::class, 'updateRolePermissions'])->name('roles.permissions.update');
    Route::get('/super-admin/divisions', [SuperAdminController::class, 'divisions'])->name('divisions.index');
    Route::get('/super-admin/settings', [SuperAdminController::class, 'settings'])->name('settings.index');
    Route::post('/super-admin/payment-settings', [App\Http\Controllers\POSController::class, 'savePaymentSettings'])->name('settings.payment.save');
    // Maintenance: Clear Data (Super Admin only)
    Route::get('/super-admin/maintenance/clear-data', [App\Http\Controllers\MaintenanceController::class, 'showClearData'])->name('admin.maintenance.clear-data');
    Route::post('/super-admin/maintenance/clear-data', [App\Http\Controllers\MaintenanceController::class, 'clearData'])->name('admin.maintenance.clear-data.post');
  });

  // Marketing Pages - Protected by division.access:marketing middleware
  Route::middleware(['division.access:marketing'])->group(function () {
    Route::get('/marketing/customers-export', [CustomerController::class, 'exportExcel'])->name('marketing.customers.export');
    Route::get('/marketing/customers-template', [CustomerController::class, 'downloadTemplate'])->name('marketing.customers.template');
    Route::post('/marketing/customers-import', [CustomerController::class, 'importExcel'])->name('marketing.customers.import');

    Route::post('/marketing/customers/bulk-delete', [CustomerController::class, 'destroyBatch'])->name('marketing.customers.bulk-delete');

    Route::resource('marketing/customers', CustomerController::class)->names([
        'index' => 'marketing.customers',
        'create' => 'marketing.customers.create',
        'store' => 'marketing.customers.store',
        'show' => 'marketing.customers.show',
        'edit' => 'marketing.customers.edit',
        'update' => 'marketing.customers.update',
        'destroy' => 'marketing.customers.destroy',
    ]);
    Route::get('/marketing/customers/{customer}/history', [CustomerController::class, 'getTransactionHistory'])->name('marketing.customers.history');
    Route::post('/marketing/customers/{customer}/manual-status', [CustomerController::class, 'updateManualStatus'])->name('marketing.customers.update-status');
    Route::post('/marketing/customers/{customer}/transactions/{salesOrder}/pay', [CustomerController::class, 'recordPayment'])->name('marketing.customers.record-payment');
    Route::get('/marketing/customers/{customer}/transactions/{salesOrder}/payments', [CustomerController::class, 'getPaymentHistory'])->name('marketing.customers.payment-history');

    Route::resource('marketing/companies', CompanyController::class)->names([
        'index' => 'marketing.companies',
        'create' => 'marketing.companies.create',
        'store' => 'marketing.companies.store',
        'show' => 'marketing.companies.show',
        'edit' => 'marketing.companies.edit',
        'update' => 'marketing.companies.update',
        'destroy' => 'marketing.companies.destroy',
    ]);
    Route::get('/marketing/companies/branches/download-template', [CompanyController::class, 'downloadBranchTemplate'])->name('marketing.companies.branches.download-template');
    Route::post('/marketing/companies/{company}/branches/import-excel', [CompanyController::class, 'importBranchesExcel'])->name('marketing.companies.branches.import-excel');
    Route::get('/marketing/approval-queue', [MarketingController::class, 'approvalQueue'])->name('marketing.approval-queue');
    Route::get('/marketing/my-requests', [MarketingController::class, 'myRequests'])->name('marketing.my-requests');
    
    // Book Management (Master Registry)
    Route::get('/marketing/book-list', [MarketingController::class, 'products'])->name('marketing.products');
    Route::get('/marketing/book-list/export', [MarketingController::class, 'exportBooks'])->name('marketing.books.export');
    Route::get('/marketing/book-bundles', [MarketingController::class, 'bundles'])->name('marketing.bundles');
    Route::get('/marketing/book-list/check-sku', [MarketingController::class, 'checkSku'])->name('marketing.books.check-sku');
    Route::get('/marketing/book-list/import-template', [MarketingController::class, 'downloadTemplate'])->name('marketing.books.import-template');
    Route::post('/marketing/book-list/import', [MarketingController::class, 'importBooks'])->name('marketing.books.import');
    Route::post('/marketing/book-list/store-book', [MarketingController::class, 'storeBook'])->name('marketing.books.store');
    Route::get('/marketing/book-list/{id}/edit-book', [MarketingController::class, 'editBook'])->name('marketing.books.edit');
    Route::post('/marketing/book-list/{id}/update-book', [MarketingController::class, 'updateBook'])->name('marketing.books.update');
    Route::delete('/marketing/book-list/{id}', [MarketingController::class, 'destroyBook'])->name('marketing.books.destroy');

    // Non-Books Management
    Route::get('/marketing/non-books', [MarketingController::class, 'nonBooks'])->name('marketing.non-books');
    Route::get('/marketing/non-books/import-template', [MarketingController::class, 'downloadTemplate'])->name('marketing.non-books.import-template');
    Route::post('/marketing/non-books/import', [MarketingController::class, 'importNonBooks'])->name('marketing.non-books.import');
    Route::post('/marketing/non-books/store', [MarketingController::class, 'storeNonBook'])->name('marketing.non-books.store');
    Route::get('/marketing/non-books/{id}/edit', [MarketingController::class, 'editNonBook'])->name('marketing.non-books.edit');
    Route::post('/marketing/non-books/{id}/update', [MarketingController::class, 'updateNonBook'])->name('marketing.non-books.update');
    Route::delete('/marketing/non-books/{id}', [MarketingController::class, 'destroyNonBook'])->name('marketing.non-books.destroy');

    // Book Bundles
    Route::get('/marketing/book-bundles/export', [MarketingController::class, 'exportBookBundles'])->name('marketing.bundles.export');
    Route::get('/marketing/book-bundles/search-books', [MarketingController::class, 'searchBooks'])->name('marketing.bundles.search-books');
    Route::post('/marketing/book-bundles/store', [MarketingController::class, 'storeBundle'])->name('marketing.bundles.store');
    Route::get('/marketing/book-bundles/{id}/edit', [MarketingController::class, 'editBundle'])->name('marketing.bundles.edit');
    Route::post('/marketing/book-bundles/{id}/update', [MarketingController::class, 'updateBundle'])->name('marketing.bundles.update');
    Route::delete('/marketing/book-bundles/{id}', [MarketingController::class, 'destroyBundle'])->name('marketing.bundles.destroy');

    // Book Indexing
    Route::get('/marketing/book-indices', [MarketingController::class, 'bookIndices'])->name('marketing.indices');
    Route::get('/marketing/book-indices/export', [MarketingController::class, 'exportBookIndices'])->name('marketing.indices.export');
    Route::post('/marketing/book-indices/store', [MarketingController::class, 'storeIndex'])->name('marketing.indices.store');
    Route::get('/marketing/book-indices/{id}/edit', [MarketingController::class, 'editIndex'])->name('marketing.indices.edit');
    Route::post('/marketing/book-indices/{id}/update', [MarketingController::class, 'updateIndex'])->name('marketing.indices.update');
    Route::delete('/marketing/book-indices/{id}', [MarketingController::class, 'destroyIndex'])->name('marketing.indices.destroy');

    // POS Listed Products
    Route::post('/marketing/pos-products/store', [MarketingController::class, 'storeProduct'])->name('marketing.products.store');
    Route::get('/marketing/pos-products/{id}/edit', [MarketingController::class, 'editProduct'])->name('marketing.products.edit');
    Route::post('/marketing/pos-products/{id}/update', [MarketingController::class, 'updateProduct'])->name('marketing.products.update');
    Route::delete('/marketing/pos-products/{id}', [MarketingController::class, 'destroyProduct'])->name('marketing.products.destroy');

    // Dynamic Categories
    Route::get('/marketing/book-categories', [MarketingController::class, 'getCategories'])->name('marketing.categories.index');
    Route::post('/marketing/book-categories', [MarketingController::class, 'storeCategory'])->name('marketing.categories.store');
    Route::get('/marketing/book-categories/{id}/subcategories', [MarketingController::class, 'getSubcategories'])->name('marketing.categories.subcategories');
    Route::delete('/marketing/book-categories/{id}', [MarketingController::class, 'destroyCategory'])->name('marketing.categories.destroy');

    // Consignment Management
    Route::prefix('marketing/consignment')->name('marketing.consignment.')->group(function () {
        Route::get('/', [App\Http\Controllers\Marketing\ConsignmentController::class, 'index'])->name('index');
        Route::post('/owners', [App\Http\Controllers\Marketing\ConsignmentController::class, 'storeOwner'])->name('owners.store');
        Route::get('/owners/{id}', [App\Http\Controllers\Marketing\ConsignmentController::class, 'getOwnerDetails'])->name('owners.show');
        Route::put('/owners/{id}', [App\Http\Controllers\Marketing\ConsignmentController::class, 'updateOwner'])->name('owners.update');
        Route::delete('/owners/{id}', [App\Http\Controllers\Marketing\ConsignmentController::class, 'destroyOwner'])->name('owners.destroy');
        Route::post('/books/{id}/update', [App\Http\Controllers\Marketing\ConsignmentController::class, 'updateBookConsignment'])->name('books.update');
        Route::post('/owners/{id}/settle', [App\Http\Controllers\Marketing\ConsignmentController::class, 'settle'])->name('owners.settle');
    });

    // Area Sales
    Route::get('/marketing/sales-orders/export', [MarketingController::class, 'exportSalesOrders'])->name('marketing.sales-orders.export');
    Route::get('/marketing/sales-orders/{id}/export-excel', [MarketingController::class, 'exportSingleSalesOrder'])->name('marketing.sales-orders.export-single');
    Route::get('/marketing/sales-orders/list', [MarketingController::class, 'salesOrdersList'])->name('marketing.sales-orders.list');
    Route::get('/marketing/sales-orders/products-by-team', [MarketingController::class, 'getProductsByTeam'])->name('marketing.sales-orders.products-by-team');
    Route::get('/marketing/sales-orders/create', [MarketingController::class, 'createSalesOrder'])->name('marketing.sales-orders.create');
    Route::post('/marketing/sales-orders/store', [MarketingController::class, 'storeSalesOrder'])->name('marketing.sales-orders.store');
    Route::get('/marketing/sales-orders', function() { return redirect()->route('marketing.sales-orders.list'); });
    Route::get('/marketing/sales-orders/{id}/edit', [MarketingController::class, 'editSalesOrder'])->name('marketing.sales-orders.edit');
    Route::put('/marketing/sales-orders/{id}', [MarketingController::class, 'updateSalesOrder'])->name('marketing.sales-orders.update');
    Route::post('/marketing/sales-orders/{id}/approve', [MarketingController::class, 'approveSalesOrder'])->name('marketing.sales-orders.approve');
    Route::post('/marketing/sales-orders/{id}/reject', [MarketingController::class, 'rejectSalesOrder'])->name('marketing.sales-orders.reject');
    Route::post('/marketing/sales-orders/{id}/quick-update', [MarketingController::class, 'updateSalesOrderQuick'])->name('marketing.sales-orders.quick-update');
    Route::post('/marketing/sales-orders/{id}/proceed-to-final', [MarketingController::class, 'proceedToFinalSalesOrder'])->name('marketing.sales-orders.proceed-to-final');
    Route::delete('/marketing/sales-orders/{id}', [MarketingController::class, 'destroySalesOrder'])->name('marketing.sales-orders.destroy');
    Route::get('/marketing/sales-order/{id?}', [MarketingController::class, 'salesOrderDetail'])->name('marketing.sales-orders.detail');
    Route::get('/marketing/sales-orders/{id}/shipping-label', [MarketingController::class, 'shippingLabel'])->name('marketing.sales-orders.shipping-label');
    Route::get('/marketing/sales-orders/{id}/print-invoice', [MarketingController::class, 'printSalesInvoiceForm'])->name('marketing.sales-orders.print-invoice');
    Route::get('/marketing/direct-invoice-website', [MarketingController::class, 'directInvoiceWebsite'])->name('marketing.direct-invoice.website');
    Route::post('/marketing/direct-invoice-website', [MarketingController::class, 'storeDirectInvoice'])->name('marketing.direct-invoice.website.store');
    Route::get('/marketing/direct-invoice-website/list', [MarketingController::class, 'directInvoiceList'])->name('marketing.direct-invoice.website.list');
    Route::post('/marketing/direct-invoice-website/{id}/approve', [MarketingController::class, 'approveDirectInvoice'])->name('marketing.direct-invoice.website.approve');
    Route::get('/marketing/direct-invoice-ecom', [MarketingController::class, 'directInvoiceEcom'])->name('marketing.direct-invoice.ecom');
    Route::post('/marketing/direct-invoice-ecom', [MarketingController::class, 'storeDirectInvoiceEcom'])->name('marketing.direct-invoice.ecom.store');
    Route::post('/marketing/direct-invoice-ecom/{id}/approve', [MarketingController::class, 'approveDirectInvoiceEcom'])->name('marketing.direct-invoice.ecom.approve');
    
    // Freight Quotations (Area Sales)
    Route::prefix('marketing/freight-quotations')->name('marketing.freight-quotations.')->group(function () {
        Route::get('/', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'list'])->name('list');
        Route::get('/create', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'store'])->name('store');
        Route::get('/{freightQuotation}', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'show'])->name('show');
        Route::post('/{freightQuotation}/create-so-directly', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'createSalesOrderFromApprovedQuotation'])->name('create-so-directly');
        Route::get('/{freightQuotation}/proceed-to-so', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'proceedToSalesOrder'])->name('proceed-to-so');
        Route::post('/{freightQuotation}/create-so', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'createSalesOrderFromQuotation'])->name('create-so');
        Route::get('/{freightQuotation}/logistics-response', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'viewLogisticsResponse'])->name('logistics-response');
        Route::delete('/{freightQuotation}', [App\Http\Controllers\Marketing\FreightQuotationController::class, 'destroy'])->name('destroy');
    });
    
    Route::get('/marketing/sales-orders/{id}', [MarketingController::class, 'salesOrderDetail'])->name('marketing.sales-orders.show');
    
    Route::get('/marketing/acknowledgement-receipt', [MarketingController::class, 'acknowledgementReceipt'])->name('marketing.acknowledgement-receipt');
    Route::get('/marketing/credit-memo', [MarketingController::class, 'creditMemo'])->name('marketing.credit-memo');
    Route::get('/marketing/proof-of-payment', [MarketingController::class, 'proofOfPayment'])->name('marketing.proof-of-payment');
    Route::get('/marketing/consignment-inventory', [MarketingController::class, 'consignmentInventoryIndex'])->name('marketing.consignment-inventory');

    // Area Sales Team Stocks Routes
    Route::get('/marketing/area-sales/team-stocks', [MarketingController::class, 'teamStocksIndex'])->name('marketing.area-sales.team-stocks.index');
    Route::get('/marketing/area-sales/team-stocks/template', [MarketingController::class, 'downloadTeamStockTransferTemplate'])->name('marketing.area-sales.team-stocks.template');
    Route::post('/marketing/area-sales/team-stocks/parse-excel', [MarketingController::class, 'parseTeamStockTransferExcel'])->name('marketing.area-sales.team-stocks.parse-excel');
    Route::post('/marketing/area-sales/team-stocks/transfer', [MarketingController::class, 'storeTeamStockTransfer'])->name('marketing.area-sales.team-stocks.transfer');
    Route::post('/marketing/area-sales/team-stocks/return', [MarketingController::class, 'storeTeamStockReturn'])->name('marketing.area-sales.team-stocks.return');
    Route::post('/marketing/area-sales/team-stocks/{id}/approve', [MarketingController::class, 'approveTeamStockTransferByMarketing'])->name('marketing.area-sales.team-stocks.approve');
    Route::post('/marketing/area-sales/team-stocks/{id}/reject', [MarketingController::class, 'rejectTeamStockTransferByMarketing'])->name('marketing.area-sales.team-stocks.reject');
    Route::get('/marketing/sales-invoice', [MarketingController::class, 'salesInvoice'])->name('marketing.sales-invoice');
    Route::get('/marketing/pick-list-management', [MarketingController::class, 'pickListManagement'])->name('marketing.pick-list.management');
    Route::get('/marketing/pick-lists', [MarketingController::class, 'pickLists'])->name('marketing.pick-lists');
    Route::get('/marketing/delivery-receipt', [MarketingController::class, 'deliveryReceipt'])->name('marketing.delivery-receipt');
    Route::get('/marketing/delivery-receipt-list', [MarketingController::class, 'deliveryReceiptList'])->name('marketing.delivery-receipt.list');
    Route::get('/marketing/order-fulfillment', [MarketingController::class, 'orderFulfillment'])->name('marketing.order-fulfillment');
    Route::get('/marketing/packing-scheduling', [MarketingController::class, 'packingScheduling'])->name('marketing.packing-scheduling');
    Route::get('/marketing/delivery-scheduling', [MarketingController::class, 'deliveryScheduling'])->name('marketing.delivery-scheduling');
    Route::get('/marketing/delivery-tracking', [MarketingController::class, 'deliveryTracking'])->name('marketing.delivery-tracking');
    Route::get('/marketing/sales-reports', [MarketingController::class, 'salesReports'])->name('marketing.sales-reports');
    Route::get('/marketing/territory-management', [MarketingController::class, 'territoryManagement'])->name('marketing.territory-management');


    // Direct Sales
    Route::get('/marketing/pos-sale', [MarketingController::class, 'posSale'])->name('marketing.pos.sale');
    Route::get('/marketing/pos-products', [MarketingController::class, 'posProducts'])->name('marketing.pos.products');

    // NBS PO Import
    Route::get('/marketing/nbs-import', [App\Http\Controllers\Marketing\NBSImportController::class, 'index'])->name('marketing.nbs-import.index');
    Route::get('/marketing/nbs-import/template', [App\Http\Controllers\Marketing\NBSImportController::class, 'downloadTemplate'])->name('marketing.nbs-import.template');
    Route::post('/marketing/nbs-import/process', [App\Http\Controllers\Marketing\NBSImportController::class, 'process'])->name('marketing.nbs-import.process');
    Route::get('/marketing/nbs-consignment-receipt/{id}', [App\Http\Controllers\Marketing\NBSImportController::class, 'viewReceipt'])->name('marketing.nbs-consignment-receipt');
    Route::get('/marketing/companies/{company}/branches', [App\Http\Controllers\CompanyController::class, 'getBranches'])->name('marketing.companies.branches');

    // POS Order Processing
    Route::post('/marketing/pos/process-order', [App\Http\Controllers\POSController::class, 'processOrder'])->name('marketing.pos.process-order');
    Route::get('/marketing/pos/orders', [App\Http\Controllers\POSController::class, 'getOrders'])->name('marketing.pos.orders');
    Route::get('/marketing/pos/orders/{id}', [App\Http\Controllers\POSController::class, 'getOrderDetails'])->name('marketing.pos.order-details');
    Route::get('/marketing/pos/payment-settings', [App\Http\Controllers\POSController::class, 'getPaymentSettings'])->name('marketing.pos.payment-settings');
    Route::post('/marketing/pos/lookup-barcode', [App\Http\Controllers\POSController::class, 'lookupByBarcode'])->name('marketing.pos.lookup-barcode');
    Route::post('/marketing/pos/process-ecom-order', [App\Http\Controllers\POSController::class, 'processEcomOrder'])->name('marketing.pos.process-ecom-order');
    Route::get('/marketing/pos/next-si-number', [App\Http\Controllers\POSController::class, 'getNextSiNumberResponse'])->name('marketing.pos.next-si-number');

    // E-Com
    Route::get('/marketing/ecom-pos', [MarketingController::class, 'ecomPos'])->name('marketing.ecom.pos');

    // Ads and Promo
    Route::get('/marketing/ads-promo/crpr', [MarketingController::class, 'crpr'])->name('marketing.ads-promo.crpr');
    Route::get('/marketing/ads-promo/sponsors', [MarketingController::class, 'sponsors'])->name('marketing.ads-promo.sponsors');
    Route::post('/marketing/ads-promo/sponsors/store', [MarketingController::class, 'storeSponsor'])->name('marketing.ads-promo.sponsors.store');
    Route::post('/marketing/ads-promo/sponsors/{id}/update', [MarketingController::class, 'updateSponsor'])->name('marketing.ads-promo.sponsors.update');
    Route::delete('/marketing/ads-promo/sponsors/{id}', [MarketingController::class, 'destroySponsor'])->name('marketing.ads-promo.sponsors.destroy');

    // Suppliers & Purchases
    Route::get('/marketing/suppliers', [SupplierController::class, 'index'])->name('marketing.suppliers');
    Route::resource('suppliers', SupplierController::class)->except(['index']);
    Route::get('/marketing/purchase-orders', [MarketingController::class, 'purchaseOrders'])->name('marketing.purchase-orders');
  });

  Route::post('/employee/cash-advance/store', [App\Http\Controllers\EmployeeCashAdvanceController::class, 'store'])->name('employee.cash-advance.store');
  Route::put('/employee/cash-advance/{id}', [App\Http\Controllers\EmployeeCashAdvanceController::class, 'update'])->name('employee.cash-advance.update');
  Route::post('/stock-transfers/{id}/approve', [SiteController::class, 'approveTransfer'])->name('stock-transfers.approve');
  Route::post('/stock-transfers/{id}/reject', [SiteController::class, 'rejectTransfer'])->name('stock-transfers.reject');
  Route::post('/stock-transfers/{id}/accounting-approve', [SiteController::class, 'approveAccountingTransfer'])->name('stock-transfers.accounting-approve');
  Route::post('/stock-transfers/{id}/assign-logistics', [SiteController::class, 'assignLogisticsTransfer'])->name('stock-transfers.assign-logistics');
  Route::post('/stock-transfers/{id}/complete', [SiteController::class, 'completeLogisticsTransfer'])->name('stock-transfers.complete');
  Route::post('/my-requests/cctv-requests', [App\Http\Controllers\Admin\MIS\CCTVReqController::class, 'store'])->name('user.cctv-requests.store');
  Route::put('/my-requests/cctv-requests/{id}', [App\Http\Controllers\Admin\MIS\CCTVReqController::class, 'update'])->name('user.cctv-requests.update');
  Route::delete('/my-requests/cctv-requests/{cctvRequest}', [App\Http\Controllers\Admin\MIS\CCTVReqController::class, 'destroy'])->name('user.cctv-requests.destroy');
    
  // Admin & Finance Division - Protected by division.access:admin-finance middleware
  Route::middleware(['division.access:admin-finance'])->prefix('admin-finance')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminFinanceController::class, 'dashboard'])->name('admin-finance.dashboard');
    Route::get('/my-requests', [App\Http\Controllers\AdminFinanceController::class, 'myRequests'])->name('admin-finance.my-requests');
    Route::get('/approval-queue', [App\Http\Controllers\AdminFinanceController::class, 'approvalQueue'])->name('admin-finance.approval-queue');
    Route::get('/memo-list', [App\Http\Controllers\AdminFinanceController::class, 'memoList'])->name('admin-finance.memo-list');
    Route::get('/sales-order/{id}/review', [App\Http\Controllers\AdminFinanceController::class, 'reviewSalesOrder'])->name('admin-finance.sales-order.detail');
    Route::post('/sales-order/{id}/approve', [App\Http\Controllers\AdminFinanceController::class, 'approveSalesOrder'])->name('admin-finance.sales-order.approve');
    Route::post('/sales-order/{id}/reject', [App\Http\Controllers\AdminFinanceController::class, 'rejectSalesOrder'])->name('admin-finance.sales-order.reject');
    Route::post('/sales-order/{id}/upload-attachment', [App\Http\Controllers\AdminFinanceController::class, 'uploadSalesOrderAttachment'])->name('admin-finance.sales-order.upload-attachment');
    Route::post('/sales-order/{id}/update-payment-method', [App\Http\Controllers\AdminFinanceController::class, 'updatePaymentMethod'])->name('admin-finance.sales-order.update-payment-method');
    Route::post('/sales-order/{id}/update-si-number', [App\Http\Controllers\AdminFinanceController::class, 'updateSiNumber'])->name('admin-finance.sales-order.update-si-number');

    // Accounting
    Route::prefix('accounting')->group(function () {
      Route::get('/sales-invoice', [App\Http\Controllers\AdminFinanceController::class, 'salesInvoice'])->name('admin-finance.accounting.sales-invoice');
      Route::get('/sales-invoice/{id}/prepare', [App\Http\Controllers\AdminFinanceController::class, 'prepareSalesInvoice'])->name('admin-finance.accounting.sales-invoice.prepare');
      Route::post('/sales-invoice/{id}/store', [App\Http\Controllers\AdminFinanceController::class, 'storeSalesInvoice'])->name('admin-finance.accounting.sales-invoice.store');
      Route::post('/sales-invoice/{id}/sign', [App\Http\Controllers\AdminFinanceController::class, 'signSalesInvoice'])->name('admin-finance.accounting.sales-invoice.sign');
      Route::post('/sales-invoice/bulk-finalize', [App\Http\Controllers\AdminFinanceController::class, 'bulkFinalizeInvoices'])->name('admin-finance.accounting.sales-invoice.bulk-finalize');
      Route::post('/sales-invoice/bulk-set-paid', [App\Http\Controllers\AdminFinanceController::class, 'bulkSetPaid'])->name('admin-finance.accounting.sales-invoice.bulk-set-paid');
      Route::get('/sales-invoice/bulk-print', [App\Http\Controllers\AdminFinanceController::class, 'bulkPrintSalesInvoice'])->name('admin-finance.accounting.sales-invoice.bulk-print');
      Route::get('/sales-invoice/{id}/print', [App\Http\Controllers\AdminFinanceController::class, 'printSalesInvoice'])->name('admin-finance.accounting.sales-invoice.print');
      Route::post('/sales-invoice/{id}/revert-to-dr', [App\Http\Controllers\AdminFinanceController::class, 'revertSalesInvoiceToDR'])->name('admin-finance.accounting.sales-invoice.revert-to-dr');
      Route::get('/complimentary-receipt', [App\Http\Controllers\AdminFinanceController::class, 'complimentaryReceiptIndex'])->name('admin-finance.accounting.complimentary-receipt');
      Route::get('/acknowledgement-receipt/{id}/prepare', [App\Http\Controllers\AdminFinanceController::class, 'prepareAR'])->name('admin-finance.accounting.ar.prepare');
      Route::post('/acknowledgement-receipt/{id}/store', [App\Http\Controllers\AdminFinanceController::class, 'storeAR'])->name('admin-finance.accounting.ar.store');
      Route::get('/acknowledgement-receipt/{id}/print', [App\Http\Controllers\AdminFinanceController::class, 'printAR'])->name('admin-finance.accounting.ar.print');
      Route::get('/check-voucher', [App\Http\Controllers\AdminFinanceController::class, 'checkVoucherIndex'])->name('admin-finance.check-voucher');
      Route::get('/check-voucher/create', [App\Http\Controllers\AdminFinanceController::class, 'checkVoucher'])->name('admin-finance.check-voucher.create');
      Route::post('/check-voucher', [App\Http\Controllers\AdminFinanceController::class, 'storeCheckVoucher'])->name('admin-finance.check-voucher.store');
      Route::get('/check-voucher/{id}', [App\Http\Controllers\AdminFinanceController::class, 'showCheckVoucher'])->name('admin-finance.check-voucher.show');

      Route::get('/materials-requisition', [App\Http\Controllers\AdminFinanceController::class, 'materialsRequisition'])->name('admin-finance.accounting.materials-requisition');
      Route::post('/materials-requisition', [App\Http\Controllers\AdminFinanceController::class, 'storeMaterialRequisition'])->name('admin-finance.accounting.materials-requisition.store');
      Route::get('/materials-requisition/{id}', [App\Http\Controllers\AdminFinanceController::class, 'showMaterialRequisition'])->name('admin-finance.accounting.materials-requisition.show');
      Route::delete('/materials-requisition/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroyMaterialRequisition'])->name('admin-finance.accounting.materials-requisition.destroy');
      Route::get('/material-requests', [App\Http\Controllers\AdminFinanceController::class, 'materialRequestsIncoming'])->name('admin-finance.accounting.material-requests.incoming');
      Route::get('/expense-management', [App\Http\Controllers\AdminFinanceController::class, 'expenseManagement'])->name('admin-finance.accounting.expense-management');
      Route::get('/cash-advance/create', [App\Http\Controllers\AdminFinanceController::class, 'createCashAdvance'])->name('admin-finance.accounting.cash-advance.create');
      
      // Payment Requests Processing
      Route::get('/payment-requests', [App\Http\Controllers\Accounting\PaymentRequestController::class, 'index'])->name('admin-finance.accounting.payment-requests');
      Route::post('/payment-requests/{id}/schedule', [App\Http\Controllers\Accounting\PaymentRequestController::class, 'schedule'])->name('admin-finance.accounting.payment-requests.schedule');
      Route::post('/payment-requests/{id}/pay', [App\Http\Controllers\Accounting\PaymentRequestController::class, 'markAsPaid'])->name('admin-finance.accounting.payment-requests.pay');

      // E-FORD Payouts Processing
      Route::get('/eford-payouts', [App\Http\Controllers\Production\FORDController::class, 'accountingIndex'])->name('admin-finance.accounting.eford-payouts');
      Route::get('/eford-payouts/{id}', [App\Http\Controllers\Production\FORDController::class, 'accountingShow'])->name('admin-finance.accounting.eford-payouts.show');
      Route::get('/eford-payouts/{id}/download/{index}', [App\Http\Controllers\Production\FORDController::class, 'downloadAttachment'])->name('admin-finance.accounting.eford-payouts.download');

      // Ecom Payouts (Direct Invoice Ecom)
      Route::get('/ecom-payouts', [App\Http\Controllers\AdminFinanceController::class, 'ecomPayoutsIndex'])->name('admin-finance.accounting.ecom-payouts.index');
      Route::post('/ecom-payouts/{id}/toggle', [App\Http\Controllers\AdminFinanceController::class, 'ecomPayoutsToggle'])->name('admin-finance.accounting.ecom-payouts.toggle');

      // Office Supplies
      Route::prefix('office-supplies')->name('admin-finance.accounting.office-supplies.')->group(function () {
          Route::get('/', [App\Http\Controllers\Accounting\OfficeSupplyController::class, 'index'])->name('index');
          Route::post('/', [App\Http\Controllers\Accounting\OfficeSupplyController::class, 'store'])->name('store');
          Route::put('/{id}', [App\Http\Controllers\Accounting\OfficeSupplyController::class, 'update'])->name('update');
          Route::delete('/{id}', [App\Http\Controllers\Accounting\OfficeSupplyController::class, 'destroy'])->name('destroy');
          Route::post('/{id}/add-stock', [App\Http\Controllers\Accounting\OfficeSupplyController::class, 'addStock'])->name('add-stock');
      });

      // Expenses
      Route::prefix('expenses')->name('admin-finance.accounting.expenses.')->group(function () {
          Route::get('/', [App\Http\Controllers\Accounting\ExpenseController::class, 'index'])->name('index');
          Route::post('/', [App\Http\Controllers\Accounting\ExpenseController::class, 'store'])->name('store');
          Route::put('/{id}', [App\Http\Controllers\Accounting\ExpenseController::class, 'update'])->name('update');
          Route::delete('/{id}', [App\Http\Controllers\Accounting\ExpenseController::class, 'destroy'])->name('destroy');
      });

      // General Journal Entry
      Route::prefix('journal')->name('accounting.journal.')->group(function () {
          Route::get('/', [App\Http\Controllers\Accounting\JournalEntryController::class, 'index'])->name('index');
          Route::get('/create', [App\Http\Controllers\Accounting\JournalEntryController::class, 'create'])->name('create');
          Route::post('/', [App\Http\Controllers\Accounting\JournalEntryController::class, 'store'])->name('store');
          Route::get('/{id}', [App\Http\Controllers\Accounting\JournalEntryController::class, 'show'])->name('show');
          Route::delete('/{id}', [App\Http\Controllers\Accounting\JournalEntryController::class, 'destroy'])->name('destroy');
      });

      // Cashier Petty Cash Approvals
      Route::get('/cashier', [App\Http\Controllers\Accounting\PettyCashController::class, 'cashierIndex'])->name('admin-finance.accounting.cashier.index');
      Route::post('/cashier/petty-cash/{id}/approve', [App\Http\Controllers\Accounting\PettyCashController::class, 'cashierApprove'])->name('admin-finance.accounting.cashier.approve');
      Route::post('/cashier/petty-cash/{id}/reject', [App\Http\Controllers\Accounting\PettyCashController::class, 'cashierReject'])->name('admin-finance.accounting.cashier.reject');
      Route::post('/cashier/petty-cash/{id}/complete', [App\Http\Controllers\Accounting\PettyCashController::class, 'cashierComplete'])->name('admin-finance.accounting.cashier.complete');

      // Payment Posting
      Route::get('/payment-posting', [App\Http\Controllers\Production\FORDController::class, 'paymentPostingIndex'])->name('admin-finance.accounting.payment-posting.index');
      Route::post('/payment-posting/store', [App\Http\Controllers\Production\FORDController::class, 'storeDirectPaymentPosting'])->name('admin-finance.accounting.payment-posting.store');
      Route::get('/payment-posting/{id}', [App\Http\Controllers\Production\FORDController::class, 'paymentPostingShow'])->name('admin-finance.accounting.payment-posting.show');
      Route::post('/payment-posting/{id}/post', [App\Http\Controllers\Production\FORDController::class, 'paymentPostingPost'])->name('admin-finance.accounting.payment-posting.post');

      // Auto Debits
      Route::get('/auto-debits', [App\Http\Controllers\Production\FORDController::class, 'accountingAutoDebitIndex'])->name('admin-finance.accounting.auto-debits.index');
      Route::get('/auto-debits/{id}', [App\Http\Controllers\Production\FORDController::class, 'accountingAutoDebitShow'])->name('admin-finance.accounting.auto-debits.show');

      // Delivery Receipts in Accounting
      Route::get('/delivery-receipt-list', [App\Http\Controllers\Production\LogisticController::class, 'deliveryReceiptList'])->name('admin-finance.accounting.delivery-receipt-list');
      Route::get('/delivery-receipt/{id}/print', [App\Http\Controllers\AdminFinanceController::class, 'printDeliveryReceipt'])->name('admin-finance.accounting.delivery-receipt.print');
      Route::match(['get', 'post'], '/delivery-receipt-bulk-print', [App\Http\Controllers\Production\LogisticController::class, 'bulkPrintDR'])->name('admin-finance.accounting.delivery-receipt.bulk-print');
      Route::get('/delivery-receipt/{id?}', [App\Http\Controllers\Production\LogisticController::class, 'deliveryReceipt'])->name('admin-finance.accounting.delivery-receipt');
      Route::post('/move-to-si/{id}', [App\Http\Controllers\Production\LogisticController::class, 'fastMoveToSI'])->name('admin-finance.accounting.delivery-receipt.move-to-si');

      // Production Costing in Accounting
      Route::get('/production-costing', [App\Http\Controllers\AdminFinanceController::class, 'productionCosting'])->name('admin-finance.accounting.production-costing');
      Route::get('/production-costing/{id}', [App\Http\Controllers\AdminFinanceController::class, 'showProductionCosting'])->name('admin-finance.accounting.production-costing.show');
    });

    // Chart of Accounts
    Route::get('/chart-of-accounts', [App\Http\Controllers\AdminFinanceController::class, 'chartOfAccounts'])->name('admin-finance.accounting.chart-of-accounts');
    Route::get('/chart-of-accounts/{id}/ledger', [App\Http\Controllers\AdminFinanceController::class, 'getAccountLedger'])->name('admin-finance.accounting.chart-of-accounts.ledger');
    Route::post('/chart-of-accounts', [App\Http\Controllers\AdminFinanceController::class, 'storeChartOfAccount'])->name('admin-finance.accounting.chart-of-accounts.store');
    Route::put('/chart-of-accounts/{id}', [App\Http\Controllers\AdminFinanceController::class, 'updateChartOfAccount'])->name('admin-finance.accounting.chart-of-accounts.update');
    Route::delete('/chart-of-accounts/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroyChartOfAccount'])->name('admin-finance.accounting.chart-of-accounts.destroy');
    Route::post('/chart-of-accounts/toggle', [App\Http\Controllers\AdminFinanceController::class, 'toggleAccountStatus'])->name('admin-finance.accounting.chart-of-accounts.toggle');

    // Account Groups
    Route::post('/account-groups', [App\Http\Controllers\AdminFinanceController::class, 'storeAccountGroup'])->name('admin-finance.accounting.account-groups.store');
    Route::put('/account-groups/{id}', [App\Http\Controllers\AdminFinanceController::class, 'updateAccountGroup'])->name('admin-finance.accounting.account-groups.update');
    Route::delete('/account-groups/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroyAccountGroup'])->name('admin-finance.accounting.account-groups.destroy');
    Route::get('/account-groups/{id}/accounts', [App\Http\Controllers\AdminFinanceController::class, 'getAccountGroupAccounts'])->name('admin-finance.accounting.account-groups.accounts');
    Route::get('/sales-management', [App\Http\Controllers\AdminFinanceController::class, 'salesManagement'])->name('admin-finance.accounting.sales-management');
    Route::get('/accounts-receivable', [App\Http\Controllers\AdminFinanceController::class, 'accountsReceivable'])->name('admin-finance.accounting.accounts-receivable');
    Route::get('/accounts-payable', [App\Http\Controllers\AdminFinanceController::class, 'accountsPayable'])->name('admin-finance.accounting.accounts-payable');
    Route::get('/inventory-valuation', [App\Http\Controllers\Accounting\InventoryValuationController::class, 'index'])->name('admin-finance.accounting.inventory-valuation');
    Route::get('/general-ledger', [App\Http\Controllers\Accounting\GeneralLedgerController::class, 'index'])->name('admin-finance.accounting.general-ledger');
    Route::get('/procurement', [App\Http\Controllers\Accounting\ProcurementController::class, 'index'])->name('admin-finance.accounting.procurement');

    // Sales Returns
    Route::prefix('sales-returns')->name('admin-finance.accounting.sales-returns.')->group(function () {
        Route::get('/', [App\Http\Controllers\Accounting\SalesReturnController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Accounting\SalesReturnController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Accounting\SalesReturnController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Accounting\SalesReturnController::class, 'show'])->name('show');
        Route::delete('/{id}', [App\Http\Controllers\Accounting\SalesReturnController::class, 'destroy'])->name('destroy');
    });

    // Purchase Returns
    Route::prefix('purchase-returns')->name('admin-finance.accounting.purchase-returns.')->group(function () {
        Route::get('/', [App\Http\Controllers\Accounting\PurchaseReturnController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Accounting\PurchaseReturnController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Accounting\PurchaseReturnController::class, 'store'])->name('store');
        Route::get('/{id}', [App\Http\Controllers\Accounting\PurchaseReturnController::class, 'show'])->name('show');
        Route::delete('/{id}', [App\Http\Controllers\Accounting\PurchaseReturnController::class, 'destroy'])->name('destroy');
    });
    Route::post('/accounts-payable/suppliers', [App\Http\Controllers\AdminFinanceController::class, 'storeSupplier'])->name('admin-finance.accounting.accounts-payable.supplier.store');
    Route::post('/accounts-payable/suppliers/{id}/update', [App\Http\Controllers\AdminFinanceController::class, 'updateSupplier'])->name('admin-finance.accounting.accounts-payable.supplier.update');
    Route::delete('/accounts-payable/suppliers/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroySupplier'])->name('admin-finance.accounting.accounts-payable.supplier.destroy');

    Route::post('/accounts-payable/invoices', [App\Http\Controllers\AdminFinanceController::class, 'storeSupplierInvoice'])->name('admin-finance.accounting.accounts-payable.invoice.store');
    Route::post('/accounts-payable/invoices/{id}/update', [App\Http\Controllers\AdminFinanceController::class, 'updateSupplierInvoice'])->name('admin-finance.accounting.accounts-payable.invoice.update');
    Route::delete('/accounts-payable/invoices/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroySupplierInvoice'])->name('admin-finance.accounting.accounts-payable.invoice.destroy');

    Route::post('/accounts-payable/payments', [App\Http\Controllers\AdminFinanceController::class, 'storeSupplierPayment'])->name('admin-finance.accounting.accounts-payable.payment.store');
    Route::delete('/accounts-payable/payments/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroySupplierPayment'])->name('admin-finance.accounting.accounts-payable.payment.destroy');
    Route::post('/customers/{id}/update-rep', [App\Http\Controllers\AdminFinanceController::class, 'updateCustomerRep'])->name('admin-finance.customers.update-rep');

    Route::match(['get', 'post'], '/team-stocks/{id}/approve', [App\Http\Controllers\AdminFinanceController::class, 'approveTeamStockTransferByAdminFinance'])->name('admin-finance.team-stocks.approve');
    Route::match(['get', 'post'], '/team-stocks/{id}/reject', [App\Http\Controllers\AdminFinanceController::class, 'rejectTeamStockTransferByAdminFinance'])->name('admin-finance.team-stocks.reject');

    // Credit Collection
    Route::prefix('credit-collection')->group(function () {
      Route::get('/billing', [App\Http\Controllers\AdminFinanceController::class, 'billing'])->name('admin-finance.credit-collection.billing');
      Route::get('/reconsignments', [App\Http\Controllers\AdminFinanceController::class, 'reconsignmentsList'])->name('admin-finance.credit-collection.reconsignment.index');
      Route::get('/billing/create/{id}', [App\Http\Controllers\AdminFinanceController::class, 'createAccountStatement'])->name('admin-finance.credit-collection.billing.create');
      Route::get('/billing/manual-create', [App\Http\Controllers\AdminFinanceController::class, 'createManualSOA'])->name('admin-finance.credit-collection.billing.manual-create');
      Route::post('/billing/store', [App\Http\Controllers\AdminFinanceController::class, 'storeAccountStatement'])->name('admin-finance.credit-collection.billing.store');
      Route::post('/billing/{id}/update-status', [App\Http\Controllers\AdminFinanceController::class, 'updateStatementStatus'])->name('admin-finance.credit-collection.billing.update-status');
      Route::get('/billing/edit/{id}', [App\Http\Controllers\AdminFinanceController::class, 'editAccountStatement'])->name('admin-finance.credit-collection.billing.edit');
      Route::get('/billing/{id}', [App\Http\Controllers\AdminFinanceController::class, 'showAccountStatement'])->name('admin-finance.credit-collection.billing.show');
      Route::post('/billing/compile', [App\Http\Controllers\AdminFinanceController::class, 'compileStatements'])->name('admin-finance.credit-collection.billing.compile');

      Route::get('/freight-billing/create', [App\Http\Controllers\AdminFinanceController::class, 'createFreightBill'])->name('admin-finance.credit-collection.freight-billing.create');
      Route::post('/freight-billing/store', [App\Http\Controllers\AdminFinanceController::class, 'storeFreightBill'])->name('admin-finance.credit-collection.freight-billing.store');
      Route::post('/freight-billing/{id}/update-status', [App\Http\Controllers\AdminFinanceController::class, 'updateFreightBillStatus'])->name('admin-finance.credit-collection.freight-billing.update-status');
      Route::delete('/freight-billing/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroyFreightBill'])->name('admin-finance.credit-collection.freight-billing.destroy');
      Route::post('/freight-billing/compile', [App\Http\Controllers\AdminFinanceController::class, 'compileFreightBills'])->name('admin-finance.credit-collection.freight-billing.compile');

      Route::get('/reports', [App\Http\Controllers\AdminFinanceController::class, 'reports'])->name('admin-finance.credit-collection.reports');
      Route::get('/invoice', [App\Http\Controllers\AdminFinanceController::class, 'invoice'])->name('admin-finance.credit-collection.invoice');
      Route::post('/invoice/{id}/finalize', [App\Http\Controllers\AdminFinanceController::class, 'finalizeInvoice'])->name('admin-finance.credit-collection.invoice.finalize');

      // JV Requests
      Route::get('/jv-requests', [AdminFinanceController::class, 'jvRequests'])->name('admin-finance.credit-collection.jv-requests.index');
      Route::get('/jv-requests/create', [AdminFinanceController::class, 'createJvRequest'])->name('admin-finance.credit-collection.jv-requests.create');
      Route::post('/jv-requests/store', [AdminFinanceController::class, 'storeJvRequest'])->name('admin-finance.credit-collection.jv-requests.store');
      Route::get('/jv-requests/{id}', [AdminFinanceController::class, 'showJvRequest'])->name('admin-finance.credit-collection.jv-requests.show');
      Route::put('/jv-requests/{id}/approve', [AdminFinanceController::class, 'approveJvRequest'])->name('admin-finance.credit-collection.jv-requests.approve');
      Route::put('/jv-requests/{id}/manager-approve', [AdminFinanceController::class, 'managerApproveJvRequest'])->name('admin-finance.credit-collection.jv-requests.manager-approve');
      Route::put('/jv-requests/{id}/reject', [AdminFinanceController::class, 'rejectJvRequest'])->name('admin-finance.credit-collection.jv-requests.reject');
      Route::get('/jv-requests/{id}/print', [AdminFinanceController::class, 'printJvRequest'])->name('admin-finance.credit-collection.jv-requests.print');
      Route::get('/jv-requests/{id}/print-summary', [AdminFinanceController::class, 'printSummaryRequest'])->name('admin-finance.credit-collection.jv-requests.print-summary');
      Route::get('/jv-requests/{id}/prepare-adjustment', [AdminFinanceController::class, 'prepareAdjustmentRequest'])->name('admin-finance.credit-collection.jv-requests.prepare-adjustment');
      Route::post('/jv-requests/{id}/update-adjustment', [AdminFinanceController::class, 'updateAdjustmentRequest'])->name('admin-finance.credit-collection.jv-requests.update-adjustment');
      // Reconsignment
      Route::post('/reconsignment/{id}/approve', [App\Http\Controllers\AdminFinanceController::class, 'approveReconsignment'])->name('admin-finance.credit-collection.reconsignment.approve');
      Route::post('/reconsignment/{id}/reject', [App\Http\Controllers\AdminFinanceController::class, 'rejectReconsignment'])->name('admin-finance.credit-collection.reconsignment.reject');
    });

    // Expenses
    Route::prefix('expenses')->group(function () {
      Route::get('/cash-advance-liquidation', [App\Http\Controllers\AdminFinanceController::class, 'cashAdvanceLiquidation'])->name('admin-finance.expenses.cash-advance-liquidation');
      Route::post('/cash-advance-liquidation', [App\Http\Controllers\AdminFinanceController::class, 'storeLiquidation'])->name('admin-finance.expenses.cash-advance-liquidation.store');
    });

    // GSD
    Route::prefix('gsd')->group(function () {
      Route::get('/asset-management', [App\Http\Controllers\Admin\GSD\AssetController::class, 'index'])->name('admin-finance.gsd.asset-management');
      Route::resource('/asset-requests', App\Http\Controllers\Admin\GSD\AssetController::class)->names('admin-finance.gsd.asset-requests');
      Route::get('/job-orders', [App\Http\Controllers\AdminFinanceController::class, 'gsdJobOrders'])->name('admin-finance.gsd.job-orders');
      Route::put('/job-orders/{type}/{id}/update-status', [App\Http\Controllers\AdminFinanceController::class, 'gsdUpdateJobOrderStatus'])->name('admin-finance.gsd.job-orders.update-status');
      Route::resource('/material-requests', App\Http\Controllers\Admin\GSD\MaterialReqController::class)->names('admin-finance.gsd.material-requests');
      Route::post('/material-requests/{id}/submit', [App\Http\Controllers\Admin\GSD\MaterialReqController::class, 'submit'])->name('admin-finance.gsd.material-requests.submit');
      Route::resource('/service-requests', App\Http\Controllers\Admin\GSD\ServiceReqController::class)->names('admin-finance.gsd.service-requests');
      Route::post('/service-requests/{id}/submit', [App\Http\Controllers\Admin\GSD\ServiceReqController::class, 'submit'])->name('admin-finance.gsd.service-requests.submit');
    });

    // HR
    Route::prefix('hr')->group(function () {
      Route::get('/job-orders', [App\Http\Controllers\AdminFinanceController::class, 'hrJobOrders'])->name('admin-finance.hr.job-orders');
    });

    // MIS
    Route::prefix('mis')->group(function () {
      Route::get('/job-orders', [App\Http\Controllers\AdminFinanceController::class, 'jobOrders'])->name('admin-finance.mis.job-orders');
      Route::put('/job-orders/{type}/{id}/update-status', [App\Http\Controllers\AdminFinanceController::class, 'misUpdateJobOrderStatus'])->name('admin-finance.mis.job-orders.update-status');
      Route::resource('/cctv-requests', App\Http\Controllers\Admin\MIS\CCTVReqController::class)->names('admin-finance.mis.cctv-requests');
      Route::resource('/material-requests', App\Http\Controllers\Admin\MIS\MaterialReqController::class)->names('admin-finance.mis.material-requests');
      Route::resource('/qb-requests', App\Http\Controllers\Admin\MIS\QBReqController::class)->names('admin-finance.mis.qb-requests');
      Route::resource('/undertime-requests', App\Http\Controllers\Admin\MIS\UndertimeReqController::class)->names('admin-finance.mis.undertime-requests');
      Route::resource('/service-requests', App\Http\Controllers\Admin\MIS\ServiceReqController::class)->names('admin-finance.mis.service-requests');
    });

    // Service Requests (Department-Specific)
    Route::prefix('service-requests')->name('admin-finance.service-requests.')->group(function () {
      Route::get('/create', [App\Http\Controllers\AdminFinanceController::class, 'createServiceRequest'])->name('create');
      Route::post('/store', [App\Http\Controllers\AdminFinanceController::class, 'storeServiceRequest'])->name('store');
    });
  });
});

// Petty Cash Vouchers - Accessible by permission only, not division-restricted
Route::prefix('admin-finance')->group(function () {
  Route::get('/petty-cash', [App\Http\Controllers\Accounting\PettyCashController::class, 'index'])->name('admin-finance.petty-cash.index');
  Route::get('/petty-cash/create', [App\Http\Controllers\Accounting\PettyCashController::class, 'create'])->name('admin-finance.petty-cash.create');
  Route::post('/petty-cash', [App\Http\Controllers\Accounting\PettyCashController::class, 'store'])->name('admin-finance.petty-cash.store');
  Route::get('/petty-cash/summary', [App\Http\Controllers\Accounting\PettyCashController::class, 'summary'])->name('admin-finance.petty-cash.summary');
  Route::post('/petty-cash/liquidate', [App\Http\Controllers\Accounting\PettyCashController::class, 'liquidate'])->name('admin-finance.petty-cash.liquidate');
  Route::get('/petty-cash/{id}', [App\Http\Controllers\Accounting\PettyCashController::class, 'show'])->name('admin-finance.petty-cash.show');
  Route::post('/petty-cash/{id}/upload-proof', [App\Http\Controllers\Accounting\PettyCashController::class, 'uploadProof'])->name('admin-finance.petty-cash.upload-proof');
  Route::delete('/petty-cash/{id}', [App\Http\Controllers\Accounting\PettyCashController::class, 'destroy'])->name('admin-finance.petty-cash.destroy');
});

// Freight Vouchers - Accessible by permission only, not division-restricted
Route::prefix('admin-finance')->group(function () {
  Route::get('/freight-voucher', [App\Http\Controllers\Accounting\FreightVoucherController::class, 'index'])->name('admin-finance.freight-voucher.index');
  Route::get('/freight-voucher/create', [App\Http\Controllers\Accounting\FreightVoucherController::class, 'create'])->name('admin-finance.freight-voucher.create');
  Route::post('/freight-voucher', [App\Http\Controllers\Accounting\FreightVoucherController::class, 'store'])->name('admin-finance.freight-voucher.store');
  Route::get('/freight-voucher/{id}', [App\Http\Controllers\Accounting\FreightVoucherController::class, 'show'])->name('admin-finance.freight-voucher.show');
  Route::delete('/freight-voucher/{id}', [App\Http\Controllers\Accounting\FreightVoucherController::class, 'destroy'])->name('admin-finance.freight-voucher.destroy');
});

// Admin & Finance Investments Module Routes
Route::prefix('admin-finance/investments')->name('admin-finance.investments.')->middleware(['auth', 'division.access:admin-finance'])->group(function () {
  Route::get('/', [App\Http\Controllers\AdminFinanceController::class, 'investments'])->name('index');
  Route::get('/{id}', [App\Http\Controllers\AdminFinanceController::class, 'showInvestment'])->name('show');
  Route::post('/store', [App\Http\Controllers\AdminFinanceController::class, 'storeInvestment'])->name('store');
  Route::delete('/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroyInvestment'])->name('destroy');
  Route::post('/transaction/store', [App\Http\Controllers\AdminFinanceController::class, 'storeInvestmentTransaction'])->name('transaction.store');
});

// Admin & Finance Donations Module Routes
Route::prefix('admin-finance/donations')->name('admin-finance.donations.')->middleware(['auth', 'division.access:admin-finance'])->group(function () {
  Route::get('/', [App\Http\Controllers\AdminFinanceController::class, 'donations'])->name('index');
  Route::get('/{id}', [App\Http\Controllers\AdminFinanceController::class, 'showDonation'])->name('show');
  Route::post('/donor/store', [App\Http\Controllers\AdminFinanceController::class, 'storeDonor'])->name('donor.store');
  Route::put('/donor/{id}', [App\Http\Controllers\AdminFinanceController::class, 'updateDonor'])->name('donor.update');
  Route::delete('/donor/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroyDonor'])->name('donor.destroy');
  Route::post('/donation/store', [App\Http\Controllers\AdminFinanceController::class, 'storeDonation'])->name('donation.store');
  Route::delete('/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroyDonation'])->name('destroy');
  Route::post('/campaign/store', [App\Http\Controllers\AdminFinanceController::class, 'storeDonationCampaign'])->name('campaign.store');
});

// Admin & Finance Budgeting Module Routes
Route::prefix('admin-finance/budgeting')->name('admin-finance.budgeting.')->middleware(['auth', 'division.access:admin-finance'])->group(function () {
  Route::get('/', [App\Http\Controllers\AdminFinanceController::class, 'budgeting'])->name('index');
  Route::get('/{id}', [App\Http\Controllers\AdminFinanceController::class, 'showBudget'])->name('show');
  Route::post('/store', [App\Http\Controllers\AdminFinanceController::class, 'storeDepartmentBudget'])->name('store');
  Route::delete('/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroyDepartmentBudget'])->name('destroy');
  Route::post('/line-item/store', [App\Http\Controllers\AdminFinanceController::class, 'storeBudgetLineItem'])->name('line-item.store');
});

// Admin & Finance Cash Management Module Routes
Route::prefix('admin-finance/cash-management')->name('admin-finance.cash-management.')->middleware(['auth', 'division.access:admin-finance'])->group(function () {
  Route::get('/', [App\Http\Controllers\AdminFinanceController::class, 'cashManagement'])->name('index');
  Route::get('/{id}', [App\Http\Controllers\AdminFinanceController::class, 'showCashManagementAccount'])->name('show');
  Route::post('/bank/store', [App\Http\Controllers\AdminFinanceController::class, 'storeCompanyBankAccount'])->name('bank.store');
  Route::put('/bank/{id}', [App\Http\Controllers\AdminFinanceController::class, 'updateCompanyBankAccount'])->name('bank.update');
  Route::delete('/bank/{id}', [App\Http\Controllers\AdminFinanceController::class, 'destroyCompanyBankAccount'])->name('bank.destroy');
  Route::post('/transaction/store', [App\Http\Controllers\AdminFinanceController::class, 'storeCashTransaction'])->name('transaction.store');
});

// Admin & Finance Financial Reports Module Routes
Route::prefix('admin-finance/financial-reports')->name('admin-finance.financial-reports.')->middleware(['auth', 'division.access:admin-finance'])->group(function () {
  Route::get('/', [App\Http\Controllers\AdminFinanceController::class, 'financialReports'])->name('index');
});

// Legacy URL Redirects for Standalone Modules
Route::redirect('admin-finance/accounting/investments', '/admin-finance/investments');
Route::redirect('admin-finance/accounting/donations', '/admin-finance/donations');
Route::redirect('admin-finance/accounting/budgeting', '/admin-finance/budgeting');
Route::redirect('admin-finance/accounting/cash-management', '/admin-finance/cash-management');
Route::redirect('admin-finance/accounting/financial-reports', '/admin-finance/financial-reports');

// COD Payment & Rider Collections - Protected Routes
Route::middleware(['auth'])->group(function () {
  // Rider Collection Routes
  Route::prefix('rider/collections')->name('rider.collections.')->group(function () {
    Route::get('/', [App\Http\Controllers\RiderCollectionController::class, 'index'])->name('index');
    // Specific routes must come BEFORE generic {id} route
    Route::get('/awaiting/handover', [App\Http\Controllers\RiderCollectionController::class, 'awaitingHandover'])->name('awaiting-handover');
    Route::get('/daily/summary', [App\Http\Controllers\RiderCollectionController::class, 'dailySummary'])->name('daily-summary');
    // Generic routes last
    Route::get('/{id}', [App\Http\Controllers\RiderCollectionController::class, 'show'])->name('show');
    Route::post('/{id}/record', [App\Http\Controllers\RiderCollectionController::class, 'recordCollection'])->name('record');
    Route::post('/{id}/hand-over', [App\Http\Controllers\RiderCollectionController::class, 'handOver'])->name('hand-over');
    Route::post('/{id}/evaluation-selection', [App\Http\Controllers\RiderCollectionController::class, 'recordEvaluationSelection'])->name('evaluation-selection');
  });

  // Cashier Payment Verification Routes (Admin & Finance)
  Route::prefix('admin-finance/cashier')->name('cashier.')->middleware('division.access:admin-finance')->group(function () {
    Route::get('/collections', [App\Http\Controllers\Accounting\CashierPaymentController::class, 'index'])->name('collections.index');
    Route::get('/collections/{id}', [App\Http\Controllers\Accounting\CashierPaymentController::class, 'show'])->name('collections.show');
    Route::post('/collections/{id}/verify', [App\Http\Controllers\Accounting\CashierPaymentController::class, 'verify'])->name('collections.verify');
    Route::post('/collections/{id}/reject', [App\Http\Controllers\Accounting\CashierPaymentController::class, 'reject'])->name('collections.reject');
    Route::get('/daily-report', [App\Http\Controllers\Accounting\CashierPaymentController::class, 'dailyReport'])->name('daily-report');
    Route::get('/export', [App\Http\Controllers\Accounting\CashierPaymentController::class, 'exportForAccounting'])->name('export');
  });
});

// Vendor Management Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('vendor-management', VendorController::class)->names([
        'index'   => 'vendor-management.index',
        'store'   => 'vendor-management.store',
        'show'    => 'vendor-management.show',
        'update'  => 'vendor-management.update',
        'destroy' => 'vendor-management.destroy',
    ])->except(['create', 'edit']);
});
