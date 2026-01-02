<?php
require_once __DIR__ . '/../includes/auth.php';

logout_user();

header('Location: index.php?menu=7');
exit;
