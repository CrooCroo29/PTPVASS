<?php
include 'database/db_connect.php'; // Ensure database connection
include 'C:\xampp\htdocs\DRAF\admin\function\function.php';
session_start();

if (isset($_POST['signup_btn'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check if passwords match
    if ($password === $confirm_password) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email already exists
        $check_email_query = "SELECT id FROM passengers WHERE email = ?";
        $stmt = $conn->prepare($check_email_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['status'] = "Email already exists!";
            header('Location: signup.php');
            exit();
        }
        $stmt->close();

        // Insert new user into the database
        $query = "INSERT INTO passengers (full_name, email, password, phone) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $phone);

        if ($stmt->execute()) {
            // Save user data to JSON file
            if (!saveUserDataToFile($full_name, $email, $phone, $hashed_password)) {
                $_SESSION['status'] = "User registered, but failed to save to JSON file.";
            } else {
                $_SESSION['success'] = "User registered successfully!";
            }
            header('Location: login.php');
            exit();
        } else {
            $_SESSION['status'] = "User registration failed. Try again!";
            header('Location: signup.php');
            exit();
        }

    } else {
        $_SESSION['status'] = "Passwords do not match!";
        header('Location: signup.php');
        exit();
    }
}

// ** LOGIN PROCESS **
if (isset($_POST['login']) || isset($_POST['admin_login'])) {
    require 'database/db_connect.php'; // Ensure database connection is included

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if (isset($_POST['admin_login'])) {
        // **Admin Login Query (Check in `admin` table)**
        $query = "SELECT * FROM admin WHERE email='$email'";
    } else {
        // **User Login Query (Check in `users` table)**
        $query = "SELECT * FROM passengers WHERE email='$email'";
    }

    $query_run = mysqli_query($conn, $query);

    if (mysqli_num_rows($query_run) > 0) {
        $userdata = mysqli_fetch_assoc($query_run);
        $hashed_password = $userdata['password'];

        // **Verify Password**
        if (password_verify($password, $hashed_password)) {
            $_SESSION['auth'] = true;
            $_SESSION['auth_user'] = [
                'user_id' => $userdata['id'],
                'name' => $userdata['full_name'],
                'email' => $userdata['email']
            ];

            if (isset($_POST['admin_login'])) {
                $_SESSION['role'] = "admin"; // Set role as admin
                header("Location: admin/index.php"); // Redirect Admin
            } else {
                $_SESSION['role'] = "user"; // Set role as user
                header("Location: index.php"); // Redirect User
            }
            exit();
        } else {
            $_SESSION["login_error"] = "Incorrect password. Please try again.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION["login_error"] = "The email address does not exist.";
        header("Location: login.php");
        exit();
    }
}

// ** Survey Submission Process **
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['survey_submit'])) {
    // Get form data
    $date = $_POST['date'];
    $time = $_POST['time'];
    $destination = $_POST['destination'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    // Convert to 24-hour format
    $time = date("H:i:s", strtotime($time));

// Get the schedule_id that corresponds to the selected date, time, and destination
$stmt = $conn->prepare("SELECT id FROM schedule_data WHERE date = ? AND arrival_time = ? AND destination = ?");
$stmt->bind_param("sss", $date, $time, $destination);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $schedule_id = $row['id'];

        // Determine the day of the week
        $day_of_week = date("l", strtotime($date)); // Monday, Tuesday, etc.

        // Determine if it's a weekday or weekend
        $day_type = (in_array($day_of_week, ['Saturday', 'Sunday'])) ? 'weekend' : 'weekday';

        // Determine the day time (Early Morning, Morning, Afternoon, Evening)
        $hour = date("H", strtotime($time));
        if ($hour >= 1 && $hour < 6) {
            $day_time = 'early morning';
        } elseif ($hour >= 6 && $hour < 12) {
            $day_time = 'morning';
        } elseif ($hour >= 12 && $hour < 18) {
            $day_time = 'afternoon';
        } else {
            $day_time = 'evening';
        }

        // Insert survey data into passenger_survey table
        $stmt = $conn->prepare("INSERT INTO passenger_survey (schedule_id, survey_date, destination, rating, comments, day_of_week, day_type, day_time) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississss", $schedule_id, $date, $destination, $rating, $comments, $day_of_week, $day_type, $day_time);

        if ($stmt->execute()) {
            // Get the last inserted ID
            $survey_id = $conn->insert_id;

            // Save data to a JSON file
            $survey_data = [
                'id' => $survey_id, // Include the auto-incremented id
                'schedule_id' => $schedule_id,
                'survey_date' => $date,
                'destination' => $destination,
                'rating' => $rating,
                'comments' => $comments,
                'day_of_week' => $day_of_week,
                'day_type' => $day_type,
                'day_time' => $day_time
            ];

            // Path to the JSON file where the survey data will be saved
            $json_file_path = 'admin/data/passenger_survey.json';

            // Read the current contents of the JSON file
            if (file_exists($json_file_path)) {
                $json_data = json_decode(file_get_contents($json_file_path), true);
            } else {
                $json_data = [];
            }

            // Append the new survey data to the JSON array
            $json_data[] = $survey_data;

            // Save the updated data back to the JSON file
            file_put_contents($json_file_path, json_encode($json_data, JSON_PRETTY_PRINT));

            // Set session variable to show success message
            $_SESSION['survey_success'] = 'Survey submitted successfully!';

            // Redirect to datacollection.php after success
            header('Location: datacollection.php');
            exit;  // Ensure that the script stops after the redirect
        } else {
            // Handle failure
            echo json_encode(['success' => false, 'message' => 'Failed to submit survey']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No matching schedule found']);
    }
}
?>