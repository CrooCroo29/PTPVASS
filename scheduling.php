<?php
session_start();
?>
<?php
include 'database/db_connect.php'; // Database connection

// Get unique destinations
$destination_options = [];
$query = "SELECT DISTINCT destination FROM schedule_data";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $destination_options[] = $row['destination'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTPVAS System Scheduling Suggestion</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/scheduling.css">

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<?php
    include 'include/navbar.php';
    include 'database/db_connect.php';

    $route_data = [];
    $current_time = date('H:i:s');
    $current_datetime = new DateTime($current_time);

    $active_routes_by_date = [];
    $inactive_routes_by_date = [];

    $query = "
    SELECT 
        schedule.id AS schedule_id, 
        schedule.date,
        route.arrived_time, 
        route.bus, 
        route.destination, 
        route.gate, 
        route.bay, 
        schedule.status
    FROM route
    JOIN schedule ON route.id = schedule.route_id
    ORDER BY 
        schedule.date ASC, 
        route.arrived_time ASC
    ";

    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $route_data[] = $row;
    }

    foreach ($route_data as $route) {
        $date = $route['date'];
        $arrival_time = $route['arrived_time'];

        if (in_array($route['status'], ['waiting', 'arriving', 'boarding', 'cancelled', 'departed'])) {
            $active_routes_by_date[$date][$arrival_time][] = $route;
        } else {
            $inactive_routes_by_date[$date][$arrival_time][] = $route;
        }
    }

?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-4">
            <select class="form-select mb-2" id="destinationSelect" onchange="updateChart()">
                <option value="">Select Destination</option>
                <?php foreach ($destination_options as $destination): ?>
                    <option value="<?= htmlspecialchars($destination) ?>"><?= htmlspecialchars($destination) ?></option>
                <?php endforeach; ?>
            </select>

            <select class="form-select mb-2" id="daySelect" onchange="updateChart()">
                <option value="">Select Day</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
        </div>

        <div class="col-md-8">
            <div class="box p-3 border rounded shadow-sm" style="background-color: #f8f9fa;">
                <canvas id="filteredChart"></canvas>
            </div>
        </div>
    </div>

    <div style="background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); margin-top: 20px; max-height: 800px; overflow-y: auto;">
        <?php foreach ($active_routes_by_date as $date => $routes_by_time): ?>
            <h4 class="text-center text-white p-2" style="background-color: rgb(0, 92, 184); font-weight: bold; border-radius: 5px;">
                Date: <?= htmlspecialchars(date("F j, Y", strtotime($date))) ?>
            </h4>
        
            <?php foreach ($routes_by_time as $arrived_time => $routes): ?>
                <?php
                $timeObj = DateTime::createFromFormat('H:i:s', $arrived_time) ?: DateTime::createFromFormat('H:i', $arrived_time);
                $formatted_time = $timeObj ? $timeObj->format('g:i A') : htmlspecialchars($arrived_time);
                ?>

                <h5 class="text-center text-white p-2" style="background-color: #007bff; font-weight: bold; border-radius: 5px;">
                    Arrival Time: <?= $formatted_time ?>
                </h5>

                <table class="table table-striped table-bordered text-center" style="background-color: white;">
                    <thead style="background-color: #5a67d8; color: white;">
                        <tr>
                            <th style="background-color: gray; color: white;">Bus</th>
                            <th style="background-color: gray; color: white;">Destination</th>
                            <th style="background-color: gray; color: white;">Gate</th>
                            <th style="background-color: gray; color: white;">Bay</th>
                            <th style="background-color: gray; color: white;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($routes as $route): ?>
                            <tr>
                                <td style="text-transform: uppercase; font-weight: bold;"><?= htmlspecialchars($route['bus']) ?></td>
                                <td style="text-transform: uppercase; font-weight: bold;"><?= htmlspecialchars($route['destination']) ?></td>
                                <td style="text-transform: uppercase; font-weight: bold;"><?= htmlspecialchars($route['gate']) ?></td>
                                <td style="text-transform: uppercase; font-weight: bold;"><?= htmlspecialchars($route['bay']) ?></td>
                                <td style="font-weight: bold;
                                    <?php
                                    switch ($route['status']) {
                                        case 'waiting':
                                            echo 'background-color: yellow; color: black;';
                                            $status_label = 'Waiting';
                                            break;
                                        case 'arriving':
                                            echo 'background-color: blue; color: white;';
                                            $status_label = 'Arriving';
                                            break;
                                        case 'boarding':
                                            echo 'background-color: green; color: white;';
                                            $status_label = 'Boarding';
                                            break;
                                        case 'cancelled':
                                            echo 'background-color: red; color: white;';
                                            $status_label = 'Cancelled';
                                            break;
                                        case 'departed':
                                            echo 'background-color: gray; color: white;';
                                            $status_label = 'Departed';
                                            break;
                                        default:
                                            echo 'background-color: white; color: black;';
                                            $status_label = 'Unknown';
                                    }
                                    ?>">
                                    <?= htmlspecialchars($status_label) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <div style="background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); margin-top: 20px;">
        <div id="inactiveRouteTable" class="table-responsive" style="max-height: 800px; overflow-y: auto;">
            <?php foreach ($inactive_routes_by_date as $date => $routes_by_time): ?>
                <h4 class="text-center text-white p-2" style="background-color: rgb(0, 92, 184); font-weight: bold; border-radius: 5px;">
                    Date: <?= htmlspecialchars(date("F j, Y", strtotime($date))) ?>
                </h4>
                <?php foreach ($routes_by_time as $arrived_time => $routes): ?>
                    <?php
                    $timeObj = DateTime::createFromFormat('H:i:s', $arrived_time) ?: DateTime::createFromFormat('H:i', $arrived_time);
                    $formatted_time = $timeObj ? $timeObj->format('g:i A') : htmlspecialchars($arrived_time);
                    ?>
                    <h5 class="text-center text-white p-2" style="background-color: #007bff; font-weight: bold; border-radius: 5px;">
                        Arrival Time: <?= $formatted_time ?>
                    </h5>
                    <table class="table table-striped table-bordered text-center">
                        <thead style="background-color: #5a67d8; color: white;">
                            <tr>
                                <th style="background-color: gray; color: white;">Bus</th>
                                <th style="background-color: gray; color: white;">Destination</th>
                                <th style="background-color: gray; color: white;">Gate</th>
                                <th style="background-color: gray; color: white;">Bay</th>
                                <th style="background-color: gray; color: white;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($routes as $route): ?>
                                <tr>
                                    <td style="text-transform: uppercase; font-weight: bold;"><?= htmlspecialchars($route['bus']) ?></td>
                                    <td style="text-transform: uppercase; font-weight: bold;"><?= htmlspecialchars($route['destination']) ?></td>
                                    <td style="text-transform: uppercase; font-weight: bold;"><?= htmlspecialchars($route['gate']) ?></td>
                                    <td style="text-transform: uppercase; font-weight: bold;"><?= htmlspecialchars($route['bay']) ?></td>
                                    <td style="font-weight: bold;
                                        <?php
                                        switch ($route['status']) {
                                            case 'departed2':
                                                echo 'background-color: darkgray; color: white;';
                                                $status_label = 'Departed';
                                                break;
                                            case 'cancelled2':
                                                echo 'background-color: brown; color: white;';
                                                $status_label = 'Cancelled';
                                                break;
                                            default:
                                                echo 'background-color: white; color: black;';
                                                $status_label = 'Unknown';
                                        }
                                        ?>">
                                        <?= htmlspecialchars($status_label) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'include/script.php'; ?>
<?php include 'include/footer.php'; ?>
</body>

</html>