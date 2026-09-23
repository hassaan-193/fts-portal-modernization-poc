<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class PWAController extends Controller
{
public function home()
{
    // Get the current authenticated user
    $user = auth()->user();

    // Pass the user to the view (roles are automatically accessible via Spatie methods)
    return view('pwa.home', compact('user'));
}

    public function presents()
    {
        return view('pwa.presents');
    }

    public function absents()
    {
        return view('pwa.absents');
    }

    public function presentHistory()
    {
        return view('pwa.present-history');
    }

    public function absentHistory()
    {
        return view('pwa.absent-history');
    }
}