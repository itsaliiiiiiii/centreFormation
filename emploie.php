<?php
include("Data.php");
include("header.php");
check($database);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="cssfiles.css">
</head>
<body>
    <?php
        echo"<h1>"."Salle ".$_POST['NumSalle']."</h1>";
    ?>
<div class='tableRow header tableHeader'>
        <div class='tableCell'>Nom Professeur</div>
        <div class='tableCell'>Prénom Professeur</div>
        <div class='tableCell'>Niveau</div>
        <div class='tableCell'>Matiere</div>
        <div class='tableCell'>Jour</div>
        <div class='tableCell'>Temps</div>
    </div>
<?php
    $num=0;
    if (isset($_POST['emploie'])) {
        $sql="SELECT * FROM `Groupe` WHERE `NumSalle`='{$_POST['NumSalle']}' ";
        $list=mysqli_query($database,$sql);
        if ($list) {
            while($grp=mysqli_fetch_assoc($list)){
                $prof="SELECT `NomProf`,`PrenomProf` FROM `Prof` WHERE `IdProf`='{$grp['IdProf']}'";
                $info=mysqli_fetch_assoc(mysqli_query($database,$prof));
                $num++;
                $css = $num % 2 + 1 ;
                echo "<div class='tableRow tableProf" . $css . "'>";
                echo "<div class='tableCell'>" . $info['NomProf'] . "</div>";
                echo "<div class='tableCell'>" . $info['PrenomProf'] . "</div>";
                echo "<div class='tableCell'>" . $grp['Niveau'] . "</div>";
                echo "<div class='tableCell'>" . $grp['NomMatiere'] . "</div>";
                echo "<div class='tableCell'>" . $grp['jour'] . "</div>";
                echo "<div class='tableCell'>" . $grp['Temps'] . "</div>";
                echo "</div>";
            }
        }
    }
?>
</body>
</html>
</body>