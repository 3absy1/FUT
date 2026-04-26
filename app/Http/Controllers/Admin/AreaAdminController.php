<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Area::withCount(['clubs', 'stadiums']);

        if ($search = $request->get('search')) {
            $query->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%$search%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%$search%"]);
        }

        $areas = $query->orderBy('id')->paginate(20)->withQueryString();

        return view('admin.pages.areas.index', compact('areas'));
    }

    public function create()
    {
        return view('admin.pages.areas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_en'     => 'required|string|max:100',
            'name_ar'     => 'nullable|string|max:100',
            'coordinates' => 'nullable|string|max:500',
        ]);

        Area::create([
            'name'        => ['en' => $data['name_en'], 'ar' => $data['name_ar'] ?? ''],
            'coordinates' => $data['coordinates'] ?? null,
        ]);

        return redirect()->route('admin.areas.index')->with('success', 'Area created.');
    }

    public function show(Area $area)
    {
        $area->loadCount(['clubs', 'stadiums']);
        $area->load(['clubs', 'stadiums']);

        return view('admin.pages.areas.show', compact('area'));
    }

    public function edit(Area $area)
    {
        return view('admin.pages.areas.edit', compact('area'));
    }

    public function update(Request $request, Area $area)
    {
        $data = $request->validate([
            'name_en'     => 'required|string|max:100',
            'name_ar'     => 'nullable|string|max:100',
            'coordinates' => 'nullable|string|max:500',
        ]);

        $area->update([
            'name'        => ['en' => $data['name_en'], 'ar' => $data['name_ar'] ?? ''],
            'coordinates' => $data['coordinates'] ?? null,
        ]);

        return redirect()->route('admin.areas.index')->with('success', 'Area updated.');
    }

    public function destroy(Area $area)
    {
        $area->delete();

        return redirect()->route('admin.areas.index')->with('success', 'Area deleted.');
    }
}
