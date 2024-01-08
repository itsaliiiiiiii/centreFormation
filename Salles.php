<?php
    include("header.php");
    include("Data.php");
    check($database);
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="cssfiles.css">
</head>
<body>
    <h1 style='margin-bottom:70px;'>SALLES</h1>
    <div class='tableRow header  Row2 tableHeader'>
        <div class='tableCell'>Salles</div>
        <div class='tableCell tableButtonCell action'>Actions</div>
    </div>
    <?php
    $importSalle = "SELECT * FROM `Salle`";
    $result = mysqli_query($database, $importSalle);
    $num = 0;
    while ($salle= mysqli_fetch_assoc($result)) {
        $num++;
        $css = $num % 2 + 1 ;
        echo "<div class='tableRow Row2 tableSalle" . $css . "'>";
        echo "<div class='tableCell'>" . 'Salle '.$salle['NumSalle'] . "</div>";
        echo "<div class='tableCell tableButtonCell'>";
        echo "<form action='emploie.php' method='POST'>";
        echo "<input type='hidden' name='NumSalle' value='".$salle['NumSalle']."'>";
        echo "<input type='submit' name='emploie' value='emploie du temps' class='emploie'>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
    }
    ?>
    <div class='empty'></div>
</body>
</html>