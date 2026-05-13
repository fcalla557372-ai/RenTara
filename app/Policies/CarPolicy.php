<?php
namespace App\Policies;

use App\Models\Car;
use App\Models\User;

class CarPolicy
{
    public function create(User $user): bool  { return $user->isAdmin(); }
    public function update(User $user, Car $car): bool { return $user->isAdmin() && $car->status === 'available'; }
    public function delete(User $user, Car $car): bool { return $user->isAdmin() && $car->status === 'available'; }
}