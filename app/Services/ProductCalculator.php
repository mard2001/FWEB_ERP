<?php

namespace App\Services;

use App\Models\Product;
// use App\Models\ProductPrices;
use App\Models\Orders\POItems;

class ProductCalculator
{

    public function transformQuantitiesAndUnits($sku, $uoms)
    {

        try {

            $product = Product::where('StockCode', $sku)->firstOrFail();

            $ConvFactAltUom = $product->ConvFactAltUom;
            $ConvFactOthUom = $product->ConvFactOthUom;
            $totalInPieces = 0;

            $itemUoms = [$product->StockUom, $product->AlternateUom, $product->OtherUom];
            $keepAvailableUomOnly = array_intersect_key($uoms, array_flip($itemUoms));

            foreach ($keepAvailableUomOnly as $key => $uom) {

                if (strcasecmp($key, "PC") === 0) {
                    $totalInPieces = $totalInPieces + $uom;
                } else if (strcasecmp($key, "IB") === 0) {
                    $totalInPieces = $totalInPieces + ($ConvFactAltUom / $ConvFactOthUom) * $uom;
                } else if (strcasecmp($key, "CS") === 0) {
                    $totalInPieces = $totalInPieces + $uom * $ConvFactAltUom;
                }
            }


            $convertIntoLargestUom = $this->convertProductToLargesttUnit($itemUoms, $totalInPieces, $ConvFactAltUom, $ConvFactOthUom);
            $convertIntoLargestUom['QuantityInPieces'] = floor($totalInPieces);
            // dd($convertIntoLargestUom);

            return [
                'success' => true,
                'result' =>   $convertIntoLargestUom,
            ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'result' => $e->getMessage(),
            ];
        }
    }

    public function convertProductToLargesttUnit($ProductUoms, $totalQuantity, $ConvFactAltUom, $ConvFactOthUom)
    {

        if (in_array('CS', $ProductUoms)) {

            return [
                'uom' => 'CS',
                'convertedToLargestUnit' => $totalQuantity / $ConvFactAltUom
            ];
        } else if (in_array('IB', $ProductUoms)) {

            return [
                'uom' => 'IB',
                'convertedToLargestUnit' => $totalQuantity / ($ConvFactAltUom / $ConvFactOthUom)
            ];
        } else  if (in_array('PC', $ProductUoms)) {
            return [
                'uom' => 'PC',
                'convertedToLargestUnit' => $totalQuantity
            ];
        }
    }

    private function ItemPackingQuantity($sku, $quantity)
    {
        try {
            $product = Product::findOrFail($sku);

            $ConvFactAltUom = $product->ConvFactAltUom;
            $ConvFactOthUom = $product->ConvFactOthUom;
            $otherUOM = $product->OtherUom;
            $result = 0;

            if (strcasecmp($otherUOM, "IB") === 0) {

                $mod1 = $quantity % $ConvFactAltUom;
                $mod2 = $mod1 % ($ConvFactAltUom / $ConvFactOthUom);

                $result = floor($mod2);
            } else {

                $result = $quantity % $ConvFactOthUom;
            }

            return response()->json([
                'success' => true,
                'result' =>  $result,
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
