<?php

namespace App\Http\Controllers;

class LanguageController extends Controller
{
    public function switchLang($locale)
    {
        if (in_array($locale, ['en', 'es'])) {
            session()->put('locale', $locale);
        }

        return redirect()->back();
    }
}
