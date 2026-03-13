<?php
/**
 * Application Bootstrap - Initializes the application
 * 
 * This file should be included at the start of every request
 */

// Load configuration
require_once __DIR__ . '/config/config.php';

// Load Logger
require_once __DIR__ . '/app/Logger.php';

// Load Database
require_once __DIR__ . '/config/database.php';

// Load Helpers
require_once __DIR__ . '/app/helpers.php';

// Load Models
require_once __DIR__ . '/app/models/Mosque.php';
require_once __DIR__ . '/app/models/PrayerTime.php';
require_once __DIR__ . '/app/models/Parking.php';
require_once __DIR__ . '/app/models/User.php';

// Load Services (if any)
// require_once __DIR__ . '/app/services/AuthService.php';

// Set error handling
error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? 1 : 0);

// Set timezone
date_default_timezone_set('Europe/London');

// Initialize security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Initialize session
initializeSession();

Logger::debug("Application bootstrapped");
