<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
  
    public function show(Request $request)
    {
        return response()->json($request->user());
    }
    
public function update(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
    ]);

    $user = $request->user();
    $user->update($request->only('name', 'email'));

    return response()->json(['message' => 'User updated successfully']);
}

public function destroy(Request $request)
{
    $user = $request->user();
    $user->delete();

    return response()->json(['message' => 'User deleted successfully']);
}
}