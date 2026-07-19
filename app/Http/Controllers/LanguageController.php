<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        abort_unless(in_array($locale, ['id', 'en'], true), 404);

        session(['locale' => $locale]);

        return back()->with('status', $locale === 'en' ? 'Language changed to English.' : 'Bahasa diubah ke Indonesia.');
    }
}
