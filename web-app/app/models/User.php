<?php
/**
 * User Model - Handles user authentication and management
 * 
 * Security: Uses bcrypt password hashing
 */

class User {
    /**
     * Authenticate user with username and password
     * 
     * @param string $username
     * @param string $password
     * @return array|bool User data or false if authentication fails
     */
    public static function authenticate($username, $password) {
        try {
            if (empty($username) || empty($password)) {
                Logger::warning("Authentication attempt with empty credentials");
                return false;
            }

            $user = self::getByUsername($username);
            
            if (!$user) {
                Logger::warning("Authentication attempt with non-existent user: " . sanitize($username));
                return false;
            }

            // Check if password is hashed (new format) or plaintext (legacy)
            if (password_verify($password, $user['password'])) {
                Logger::info("User authenticated successfully: " . sanitize($username));
                return $user;
            } else if ($user['password'] === $password) {
                // Legacy plaintext password support - upgrade to hash on next login
                Logger::warning("Legacy plaintext password detected for user: " . sanitize($username));
                self::updatePassword($user['id'], $password);
                return $user;
            }

            Logger::warning("Authentication failed for user: " . sanitize($username));
            return false;
        } catch (Exception $e) {
            Logger::error("Authentication error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by ID
     */
    public static function getById($id) {
        try {
            return Database::getInstance()->fetch(
                "SELECT id, username, email FROM admin_users WHERE id = ?",
                [$id]
            );
        } catch (Exception $e) {
            Logger::error("Error fetching user by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by username
     */
    public static function getByUsername($username) {
        try {
            return Database::getInstance()->fetch(
                "SELECT * FROM admin_users WHERE username = ?",
                [$username]
            );
        } catch (Exception $e) {
            Logger::error("Error fetching user by username: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update password with bcrypt hash
     */
    public static function updatePassword($userId, $password) {
        try {
            $hash = hashPassword($password);
            Database::getInstance()->execute(
                "UPDATE admin_users SET password = ? WHERE id = ?",
                [$hash, $userId]
            );
            Logger::info("Password updated for user ID: " . $userId);
            return true;
        } catch (Exception $e) {
            Logger::error("Error updating password: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a new admin user
     */
    public static function create($username, $email, $password) {
        try {
            if (empty($username) || empty($email) || empty($password)) {
                throw new Exception("Missing required fields");
            }

            if (!validateEmail($email)) {
                throw new Exception("Invalid email format");
            }

            $hash = hashPassword($password);
            
            Database::getInstance()->execute(
                "INSERT INTO admin_users (username, email, password, created_at) VALUES (?, ?, ?, NOW())",
                [$username, $email, $hash]
            );

            Logger::info("New admin user created: " . sanitize($username));
            return true;
        } catch (Exception $e) {
            Logger::error("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if username exists
     */
    public static function usernameExists($username) {
        return (bool) self::getByUsername($username);
    }

    /**
     * Check if email exists
     */
    public static function emailExists($email) {
        try {
            return (bool) Database::getInstance()->fetch(
                "SELECT id FROM admin_users WHERE email = ?",
                [$email]
            );
        } catch (Exception $e) {
            Logger::error("Error checking email: " . $e->getMessage());
            return false;
        }
    }
}
