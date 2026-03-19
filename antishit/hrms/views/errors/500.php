<?php
http_response_code(500);
$pageTitle = '500 - Server Error';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>500 Internal Server Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-danger">500</h1>
        <h2 class="mb-4">Internal Server Error</h2>
        <p class="text-muted mb-4">Something went wrong on our end. Please try again later.</p>
        <a href="index.php" class="btn btn-primary">Go to Dashboard</a>
    </div>
</body>
</html>
