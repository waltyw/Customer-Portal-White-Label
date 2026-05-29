<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\Auth;
use App\Core\Security;
use App\Core\View;
use App\Models\User;

class AccountController
{
    public function index(): void
    {
        Auth::requireAuth();
        View::render('customer/account', [
            'title' => 'My Account',
            'user'  => User::find(Auth::id()),
        ]);
    }

    public function update(): void
    {
        Auth::requireAuth();
        Security::checkCsrf();

        $name = trim($_POST['name'] ?? '');
        if (!$name) {
            Security::flash('error', 'Name cannot be empty.');
            Security::redirect('/account');
        }

        User::update(Auth::id(), [
            'name'        => $name,
            'company'     => trim($_POST['company'] ?? ''),
            'phone'       => trim($_POST['phone'] ?? ''),
            'website_url' => trim($_POST['website_url'] ?? ''),
            'is_active'   => 1,
        ]);

        // Update session name immediately
        $_SESSION['user_name'] = $name;

        Security::flash('success', 'Your account details have been updated.');
        Security::redirect('/account');
    }
}
