<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olivarez College</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/index.css">

    </style>
</head>
<body>
    <!-- Video Background -->
    <video autoplay loop muted playsinline class="video-backdrop">
        <source src="bg/vid.mp4" type="video/mp4">
    </video>

    <!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="bg/oc.png" alt="Olivarez College Logo" class="logo-img">
            <span class="ms-2 fw-bold">Olivarez College</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="registration.php">Get Started</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4">Stay Connected with <span class="text-success">Olivarez College</span></h1>
            <p class="lead">Submit your details to stay updated with important announcements.</p>
            <a href="registration.php" class="btn btn-primary btn-lg mt-3">Get Started</a>
        </div>
    </section>

    <!-- Success/Error Messages -->
    <div class="container mt-3 position-absolute top-0 start-50 translate-middle-x" style="z-index: 1000; margin-top: 80px;">
        <?php
        if (isset($_GET['success'])) {
            echo "<div class='alert alert-success alert-dismissible fade show' role='alert' id='success-alert'>
                    ✅ Registration successful! Student has been added.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
        } elseif (isset($_GET['error'])) {
            if ($_GET['error'] == 'duplicate') {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        ⚠️ Error: Email or phone number already registered.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
            } elseif ($_GET['error'] == 'missing_fields') {
                echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                        ⚠️ Please fill in all required fields.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
            } elseif ($_GET['error'] == 'db') {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        ❌ Database error! Please try again later.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
            } else {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        ❌ Registration failed. Please try again.
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
            }
        }
        ?>
    </div>

    <!-- Footer -->
    <footer class="footer-transparent text-white py-3">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 Olivarez College. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss success or error alerts after 4 seconds
        window.addEventListener('DOMContentLoaded', function() {
            const alert = document.querySelector('.alert-dismissible');
            if(alert) {
                setTimeout(() => {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                }, 4000); // 4 seconds
            }
        });
    </script>
</body>
</html>