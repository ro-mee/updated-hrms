<?php
require_once dirname(__DIR__) . '/config/database.php';
$db = db();
$stmt = $db->prepare('SELECT p.module, p.action, p.description FROM permissions p JOIN role_permissions rp ON p.id=rp.permission_id JOIN roles r ON rp.role_id=r.id WHERE r.slug=\'recruitment_officer\' ORDER BY p.module, p.action');
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
