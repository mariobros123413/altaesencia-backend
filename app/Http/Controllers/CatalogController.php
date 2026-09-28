<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class CatalogController extends Controller
{
    public function cosmetics(): View
    {
        return view('catalog.cosmetics');
    }
}
