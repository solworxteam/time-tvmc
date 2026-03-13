<?php
require_once __DIR__ . '/../../config/database.php';

class User {
    public static function authenticate($username, $password) {
        $user = self::getByUsername($username);
        
        if (!$user) {
            return false;
        }
        
        // Check password (matches legacy plaintext method)
        if ($user['password'] === $password) {
            return $user;
        }
        
        return false;
    }

    public static function getById($id) {
        return getOneDB("SELECT id, username, email FROM admin_users WHERE id = ?", [$id]);
    }

    public static function getByUsername($username) {
        return getOneDB("SELECT * FROM admin_users WHERE username = ?", [$username]);
    }
}
