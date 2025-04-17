<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PTPVAS System Data Collection</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/datacollection.css">
</head>

<body>
    <?php include 'include/navbar.php'; ?>

    <!-- Notification -->
    <?php
    if (isset($_SESSION['survey_success'])) {
        echo '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                ' . $_SESSION['survey_success'] . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        // Unset the session variable to avoid showing the message again
        unset($_SESSION['survey_success']);
    }
    ?>

    <!-- Passenger Volume Graph Title - Move it Above the Box -->
    <div class="container my-3 mb-5">
        <h2 class="graph-title">Passenger Volume Graph</h2>

        <!-- Graph Box -->
        <div class="graph-box">
            <!-- Date Selector (🔹 Add it here) -->
            <label for="dateSelector" class="form-label">Select Date:</label>
            <select id="dateSelector" class="form-select mb-3"></select>

            <div class="chart-scroll-container">
    <div class="chart-container">
        <canvas id="graphCanvas"></canvas>
    </div>
</div>


            <!-- Buttons Below Graph -->
            <div class="mt-4">
                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#surveyModal">
                    Open Survey Form
                </button>
            </div>
        </div>
    </div>

    <!-- Survey Form Modal -->
    <div class="modal fade" id="surveyModal" tabindex="-1" aria-labelledby="surveyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="surveyModalLabel">Passenger Survey Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="code.php" method="POST">
                        <!-- Date Question -->
                        <div class="mb-3">
                            <label for="date" class="form-label">Date of Journey</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>

                        <!-- Time Question (Updated to be dynamically populated) -->
                        <div class="mb-3">
                            <label for="time" class="form-label">Available Arrival Times</label>
                            <select class="form-select" id="time" name="time" required>
                                <option value="">Select time</option>
                                <!-- Options will be loaded dynamically with JS -->
                            </select>
                        </div>

                        <!-- Destination Question (Now dynamic) -->
                        <div class="mb-3">
                            <label for="destination" class="form-label">Destination</label>
                            <select class="form-select" id="destination" name="destination" required>
                                <option value="">Select destination</option>
                            </select>
                        </div>

                        <!-- Rating Question -->
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rate Your Experience</label>
                            <select class="form-select" id="rating" name="rating" required>
                                <option value="1">1 - Very Poor</option>
                                <option value="2">2 - Poor</option>
                                <option value="3">3 - Average</option>
                                <option value="4">4 - Good</option>
                                <option value="5">5 - Excellent</option>
                            </select>
                        </div>

                        <!-- Additional Comments -->
                        <div class="mb-3">
                            <label for="comments" class="form-label">Any Other Comments</label>
                            <textarea class="form-control" id="comments" name="comments" rows="3"
                                placeholder="Enter any other comments..."></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" name="survey_submit" class="btn btn-success w-100">Submit Survey</button>
                        </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/script.php'; ?>
    <?php include 'include/footer.php'; ?>

</body>
</html>
