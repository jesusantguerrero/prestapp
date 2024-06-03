<?php

namespace App\Domains\Core\Http\Controllers;


use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Support\Facades\Auth;

use App\Domains\Core\Services\SSOService;

class SSOController {

    public function __construct(private SSOService $ssoService) {}

    public function connect() {
      $state = Str::random(40);
      request()->session()->put("state", $state);
      return $this->ssoService->authorize($state);
    }

    public function accept() {
        $state = request()->session()->pull('state');
        throw_unless(strlen($state) > 0 && $state == request()->state,
          InvalidArgumentException::class
        );

        $this->ssoService->acceptOath($state);
        $userInfo = $this->ssoService->getUser();
        $user = $this->ssoService->linkUser($userInfo);

        Auth::login($user);
        return redirect('/dashboard');
    }

    public function getUser() {
      $accessToken = session('access_token');

      return $this->ssoService->getUser($accessToken);
    }
}
