<?php
session_start();
?>
<?php
include 'include/navbar.php';
include 'database/db_connect.php'; // Database connection


// Fetch Passenger Volume Over Time (Hourly) from schedule_data
$hourly_data = [];
$query = "SELECT arrival_time, SUM(passenger_count) AS count FROM schedule_data GROUP BY arrival_time";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $hourly_data[] = $row;
}

// Fetch Passenger Volume by Day of the Week from schedule_data
$week_data = [];
$query = "SELECT day, SUM(passenger_count) AS count FROM schedule_data GROUP BY day";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $week_data[] = $row;
}

// Fetch Passenger Volume by Time of Day from schedule_data
$time_of_day_data = [];
$query = "SELECT time_of_day, SUM(passenger_count) AS count FROM schedule_data GROUP BY time_of_day";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $time_of_day_data[] = $row;
}

// Fetch Weekday vs. Weekend Passenger Volume from schedule_data
$day_type_data = [];
$query = "SELECT day_type, SUM(passenger_count) AS count FROM schedule_data GROUP BY day_type";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $day_type_data[] = $row;
}

// Passenger Volume Trend by Date
$daily_trend_data = [];
$query = "SELECT date, SUM(passenger_count) AS count FROM schedule_data GROUP BY date ORDER BY date ASC";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $daily_trend_data[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTPVAS System Analysis & Reports</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/analysis&reports.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container my-5">
        <div class="row">
            <!-- Left Side - Dropdown and Graph -->
<!-- Day Dropdown and Filtered Chart -->
<div class="col-md-4">
    <select class="form-select mb-3" id="daySelect" onchange="updateDayChart()">
        <option value="">Select Day</option>
        <option value="Monday">Monday</option>
        <option value="Tuesday">Tuesday</option>
        <option value="Wednesday">Wednesday</option>
        <option value="Thursday">Thursday</option>
        <option value="Friday">Friday</option>
        <option value="Saturday">Saturday</option>
        <option value="Sunday">Sunday</option>
    </select>

    <div class="box">
        <canvas id="filteredChart"></canvas>
    </div>
</div>

<!-- Right Side - Remaining Charts -->
<div class="col-md-8">
    <div class="chart-container">
        <div class="chart-box mb-4">
            <h6>Passenger Volume Over Time (Hourly)</h6>
            <canvas id="lineChartHourly"></canvas>
        </div>
        <div class="chart-box mb-4">
            <h6>Passenger Volume by Time of Day</h6>
            <canvas id="pieChartTimeOfDay"></canvas>
        </div>
        <div class="chart-box mb-4">
            <h6>Weekday vs. Weekend Passenger Volume</h6>
            <canvas id="stackedBarChart"></canvas>
        </div>
        <div class="chart-box">
            <h6>Passenger Volume Trend by Date</h6>
            <canvas id="dailyTrendChart"></canvas>
        </div>
    </div>
</div>

        </div>
    </div>

    <?php include 'include/script.php'; ?>
    <?php include 'include/footer.php'; ?>