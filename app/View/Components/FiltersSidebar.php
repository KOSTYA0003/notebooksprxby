<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FiltersSidebar extends Component
{
    public $location;

    public function __construct($location = 'modal')
    {
        $this->location = $location;
    }

    public function render()
    {
        $sidebarSpecs = \Cache::remember('sidebar_specs', 3600, function () {
            return \App\Models\Attribute::query()
                ->forLocation('sidebar')
                ->whereNull('parent_id')
                ->select('id', 'name', 'filter_type')
                ->with(['children' => fn ($q) => $q->forLocation('sidebar')->select('id', 'name', 'parent_id', 'filter_type')])
                ->get();
        });

        $brands = \Cache::remember('sidebar_brands', 3600, function () {
            return \App\Models\Brand::select('id', 'name', 'slug')->orderBy('name')->get();
        });

        return view('components.filters-sidebar', compact('sidebarSpecs', 'brands'));
    }
}
