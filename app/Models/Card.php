<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;
    protected $table = 'cards';
    protected $keyType = 'string';
    protected $fillable = [ 'userId',
        'firstNameKh',
        'lastNameKh',
        'firstName',
        'lastName',
        'role',
        'entityName',
        'areaCode',
        'phoneNumber',
        'address',
        'description',
        'isApprove',
        'img'
    ];
}
