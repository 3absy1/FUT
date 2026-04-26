<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;

class ConfigAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Config::query();

        if ($search = $request->get('search')) {
            $query->where('key', 'like', "%$search%")
                  ->orWhere('value', 'like', "%$search%");
        }

        $configs = $query->orderBy('key')->paginate(30)->withQueryString();

        return view('admin.pages.configs.index', compact('configs'));
    }

    public function create()
    {
        return view('admin.pages.configs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key'   => 'required|string|max:255|unique:configs,key',
            'value' => 'required|string',
        ]);

        Config::create($data);

        return redirect()->route('admin.configs.index')->with('success', 'Config key created successfully.');
    }

    public function edit(Config $config)
    {
        return view('admin.pages.configs.edit', compact('config'));
    }

    public function update(Request $request, Config $config)
    {
        $data = $request->validate([
            'key'   => 'required|string|max:255|unique:configs,key,' . $config->id,
            'value' => 'required|string',
        ]);

        $config->update($data);

        return redirect()->route('admin.configs.index')->with('success', 'Config updated successfully.');
    }

    public function destroy(Config $config)
    {
        $config->delete();

        return redirect()->route('admin.configs.index')->with('success', 'Config deleted.');
    }
}
