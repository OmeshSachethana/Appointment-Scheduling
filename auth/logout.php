<?php
require_once __DIR__ . '/../includes/auth.php';

logoutUser();
setFlash('success', __('logout_success'));
redirect(basePath('index.php'));
