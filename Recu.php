<?php
include("Data.php");
?>
<!DOCTYPE html>
<html>
<head>
    <link href="cssfiles.css" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <h1 class="Recu" style="color: #EA906C;">REÇU</h1>
    <?php
    echo '<div class="recu" >';
    if (isset($_GET['nom']) && isset($_GET['prenom']) && isset($_GET['telephone']) && isset($_GET['niveau']) && isset($_GET['matiere'])) {
        $rech = "SELECT Prix from matiere where NomMatiere= '{$_GET['matiere']}'";
        $prixmatiere = mysqli_fetch_assoc(mysqli_query($database, $rech));
        if($_GET['istest']=='test'){
            $prix = $prixmatiere['Prix'] * $_GET['nbrseance'] * 2;
        }
        else{
            $prix = $prixmatiere['Prix'] * $_GET['nbrseance'];
        }
        echo '<div class=aligne>';
        echo '<div class="info">';
        echo '<label class="ligne">Nom :</label><span class="value">' . $_GET['nom'] . '</span>';
            echo '</div>';

        echo '<div class="info">';
        echo '<label class="ligne">Prenom :</label><span class="value">' . $_GET['prenom'] . '</span>';
        echo '</div>';

        echo '<div class="info">';
        echo '<label class="ligne">Téléphone :</label><span class="value">' . $_GET['telephone'] . '</span>';
        echo '</div>';

        echo '<div class="info">';
        echo '<label class="ligne">Niveau :</label><span class="value">' . $_GET['niveau'] . '</span>';
        echo '</div>';

        echo '<div class="info">';
        echo '<label class="ligne">Inscrit en :</label><span class="value">' . $_GET['istest'] . ' ' . $_GET['matiere'] . '</span>';
        echo '</div>';

        echo '<div class="info">';
        echo '<label class="ligne">Nombre de séances :</label><span class="value">' . $_GET['nbrseance'] . '</span>';
        echo '</div>';

        echo '<div class="info">';
        echo '<label class="ligne">Prix :</label><span class="value">' . $prix . '</span>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    echo "<form action='CoursDeSoutien.php' class=precedent>";
        echo "<input type='submit' name='précédent' value='précédent' class='absenceButton'>";
    echo "</form>";
    ?>
</body>

</html>