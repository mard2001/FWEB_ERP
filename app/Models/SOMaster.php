<?php

namespace App\Models;

use App\Models\SODetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SOMaster extends Model
{
    use HasFactory;

    protected $table = "SorMaster";
    protected $primaryKey = 'SalesOrder'; 
    public $incrementing = false; 
    protected $casts = [
        'SalesOrder' => 'string',
    ];

    public function sodetails()
    {
        return $this->hasMany(SODetail::class, 'SalesOrder', 'SalesOrder')
                ->select(['SalesOrder', 'SalesOrderLine', 'MStockCode', 'MStockDes', 'MWarehouse', 'MOrderQty', 'MOrderUom', 'MStockQtyToShp', 'MStockingUom', 'MconvFactOrdUm', 'MPrice', 'MPriceUom', 'MProductClass', 'MStockUnitMass', 'MStockUnitVol', 'MPriceCode', 'MConvFactAlloc', 'MConvFactUnitQ', 'MAltUomUnitQ']);
    }
}
