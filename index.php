<?php
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date1 = $_POST['date1'];
    $date2 = $_POST['date2'];

    // Validate the dates
    $datetime1 = DateTime::createFromFormat('Y-m-d', $date1);
    $datetime2 = DateTime::createFromFormat('Y-m-d', $date2);

    if ($datetime1 && $datetime1->format('Y-m-d') === $date1 && $datetime2 && $datetime2->format('Y-m-d') === $date2) {
        $result = dateDifference($date1, $date2);
        $message = "The difference between the two dates is: " . $result;
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

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <label for="date1">Date 1:</label><br>
    <input type="date" id="date1" name="date1"><br>
    <label for="date2">Date 2:</label><br>
    <input type="date" id="date2" name="date2"><br>
    <input type="submit" value="Submit">
</form>

<p><?php echo $message; ?></p>