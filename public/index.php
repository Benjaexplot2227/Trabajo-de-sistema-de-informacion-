<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/bootstrap.php';

$userCount = (int) db()->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
redirect($userCount === 0 ? 'setup.php' : (!empty($_SESSION['user_id']) ? 'dashboard.php' : 'login.php'));
