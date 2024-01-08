<?php
    include("Data.php");
    check($database);
?>
<?php
    if(!empty($_POST['Nbrseance']) && isset($_POST['ajouterSeance'])){
        $Seance = " SELECT `NbrSeance` FROM `Appartenance` WHERE `IdPersonne` = '{$_POST['IdPersonne']}' AND IdGrp = '{$_POST['IdGrp']}'";
        $result  = mysqli_query($database,$Seance);
        if ($result && $numSeance = mysqli_fetch_assoc($result)) {
            $num = $numSeance['NbrSeance'] + $_POST['Nbrseance'];
            $updateQuery = "UPDATE `Appartenance` SET `NbrSeance`='{$num}' WHERE `IdPersonne` = '{$_POST['IdPersonne']}' AND IdGrp = '{$_POST['IdGrp']}'";
            mysqli_query($database, $updateQuery);
        }
    }
    if(isset($_POST['ajouterAbsence'])){
        $Abscence = " SELECT `NbrAbscence` FROM `Appartenance` WHERE `IdPersonne` = '{$_POST['IdPersonne']}' AND IdGrp = '{$_POST['IdGrp']}'";
        $result  = mysqli_query($database,$Abscence);
        if ($result && $numAbscence = mysqli_fetch_assoc($result)) {
            $num = $numAbscence['NbrAbscence'] + 1;
            $updateQuery = "UPDATE `Appartenance` SET `NbrAbscence`='{$num}' WHERE `IdPersonne` = '{$_POST['IdPersonne']}' AND IdGrp = '{$_POST['IdGrp']}'";
            mysqli_query($database, $updateQuery);
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="cssfiles.css">
</head>
<body>
    <?php
        include("header.php");
        $importDataEtudiant = "SELECT * FROM `Personne` WHERE `idPersonne` = '{$_POST['IdPersonne']}'";
        $Etudiant = mysqli_fetch_assoc(mysqli_query($database,$importDataEtudiant));
        $importifTest = "SELECT COUNT(*) AS test FROM `Suivie` WHERE `IdPersonne` = '{$_POST['IdPersonne']}' AND `NomFormation` = 'test'";
        $Test = mysqli_fetch_assoc(mysqli_query($database,$importifTest));
        $selectGroups = "SELECT * FROM `Appartenance`  WHERE `IdPersonne` = '{$_POST['IdPersonne']}'";
        $Groups = mysqli_query($database,$selectGroups);
        echo "<div class = 'Etudiant'>".$Etudiant['Nom']." ".$Etudiant['Prenom']." - ".$Etudiant['tele']."</div>";
        if($Test['test'] != 0){
            echo "<div class ='faitTest'>Étudiant déja fait le Test</div>";
        }else{
            echo "<div class ='faitTest'>Étudiant n'a pas passé le test</div>";
        }
        echo "<div class='table'>";
        echo "<div class='tableRow table2 header'>";
        echo "<div class='tableCell'>Matière</div>";
        echo "<div class='tableCell'>Jour</div>";
        echo "<div class='tableCell'>Niveau</div>";
        echo "<div class='tableCell'>Temps</div>";
        echo "<div class='tableCell'>Seance</div>";
        echo "<div class='tableCell'>Abscence</div>";
        echo "<div class='tableCell'>Date Debut</div>";
        echo "<div class='tableCell'>Date Fin</div>";
        echo "<div class='tableCell'>Actions</div>";
        echo "</div>";
        $num = 0;
        while ($group = mysqli_fetch_assoc($Groups)) {
            $FromGroup = "SELECT * FROM `Groupe`  WHERE `IdGrp` = '{$group['IdGrp']}'";
            $DataGrp = mysqli_query($database, $FromGroup);
            while ($Grp = mysqli_fetch_assoc($DataGrp)) {
                $num++;
                $css = $num % 2 + 1;
                echo "<div class='tableRow table2 Row2 tablePerson". $css ."'>";
                echo "<div class='tableCell'>" . $Grp['NomMatiere'] . "</div>";
                echo "<div class='tableCell'>" . $Grp['jour'] . "</div>";
                echo "<div class='tableCell'>" . $Grp['Niveau'] . "</div>";
                echo "<div class='tableCell'>" . $Grp['Temps'] . " PM</div>";
                if (isset($group['DateDebut'])) {
                    $Nbrseance = ($group['NbrAbscence'] == 0) ? $group['NbrSeance'] : $group['NbrSeance'] + 1;
                    echo "<div class='tableCell'>" . $group['NbrSeance'] . "</div>";
                    echo "<div class='tableCell'>" . $group['NbrAbscence'] . "</div>";
                    echo "<div class='tableCell'>" . $group['DateDebut'] . "</div>";
                    echo "<div class='tableCell'>" . DateFin($group['DateDebut'], $Nbrseance) . "</div>";
                    echo "<div class='tableCell'>";
                    if (!compareDate(DateFin($group['DateDebut'], $Nbrseance))) {
                        echo "<span class='doitPayer'>Doit Payer</span>";
                    }
                    echo "<form method='POST'>";
                    echo "<input type='text' name='Nbrseance' class='inputNbrSeance'>";
                    echo "<input type='hidden' name='IdGrp' value='" . $group['IdGrp'] . "'>";
                    echo "<input type='hidden' name='IdPersonne' value='" . $_POST['IdPersonne'] . "'>";
                    echo "<input type='submit' name='ajouterSeance' value='Ajouter Seance' class='seanceButton'>";
                    echo "</form>";
                    echo "<form method='POST'>";
                    echo "<input type='hidden' name='IdGrp' value='" . $group['IdGrp'] . "'>";
                    echo "<input type='hidden' name='IdPersonne' value='" . $_POST['IdPersonne'] . "'>";
                    echo "<input type='submit' name='ajouterAbsence' value='Ajouter Absence' class='absenceButton'>";
                    echo "</form>";
                    echo "</div>";
                    } else {
                    echo "<div class='tableCell'>" . $group['NbrSeance'] . "</div>";
                    echo "<div class='tableCell'>-</div>";
                    echo "<div class='tableCell'>-</div>";
                    echo "<div class='tableCell'>-</div>";
                    echo "<div class='tableCell'>-</div>";
                    }
                    echo "</div>";
                }
                mysqli_free_result($DataGrp);
        }
        echo "</div>";
    ?>
    <div class='empty'></div>
</body>
</html>