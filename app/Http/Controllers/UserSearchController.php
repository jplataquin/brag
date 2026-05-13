<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Battle;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    public function search(Request $request, Battle $battle)
    {
        $q = $request->get('q');
        if (strlen($q) < 2) return response()->json([]);

        $except = [];
        for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
            if ($battle->{"team_a_user_{$i}"}) $except[] = $battle->{"team_a_user_{$i}"};
            if ($battle->{"team_b_user_{$i}"}) $except[] = $battle->{"team_b_user_{$i}"};
        }
        if ($battle->marshall_id) $except[] = $battle->marshall_id;

        $users = User::where('username', 'like', "%{$q}%")
            ->whereNotIn('id', $except)
            ->take(5)
            ->get(['id', 'username', 'avatar', 'is_verified']);

        return response()->json($users);
    }
}
