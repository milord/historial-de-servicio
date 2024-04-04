<?php

function dateDifference($date1, $date2) {
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    return array('years' => $interval->y, 'months' => $interval->m, 'days' => $interval->d);
}

$result = dateDifference('2000-01-01', '2020-12-31');
print_r($result);  // Outputs: Array ( [years] => 20 [months] => 11 [days] => 30 )

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date1 = $_POST['date1'];
    $date2 = $_POST['date2'];

    // Validate the dates
    $datetime1 = DateTime::createFromFormat('Y-m-d', $date1);
    $datetime2 = DateTime::createFromFormat('Y-m-d', $date2);

    if ($datetime1 && $datetime1->format('Y-m-d') === $date1 && $datetime2 && $datetime2->format('Y-m-d') === $date2) {
        $result = dateDifference($date1, $date2);
        echo $result;
    } else {
        echo "Invalid date format. Please enter dates in the format 'YYYY-MM-DD'.";
    }
}

?>