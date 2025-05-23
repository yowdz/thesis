<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | Olivarez College Pre-Enrollment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
    <style>
        .about-container {
            max-width: 900px;
            margin: 7rem auto 3rem auto;
            background: rgba(255,255,255,0.93);
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.13);
            padding: 2.7rem 2.2rem 2.2rem 2.2rem;
            z-index: 2;
        }
        .about-title {
            color: #02680c;
            font-weight: 900;
        }
        .info-law {
            background: #e8f5e9;
            border-left: 6px solid #02680c;
            padding: 1.5rem 1.5rem 1.5rem 2rem;
            margin-top: 2.5rem;
            border-radius: 8px;
            color: #222;
        }
        .about-logo {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        @media (max-width: 768px) {
            .about-container {
                padding: 1.5rem 0.7rem;
                margin: 6rem 0 2rem 0;
            }
            .about-title {
                font-size: 2rem;
            }
        }
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
                <li class="nav-item"><a class="nav-link active" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="registration.php">Get Started</a></li>
            </ul>
        </div>
    </div>
</nav>
    <!-- About Content -->
    <div class="about-container content-container shadow">
        <div class="about-logo">
            <img src="bg/oc.png" alt="Olivarez College Logo" class="logo-img">
            <span class="about-title h2 mb-0">About the Pre-Enrollment Web Application</span>
        </div>
        <p>
            The <b>Olivarez College Pre-Enrollment Interest Form</b> web application is designed to provide students, parents, and guardians a seamless and convenient platform for expressing their intent to enroll at Olivarez College. This system collects essential information to help the school connect with prospective students, streamline pre-enrollment processes, and facilitate communication regarding important announcements and next steps.
        </p>
        <h4 class="mt-4 mb-2 college-green">Purpose</h4>
        <ul>
            <li><b>Accessibility:</b> Make it easy for interested students to submit their details online anytime, anywhere.</li>
            <li><b>Efficiency:</b> Help the school manage and organize pre-enrollment data efficiently for planning and communication.</li>
            <li><b>Communication:</b> Ensure that students and their families receive timely updates about requirements, deadlines, and school events.</li>
        </ul>
        <div class="info-law mt-4">
            <h5 class="mb-2 college-green">Data Privacy and the Philippine Data Privacy Act (RA 10173)</h5>
            <p>
                Olivarez College is committed to protecting the privacy and security of your personal information. All data collected through this platform is handled in accordance with the <b>Data Privacy Act of 2012 (Republic Act No. 10173)</b>.
            </p>
            <ul>
                <li><b>Purpose Limitation:</b> Information collected is used solely for pre-enrollment processing, outreach, and compliance with educational requirements.</li>
                <li><b>Data Protection:</b> Your data is stored securely and will not be shared with unauthorized parties.</li>
                <li><b>Rights of Data Subjects:</b> You have the right to access, correct, and request deletion of your personal data. For concerns, you may contact the school's Data Privacy Officer.</li>
            </ul>
            <p>
                For more information, you may visit the <a href="https://privacy.gov.ph/data-privacy-act/" target="_blank" rel="noopener noreferrer">National Privacy Commission’s website</a>.
            </p>
        </div>
    </div>
    <footer class="footer-transparent text-white py-3">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 Olivarez College. All rights reserved.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>