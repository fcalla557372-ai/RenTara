<?php
namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class StaffCarInventoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = intval($request->query('per_page', 6));
        $allowed = [6, 9, 12];

        if (! in_array($perPage, $allowed, true)) {
            $perPage = 6;
        }

        $query = Car::latest();

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $cars = $query->paginate($perPage)->withQueryString();

        $carTypes = Car::select('type')->distinct()->orderBy('type')->pluck('type');

        return view('staff.car-inventory', compact('cars', 'carTypes'));
    }
}