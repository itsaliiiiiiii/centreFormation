<?php
include("Data.php");
check($database);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="cssfiles.css">
</head>

<body>
    <?php include("header.php"); ?>
    <h1>GROUPE</h1>
    <form method="POST" class="formGroup">
        <select name="matiere" class="selectGroup" required>
            <option selected disabled>Matière</option>
            <optgroup label="Cours De Soutien">
                <option value="Math">Mathematique</option>
                <option value="PC">Physique et Chimie</option>
                <option value="SVT">Science de vie et de terre</option>
                <option value="ENG">Anglais</option>
                <option value="Philo">Philosophie</option>
                <option value="EI">Education Islamique</option>
                <option value="HG">Histoire et Geographie</option>
                <option value="FR">Français</option>
                <option value="ARAB">Arab</option>
            </optgroup>
            <optgroup label="Formations">
                <option value="Design">Design graphique</option>
                <option value="Programmation Web">Programmation Web</option>
                <option value="Bureautique">Bureautique</option>
                <option value="Framework JAVA">Framework JAVA</option>
            </optgroup>
            <optgroup label="langue">
                <option value="ANG">Anglais</option>
                <option value="FRA">Français</option>
                <option value="GER">Allemand</option>
                <option value="ITA">Italien</option>
                <option value="ESP">Espagnol</option>
            </optgroup>
        </select>
        <select name="niveau" class="SelectProf" required>
            <option value="niveau" selected disabled>Niveau</option>
            <optgroup label="college">
                <option value="1college">1ere année collège</option>
                <option value="2college">2eme année collège</option>
                <option value="3college">3eme année collège</option>
            </optgroup>
            <optgroup label="Lycée">
                <option value="1lycee">1ere année lycée</option>
                <option value="1bac">1ere année bac</option>
                <option value="2bac">2eme année bac</option>
            </optgroup>
            <optgroup label="Formations">
                <option value="debutant">débutant</option>
                <option value="avance">avancé</option>
            </optgroup>
            <optgroup label="langues">
                <option value="A1_A2">A1 - A2</option>
                <option value="B1_B2">B1 - B2</option>
                <option value="C1_C2">C1 - C2</option>
            </optgroup>
        </select>
        <button class="buttonGroup">Chercher</button>
    </form>
    <?php
    if (isset($_POST['niveau']) && isset($_POST['matiere'])) {
        $sql = "SELECT `IdGrp`, `jour`, `Temps`, `NumSalle`, `Idprof` FROM `Groupe` WHERE `Niveau`='{$_POST['niveau']}' AND `NomMatiere`='{$_POST['matiere']}'";
        $result = mysqli_query($database, $sql);
        if (mysqli_num_rows($result) == 0) {
            header("Location:refus.php?refus=nongroupe");
            exit;
        } else {
            echo "<h3 class='groups'>".$_POST['niveau']." - ".$_POST['matiere']."</h3>";
            echo "<div class='tableRow header tableHeader'>";
            echo "<div class='tableCell'>Jour</div>";
            echo "<div class='tableCell'>Temps</div>";
            echo "<div class='tableCell'>Salle</div>";
            echo "<div class='tableCell'>Professeur</div>";
            echo "<div ,class='tableCell tableButtonCell'>Actions</div>";
            echo "</div>";
            $num = 0;
            while ($list = mysqli_fetch_assoc($result)) {
                $num++;
                $css = $num % 2 + 1;
                echo "<div class='tableRow tableProf" . $css . "'>";
                echo "<div class='tableCell'>" . $list['jour'] . "</div>";
                echo "<div class='tableCell'>" . $list['Temps'] . "</div>";
                echo "<div class='tableCell'>" . $list['NumSalle'] . "</div>";
                $sqlProf = "SELECT `NomProf`, `PrenomProf` FROM `Prof` WHERE `IdProf`='{$list['Idprof']}'";
                $resultProf = mysqli_query($database, $sqlProf);
                $nomProf = mysqli_fetch_assoc($resultProf);
                echo "<div class='tableCell'>" . $nomProf['NomProf'] . ' ' . $nomProf['PrenomProf'] . "</div>";
                echo "<form action='etudiantGroupe.php' method='POST'>";
                    echo "<input type='hidden' name='IdGrp' value='" . $list['IdGrp']. "'>";
                    echo "<input type='submit' style='margin-left: 10px;margin-right: 35px;' name='show' value='afficher' class='show'>";
                echo "</form>";
                echo "</div>";
                echo "</div>";
            }
        }
    }
    ?>
    </form>
</body>
</html>