<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PageMember;
use App\Models\MarketingPage;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{

    public function create(Request $request)
    {

        $request->validate([
            'marketing_page_id' => 'required|exists:marketing_pages,id',
            'user_id' => 'required|exists:users,id',
        ]);


        $marketingPage = MarketingPage::findOrFail($request->input('marketing_page_id'));


        if (Auth::user()->id !== $marketingPage->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }


        if ($marketingPage->members()->where('user_id', $request->input('user_id'))->exists()) {
            return response()->json(['message' => 'Member already added'], 400);
        }


        $member = new PageMember([
            'user_id' => $request->input('user_id'),
            'marketing_page_id' => $request->input('marketing_page_id'),
        ]);
        $member->save();


        return response()->json(['message' => 'Member added successfully']);
    }


    public function destroy($id)
    {

        $member = PageMember::findOrFail($id);


        $marketingPage = MarketingPage::findOrFail($member->marketing_page_id);


        if (Auth::user()->id !== $marketingPage->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }


        $member->delete();


        return response()->json(['message' => 'Member removed successfully']);
    }
}
