<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
      {
          $google_user = Socialite::driver('google')->stateless()->user();
          // Check if the user already exists in the database
          $existingUser = User::where('email', $google_user->email)->first();
          if ($existingUser) {
              // If the user already exists, generate an access token for the user
              $token = $existingUser->createToken('Token Name')->accessToken;
              return response()->json([
                  'user' => $existingUser,
                  'access_token' => $token,
                  'token_type' => 'Bearer',
              ]);
          } else {
              $user = User::where('google_id', $google_user->id)->first();
              if (!$user) {
                  $user = User::Create([
                      'google_id' => $google_user->id,
                      'name' => $google_user->name,
                      'email' => $google_user->email,
                      'google_token' => $google_user->token,
                  ]);
                  // Generate an access token for the new user
                  $token = $user->createToken('access_token')->accessToken;
                  return response()->json([
                      'user' => $user,
                      'access_token' => $token,
                      'token_type' => 'Bearer',
                  ]);
              } else {
                  $token = $user->createToken('access_token')->accessToken;
                  return response()->json([
                      'user' => $user,
                      'access_token' => $token,
                      'token_type' => 'Bearer',
                  ]);
              }
          }
      }
      }


}
