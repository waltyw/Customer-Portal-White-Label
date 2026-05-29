<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\Auth;
use App\Core\View;
use App\Models\User;

class HelpController
{
    public function index(): void
    {
        Auth::requireAuth();
        $user       = User::find(Auth::id());
        $mailServer = User::mailServer($user['website_url'] ?? null);

        View::render('customer/help', [
            'title'      => 'Help & Email Setup',
            'user'       => $user,
            'mailServer' => $mailServer ?: 'mail.yourdomain.com',
            'hasMailServer' => (bool)$mailServer,
        ]);
    }
}
