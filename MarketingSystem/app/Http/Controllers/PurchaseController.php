<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function purchaseProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->input('product_id'));
        $buyer = Auth::user();

        if ($product->marketingPage->user_id === $buyer->id) {
            return response()->json(['message' => 'You cannot purchase your own product'], 400);
        }

        if ($product->is_sold) {
            return response()->json(['message' => 'Product is already sold'], 400);
        }

        $profit = ($product->price * 0.02);

        if ($product->offer_price && now()->isBetween($product->offer_start_date, $product->offer_end_date)) {
            $purchasePrice = $product->offer_price;
        }

        $purchase = new Purchase([
            'buyer_id' => $buyer->id,
            'product_id' => $product->id,
            'purchase_price' => $purchasePrice,
        ]);
        $purchase->save();

        $product->is_sold = true;
        $product->save();

        $buyerTransaction = new FinancialTransaction([
            'user_id' => $buyer->id,
            'type' => 'Payment',
            'amount' => -$product->price,
            'transaction_date' => now(),
        ]);
        $buyerTransaction->save();

        $sellerProfitTransaction = new FinancialTransaction([
            'user_id' => $product->marketingPage->user_id,
            'type' => 'Profit',
            'amount' => $profit,
            'transaction_date' => now(),
        ]);
        $sellerProfitTransaction->save();

        return response()->json(['message' => 'Product purchased successfully']);
    }
}
