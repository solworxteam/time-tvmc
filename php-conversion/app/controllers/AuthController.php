<?php
/**
 * Auth Controller
 * Handles authentication-related API endpoints
 */

class AuthController
{
    private $user_model;

    public function __construct($user_model)
    {
        $this->user_model = $user_model;
    }

    /**
     * POST /api/login
     */
    public function login()
    {
        try {
            $data = get_json_input();

            if (empty($data['username']) || empty($data['password'])) {
                return json_response(['error' => 'Username and password are required'], 400);
            }

            $user = $this->user_model->authenticate($data['username'], $data['password']);

            if (!$user) {
                return json_response(['error' => 'Invalid username or password'], 401);
            }

            // Set session for authenticated user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            return json_response([
                'message' => 'Login successful',
                'user' => $user['username']
            ], 200);
        } catch (Exception $e) {
            return json_response(['error' => 'Something went wrong'], 500);
        }
    }

    /**
     * GET /api/auth/me
     * Get current authenticated user
     */
    public function me()
    {
        if (empty($_SESSION['user_id'])) {
            return json_response(['error' => 'Not authenticated'], 401);
        }

        $user = $this->user_model->getById($_SESSION['user_id']);

        if (!$user) {
            return json_response(['error' => 'User not found'], 404);
        }

        return json_response($user, 200);
    }

    /**
     * POST /api/logout
     */
    public function logout()
    {
        session_destroy();
        return json_response(['message' => 'Logged out successfully'], 200);
    }
}
