<?php include 'process.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

<header>
    <h3>Acumulado de días, meses y años para el personal de DGETI</h3>
</header>

<form method="post" action="index.php">
    <label for="date1" id="label-date1">Date 1:</label><br>
    <input type="date" id="date1" name="date1"><br>
    <label for="date2">Date 2:</label><br>
    <input type="date" id="date2" name="date2"><br>
    <input type="submit" value="Submit">
    <input type="submit" name="reset" value="Reset">
</form>

<!-- Your results display code goes here -->

<?php
$index = 1;
foreach ($dates as $date) {
    echo sprintf("<span class='date-difference'>%d. The difference between %s and %s is: %s</span><br>", $index, $date['date1'], $date['date2'], $date['difference']);
    $index++;
}
// Display the total sum of date differences
    
    echo sprintf("<span class='total-difference'>The total sum of differences is: %d years, %d months, %d days</span>", $totalYears, $totalMonths, $totalDays);
    
?>

</body>
</html>