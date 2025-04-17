<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Public Transport Passenger Volume Analysis and Scheduling System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/index.css">
</head>

<body>

    <?php include 'include/navbar.php'; ?>

    <main class="content-wrapper">
        <!-- Header stays at the top and aligned to the right -->
        <header class="my-4">
            <h1>Public Transport Passenger Volume <br> Analysis and Scheduling System</h1>
            <p class="description">A data-driven solution for optimizing <br> public transport schedules and improving
                commuter experience.</p>
        </header>


    </main>

    <?php include 'include/script.php'; ?>
    <?php include 'include/footer.php'; ?>