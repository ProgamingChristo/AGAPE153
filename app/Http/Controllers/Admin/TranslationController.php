<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentTranslation;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function index()
    {
        return view('admin.translations.index', [
            'translations' => ContentTranslation::query()->orderBy('group')->orderBy('key')->orderBy('locale')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'locale' => ['required', 'in:id,en'],
            'group' => ['required', 'string', 'max:80'],
            'key' => ['required', 'string', 'max:160'],
            'value' => ['nullable', 'string'],
        ]);

        ContentTranslation::query()->updateOrCreate(
            ['locale' => $data['locale'], 'group' => $data['group'], 'key' => $data['key']],
            ['value' => $data['value']]
        );

        return back()->with('status', 'Translation saved.');
    }
}
