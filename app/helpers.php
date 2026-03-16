<?php
/**
 * Helper Functions - Core utility functions
 * 
 * Security Note: All user input should be sanitized before output
 */

/**
 * Format date for display
 */
function formatDate($date) {
    if (!$date) return '';
    return date('d M Y', strtotime($date));
}

/**
 * Format time for display
 */
function formatTime($time) {
    if (!$time) return '';
    return date('H:i', strtotime($time));
}

/**
 * Check if user is authenticated as admin
 */
function isAdmin() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Require admin authentication - redirect if not authenticated
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /admin/login.php');
        exit;
    }
}

/**
 * Sanitize input for safe HTML output
 */
function sanitize($input) {
    if (is_null($input) || $input === '') {
        return '';
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate and sanitize email
 */
function validateEmail($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $email;
    }
    return false;
}

/**
 * Hash password using bcrypt
 */
function hashPassword($password) {
    if (strlen($password) < 8) {
        throw new Exception("Password must be at least 8 characters");
    }
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password against hash
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCsrfToken($token) {
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token input field HTML
 */
function getCsrfField() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCsrfToken()) . '">';
}

/**
 * Encode data for JSON
 */
function json_encode_safe($data) {
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

/**
 * Calculate distance between two coordinates (Haversine formula)
 */
function getDistance($lat1, $lon1, $lat2, $lon2) {
    $R = 6371; // Earth radius in km
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $R * $c;
}

/**
 * Determine current and next prayer keys for a mosque schedule.
 *
 * @param array $prayerRow
 * @param array $prayerDefs
 * @param int $nowUnix
 * @return array{next:string,current:?string}
 */
function getNextCurrentPrayer(array $prayerRow, array $prayerDefs, $nowUnix = null) {
    $now = is_null($nowUnix) ? time() : (int)$nowUnix;
    $next = null;
    $current = null;

    foreach ($prayerDefs as $def) {
        $startVal = $prayerRow[$def['start']] ?? '';
        $jamaatVal = (!empty($def['jamaat']) && !empty($prayerRow[$def['jamaat']])) ? $prayerRow[$def['jamaat']] : '';
        $compareVal = $jamaatVal ?: $startVal;

        if (empty($compareVal)) {
            continue;
        }

        $timestamp = strtotime(date('Y-m-d') . ' ' . date('H:i', strtotime($compareVal)));
        if ($timestamp > $now) {
            if ($next === null) {
                $next = $def['key'];
            }
        } else {
            $current = $def['key'];
        }
    }

    if ($next === null) {
        $next = 'fajr';
    }

    return ['next' => $next, 'current' => $current];
}

/**
 * Validate date format
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Validate time format
 */
function validateTime($time, $format = 'H:i') {
    $t = DateTime::createFromFormat($format, $time);
    return $t && $t->format($format) === $time;
}

/**
 * Start secure session if not already started
 */
function initializeSession() {
    if (session_status() === PHP_SESSION_NONE) {
        $cookieSecure = defined('SESSION_COOKIE_SECURE') ? SESSION_COOKIE_SECURE : false;
        $cookieHttpOnly = defined('SESSION_COOKIE_HTTPONLY') ? SESSION_COOKIE_HTTPONLY : true;
        $cookieSameSite = defined('SESSION_COOKIE_SAMESITE') ? SESSION_COOKIE_SAMESITE : 'Lax';
        $sessionLifetime = defined('SESSION_LIFETIME') ? SESSION_LIFETIME : 3600;

        // Set session configuration
        ini_set('session.cookie_secure', $cookieSecure ? '1' : '0');
        ini_set('session.cookie_httponly', $cookieHttpOnly ? '1' : '0');
        ini_set('session.cookie_samesite', $cookieSameSite);
        ini_set('session.gc_maxlifetime', (string) $sessionLifetime);
        
        session_start();
        
        // Check session timeout
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > $sessionLifetime) {
                session_destroy();
                $_SESSION = [];
            }
        }
        
        $_SESSION['last_activity'] = time();
    }
}
