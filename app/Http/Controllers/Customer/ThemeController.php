<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function update(Request $request)
    {
        $request->validate(['theme' => 'required|in:light,dark']);
        auth()->user()->update(['theme' => $request->theme]);
        return response()->json(['theme' => $request->theme]);
    }
}