<?php
define('APP_NAME', 'Divisional Secretariat Service Management System – Minipe');
define('APP_SHORT', 'DSSMS Minipe');
define('BASE_URL', '/');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx']);

define('SERVICE_TYPES', [
    'birth_certificate',
    'death_certificate',
    'marriage_certificate',
    'residence_certificate',
    'character_certificate',
    'land_related',
    'samurdhi_related',
    'pension_related',
    'grama_niladhari',
    'other_public',
]);

define('APPOINTMENT_STATUSES', ['pending', 'approved', 'rejected', 'completed', 'cancelled']);
define('REQUEST_STATUSES', ['pending', 'processing', 'approved', 'rejected', 'completed']);
