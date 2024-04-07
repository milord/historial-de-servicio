<?php
session_start();

$message = '';
$dates = isset($_SESSION['dates']) ? $_SESSION['dates'] : array();
$totalYears = isset($_SESSION['totalYears']) ? $_SESSION['totalYears'] : 0;
$totalMonths = isset($_SESSION['totalMonths']) ? $_SESSION['totalMonths'] : 0;
$totalDays = isset($_SESSION['totalDays']) ? $_SESSION['totalDays'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reset'])) {
        // The reset button was clicked
        session_unset();
        header("Refresh:0");
        exit();
    }

    $date1 = $_POST['date1'];
    $date2 = $_POST['date2'];

    // Validate the dates
    $datetime1 = DateTime::createFromFormat('Y-m-d', $date1);
    $datetime2 = DateTime::createFromFormat('Y-m-d', $date2);

    if ($datetime1 && $datetime1->format('Y-m-d') === $date1 && $datetime2 && $datetime2->format('Y-m-d') === $date2) {
        $result = dateDifference($date1, $date2);
        $dates[] = array('date1' => $date1, 'date2' => $date2, 'difference' => $result);
        $_SESSION['dates'] = $dates;

        // Extract the years, months, and days from the date difference and add them to the total
        list($years, $months, $days) = sscanf($result, '%d years, %d months, %d days');
        $totalYears += $years;
        $totalMonths += $months;
        $totalDays += $days;

        // Store the total years, months, and days in the session
        $_SESSION['totalYears'] = $totalYears;
        $_SESSION['totalMonths'] = $totalMonths;
        $_SESSION['totalDays'] = $totalDays;
    } else {
        $message = "Invalid date format. Please enter dates in the format 'YYYY-MM-DD'.";
    }
}

function dateDifference($date1, $date2) {
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    return sprintf('%d years, %d months, %d days', $interval->y, $interval->m, $interval->d);

}
?>



<p><?php echo $message; ?></p>
