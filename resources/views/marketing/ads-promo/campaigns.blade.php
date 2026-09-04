<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ads and Promo - Campaigns') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="d-flex align-items-center mb-4">
                        <i class="las la-bullhorn fs-1 me-3 text-primary"></i>
                        <div>
                            <h3 class="fw-bold mb-0">Marketing Campaigns</h3>
                            <p class="text-muted mb-0">Manage and track your advertising campaigns here.</p>
                        </div>
                    </div>
                    
                    <div class="alert alert-info border-0 shadow-sm">
                        <i class="las la-info-circle me-1"></i> This module is currently under development. Specific campaign management tools will be integrated soon.
                    </div>

                    <div class="mt-5 text-center py-5 bg-light rounded-3 border-dashed border-2">
                        <i class="las la-tools fs-1 text-muted mb-3 d-block"></i>
                        <h4 class="text-muted">Campaign Dashboard Placeholder</h4>
                        <p class="text-muted small">Tracking for Radio, TV, and Print campaigns will appear here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
