<?php
require_once '../config/config.php';

// Clear session
session_destroy();

// Redirect to login
redirect(ADMIN_URL . '/login.php');
