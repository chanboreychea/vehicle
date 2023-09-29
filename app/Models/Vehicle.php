<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;
    protected $table = 'vehicles';
    protected $keyType = 'string';
    protected $fillable = [
        'userId',
        'firstNameKh',
        'lastNameKh',
        'firstName',
        'lastName',
        'role',
        'entityName',
        'phoneNumber',
        'email',
        'address',
        'vechicleReleaseYear',
        'vehicleLicensePlate',
        'vehicleModel',
        'vehicleColor',
        'description',
        'isApprove',
        'img'
    ];
}
