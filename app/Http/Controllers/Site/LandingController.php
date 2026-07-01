<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Barbearia;

class LandingController extends Controller
{
    public function index()
    {
        $barbearias = Barbearia::whereNull('parent_id')
            ->whereNotNull('slug')
            ->orderBy('nome')
            ->get();

        return view('site.landing', compact('barbearias'));
    }

    public function adminAccess()
    {
        $barbearias = Barbearia::whereNull('parent_id')
            ->whereNotNull('slug')
            ->orderBy('nome')
            ->get();

        return view('site.admin-access', compact('barbearias'));
    }
}
