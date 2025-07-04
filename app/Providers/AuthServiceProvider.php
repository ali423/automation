<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\WithdrawalRequest;
use App\Policies\ActivityPolicy;
use App\Policies\CommodityPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\ImportingRequestPolicy;
use App\Policies\OrderPolicy;
use App\Policies\RolePolicy;
use App\Policies\SellerPolicy;
use App\Policies\UnitPolicy;
use App\Policies\UnitConversionPolicy;
use App\Policies\UserPolicy;
use App\Policies\WarehousePolicy;
use App\Policies\WithdrawalRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        \App\Models\UnitConversion::class => \App\Policies\UnitConversionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('read_user',[UserPolicy::class,'viewAny']);
        Gate::define('create_user',[UserPolicy::class,'create']);

        Gate::define('read_role',[RolePolicy::class,'viewAny']);
        Gate::define('create_role',[RolePolicy::class,'create']);

        Gate::define('read_activity',[ActivityPolicy::class,'viewAny']);

        Gate::define('read_commodity',[CommodityPolicy::class,'viewAny']);
        Gate::define('create_commodity',[CommodityPolicy::class,'create']);

        Gate::define('read_warehouse',[WarehousePolicy::class,'viewAny']);
        Gate::define('create_warehouse',[WarehousePolicy::class,'create']);
        Gate::define('edit_warehouse',[WarehousePolicy::class,'update']);

        Gate::define('read_importing',[ImportingRequestPolicy::class,'viewAny']);
        Gate::define('create_importing',[ImportingRequestPolicy::class,'create']);

        Gate::define('read_customer',[CustomerPolicy::class,'viewAny']);
        Gate::define('create_customer',[CustomerPolicy::class,'create']);

        Gate::define('read_order',[OrderPolicy::class,'viewAny']);
        Gate::define('create_order',[OrderPolicy::class,'create']);

        Gate::define('read_withdrawal',[WithdrawalRequestPolicy::class,'viewAny']);
        Gate::define('create_withdrawal',[WithdrawalRequestPolicy::class,'create']);

        Gate::define('read_seller',[SellerPolicy::class,'viewAny']);
        Gate::define('create_seller',[SellerPolicy::class,'create']);

        Gate::define('read_unit',[UnitPolicy::class,'viewAny']);
        Gate::define('create_unit',[UnitPolicy::class,'create']);
        Gate::define('edit_unit',[UnitPolicy::class,'update']);

        Gate::define('read_unit_conversion',[UnitConversionPolicy::class,'viewAny']);
        Gate::define('create_unit_conversion',[UnitConversionPolicy::class,'create']);
        Gate::define('edit_unit_conversion',[UnitConversionPolicy::class,'update']);
        Gate::define('delete_unit_conversion',[UnitConversionPolicy::class,'delete']);
    }
}
