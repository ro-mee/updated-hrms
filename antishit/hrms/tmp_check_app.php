<?php
define('APP_ROOT', __DIR__);
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/app.php';

foreach (glob(__DIR__ . '/models/*.php') as $model) {
    require_once $model;
}
$appModel = new Applicant();
print_r($appModel->countByStatus());
