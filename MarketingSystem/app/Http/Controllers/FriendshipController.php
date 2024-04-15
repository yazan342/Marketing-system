<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Friendship;


class FriendshipController extends Controller
{

    public function sendFriendRequest(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        $recipient = User::findOrFail($request->input('friend_id'));

        $existingRequest = Friendship::where('user_id', auth()->id())
            ->where('friend_id', $recipient->id)
            ->where('status', 'Pending')
            ->first();

        if ($existingRequest) {
            return response()->json(['message' => 'Friend request already sent'], 400);
        }

        $friendship = new Friendship([
            'user_id' => auth()->id(),
            'friend_id' => $recipient->id,
            'status' => 'Pending',
        ]);
        $friendship->save();

        return response()->json(['message' => 'Friend request sent successfully']);
    }


    public function acceptFriendRequest(Request $request)
    {
        $request->validate([
            'friendship_id' => 'required|exists:friendships,id',
        ]);

        $friendship = Friendship::findOrFail($request->input('friendship_id'));

        if (auth()->id() !== $friendship->friend_id || $friendship->status !== 'Pending') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $friendship->status = 'Accepted';
        $friendship->save();

        return response()->json(['message' => 'Friend request accepted successfully']);
    }

    public function rejectFriendRequest(Request $request)
    {
        $request->validate([
            'friendship_id' => 'required|exists:friendships,id',
        ]);

        $friendship = Friendship::findOrFail($request->input('friendship_id'));

        if (auth()->id() !== $friendship->friend_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $friendship->status = 'Rejected';
        $friendship->save();

        return response()->json(['message' => 'Friend request rejected successfully']);
    }
}
