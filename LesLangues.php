<?php include("header.php");
include("Data.php");
?>
<html>

<head>
    <link href="cssfiles.css" rel="stylesheet">
    <meta charset="UTF-8">
</head>
<?php
check($database);
if (!empty($_POST['Nom']) && isset($_POST['valider'])) {
    if ((searchPerson($database, $_POST['Nom'], $_POST['Prenom'], $_POST['telephone'])) != 0) {
        $idPersonne = searchId($database, $_POST['Nom'], $_POST['Prenom'], $_POST['telephone']);
        if (max_inscription($database, $idPersonne)) {
            if ($_POST['isTest'] == "test" && searchtest($database, $_POST['Nom'], $_POST['Prenom'], $_POST['telephone']) == 0) {
                if (searchmatiere($database, $idPersonne, $_POST['matiere'], $_POST['Niveau']) == false) {
                    $idGroupe = searchgrp($database, $_POST['Niveau'], $_POST['matiere'], $idPersonne);
                    $insertAppartenance = "INSERT INTO `Appartenance`(`IdPersonne`,`IdGrp`,`NbrAbscence`,`NbrSeance`) VALUES ('{$idPersonne}','{$idGroupe}','0','1')";
                    mysqli_query($database, $insertAppartenance);
                    datedebut($database, $idGroupe, $idPersonne);
                    $insert = "INSERT INTO `Suivie`(`IdPersonne`,`NomFormation`)VALUES($idPersonne,'{$_POST['isTest']}')";
                    mysqli_query($database, $insert);
                    header("Location: Recu.php?nom=" . $_POST['Nom'] . "&prenom=" . $_POST['Prenom'] . "&telephone=" . $_POST['telephone'] . "&matiere=" . $_POST['matiere'] . "&niveau=" . $_POST['Niveau'] . "&nbrseance=" . '1' . "&istest=" . $_POST['isTest']);
                    exit;
                } else {
                    header("Location:refus.php?refus=langue");
                    exit;
                }
            } elseif ($_POST['isTest'] == "test" && searchtest($database, $_POST['Nom'], $_POST['Prenom'], $_POST['telephone']) != 0) {
                header("Location:refus.php?refus=test");
                exit();
            }
            if ($_POST['isTest'] == "langue") {
                if (searchmatiere($database, $idPersonne, $_POST['matiere'], $_POST['Niveau']) == false) {
                    $idGroupe = searchgrp($database, $_POST['Niveau'], $_POST['matiere'], $idPersonne);
                    $insertAppartenance = "INSERT INTO `Appartenance` (`IdPersonne`,`IdGrp`,`NbrAbscence`,`NbrSeance`) VALUES ('{$idPersonne}','{$idGroupe}','0','{$_POST['NbrSeance']}')";
                    mysqli_query($database, $insertAppartenance);
                    datedebut($database, $idGroupe, $idPersonne);
                    $sql = "SELECT COUNT(`NomFormation`) AS countf FROM `Suivie` WHERE `IdPersonne`='{$idPersonne}' AND `NomFormation`='{$_POST['isTest']}'";
                    $formations = mysqli_fetch_assoc(mysqli_query($database, $sql));
                    if ($formations['countf'] == 0) {
                        $insert = "INSERT INTO `Suivie`(`IdPersonne`,`NomFormation`)VALUES('{$idPersonne}','{$_POST['isTest']}')";
                        mysqli_query($database, $insert);
                    }
                    header("Location: Recu.php?nom=" . $_POST['Nom'] . "&prenom=" . $_POST['Prenom'] . "&telephone=" . $_POST['telephone'] . "&matiere=" . $_POST['matiere'] . "&niveau=" . $_POST['Niveau'] . "&nbrseance=" . $_POST['NbrSeance'] . "&istest=" . $_POST['isTest']);
                    exit;
                } else {
                    header("Location:refus.php?refus=langue");
                    exit();
                }
            } else {
                header("Location:refus.php?refus=group");
                exit();
            }
        }
    } else {
        $newid = IdGenerator($database, 'personne');
        $insertPersonne = "INSERT INTO `Personne`(`idPersonne`,`Nom`,`Prenom`,`tele`) VALUES ('{$newid}','{$_POST['Nom']}','{$_POST['Prenom']}','{$_POST['telephone']}') ";
        mysqli_query($database, $insertPersonne);
        $insert = "INSERT INTO `Suivie`(`IdPersonne`,`NomFormation`)VALUES('{$newid}','{$_POST['isTest']}')";
        mysqli_query($database, $insert);
        $idGroupe = searchgrp($database, $_POST['Niveau'], $_POST['matiere'], $newid);
        $nbrseance = $_POST['NbrSeance'];
        if ($_POST['isTest'] == "test") {
            $nbrseance = 1;
        } elseif ($_POST['isTest'] == "langue") {
            $nbrseance = $_POST['NbrSeance'];
        }
        $insertAppartenance = "INSERT INTO `Appartenance` (`IdPersonne`,`IdGrp`,`NbrAbscence`,`NbrSeance`) VALUES ('{$newid}','{$idGroupe}','0','{$nbrseance}')";
        mysqli_query($database, $insertAppartenance);
        datedebut($database, $idGroupe, $newid);
        header("Location: Recu.php?nom=" . $_POST['Nom'] . "&prenom=" . $_POST['Prenom'] . "&telephone=" . $_POST['telephone'] . "&matiere=" . $_POST['matiere'] . "&niveau=" . $_POST['Niveau'] . "&nbrseance=" . $nbrseance . "&istest=" . $_POST['isTest']);
        exit;
    }
}
?>

<body>
    <h1>INSCRIPTION AUX LANGUES</h1>
    <form class='inscription' method="post">
        <input type="text" name="Nom" placeholder="Nom" required>
        <input type="text" name="Prenom" placeholder="Prenom" required><br>
        <input type="text" name="telephone" placeholder="Téléphone" required>
        <select name="matiere" id="matiere">
            <option value="langue" selected disabled>Langues</option>
            <option value="ANG">Anglais</option>
            <option value="FRA">Français</option>
            <option value="GER">Allemand</option>
            <option value="ITA">Italien</option>
            <option value="ESP">Espagnol</option>
        </select></br>
        <select name="Niveau">
            <option value="Niveau" selected disabled> Niveau</option>
            <option value="A1_A2">A1 - A2</option>
            <option value="B1_B2">B1 - B2</option>
            <option value="C1_C2">C1 - C2</option>
        </select><br>
        <input type="number" name="NbrSeance" placeholder="Nombre Des Séances" required min="8">
        <input type="radio" name="isTest" value="test"><label>Test</label>
        <input type="radio" name="isTest" value="langue"><label>Langue</label>
        <button name='valider' type="submit">Valider</button>
    </form>
</body>

</html>