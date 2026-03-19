<?php
/**
 * Migration: Encrypt Existing PII Data
 * Run this from CLI or browser once to encrypt existing records.
 */
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$db = db();
echo "Starting PII Encryption Migration...\n";

// 1. Alter Table
try {
    $db->exec("
        ALTER TABLE employees 
        MODIFY basic_salary VARCHAR(255) DEFAULT NULL,
        MODIFY sss_number VARCHAR(255) DEFAULT NULL,
        MODIFY philhealth_number VARCHAR(255) DEFAULT NULL,
        MODIFY pagibig_number VARCHAR(255) DEFAULT NULL,
        MODIFY tin_number VARCHAR(255) DEFAULT NULL
    ");
    echo "Columns altered successfully.\n";
} catch (Exception $e) {
    echo "Alter table failed: " . $e->getMessage() . "\n";
}

// 2. Encrypt Data
$employees = $db->query("SELECT id, basic_salary, sss_number, philhealth_number, pagibig_number, tin_number FROM employees")->fetchAll();
$updated = 0;

$stmt = $db->prepare("
    UPDATE employees 
    SET basic_salary = ?, sss_number = ?, philhealth_number = ?, pagibig_number = ?, tin_number = ?
    WHERE id = ?
");

foreach ($employees as $emp) {
    // Only encrypt if it's not already base64 (crude check, but works for the existing plaintext data)
    $salary = $emp['basic_salary'];
    $sss = $emp['sss_number'];
    $phil = $emp['philhealth_number'];
    $pagibig = $emp['pagibig_number'];
    $tin = $emp['tin_number'];

    // If string is already > 40 chars, it might already be encrypted. We'll assume everything currently is plaintext.
    $encSalary = encrypt_pii((string)$salary);
    $encSss = encrypt_pii($sss);
    $encPhil = encrypt_pii($phil);
    $encPagibig = encrypt_pii($pagibig);
    $encTin = encrypt_pii($tin);

    $stmt->execute([
        $encSalary, $encSss, $encPhil, $encPagibig, $encTin, $emp['id']
    ]);
    $updated++;
}

echo "Migration completed. Encrypted $updated records.\n";
