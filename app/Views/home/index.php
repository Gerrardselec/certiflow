<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= asset('/css/main.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-dark text-white py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">CertiFlow</h1>
                <nav>
                    <ul class="nav">
                        <li class="nav-item"><a href="<?= url('/') ?>" class="nav-link text-white active">Home</a></li>
                        <li class="nav-item"><a href="<?= url('/home/about') ?>" class="nav-link text-white">About</a></li>
                        <li class="nav-item"><a href="<?= url('/home/contact') ?>" class="nav-link text-white">Contact</a></li>
                        <li class="nav-item"><a href="<?= url('/auth/login') ?>" class="nav-link text-white">Login</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    
    <main>
        <section class="py-5 bg-primary text-white text-center">
            <div class="container">
                <h2 class="display-4">Electrical Testing Certificate Management</h2>
                <p class="lead">Streamline your electrical certification process with our comprehensive management system.</p>
                <a href="<?= url('/auth/register') ?>" class="btn btn-light btn-lg mt-3">Get Started</a>
            </div>
        </section>
        
        <section class="py-5">
            <div class="container">
                <h2 class="text-center mb-4">Key Features</h2>
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h3 class="card-title h5">BS 7671 Compliant</h3>
                                <p class="card-text">All certificates comply with the latest BS 7671 regulations.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h3 class="card-title h5">Customer Portal</h3>
                                <p class="card-text">Provide your customers with secure access to their certificates.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h3 class="card-title h5">PDF Generation</h3>
                                <p class="card-text">Generate professional PDF certificates with your company branding.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h3 class="card-title h5">Email Notifications</h3>
                                <p class="card-text">Automated email notifications for certificate completion and expiry.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <p class="text-center mb-0">&copy; <?= date('Y') ?> Gerrards Electrical Ltd. All rights reserved.</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('/js/main.js') ?>"></script>
</body>
</html>