<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Observers\POObserver;

class PO extends Model
{
    const CREATED_AT = 'DateUploaded';
    const UPDATED_AT = null;

    protected $table = 'tblPOHeader';
    // protected $primaryKey = 'PONumber';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'PONumber',
        'OrderNumber',
        'PODate',
        'SupplierCode',
        'SupplierName',
        'productType',
        'orderPlacer',
        'FOB',
        'orderPlacerEmail',
        'deliveryAddress',
        'contactNumber',
        'contactPerson',
        'deliveryMethod',
        'totalNetVol',
        'volumeUOM',
        'totalNetWeight',
        'totalGrossWeight',
        'weightUOM',
        'subTotal',
        'totalDiscount',
        'totalTax',
        'totalCost',
        'usedCurrency',
        'SpecialInstruction',
        'EncoderID',
        'FileName',
        'TermsCode'
    ];

    protected static function boot()
    {
        parent::boot();
        PO::observe(POObserver::class);

    }

    public function POItems()
    {
        return $this->hasMany(POItems::class, 'PONumber', 'PONumber');
    }

    // Method to update totalCost
    public function updateTotalCost()
    {
        $subTotal = $this->POItems()->sum('TotalPrice');
        $this->subTotal = $subTotal;

        // comment as of now because there is no discount on po items
        // $totalDiscount = $this->POItems()->sum('TotalDiscount');
        $this->totalCost = $subTotal;
        $this->save();
        
    }

}
