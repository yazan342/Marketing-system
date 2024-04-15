<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MarketingPage;
use App\Models\PagePrivacySetting;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class PrivacySettingsController extends Controller
{
    public function getPrivacySettings($marketingPageId)
    {
        $marketingPage = MarketingPage::findOrFail($marketingPageId);

        if ($marketingPage->user_id !== Auth::user()->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $blockedUsers = $marketingPage->blockedUsers;

        return response()->json(['blocked_users' => $blockedUsers]);
    }

    public function blockUser(Request $request, $marketingPageId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $marketingPage = MarketingPage::findOrFail($marketingPageId);


        if ($marketingPage->user_id !== Auth::user()->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $userToBlock = User::findOrFail($request->input('user_id'));


        if ($marketingPage->isUserBlocked($userToBlock->id)) {
            return response()->json(['message' => 'User is already blocked'], 400);
        }


        $privacySetting = new PagePrivacySetting([
            'marketing_page_id' => $marketingPage->id,
            'user_id' => $userToBlock->id,
        ]);
        $privacySetting->save();

        return response()->json(['message' => 'User blocked successfully']);
    }


    public function unblockUser(Request $request, $marketingPageId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $marketingPage = MarketingPage::findOrFail($marketingPageId);


        if ($marketingPage->user_id !== Auth::user()->id) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $userToUnblock = User::findOrFail($request->input('user_id'));


        $privacySetting = PagePrivacySetting::where('marketing_page_id', $marketingPage->id)
            ->where('user_id', $userToUnblock->id)
            ->first();

        if (!$privacySetting) {
            return response()->json(['message' => 'User is not blocked'], 400);
        }

        $privacySetting->delete();

        return response()->json(['message' => 'User unblocked successfully']);
    }
}
