<?php
session_start(); // Start the session

// Check if user is already logged in
if (isset($_SESSION["id"])) {
    header("Location: index.php"); // Redirect to index.php if already logged in
    exit();
}

// Check if there’s an error message from previous login attempt
$error = "";
if (isset($_SESSION["login_error"])) {
    $error = $_SESSION["login_error"];
    unset($_SESSION["login_error"]); // Clear error message after displaying
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTPVAS System Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>

    <?php include 'include/navbar.php'; ?>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4 shadow-lg login-box">
            <h3 class="text-center mb-4">Login</h3>

            <!-- Display error message if login fails -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="code.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <!-- Regular User Login Button -->
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>

                <!-- Admin Login Button -->
                <button type="submit" name="admin_login" class="btn btn-danger w-100 mt-2">Admin Login</button>
            </form>
            <!-- Signup Link -->
            <!-- Signup Link -->
            <div class="text-center mt-3">
                <a href="signup.php" class="create-account-link">Create your Account</a>
            </div>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>

</body>

</html>