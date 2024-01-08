<?php
$DB_Server = 'localhost';
$DB_User = 'root';
$DB_Password = '';
$DB_Name = 'CentreFormation';
$database = mysqli_connect($DB_Server, $DB_User, $DB_Password, $DB_Name);

function IdGenerator($database, $type)
{
    switch ($type) {
        case 'personne':
            $sql = 'SELECT `idPersonne` FROM `Personne`';
            break;
        case 'prof':
            $sql = 'SELECT `IdProf` FROM `Prof`';
            break;
        case 'groupe':
            $sql = 'SELECT `IdGrp` FROM `Groupe`';
            break;
        default:
            $sql = 'SELECT `idPersonne` FROM `Personne`';
    }
    $ListId = mysqli_query($database, $sql);
    $randomId = rand(100000, 999999);
    while (findValueInList($ListId, $randomId)) {
        $randomId = rand(100000, 999999);
    }
    return $randomId;
}
$personneBase = 'CREATE TABLE IF NOT EXISTS Personne (idPersonne INT,Nom VARCHAR(20),Prenom VARCHAR(20),tele VARCHAR(12),PRIMARY KEY(idPersonne))';
$profBase = 'CREATE TABLE IF NOT EXISTS Prof( IdProf INT,NomProf VARCHAR(20),PrenomProf VARCHAR(20),TelProf VARCHAR(12),NomMatiere VARCHAR(20),PRIMARY KEY (IdProf),FOREIGN KEY (NomMatiere) REFERENCES Matiere (NomMatiere))';
$grpBase = "CREATE TABLE IF NOT EXISTS Groupe (IdGrp INT,jour VARCHAR(20),Temps TIME, Niveau  VARCHAR(10),NumSalle VARCHAR(2),NomMatiere VARCHAR(20),IdProf INT,
PRIMARY KEY (IdGrp),
FOREIGN KEY (Niveau) REFERENCES NiveauScolaire(Niveau),
FOREIGN KEY (Numsalle) REFERENCES Salle(NumSalle),
FOREIGN KEY (NomMatiere) REFERENCES Matiere(NomMatiere),
FOREIGN KEY (IdProf) REFERENCES Prof(IdProf))";

$matiereBase = 'CREATE TABLE IF NOT EXISTS Matiere (NomMatiere VARCHAR(20),Prix FLOAT, PRIMARY KEY (NomMatiere))';

$niveauBase = 'CREATE TABLE IF NOT EXISTS NiveauScolaire (Niveau  VARCHAR(10),PRIMARY KEY (Niveau))';

$suivieBase = 'CREATE TABLE IF NOT EXISTS Suivie (IdPersonne INT,NomFormation VARCHAR(20),FOREIGN KEY(IdPersonne) REFERENCES Personne(idPersonne),FOREIGN KEY(NomFormation) REFERENCES Formation(NomFormation))';

$appartenanceBase = 'CREATE TABLE IF NOT EXISTS Appartenance (IdPersonne INT,IdGrp INT,NbrAbscence INT,NbrSeance INT,DateDebut DATE,FOREIGN KEY(IdPersonne) REFERENCES Personne(idPersonne))';

$formationBase = 'CREATE TABLE IF NOT EXISTS Formation (NomFormation VARCHAR(20),PRIMARY KEY (NomFormation))';

$salleBase = 'CREATE TABLE IF NOT EXISTS Salle (NumSalle VARCHAR(2),PRIMARY KEY (NumSalle))';

mysqli_query($database, $niveauBase);
mysqli_query($database, $salleBase);
mysqli_query($database, $matiereBase);
mysqli_query($database, $formationBase);
mysqli_query($database, $personneBase);
mysqli_query($database, $suivieBase);
mysqli_query($database, $profBase);
mysqli_query($database, $grpBase);
mysqli_query($database, $appartenanceBase);

# function
function searchPerson($database, $nom, $prenom, $telephone)
{
    $countPerson = "SELECT COUNT(*) AS count_personne FROM `Personne` WHERE (`Nom` = '{$nom}' AND `Prenom` = '{$prenom}' AND `tele` = '{$telephone}') OR (`Nom` = '{$prenom}' AND `Prenom` = '{$nom}' AND `tele` = '{$telephone}')";
    $number = mysqli_fetch_assoc(mysqli_query($database, $countPerson));
    return $number['count_personne'];
}
function searchId($database, $nom, $prenom, $telephone)
{
    $findId = "SELECT `idPersonne` FROM `Personne` WHERE (`Nom` = '{$nom}' AND `Prenom` = '{$prenom}' AND `tele` ='{$telephone}')OR(`Nom` = '{$prenom}' AND `Prenom` = '{$nom}' AND `tele` ='{$telephone}') ";
    $findIdResult = mysqli_query($database, $findId);
    if ($findIdResult) {
        $found = mysqli_fetch_assoc($findIdResult);
        return $found['idPersonne'];
    }
}
function findValueInList($list, $A)
{
    if (!empty($list)) {
        foreach ($list as $Elem) {
            if ($Elem == $A) {
                return true;
            }
        }
    }
    return false;
}
function searchgrp($database, $niveau, $matiere, $IdPersonne)
{
    $test = 1;
    $grp = "SELECT * FROM `Groupe` WHERE `Niveau` = '{$niveau}' AND `NomMatiere` = '{$matiere}' ";
    $Listgrp = mysqli_query($database, $grp);
    foreach ($Listgrp as $Elem) {
        $nbrperson = "SELECT COUNT(*) AS nbrperson FROM `Appartenance` WHERE IdGrp = {$Elem['IdGrp']}";
        $number = mysqli_fetch_assoc(mysqli_query($database, $nbrperson));
        if ($number['nbrperson'] < 8) {
            $sql = "SELECT `IdGrp` FROM `Appartenance`  WHERE `Idpersonne`='{$IdPersonne}'";
            $grpliste = mysqli_query($database, $sql);
            while ($grp = mysqli_fetch_assoc($grpliste)) {
                $sql = "SELECT `jour`,`Temps`FROM `Groupe` WHERE `IdGrp`='{$grp['IdGrp']}'";
                $emploie = mysqli_fetch_assoc(mysqli_query($database, $sql));
                if ($emploie['jour'] == $Elem['jour'] && $emploie['Temps'] == $Elem['Temps']) {
                    $test = 0;
                    break;
                }
            }
            if ($test == 1) {
                return $Elem['IdGrp'];
            }
        }
    }
    $idgrp = newgrp($database, $niveau, $matiere);
    emptysalle($database, $idgrp, $IdPersonne);
    return $idgrp;
}
function newgrp($database, $niveau, $matiere)
{
    $prof = "SELECT `IdProf` FROM `Prof` WHERE `NomMatiere`= '{$matiere}' ";
    $Listprof = mysqli_query($database, $prof);
    foreach ($Listprof as $Elem) {
        $nbrgrp = "SELECT COUNT(*) AS nbrgrp FROM `Groupe`  WHERE `IdProf`='{$Elem['IdProf']}'";
        $number = mysqli_fetch_assoc(mysqli_query($database, $nbrgrp));
        if ($number['nbrgrp'] < 3) {
            $id = IdGenerator($database, 'groupe');
            $sql = "INSERT INTO `Groupe`(`IdGrp`,`Niveau`,`NomMatiere`,`IdProf`) VALUES ('{$id}' ,'{$niveau}','{$matiere}','{$Elem['IdProf']}') ";
            mysqli_query($database, $sql);
            return $id;
        }
    }
}
function emptysalle($database, $idGrp, $IdPersonne)
{
    $randomSalle = rand(1, 7);
    $randomJour = rand(0, 5);
    $randomTemps = rand(0, 1);
    $jour = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    $temp = ['17:00:00', '19:00:00'];
    while (true) {
        $sql = "SELECT `NumSalle`,`jour`,`Temps` FROM `Groupe`";
        $result = mysqli_query($database, $sql);
        while ($list = mysqli_fetch_assoc($result)) {
            $test = 1;
            $sql = "SELECT `IdGrp` FROM `Appartenance`  WHERE `Idpersonne`='{$IdPersonne}'";
            $grpliste = mysqli_query($database, $sql);
            while ($grp = mysqli_fetch_assoc($grpliste)) {
                $sql = "SELECT `jour`,`Temps`FROM `Groupe` WHERE `IdGrp`='{$grp['IdGrp']}'";
                $emploie = mysqli_fetch_assoc(mysqli_query($database, $sql));
                if ($emploie['jour'] == $jour[$randomJour] && $emploie['Temps'] == $temp[$randomTemps]) {
                    $test = 0;
                    break;
                }
            }
            if ($test == 1 && ($list['NumSalle'] == $randomSalle && $list['jour'] == $jour[$randomJour] && $list['Temps'] == $temp[$randomTemps])) {
                $test = 0;
                break;
            }
        }

        if ($test == 1) {
            $upS = "UPDATE `Groupe` SET `NumSalle`='{$randomSalle}' WHERE `IdGrp`='{$idGrp}'";
            mysqli_query($database, $upS);

            $upJ = "UPDATE `Groupe` SET `jour`='{$jour[$randomJour]}' WHERE `IdGrp`='{$idGrp}'";
            mysqli_query($database, $upJ);

            $upT = "UPDATE `Groupe` SET `Temps`='{$temp[$randomTemps]}' WHERE `IdGrp`='{$idGrp}'";
            mysqli_query($database, $upT);

            break;
        } else {
            $randomSalle = rand(1, 7);
            $randomJour = rand(0, 5);
            $randomTemps = rand(0, 1);
        }
    }
}
function compareDate($date)
{
    $todayDate = new DateTime();
    $Date = new DateTime($date);
    if ($Date < $todayDate) {
        return False;
    } else {
        return True;
    }
}
function DateFin($DateDebut, $nbrSeance)
{
    $numberOfDays = ($nbrSeance - 1) * 7;
    return date('Y-m-d', strtotime($DateDebut . ' +' . $numberOfDays . ' days'));
}
function check($database)
{
    $idGrp = 'SELECT * FROM `Appartenance`';
    $result = mysqli_query($database, $idGrp);
    while ($IdGrp = mysqli_fetch_assoc($result)) {
        $delai = DateFin($IdGrp['DateDebut'], ($IdGrp['NbrSeance']) + 1);
        if (!compareDate($delai)) {
            $delete = "DELETE FROM `Appartenance` WHERE `IdGrp`='{$IdGrp['IdGrp']}' ";
            mysqli_query($database, $delete);
        }
    }
}
function datedebut($database, $idGrp, $IdPersonne)
{
    $nbrperson = "SELECT COUNT(*) AS nbrperson FROM `Appartenance` WHERE `IdGrp` = '{$idGrp}'";
    $number = mysqli_fetch_assoc(mysqli_query($database, $nbrperson));
    if ($number['nbrperson'] == 4) {
        $personne = "SELECT `IdPersonne` FROM `Appartenance` WHERE `IdGrp`='{$idGrp}' ";
        $idlist = mysqli_query($database, $personne);
        $sql = "SELECT `jour` FROM `Groupe`  where `IdGrp`='{$idGrp}'";
        $jour = mysqli_fetch_assoc(mysqli_query($database, $sql));
        $NextDate = date('Y-m-d', strtotime('next ' . $jour['jour']));
        while ($id = mysqli_fetch_assoc($idlist)) {
            $up = "UPDATE `Appartenance` SET `DateDebut`='{$NextDate}' WHERE `IdPersonne`='{$id['IdPersonne']}' AND `IdGrp`='{$idGrp}'";
            mysqli_query($database, $up);
        }
    } elseif ($number['nbrperson'] > 4) {
        $sql = "SELECT `jour` FROM `Groupe`  where `IdGrp`='{$idGrp}'";
        $jour = mysqli_fetch_assoc(mysqli_query($database, $sql));
        $NextDate = date('Y-m-d', strtotime('next ' . $jour['jour']));
        $update = "UPDATE `Appartenance` SET `DateDebut`='{$NextDate}' WHERE `IdPersonne`='{$IdPersonne}' AND `IdGrp`='{$idGrp}'";
        mysqli_query($database, $update);
    }
}
function searchTest($database, $nom, $prenom, $telephone)
{
    $idPersonne = searchId($database, $nom, $prenom, $telephone);
    $test = "SELECT COUNT(*) AS test FROM `Suivie` WHERE `IdPersonne`='{$idPersonne}' AND `NomFormation`='test'";
    $number = mysqli_fetch_assoc(mysqli_query($database, $test));
    return $number['test'];
}
function searchmatiere($database, $idpersonne, $matiere, $niveau)
{
    $g = "SELECT `IdGrp` FROM `Groupe` WHERE `Niveau`='{$niveau}' AND `NomMatiere`='{$matiere}'";
    $list = mysqli_query($database, $g);
    while ($grp = mysqli_fetch_assoc($list)) {
        $inscrit = "SELECT COUNT(*) AS `inscrit` FROM `Appartenance` WHERE `IdPersonne`='{$idpersonne}' AND IdGrp='{$grp['IdGrp']}'";
        $nbr = mysqli_fetch_assoc(mysqli_query($database, $inscrit));
        if ($nbr['inscrit'] != 0) {
            return true;
        }
        return false;
    }
}
function max_inscription($database, $IdPersonne)
{
    $countPerson = "SELECT COUNT(*) AS count_personne FROM Appartenance WHERE IdPersonne='{$IdPersonne}' ";
    $number = mysqli_fetch_assoc(mysqli_query($database, $countPerson));
    if ($number['count_personne'] == 12) {
        return false;
    } else {
        return true;
    }
}