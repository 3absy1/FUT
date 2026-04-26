<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Division::query();

        if ($search = $request->get('search')) {
            $query->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%$search%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%$search%"]);
        }

        $divisions = $query->orderBy('sort_order')->paginate(20)->withQueryString();

        return view('admin.pages.divisions.index', compact('divisions'));
    }

    public function create()
    {
        return view('admin.pages.divisions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_en'       => 'required|string|max:100',
            'name_ar'       => 'nullable|string|max:100',
            'matches_count' => 'required|integer|min:1',
            'exp_win'       => 'required|integer|min:0',
            'draw_exp'      => 'required|integer|min:0',
            'sort_order'    => 'required|integer|min:0',
            'checkpoints'   => 'nullable|string',
        ]);

        Division::create([
            'name'          => ['en' => $data['name_en'], 'ar' => $data['name_ar'] ?? ''],
            'matches_count' => $data['matches_count'],
            'exp_win'       => $data['exp_win'],
            'draw_exp'      => $data['draw_exp'],
            'sort_order'    => $data['sort_order'],
            'checkpoints'   => $data['checkpoints']
                ? array_map('intval', explode(',', $data['checkpoints']))
                : [],
        ]);

        return redirect()->route('admin.divisions.index')->with('success', 'Division created.');
    }

    public function edit(Division $division)
    {
        return view('admin.pages.divisions.edit', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $data = $request->validate([
            'name_en'       => 'required|string|max:100',
            'name_ar'       => 'nullable|string|max:100',
            'matches_count' => 'required|integer|min:1',
            'exp_win'       => 'required|integer|min:0',
            'draw_exp'      => 'required|integer|min:0',
            'sort_order'    => 'required|integer|min:0',
            'checkpoints'   => 'nullable|string',
        ]);

        $division->update([
            'name'          => ['en' => $data['name_en'], 'ar' => $data['name_ar'] ?? ''],
            'matches_count' => $data['matches_count'],
            'exp_win'       => $data['exp_win'],
            'draw_exp'      => $data['draw_exp'],
            'sort_order'    => $data['sort_order'],
            'checkpoints'   => $data['checkpoints']
                ? array_map('intval', explode(',', $data['checkpoints']))
                : [],
        ]);

        return redirect()->route('admin.divisions.index')->with('success', 'Division updated.');
    }

    public function destroy(Division $division)
    {
        $division->delete();

        return redirect()->route('admin.divisions.index')->with('success', 'Division deleted.');
    }
}
