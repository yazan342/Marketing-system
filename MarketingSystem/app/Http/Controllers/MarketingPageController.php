<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MarketingPageResource;
use App\Models\MarketingPage;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class MarketingPageController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:clothes,accessories,electronics,furniture',
        ]);

        $user = Auth::user();


        $marketingPage = new MarketingPage([
            'user_id' => $user->id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
        ]);
        $marketingPage->save();


        return response(new MarketingPageResource($marketingPage->load('products')));
    }


    public function index()
    {

        $user = Auth::user();

        $marketingPages = MarketingPage::whereNotBlockedForUser($user->id)->get();

        return response()->json(MarketingPageResource::collection($marketingPages->load('products')));
    }


    public function show($id)
    {

        $marketingPage = MarketingPage::findOrFail($id);
        $user = Auth::user();

        if ($marketingPage->isUserBlocked($user->id)) {
            return response()->json(['message' => 'You are blocked from accessing this page.'], 403);
        }

        return response(new MarketingPageResource($marketingPage->load('products')));
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:clothes,accessories,electronics,furniture',
        ]);

        $marketingPage = MarketingPage::findOrFail($id);

        if (Auth::user()->id !== $marketingPage->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $marketingPage->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
        ]);


        return response()->json(['message' => 'Marketing page updated successfully', new MarketingPageResource($marketingPage->load('products'))]);
    }


    public function destroy($id)
    {

        $marketingPage = MarketingPage::findOrFail($id);


        if (Auth::user()->id !== $marketingPage->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }


        $marketingPage->products()->delete();
        $marketingPage->delete();


        return response()->json(['message' => 'Marketing page deleted successfully']);
    }
}
