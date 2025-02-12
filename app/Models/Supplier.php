<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    const CREATED_AT = 'lastUpdated';
    protected $table = 'tblSupplier';

    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'SupplierCode' ,
        'SupplierName' ,
        'SupplierType' ,
        'TermsCode' ,
        'ContactPerson' ,
        'ContactNo' ,
        'CompleteAddress' ,
        'Region' ,
        'Province' ,
        'City' ,
        'Municipality' ,
        'Barangay' ,
    ];
}
