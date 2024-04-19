<?php 
include 'process.php';

require 'vendor/autoload.php';

use Carbon\Carbon;
use Carbon\CarbonInterval;

Carbon::setLocale('es');

?>

<!DOCTYPE html>
<html>
<head>
    <script src="script.js"></script>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

<header>
    <h3>Acumulado de días, meses y años para el personal de la DGETI</h3>
</header>

<div class="container">
    <div class="section">
        <form method="post" action="index.php" onsubmit="return validateForm()">
            <label for="date1" id="label-date1">Inicio:</label>
            <input type="date" id="date1" name="date1"><br>
            <label for="date2" id="label-date2">Término:</label>
            <input type="date" id="date2" name="date2"><br>
            <input type="submit" value="Calcular">
            <input type="button" value="Reiniciar" onclick="resetForm()">
        </form>

        <?php
        // Display the total sum of date differences
        
        $interval = CarbonInterval::create(0, $totalYears, $totalMonths, $totalDays);
        
        echo sprintf("<span id='total-difference' class='total-difference'>Cómputo: %s</span>", $interval->forHumans(['parts' => 3]));

        ?>
    </div>

    <!-- Display the date difference -->
    <div id="results-display" class="section">
        <?php if (isset($_SESSION['dates'])): ?>
            <?php foreach ($_SESSION['dates'] as $index => $date): ?>
                <div class="result-row">
                    <div class="column"><?php echo ($index + 1) . '.-'; ?></div>
                    <div class="column">
                        <?php 
                            $date1 = Carbon::parse($date['date1']);
                            echo $date1->format('d/m/Y'); 
                        ?> 
                        and 
                        <?php 
                            $date2 = Carbon::parse($date['date2']);
                            echo $date2->format('d/m/Y'); 
                        ?>:
                    </div>
                    <div class="column">
                        <?php 
                            $totalYears = $date1->diff($date2)->y;
                            $totalMonths = $date1->diff($date2)->m;
                            $totalDays = $date1->diff($date2)->d;
                            $interval = CarbonInterval::create(0, $totalYears, $totalMonths, $totalDays);
                            if ($totalYears == 0 && $totalMonths == 0 && $totalDays == 0) {
                                echo "<span id='total-years' class='total-years'>0A/</span>";
                                echo "<span id='total-months' class='total-months'>0M/</span>";
                                echo "<span id='total-days' class='total-days'>00D/</span>";
                            } else {
                                echo sprintf("<span id='total-years' class='total-years'>%sA/</span>", $totalYears);
                                echo sprintf("<span id='total-months' class='total-months'>%sM/</span>", $totalMonths);
                                echo sprintf("<span id='total-days' class='total-days'>%02dD/</span>", $totalDays);
                            }
                        ?>
                    </div>
                    <div class="column">
                        <!-- Delete button -->
                        <form action="process.php" method="post" style="display: inline;">
                            <input type="hidden" name="delete_index" value="<?php echo $index; ?>">
                            <input type="submit" value="x">
                        </form>
                    </div>
                </div>
                
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>