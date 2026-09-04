<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ads and Promo - Promotions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="d-flex align-items-center mb-4">
                        <i class="las la-percentage fs-1 me-3 text-primary"></i>
                        <div>
                            <h3 class="fw-bold mb-0">Marketing Promotions</h3>
                            <p class="text-muted mb-0">Plan and execute special promotional offers and discounts.</p>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 shadow-sm">
                        <i class="las la-info-circle me-1"></i> Promotion management tools are being prepared for the next phase of deployment.
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm bg-light">
                                <div class="card-body text-center py-4">
                                    <i class="las la-ticket-alt fs-2 text-primary mb-2"></i>
                                    <h5 class="fw-bold">Vouchers</h5>
                                    <p class="small text-muted mb-0">Manage discount codes and vouchers.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm bg-light">
                                <div class="card-body text-center py-4">
                                    <i class="las la-gift fs-2 text-success mb-2"></i>
                                    <h5 class="fw-bold">Bundles</h5>
                                    <p class="small text-muted mb-0">Create book bundle offers.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm bg-light">
                                <div class="card-body text-center py-4">
                                    <i class="las la-calendar-check fs-2 text-warning mb-2"></i>
                                    <h5 class="fw-bold">Seasonal</h5>
                                    <p class="small text-muted mb-0">Plan holiday and event promos.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
