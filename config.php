<?php
// VexAI Config - Render Fix
error_reporting(0);
ini_set('display_errors', 0);

// HuggingFace Token from Render Environment
$hf_token = getenv('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') ?: '';

// Database - Make it optional, don't crash site if fails
$host = 'sql305.infinityfree.com';
$user = 'if0_42898622';
$pass = 'ArifYt09';
$dbname = 'if0_42898622_vexai';

$conn = null;
try {
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        $conn = null;
    }
} catch (Exception $e) {
    $conn = null;
}
?>
