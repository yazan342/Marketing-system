<?php

namespace App\Http\Middleware;

use App\Models\MarketingPage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductAuthorizationMiddleware
{
    public function handle($request, Closure $next)
    {
        $productId = $request->route('id');

        $product = Product::find($productId);
        if ($product) {
            $marketingPage = $product->marketingPage;
        } else {
            $marketingPage = MarketingPage::query()->findOrFail($request->marketing_page_id);
        }



        if (
            Auth::user()->id === $marketingPage->user_id ||
            $marketingPage->members()->where('user_id', Auth::user()->id)->exists()
        ) {
            return $next($request);
        }

        // Unauthorized access
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
