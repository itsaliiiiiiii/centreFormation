<?php
include("header.php");
include("Data.php");
?>
<!DOCTYPE html>
<head>
    <title>refus</title>
    <link rel="stylesheet" href="cssfiles.css">
    <meta charset="UTF-8">
</head>
<body>
    <h1 class="refus">ECHOUE</h1>
    <?php
    if ($_GET['refus'] == 'test') {
        echo "<h1 class='msg'>Le test est déjà fait </h1>";
    } elseif ($_GET['refus'] == 'matiere') {
        echo "<h1 class='msg'>L'étudiant est deja inscrit dans cette Matiere</h1>";
    } elseif ($_GET['refus'] == 'formation') {
        echo "<h1 class='msg'>L'étudiant est deja inscrit dans cette Formation</h1>";
    } elseif ($_GET['refus'] == 'langue') {
        echo "<h1 class='msg'>L'étudiant est deja inscrit dans cette Langue</h1>";
    }elseif ($_GET['refus'] == 'group') {
        echo "<h1 class='msg'>L'étudiant peut inscrire aux 12 groupes  au maximum </h1>";
    }elseif ($_GET['refus'] == 'inscription') {
        echo "<h1 class='msg'>l'etudiant ne peut pas s'incrire dans cette matiere pour ce niveau </h1>";
    }elseif ($_GET['refus'] == 'nongroupe') {
        echo "<h1 class='msg'>il n'y a pas de groupe dans cette matiere pour ce niveau</h1>";
    }
    ?>
</body>