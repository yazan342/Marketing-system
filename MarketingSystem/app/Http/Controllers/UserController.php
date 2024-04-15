<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MarketingPageResource;
use App\Models\Product;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getMarketingPages()
    {
        $user = Auth::user();
        $marketingPages = $user->marketingPages;

        return response()->json(['marketing_pages' => MarketingPageResource::collection($marketingPages->load('products'))]);
    }

    public function getProducts()
    {
        $user = Auth::user();
        $products = $user->products;

        $availableProducts = $products->where('is_sold', false)->count();
        $soldProducts = $products->where('is_sold', true)->count();

        return response()->json([
            'products' => $products,
            'available_products' => $availableProducts,
            'sold_products' => $soldProducts,
        ]);
    }

    public function getEarnings()
    {
        $user = Auth::user();
        $earnings = $user->financialTransactions()->where('type', 'Profit')->sum('amount');

        return response()->json(['earnings' => $earnings]);
    }

    public function setLanguage(Request $request)
    {
        $request->validate([
            'language' => 'required|in:Arabic,English',
        ]);

        $user = Auth::user();
        $user->language = $request->input('language');
        $user->save();

        return response()->json(['message' => 'Language preference updated successfully']);
    }

    public function getFinancialDetails()
    {
        $user = Auth::user();
        $payments = $user->financialTransactions()->where('type', 'Payment')->sum('amount');
        $profits = $user->financialTransactions()->where('type', 'Profit')->sum('amount');

        return response()->json(['payments' => $payments, 'profits' => $profits]);
    }
}
