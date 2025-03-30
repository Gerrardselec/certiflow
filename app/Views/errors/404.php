<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - CertiFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('/css/main.css') ?>">
    <style>
        .error-container {
            text-align: center;
            padding: 80px 0;
        }
        .error-container h1 {
            font-size: 120px;
            font-weight: 700;
            color: #dc3545;
            margin-bottom: 0;
        }
        .error-container h2 {
            font-size: 36px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <h1>404</h1>
            <h2>Page Not Found</h2>
            <p class="lead">Sorry, the page you are looking for could not be found.</p>
            <a href="<?= url('/') ?>" class="btn btn-primary mt-3">Return to Homepage</a>
        </div>
    </div>
</body>
</html>