<?php

class Csrf
{
    public static function token(): string
    {
        Session::start();
        if (!Session::get('_csrf')) {
            Session::set('_csrf', bin2hex(random_bytes(32)));
        }
        return Session::get('_csrf');
    }

    public static function validate(?string $token): bool
    {
        Session::start();
        return is_string($token) && hash_equals((string) Session::get('_csrf', ''), $token);
    }
}
