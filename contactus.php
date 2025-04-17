<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTPVAS System Contact Us</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/contactus.css">
</head>

<body>
    <?php include 'include/navbar.php'; ?>
    <div class="container my-5">
        <h2 class="text-center mb-4">Contact Us</h2>

                <!-- Display Success or Error Message -->
                <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Contact Form -->
        <div class="row">
            <div class="col-md-6">
                <form action="function/contact_process.php" method="POST" class="p-4 border rounded bg-white shadow">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Message</button>
                </form>
            </div>

            <!-- Contact Info & Google Map -->
            <div class="col-md-6">
                <div class="p-4 border rounded bg-white shadow">
                    <h4>Our Office</h4>
                    <p><strong>Address:</strong> Pag-asa St., Brgy. Caniogan, Pasig City</p>
                    <p><strong>Phone:</strong> +63 912 345 6789</p>
                    <p><strong>Email:</strong> support@ptpvas.com</p>
                </div>

<!-- Google Map Embed -->
<div class="mt-3">
    <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1930.1082411111878!2d121.07382017840253!3d14.57625406652895!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c84857e0faed%3A0x8e24b614e52c8c8a!2sPag-asa%20St%2C%20Pasig%2C%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1712816340000"
        width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
    </iframe>
</div>

            </div>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>