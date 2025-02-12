<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPrices;

class ProductCalculator
{
    public function calculateResult($id)
    {
        $test = $this->convertUOMIntoPieces('1515151', 15);
    }

    public function getTotalQtyInPCS($sku, $quantity, $uom)
    {

        try {
            $product = Product::where('StockCode', $sku)->first();

            $ConvFactAltUom = $product->ConvFactAltUom;
            $ConvFactOthUom = $product->ConvFactOthUom;

            $StockUom = $product->StockUom;
            $AlternateUom = $product->AlternateUom;
            $otherUOM = $product->OtherUom;

            $totalInPieces = 0;            


            if (strcasecmp($uom, "PC") === 0) {
                $totalInPieces = $quantity;
            } else if (strcasecmp($uom, "IB") === 0) {
                $totalInPieces = ($ConvFactAltUom / $ConvFactOthUom) * $quantity;
            } else if (strcasecmp($uom, "CS") === 0) {
                $totalInPieces = $quantity * $ConvFactAltUom;
            }

            return [
                'success' => true,
                'result' =>  floor($totalInPieces),
            ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'result' => $e->getMessage(),
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
