<!DOCTYPE html>
<?php
include("header.php");
include("Data.php");
?>
<head>
    <title> INSCRIPTION AU SOUTIEN </title>
    <link rel="stylesheet" href="cssfiles.css">
    <meta charset="UTF-8">
</head>
<?php
check($database);
$verificationMatiere = true;
if(!empty($_POST['nom']) && isset($_POST['valider'])){
    switch($_POST['niveau']){
        case '1college':
        case '2college':
        case '3college':
        case '1lycee':
            if ($_POST['matiere']!= 'Math' && $_POST['matiere']!= 'PC' && $_POST['matiere']!= 'SVT'){
                $verificationMatiere = false;
            }
            break;
        case '1bac':
            if ($_POST['matiere']!= 'Math' && $_POST['matiere']!= 'PC' && $_POST['matiere']!= 'SVT' && $_POST['matiere']!='HG' && $_POST['matiere']!='FR' && $_POST['matiere']!='EI' && $_POST['matiere']!='ARAB'){
                $verificationMatiere = false;
            }
            break;
        case '2bac':
            if ($_POST['matiere']!= 'Math' && $_POST['matiere']!= 'PC' && $_POST['matiere']!= 'SVT' && $_POST['matiere']!= 'Philo' && $_POST['matiere']!= 'ENG' ){
                $verificationMatiere = false;
            }
            break;
    }
    if($verificationMatiere == false){
        header("Location:refus.php?refus=inscription");
    }else{
        if((searchPerson($database,$_POST['nom'],$_POST['prenom'],$_POST['telephone']))!= 0){
            $idPersonne = searchId($database, $_POST['nom'], $_POST['prenom'], $_POST['telephone']);
            if(max_inscription($database,$idPersonne)){
                if ($_POST['isTest'] == "test" && searchtest($database, $_POST['nom'], $_POST['prenom'], $_POST['telephone']) == 0) {
                    if (searchmatiere($database, $idPersonne, $_POST['matiere'], $_POST['niveau']) == false){
                        $idGroupe = searchgrp($database, $_POST['niveau'], $_POST['matiere'],$idPersonne);
                        $insertAppartenance = "INSERT INTO `Appartenance` (`IdPersonne`, `IdGrp`, `NbrAbscence`, `NbrSeance`) VALUES ('{$idPersonne}', '{$idGroupe}', '0', '1')";
                        mysqli_query($database, $insertAppartenance);
                        datedebut($database, $idGroupe, $idPersonne);
                        $insert = "INSERT INTO `Suivie`(`IdPersonne`,`NomFormation`)VALUES($idPersonne,'{$_POST['isTest']}')";
                        mysqli_query($database, $insert);
                        header("Location: Recu.php?nom=" . $_POST['nom'] . "&prenom=" . $_POST['prenom'] . "&telephone=" . $_POST['telephone'] . "&matiere=" . $_POST['matiere'] . "&niveau=" . $_POST['niveau'] . "&nbrseance=" . '1' . "&istest=" . $_POST['isTest']);
                        exit();
                    } else {
                        header("Location:refus.php?refus=matiere");
                        exit();
                    }
                }elseif($_POST['isTest'] == "test" && searchtest($database,$_POST['nom'],$_POST['prenom'],$_POST['telephone']) != 0) {
                    header("Location:refus.php?refus=test");
                    exit();
                }
                if($_POST['isTest'] == "soutien"){
                    if (searchmatiere($database, $idPersonne, $_POST['matiere'], $_POST['niveau']) == false) {
                        $idGroupe = searchgrp($database, $_POST['niveau'], $_POST['matiere'],$idPersonne);
                        $insertAppartenance = "INSERT INTO `Appartenance` (`IdPersonne`, `IdGrp`, `NbrAbscence`, `NbrSeance`) VALUES ('{$idPersonne}', '{$idGroupe}', '0', '{$_POST['NbrSeance']}')";
                        mysqli_query($database, $insertAppartenance);
                        datedebut($database, $idGroupe, $idPersonne);
                        $sql = "SELECT COUNT(`NomFormation`)  AS countFormation FROM `Suivie` WHERE `IdPersonne`=$idPersonne AND NomFormation='{$_POST['isTest']}'";
                        $formations = mysqli_fetch_assoc(mysqli_query($database, $sql));
                        if ($formations['countFormation'] == 0) {
                            $insert = "INSERT INTO `Suivie`(`IdPersonne`,`NomFormation`)VALUES( $idPersonne,'{$_POST['isTest']}')";
                            mysqli_query($database, $insert);
                        }
                        header("Location: Recu.php?nom=" . $_POST['nom'] . "&prenom=" . $_POST['prenom'] . "&telephone=" . $_POST['telephone'] . "&matiere=" . $_POST['matiere'] . "&niveau=" . $_POST['niveau'] . "&nbrseance=" . $_POST['NbrSeance'] . "&istest=" . $_POST['isTest']);
                        exit();
                    } else {
                        header("Location:refus.php?refus=matiere");
                        exit();
                    }
                }
            } else {
                header("Location:refus.php?refus=group");
                exit();
            }
        }else{
            $newid = IdGenerator($database, 'personne');
            $insertPersonne = "INSERT INTO `Personne`(`idPersonne`,`Nom`,`Prenom`,`tele`) VALUES ('{$newid}','{$_POST['nom']}','{$_POST['prenom']}','{$_POST['telephone']}') ";
            mysqli_query($database, $insertPersonne);
            $insert = "INSERT INTO `Suivie`(`IdPersonne`,`NomFormation`)VALUES($newid,'{$_POST['isTest']}')";
            mysqli_query($database, $insert);
            $idGroupe = searchgrp($database, $_POST['niveau'], $_POST['matiere'],$newid);
            if($_POST['isTest'] == "test") {
                $nbrseance = 1;
            }elseif($_POST['isTest'] == "soutien"){
                $nbrseance = $_POST['NbrSeance'];
            }
            $insertAppartenance = "INSERT INTO `Appartenance` (`IdPersonne`, `IdGrp`, `NbrAbscence`, `NbrSeance`) VALUES ('{$newid}', '{$idGroupe}', '0', '{$nbrseance}')";
            mysqli_query($database, $insertAppartenance);
            datedebut($database, $idGroupe, $newid);
            header("Location: Recu.php?nom=" . $_POST['nom'] . "&prenom=" . $_POST['prenom'] . "&telephone=" . $_POST['telephone'] . "&matiere=" . $_POST['matiere'] . "&niveau=" . $_POST['niveau'] . "&nbrseance=" .  $nbrseance . "&istest=" . $_POST['isTest']);
            exit();
        }
    }
}
?>
<body>
    <h1>INSCRIPTION AU SOUTIEN</h1>
    <form class="inscription" method="post">
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prenom" required><br>
        <input type="text" name="telephone" placeholder="Téléphone" required>
        <select name="niveau" required>
            <option selected disabled>Niveau</option>
            <optgroup label="Collège">
                <option value="1college">première année collège</option>
                <option value="2college">deuxième année collège</option>
                <option value="3college">troisième année collège</option>
            </optgroup>
            <optgroup label="Lycée">
                <option value="1lycee">première année Lycée</option>
                <option value="1bac">deuxième année Lycée</option>
                <option value="2bac">troisième année Lycée</option>
            </optgroup>
        </select><br>
        <select name="matiere" required>
            <option selected disabled>Matière</option>
            <option value="Math">Mathematique</option>
            <option value="PC">Physique et Chimie</option>
            <option value="SVT">Science de vie et de terre</option>
            <option value="ENG">Anglais</option>
            <option value="Philo">Philosophie</option>
            <option value="EI">Education Islamique</option>
            <option value="HG">Histoire et Geographie</option>
            <option value="FR">Français</option>
            <option value="ARAB">Arabe</option>
        </select><br>
        <input type="number" name="NbrSeance" placeholder="nombre de séances" required min="2">
        <input type="radio" name="isTest" value="test" required><label>Test</label>
        <input type="radio" name="isTest" value="soutien" required><label>Soutien</label>
        <button name='valider'>Valider</button>
    </form>
</body>
</html>