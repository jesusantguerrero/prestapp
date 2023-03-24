<?php

namespace App\Domains\Admin\Guards;
use \Lab404\Impersonate\Guard\SessionGuard as BaseSessionGuard;

class SessionGuard extends BaseSessionGuard
{
    /**
     * @inheritDoc
     */
    public function quietLogout()
    {
        parent::quietLogout();

        foreach (array_keys(config('auth.guards')) as $guard) {
            $this->session->remove('password_hash_' . $guard);
        }
    }
}
