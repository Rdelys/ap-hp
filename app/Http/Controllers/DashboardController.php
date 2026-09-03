<?php

namespace App\Http\Controllers;

use App\Enums\RoleUtilisateur;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function secretariat(Request $request)
    {
        return view('dashboard.secretariat', ['user' => $request->user()]);
    }

    public function referent(Request $request)
    {
        return view('dashboard.referent', ['user' => $request->user()]);
    }

    public function adminAphp(Request $request)
    {
        return view('dashboard.admin-aphp', ['user' => $request->user()]);
    }

    public function operateur(Request $request)
    {
        return view('dashboard.operateur', ['user' => $request->user()]);
    }

    public function relecteur(Request $request)
    {
        return view('dashboard.relecteur', ['user' => $request->user()]);
    }

    public function adminTitulaire(Request $request)
    {
        return view('dashboard.admin-titulaire', ['user' => $request->user()]);
    }
}