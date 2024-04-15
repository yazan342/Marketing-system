<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\MarketingPage;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('product.auth')->except(['index', 'show', 'update']);
    }

    public function create(Request $request)
    {
        $request->validate([
            'marketing_page_id' => 'required|exists:marketing_pages,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|',
            'offer_price' => 'nullable|numeric|min:0',
            'offer_start_date' => 'nullable|date',
            'offer_end_date' => 'nullable|date|after:offer_start_date',
        ]);

        $marketingPage = MarketingPage::findOrFail($request->input('marketing_page_id'));

        if (
            Auth::user()->id !== $marketingPage->user_id &&
            !$marketingPage->members()->where('user_id', Auth::user()->id)->exists()
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $image = $request->file('image');
        $imageName = time() . '.' . $image->extension();
        $image->move(public_path('images'), $imageName);
        $product = new Product([
            'marketing_page_id' => $request->input('marketing_page_id'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'image' => $imageName,
            'offer_price' => $request->input('offer_price'),
            'offer_start_date' => $request->input('offer_start_date'),
            'offer_end_date' => $request->input('offer_end_date'),
        ]);
        $product->save();


        return response()->json(['message' => 'Product created successfully', new ProductResource($product)]);
    }

    public function index($marketingPageId)
    {
        $products = Product::where('marketing_page_id', $marketingPageId)->get();
        return ProductResource::collection($products);
    }

    public function show($id)
    {

        $product = Product::findOrFail($id);
        return new ProductResource($product);
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'marketing_page_id' => 'required|exists:marketing_pages,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'offer_price' => 'nullable|numeric|min:0',
            'offer_start_date' => 'nullable|date',
            'offer_end_date' => 'nullable|date|after:offer_start_date',
        ]);

        $product = Product::findOrFail($id);
        $marketingPage = MarketingPage::findOrFail($request->input('marketing_page_id'));
        if (
            Auth::user()->id !== $marketingPage->user_id &&
            !$marketingPage->members()->where('user_id', Auth::user()->id)->exists()
        ) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $image = $request->file('image');
        $imageName = time() . '.' . $image->extension();
        $image->move(public_path('images'), $imageName);
        $product->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'image' => $imageName,
            'offer_price' => $request->input('offer_price'),
            'offer_start_date' => $request->input('offer_start_date'),
            'offer_end_date' => $request->input('offer_end_date'),
        ]);

        return response()->json(['message' => 'Product updated successfully', new ProductResource($product)]);
    }


    public function destroy($id)
    {

        $product = Product::findOrFail($id);

        $product->delete();


        return response()->json(['message' => 'Product deleted successfully']);
    }
}
