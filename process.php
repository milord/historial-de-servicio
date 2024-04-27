<?php
session_start();

require 'vendor/autoload.php';

use Carbon\Carbon;
use Carbon\CarbonInterval;

$grandTotalYears = 0;
$grandTotalMonths = 0;
$grandTotalDays = 0;

$message = '';
$dates = isset($_SESSION['dates']) ? $_SESSION['dates'] : array();
$totalYears = isset($_SESSION['totalYears']) ? $_SESSION['totalYears'] : 0;
$totalMonths = isset($_SESSION['totalMonths']) ? $_SESSION['totalMonths'] : 0;
$totalDays = isset($_SESSION['totalDays']) ? $_SESSION['totalDays'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_index'])) {
        // The delete button was clicked
        $index = $_POST['delete_index'];
        unset($_SESSION['dates'][$index]);
        header("Location: index.php");
        exit();
    }

    // Retrieve the dates from the POST data
    $date1String = $_POST['date1'];
    $date2String = $_POST['date2'];

    // Create DateTime objects from the date strings
    $datetime1 = new DateTime($date1String);
    $datetime2 = new DateTime($date2String);

    // Check if the dates are valid
    if ($datetime1 && $datetime1->format('Y-m-d') === $date1String && $datetime2 && $datetime2->format('Y-m-d') === $date2String) {
        // The dates are valid
        $date1 = Carbon::parse($date1String);
        $date2 = Carbon::parse($date2String);
        $interval = $date1->diff($date2);

        // Format the interval as "X years, Y months, Z days"
        $result = sprintf('%d years, %d months, %d days', $interval->y, $interval->m, $interval->d);

        // Extract the years, months, and days from the date difference and add them to the total
        list($years, $months, $days) = sscanf($result, '%d years, %d months, %d days');
        $totalYears += $years;
        $totalMonths += $months;
        $totalDays += $days;

        // Store the date strings and the date difference in the session
        $_SESSION['dates'][] = array('date1' => $date1String, 'date2' => $date2String, 'difference' => $result);

    } else {
        $message = "Invalid date format. Please enter dates in the format 'DD-MM-YYYY'.";
    }

}

// Function to calculate the difference between two dates
function dateDifference($date1, $date2) {
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
   
    $interval = $datetime1->diff($datetime2);

    return sprintf('%d years, %d months, %d days', $interval->y, $interval->m, $interval->d);
}

?>



<p><?php echo $message; ?></p>

