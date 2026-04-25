<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Stadium;
use Illuminate\Http\Request;

class StadiumAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Stadium::with(['area', 'pitches'])->withCount(['matches', 'pitches']);

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%$search%");
        }

        if ($area = $request->get('area_id')) {
            $query->where('area_id', $area);
        }

        $stadiums = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $areas = Area::orderBy('name')->get();

        return view('admin.pages.stadiums.index', compact('stadiums', 'areas'));
    }

    public function show(Stadium $stadium)
    {
        $stadium->load(['area', 'pitches', 'matches' => fn($q) => $q->latest()->limit(10)]);
        return view('admin.pages.stadiums.show', compact('stadium'));
    }

    public function create()
    {
        $areas = Area::orderBy('name')->get();
        return view('admin.pages.stadiums.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:200',
            'location'       => 'nullable|string|max:500',
            'area_id'        => 'required|exists:areas,id',
            'price_per_hour' => 'required|numeric|min:0',
            'whatsapp_number'=> 'nullable|string|max:30',
            'rating'         => 'nullable|numeric|min:0|max:5',
        ]);

        Stadium::create($data);
        return redirect()->route('admin.stadiums.index')->with('success', 'Stadium created.');
    }

    public function edit(Stadium $stadium)
    {
        $areas = Area::orderBy('name')->get();
        return view('admin.pages.stadiums.edit', compact('stadium', 'areas'));
    }

    public function update(Request $request, Stadium $stadium)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:200',
            'location'       => 'nullable|string|max:500',
            'area_id'        => 'required|exists:areas,id',
            'price_per_hour' => 'required|numeric|min:0',
            'whatsapp_number'=> 'nullable|string|max:30',
            'rating'         => 'nullable|numeric|min:0|max:5',
        ]);

        $stadium->update($data);
        return redirect()->route('admin.stadiums.show', $stadium)->with('success', 'Stadium updated.');
    }

    public function destroy(Stadium $stadium)
    {
        $stadium->delete();
        return redirect()->route('admin.stadiums.index')->with('success', 'Stadium deleted.');
    }
}
