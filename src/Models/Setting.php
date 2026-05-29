<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Setting
{
    private static ?array $cache = null;

    private static array $defaults = [
        'app_name'       => 'Customer Portal',
        'primary_color'  => '#2563eb',
        'primary_dark'   => '#1d4ed8',
        'sidebar_bg'     => '#0f172a',
        'sidebar_text'   => '#94a3b8',
        'sidebar_active' => '#2563eb',
        'support_email'  => '',
        'logo_ext'       => 'png',
    ];

    public static function all(): array
    {
        if (self::$cache !== null) return self::$cache;

        try {
            $rows = DB::fetchAll('SELECT `key`, `value` FROM settings');
            self::$cache = array_merge(
                self::$defaults,
                array_column($rows, 'value', 'key')
            );
        } catch (\Exception) {
            // Settings table doesn't exist yet — return defaults
            self::$cache = self::$defaults;
        }

        return self::$cache;
    }

    public static function get(string $key): string
    {
        return self::all()[$key] ?? self::$defaults[$key] ?? '';
    }

    public static function set(string $key, string $value): void
    {
        DB::execute(
            'INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?',
            [$key, $value, $value]
        );
        self::$cache = null;
    }

    public static function saveMany(array $data): void
    {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, self::$defaults)) {
                self::set($key, (string)$value);
            }
        }
    }

    public static function cssVars(): string
    {
        $s = self::all();
        return sprintf(
            '<style>:root{--primary:%s;--primary-dark:%s;--sidebar-bg:%s;--sidebar-text:%s;--sidebar-active:%s;}</style>',
            htmlspecialchars($s['primary_color']),
            htmlspecialchars($s['primary_dark']),
            htmlspecialchars($s['sidebar_bg']),
            htmlspecialchars($s['sidebar_text']),
            htmlspecialchars($s['sidebar_active'])
        );
    }
}
