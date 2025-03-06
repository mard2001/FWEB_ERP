<?php

namespace App\Models;

use App\Models\Salesman;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    protected $table = "tblCustomer";

    protected $primaryKey = 'custCode'; 
    public $incrementing = false; 

    public $timestamps = false;

    protected $hidden = ['time_stamp'];

    protected $fillable = [
        "mdCode",
        "custCode",
        "custName",
        "contactPerson",
        "contactLandline",
        "address",
        "frequencyCategory",
        "contactCellNumber",
        "mcpDay",
        "mcpSchedule",
        "priceCode",
        "custType",
    ];

    protected $attributes = [
        'isLockOn' => '1',
    ];


    public function salesman(){
        return $this->belongsTo(Salesman::class, "mdCode", "mdCode");
    }
}
