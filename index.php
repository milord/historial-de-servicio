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

<div class="container">
    <div class="section">
        <form method="post" action="index.php">
            <label for="date1" id="label-date1">Inicio:</label>
            <input type="date" id="date1" name="date1"><br>
            <label for="date2" id="label-date2">Término:</label>
            <input type="date" id="date2" name="date2"><br>
            <input type="submit" value="Calcular">
            <input type="submit" name="reset" value="Reiniciar">
        </form>
    </div>

    <div class="section">
        <!-- Your results display code goes here -->

        <?php
        $index = 1;
        
        echo "<div class='flex-container'>";
        echo "<div class='flex-row header'>
                <div>Caso No.</div>
                <div>Inicio</div>
                <div>Término</div>
                <div>Cómputo</div>
            </div>";
        foreach ($dates as $date) {
            echo sprintf("<div class='flex-row'><div>%d</div><div>%s</div><div>%s</div><div>%s</div></div>", $index, $date['date1'], $date['date2'], $date['difference']);
            $index++;
        }
        echo "</div>";

        // Display the total sum of date differences
        echo sprintf("<span class='total-difference'>Cómputo: %d años, %d meses, %d días</span>", $totalYears, $totalMonths, $totalDays);
        ?>
    </div>
</div>

</body>
</html>