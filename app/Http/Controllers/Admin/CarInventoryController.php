<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarRequest;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarInventoryController extends Controller
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

        // Get distinct types that exist in the database — not hardcoded
        $carTypes = Car::select('type')->distinct()->orderBy('type')->pluck('type');

        return view('admin.car-inventory', compact('cars', 'carTypes'));
    }

    public function store(StoreCarRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = uniqid('car_', true).'.'.strtolower($image->getClientOriginalExtension());
            $data['image_path'] = $image->storeAs('cars', $filename, 'public');
        }
        unset($data['image']);

        Car::create($data);

        return back()->with('success', 'Car added successfully.');
    }

    public function update(StoreCarRequest $request, Car $car)
    {
        if ($car->status === 'rented') {
            return back()->with('error', 'Cannot edit a car that is currently rented.');
        }

        $data = $request->validated();
        $image = $request->file('image');

        if ($image && ! $image->isValid()) {
            return back()->withErrors([
                'image' => 'The selected image could not be uploaded. Check that it is below your PHP upload limit.',
            ])->withInput();
        }

        if ($image) {
            if ($car->image_path) {
                $oldImagePath = str_starts_with($car->image_path, 'cars/')
                    ? $car->image_path
                    : 'cars/'.$car->image_path;

                Storage::disk('public')->delete($oldImagePath);
            }

            $filename = uniqid('car_', true).'.'.strtolower($image->getClientOriginalExtension());
            $data['image_path'] = $image->storeAs('cars', $filename, 'public');
        }

        unset($data['image']);
        $car->update($data);

        return back()->with('success', 'Car updated successfully.');
    }

    public function destroy(Car $car)
    {
        if ($car->status === 'rented') {
            return back()->with('error', 'Cannot archive a car that is currently rented.');
        }

        $car->delete();

        return back()->with('success', 'Car archived successfully.');
    }
}
