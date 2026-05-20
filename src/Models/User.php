<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class User
{
    public static function find(int $id): ?array
    {
        return DB::fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return DB::fetchOne('SELECT * FROM users WHERE email = ?', [strtolower($email)]);
    }

    public static function all(): array
    {
        return DB::fetchAll(
            'SELECT id, email, name, company, phone, role, is_active, created_at FROM users ORDER BY name ASC'
        );
    }

    public static function customers(): array
    {
        return DB::fetchAll(
            'SELECT id, email, name, company, phone, is_active, created_at FROM users WHERE role = ? ORDER BY name ASC',
            ['customer']
        );
    }

    public static function create(array $data): int
    {
        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        return DB::insert(
            'INSERT INTO users (email, password_hash, name, company, phone, role) VALUES (?, ?, ?, ?, ?, ?)',
            [
                strtolower($data['email']),
                $hash,
                $data['name'],
                $data['company'] ?? null,
                $data['phone'] ?? null,
                $data['role'] ?? 'customer',
            ]
        );
    }

    public static function update(int $id, array $data): void
    {
        DB::execute(
            'UPDATE users SET name = ?, company = ?, phone = ?, is_active = ? WHERE id = ?',
            [$data['name'], $data['company'] ?? null, $data['phone'] ?? null, $data['is_active'] ?? 1, $id]
        );
    }

    public static function toggleActive(int $id): void
    {
        DB::execute('UPDATE users SET is_active = NOT is_active WHERE id = ?', [$id]);
    }

    public static function changePassword(int $id, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        DB::execute('UPDATE users SET password_hash = ? WHERE id = ?', [$hash, $id]);
    }

    public static function stats(int $id): array
    {
        $tickets  = DB::fetchOne('SELECT COUNT(*) as cnt FROM tickets WHERE user_id = ?', [$id]);
        $invoices = DB::fetchOne('SELECT COUNT(*) as cnt, COALESCE(SUM(amount_due),0) as outstanding FROM invoices WHERE user_id = ? AND status NOT IN (?,?)', [$id, 'paid', 'voided']);
        return [
            'ticket_count'   => (int)($tickets['cnt'] ?? 0),
            'invoice_count'  => (int)($invoices['cnt'] ?? 0),
            'amount_outstanding' => (float)($invoices['outstanding'] ?? 0),
        ];
    }
}
