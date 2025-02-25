<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Orders\PO;
use App\Models\ReceivingRDetails;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceivingRHeader extends Model
{
    use HasFactory;

    protected $table = 'tblInvRRHeader';

    public $incrementing = false;

    protected $fillable = [
        'SupplierCode',
        'SupplierName',
        'SupplierTIN',
        'RRNo',
        'Date',
        'Reference',
        'Status',
        'Total',
        'ApprovedBy',
        'CheckedBy',
        'UpdatedBy',
        'PreparedBy',
        'DateUpdated',
        'PrintedBy',
        'Address',
        'FileName',
        'RRDATE',
        'LOCALID_HEADER',
        'PO_NUMBER'
    ]; 

    public function rrdetails()
    {
        return $this->hasMany(ReceivingRDetails::class, 'RRNo', 'RRNo')
            ->select('SKU','Quantity','UOM','WhsCode','UnitPrice','NetVat','Vat','Gross','RRNo','PO_NUMBER');
    }

    public function poincluded()
    {
        return $this->belongsTo(PO::class, 'PO_NUMBER', 'PONumber');
    }
    
}
