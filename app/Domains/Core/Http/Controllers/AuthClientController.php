<?php

namespace App\Domains\Core\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\LoginToken;
use Illuminate\Http\Request;
use App\Domains\CRM\Models\Client;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthClientController extends Controller
{


  public function show() {
    return inertia('Auth/Portal/Login');
  }

   public function login () {

    $data = request()->post();

    // $this->validate(request(),[
    //   'dni' => ['required', 'dni', 'exists:clients,dni'],
    //   'email' => ['required', 'email', 'exists:clients,email']
    // ]);

    try {
        $client = Client::where([
            'email' => $data['email'],
            'dni' => $data['dni']
        ])->first();

        $user = User::where([
          'email' => $data['email'],
        ])->first();

        if ($user) {
          $user->sendLoginLink();
          session()->flash('success', true);
          return redirect()->back();
        }

        if (!$client->user) {
          $client->createUser();
        }

        $client->user->sendLoginLink();

        session()->flash('success', true);
        return redirect()->back();
    } catch (Exception $e) {
        return response()->json([
            "status" => 402,
            "message" => $e->getMessage()
        ], 402);
    }
  }

  public function verify(string $token) {
      $loginToken = LoginToken::where("token", hash('sha256', $token))->first();
      abort_unless(request()->hasValidSignature() && $loginToken->isValid(), 401);
      $loginToken->consume();
      Auth::login($loginToken->user);
      return redirect('/dashboard');
  }
}
