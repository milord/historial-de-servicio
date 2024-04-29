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
        
    </div>

    <!-- Display the date difference -->
    <div class="grid-container">

        <!-- Add a header row here -->
        <div class="header-row">
            <div class="column"></div>
            <div class="column centered-column">Inicio</div>
            <div class="column centered-column">Término</div>
            <div class="column centered-column">Cómputo</div>
            <div class="column"></div>
        </div>

        <div class="header-row">
            <div class="column"></div>
            <div class="column centered-column">dd/mm/aaaa</div>
            <div class="column centered-column">dd/mm/aaaa</div>
            <div class="column centered-column">AA/MM/DD</div>
            <div class="column"></div>
        </div>


        <?php if (isset($_SESSION['dates'])): ?>
            <?php foreach ($_SESSION['dates'] as $index => $date): ?>
                
                <div class="result-row">
                    
                    <div class="column centered-column"><?php echo ($index + 1) . '.-'; ?></div>
                    <div class="column centered-column">
                        <?php
                            // Parse the date string and format it as "dd/mm/yyyy" 
                            $date1 = Carbon::parse($date['date1']);
                            echo $date1->format('d/m/Y'); 
                        ?>
                    </div> 
                    <div class="column centered-column">
                        <?php
                            // Parse the date string and format it as "dd/mm/yyyy"
                            $date2 = Carbon::parse($date['date2']);
                            echo $date2->format('d/m/Y'); 
                        ?>
                    </div>
                    <div class="column centered-column">
                        <?php 
                            // Calculate the difference between the two dates
                            $totalYears = $date1->diff($date2)->y;
                            $totalMonths = $date1->diff($date2)->m;
                            $totalDays = $date1->diff($date2)->d;

                            // Convert days to months
                            $totalMonths += floor($totalDays / 30);
                            $totalDays %= 30;

                            // Convert months to years
                            $totalYears += floor($totalMonths / 12);
                            $totalMonths %= 12;

                            

                            // Format the interval as "X years, Y months, Z days"
                            echo sprintf("<span id='total-years' class='total-years'>%sA/</span>", $totalYears);
                            echo sprintf("<span id='total-months' class='total-months'>%sM/</span>", $totalMonths);
                            echo sprintf("<span id='total-days' class='total-days'>%02dD/</span>", $totalDays);

                            // Add the years, months, and days to the grand total
                            $grandTotalYears += $totalYears;
                            $grandTotalMonths += $totalMonths;
                            $grandTotalDays += $totalDays;

                            // Display a warning if the grand total of years exceeds 28
                            if ($grandTotalYears > 28) {
                                echo "<script type='text/javascript'>alert('Warning: The grand total of years exceeds 28.');</script>";
                            }

                        ?>
                    </div>
                    <div class="column centered-column">
                        <!-- Delete button -->
                        <form action="process.php" method="post" style="display: inline;">
                            <input type="hidden" name="delete_index" value="<?php echo $index; ?>">
                            <input type="submit" value="x">
                        </form>
                    </div>
                </div>
                
            <?php endforeach; ?>
        <?php endif; ?>
        Cómputo:
        <div class="compute-row">
            <p>
                <!--  Display the grand total -->
                <?php
                    echo sprintf("<span id='grand-total-years' class='grand-total-years'>%sA/</span>", $grandTotalYears);
                    echo sprintf("<span id='grand-total-months' class='grand-total-months'>%sM/</span>", $grandTotalMonths);
                    echo sprintf("<span id='grand-total-days' class='grand-total-days'>%02dD/</span>", $grandTotalDays);
                ?>
            </p>
            
        </div>

        <p> 
                <?php
                // Convert excess days to months
                $excessMonths = floor($grandTotalDays / 30);
                $grandTotalMonths += $excessMonths;
                $grandTotalDays %= 30;

                // Convert excess months to years
                $excessYears = floor($grandTotalMonths / 12);
                $grandTotalYears += $excessYears;
                $grandTotalMonths %= 12;

                // Create a CarbonInterval instance
                $interval = CarbonInterval::years($grandTotalYears)
                ->months($grandTotalMonths)
                ->days($grandTotalDays);

                // Format the interval as "X years, Y months, Z days"
                $formattedInterval = $interval->forHumans([
                    'parts' => 3,
                    'join' => '/',
                    'short' => true]);

                echo sprintf("<span id='grand-total-interval' class='grand-total-interval'>%s</span>", $formattedInterval);
                ?>
            </p>

    </div>
</div>

</body>
</html>