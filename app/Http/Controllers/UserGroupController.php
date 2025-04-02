
<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    public function index()
    {
        return response()->json(UserGroup::with('users')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $userGroup = UserGroup::create($validated);

        return response()->json($userGroup, 201);
    }

    public function update(Request $request, UserGroup $userGroup)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        $userGroup->update($validated);

        return response()->json($userGroup);
    }

    public function destroy(UserGroup $userGroup)
    {
        $userGroup->delete();
        return response()->json(['message' => 'User group deleted']);
    }
}
