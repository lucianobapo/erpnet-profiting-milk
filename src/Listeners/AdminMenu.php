<?php

namespace ErpNET\Profiting\Milk\Listeners;

use App\Events\AdminMenuCreated;
use Menu;
use Nwidart\Menus\MenuItem;
use App\Models\Module\Module;
use Illuminate\Support\Facades\Cache;
use App\Utilities\CacheUtility;

class AdminMenu
{
    private $menuItems;
    protected $cache;

    /**
     * The constructor.
     *
     * @param Factory    $views
     * @param Repository $config
     */
    public function __construct(MenuItem $menuItems)
    {
        $this->menuItems = $menuItems;
        $this->cache = new CacheUtility;
    }

    /**
     * Handle the event.
     *
     * @param  AdminMenuCreated $event
     * @return void
     */
    public function handle(AdminMenuCreated $event)
    {
        $modules = $this->cache->remember('modules_pluck_alias', function () {
            return Module::all()->pluck('alias')->toArray();
        }, [Module::class]);

        if (!in_array('inventory', $modules)) {
            return false;
        }

        $user = auth()->user();

        if (!$user->can([
            //'read-inventory-item-groups',
            'read-common-items',
            //'read-inventory-options',
            //'read-inventory-manufacturers',
            //'read-inventory-warehouses',
        ])) {
            return;
        }

        $attr = ['icon' => 'fa fa-angle-double-right'];

        $event->menu->dropdown(trans('erpnet-profiting-milk::menu.production'), function ($sub) use ($user, $attr) {
            if ($user->can('read-common-items')) {
                $sub->url('milk/production', trans('erpnet-profiting-milk::menu.milk_map'), 1, $attr);
                
                $sub->route('production.index', trans('erpnet-profiting-milk::menu.milk_map'), [], 2, $attr);
            }

        }, 2.5, [
            'title' => trans('erpnet-profiting-milk::general.title'),
            'icon' => 'fa fa-cubes',
        ]);
    }
}
