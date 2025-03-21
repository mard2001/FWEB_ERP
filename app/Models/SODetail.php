<?php

namespace App\Models;

use App\Models\SOMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SODetail extends Model
{
    use HasFactory;

    protected $table = "SorDetail";
    protected $primaryKey = ['SalesOrder', 'SalesOrderLine'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $casts = [
        'SalesOrder' => 'string',
    ];


    public function soheader()
    {
        return $this->belongsTo(SOMaster::class, 'SalesOrder', 'SalesOrder')
                ->select(['SalesOrder', 'NextDetailLine', 'OrderStatus', 'DocumentType', 'Customer', 'CustomerName', 'Salesperson', 'CustomerPoNumber', 'OrderDate', 'EntrySystemDate', 'ReqShipDate', 'DateLastDocPrt', 'InvoiceCount', 'Branch', 'Warehouse', 'ShipAddress1', 'ShipToGpsLat', 'ShipToGpsLong',]);
    }
}
