<?php
    include("Data.php");
    check($database);
    $exists = false ;
    $profInGroup = false ;
    if(isset($_POST['nom'])){
        $countProf = "SELECT COUNT(*) AS countProf FROM `Prof` WHERE `NomProf` = '{$_POST['nom']}' AND `PrenomProf` = '{$_POST['prenom']}' AND `TelProf` = '{$_POST['telephone']}'";
        $numberOfProf = mysqli_fetch_assoc(mysqli_query($database,$countProf));
        if($numberOfProf['countProf']==0){
            $idProf=IdGenerator($database,'prof');
            $insertProf ="INSERT INTO `Prof`(`IdProf`, `NomProf`, `PrenomProf`, `NomMatiere`, `TelProf`) VALUES ('{$idProf}', '{$_POST['nom']}','{$_POST['prenom']}', '{$_POST['matiere']}','{$_POST['telephone']}')";
            mysqli_query($database,$insertProf);
        }else{
            $exists = true ;
        }
    }
    if (isset($_POST['deleteProf'])) {
        $countProfFromGroup = "SELECT COUNT(*) AS countProf FROM `Groupe` WHERE `IdProf` = '{$_POST['IdProf']}' ";
        $numOfProf  = mysqli_fetch_assoc(mysqli_query($database,$countProfFromGroup));
        if($numOfProf['countProf']==0){
            $deleteProf = "DELETE FROM `Prof` WHERE `IdProf` = '{$_POST['IdProf']}'";
            mysqli_query($database,$deleteProf);
            $profInGroup = false ;
        }else{
            $profInGroup = true ;
        }
    }
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="cssfiles.css">
</head>
<body>
    <?php include("header.php");?>
    <h1>PROFESSEURS</h1>
    <form action="Professeurs.php" method="POST" class="formAddProf">
        <input type="text" name="nom" placeholder="Nom" class="addProf1" required>
        <input type="text" name="prenom" placeholder="Prenom" class="addProf2" required>
        <select name="matiere" class="SelectProf" required>
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
            <optgroup label="Languages">
                <option value="ANG">Anglais</option>
                <option value="FRA">Français</option>
                <option value="GER">Allemand</option>
                <option value="ITA">Italien</option>
                <option value="ESP">Espagnol</option>
            </optgroup>
        </select>
        <input type="text" name="telephone" placeholder="Telephone" class="addProf2" required>
        <button class="button">Ajouter Professeur</button>
    </form>
    <?php
        if(isset($_POST['nom'])){
            if($exists == false ){
                echo "<div class ='succes'> Professeur ajouté avec succès </div>";
            }else{
                echo "<div class ='notsucces'> Le professeur existe déjà </div>";
            }
        }
        if(isset($_POST['deleteProf'])){
            if($profInGroup == false ){
                echo "<div class ='succes'> Professeur supprimé </div>";
            }else{
                echo "<div class ='notsucces'> Le professeur est associé à un groupe. Il ne peut pas être supprimé. </div>";
            }
        }
    ?>
    <div class='tableRow header tableHeader'>
        <div class='tableCell'>Nom</div>
        <div class='tableCell'>Prénom</div>
        <div class='tableCell'>Matière</div>
        <div class='tableCell'>Téléphone</div>
        <div class='tableCell tableButtonCell'>Actions</div>
    </div>
    <?php
    $importProf = "SELECT * FROM `Prof`";
    $result = mysqli_query($database, $importProf);
    $num = 0;
    while ($prof = mysqli_fetch_assoc($result)) {
        $num++;
        $css = $num % 2 + 1 ;
        echo "<div class='tableRow tableProf" . $css . "'>";
        echo "<div class='tableCell'>" . $prof['NomProf'] . "</div>";
        echo "<div class='tableCell'>" . $prof['PrenomProf'] . "</div>";
        echo "<div class='tableCell'>" . $prof['NomMatiere'] . "</div>";
        echo "<div class='tableCell'>" . $prof['TelProf'] . "</div>";

        echo "<div class='tableCell tableButtonCell'>";
        echo "<form method='POST'>";
            echo "<input type='hidden' name='IdProf' value='" . $prof['IdProf'] . "'>";
            echo "<input type='submit' name='deleteProf' value='Supprimer' class='deleteBotton'>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
    }
    ?>
    <div class='empty'></div>
</body>
</html>