<?php
    include("header.php");
    include("Data.php");
    check($database);
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="cssfiles.css">
</head>
<body>
    <h1>ÉTUDIANTS</h1>
    <div class='Row tableRow header tableHeaderPerson'>
        <div class='tableCell'>Nom</div>
        <div class='tableCell'>Prénom</div>
        <div class='tableCell'>Téléphone</div>
        <div class='tableCell tableButtonCell'>Actions</div>
    </div>
    <?php
        $importPerson = "SELECT * FROM `Personne`";
        $result = mysqli_query($database, $importPerson);
        $num = 0;
        while ($person = mysqli_fetch_assoc($result)) {
            $searchApp = "SELECT COUNT(*) AS Person FROM `Appartenance` WHERE IdPersonne = '{$person['idPersonne']}'";
            $numPerson = mysqli_fetch_assoc(mysqli_query($database,$searchApp));
            if($numPerson['Person'] != 0){
                $num++;
                $css = $num % 2 + 1 ;
                echo "<div class='Row tableRow tablePerson" . $css . "'>";
                echo "<div class='tableCell'>" . $person['Nom'] . "</div>";
                echo "<div class='tableCell'>" . $person['Prenom'] . "</div>";
                echo "<div class='tableCell'>" . $person['tele'] . "</div>";
                $selectGroups = "SELECT * FROM `Appartenance` WHERE `IdPersonne` = '{$person['idPersonne']}'";
                $Groups = mysqli_query($database,$selectGroups);
                $payer = true;
                while($Group=mysqli_fetch_assoc($Groups)){
                    if($Group['NbrAbscence']==0){
                        $Nbrseance = $Group['NbrSeance'];
                    }else{
                        $Nbrseance = $Group['NbrSeance']+1;
                    }
                    if(isset($Group['DateDebut'])){
                        if(!compareDate(DateFin($Group['DateDebut'],$Nbrseance))){
                            echo "<div><div class='point'></div></div>";
                            $payer = false;
                            break;
                        }
                    }
                }
                if($payer == true){
                    echo "<div><div class='notpoint'></div></div>";
                }
                echo "<div class='tableCell Row tableButtonCell'>";
                echo "<form action='editerEtudiant.php' method='POST'>";
                    echo "<input type='hidden' name='IdPersonne' value='".$person['idPersonne']."'>";
                    echo "<input type='submit' name='editePersonne' value='Éditer' class='editerBotton'>";
                echo "</form></div></div>";
            }
        }
    ?>
    <div class='empty'></div>
</body>
</html>