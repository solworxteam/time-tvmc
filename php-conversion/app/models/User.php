<?php
/**
 * User Model
 * Handles authentication and user-related database operations
 */

class User
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Authenticate user with plaintext password (matches current Node.js implementation)
     * NOTE: In production, passwords should be hashed. Current implementation matches existing behavior.
     */
    public function authenticate($username, $password)
    {
        $user = $this->db->fetchOne(
            'SELECT * FROM admin_users WHERE username = ?',
            [$username]
        );

        if (!$user) {
            return null;
        }

        // Plaintext comparison (matches current Node.js auth.js)
        if ($user['password'] !== $password) {
            return null;
        }

        return $user;
    }

    /**
     * Get user by ID
     */
    public function getById($id)
    {
        return $this->db->fetchOne(
            'SELECT id, username FROM admin_users WHERE id = ?',
            [$id]
        );
    }

    /**
     * Get user by username
     */
    public function getByUsername($username)
    {
        return $this->db->fetchOne(
            'SELECT id, username FROM admin_users WHERE username = ?',
            [$username]
        );
    }
}
