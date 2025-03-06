<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salesperson extends Model
{
    use HasFactory;

    protected $table = "tblSalesperson";

    protected $primaryKey = 'Salesperson'; 
    public $incrementing = false; 

    public $timestamps = false;

    protected $fillable = [
        "EmployeeID",
        "Branch",
        "Type",
        "Salesperson",
        "Name",
        "Warehouse",
        "SourceWarehouse",
        "ContactNo",
        "ContactHP",
        "ContacteMail",
        "Addr1",
        "Addr2",
        "Addr3",
        "Group1",
        "Group2",
        "Group3",
        "mdCode",
        "lastUpdated",
    ];

}
