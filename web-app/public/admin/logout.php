<?php
require_once __DIR__ . '/../../app/helpers.php';

session_start();
session_destroy();

header('Location: /');
exit;
