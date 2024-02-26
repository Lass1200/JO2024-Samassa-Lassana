<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nomEpreuve = filter_input(INPUT_POST, 'nomEpreuve', FILTER_SANITIZE_STRING);
    $dateEpreuve = filter_input(INPUT_POST, 'dateEpreuve', FILTER_SANITIZE_STRING);
    $heureEpreuve = filter_input(INPUT_POST, 'heureEpreuve', FILTER_SANITIZE_STRING);
    $newSport = filter_input(INPUT_POST, 'newSport', FILTER_SANITIZE_STRING);
    $newLieu = filter_input(INPUT_POST, 'newLieu', FILTER_SANITIZE_STRING);
    $idSport = filter_input(INPUT_POST, 'existingSport', FILTER_VALIDATE_INT);
$idLieu = filter_input(INPUT_POST, 'existingLieu', FILTER_VALIDATE_INT);

    // Vérifiez si les champs obligatoires sont vides
    if (empty($nomEpreuve) || empty($dateEpreuve) || empty($heureEpreuve)) {
        $_SESSION['error'] = "Tous les champs doivent être remplis.";
        header("Location: add-event.php");
        exit();
    }

    try {
        // Requête pour ajouter un nouveau sport si fourni
        if (!empty($newSport)) {
            $queryAddSport = "INSERT INTO sport (nom_sport) VALUES (:newSport)";
            $statementAddSport = $connexion->prepare($queryAddSport);
            $statementAddSport->bindParam(":newSport", $newSport, PDO::PARAM_STR);
            $statementAddSport->execute();
        }

        // Requête pour ajouter un nouveau lieu si fourni
        if (!empty($newLieu)) {
            $queryAddLieu = "INSERT INTO lieu (nom_lieu) VALUES (:newLieu)";
            $statementAddLieu = $connexion->prepare($queryAddLieu);
            $statementAddLieu->bindParam(":newLieu", $newLieu, PDO::PARAM_STR);
            $statementAddLieu->execute();
        }

        // Requête pour ajouter un événement
        $queryAddEvent = "INSERT INTO epreuve (nom_epreuve, date_epreuve, heure_epreuve, id_sport, id_lieu) VALUES (:nomEpreuve, :dateEpreuve, :heureEpreuve, :idSport, :idLieu)";
        $statementAddEvent = $connexion->prepare($queryAddEvent);
        $statementAddEvent->bindParam(":nomEpreuve", $nomEpreuve, PDO::PARAM_STR);
        $statementAddEvent->bindParam(":dateEpreuve", $dateEpreuve, PDO::PARAM_STR);
        $statementAddEvent->bindParam(":heureEpreuve", $heureEpreuve, PDO::PARAM_STR);
        $statementAddEvent->bindParam(":idSport", $idSport, PDO::PARAM_INT);
        $statementAddEvent->bindParam(":idLieu", $idLieu, PDO::PARAM_INT);


        // Exécutez la requête
        if ($statementAddEvent->execute()) {
            $_SESSION['success'] = "L'événement a été ajouté avec succès.";
            header("Location: manage-events.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de l'ajout de l'événement.";
            header("Location: add-event.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-event.php");
        exit();
    }
}

// Afficher les erreurs en PHP (fonctionne à condition d’avoir activé l’option en local)
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/normalize.css">
    <link rel="stylesheet" href="../../../css/styles-computer.css">
    <link rel="stylesheet" href="../../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../../img/favicon-jo-2024.ico" type="image/x-icon">
    <title>Ajouter un Événement - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
    </style>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
                <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="manage-sports.php">Gestion Sports</a></li>
                <li><a href="manage-places.php">Gestion Lieux</a></li>
                <li><a href="manage-events.php">Gestion Calendrier</a></li>
                <li><a href="manage-countries.php">Gestion Pays</a></li>
                <li><a href="manage-gender.php">Gestion Genres</a></li>
                <li><a href="manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Ajouter un Événement</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="add-event.php" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter cet événement?')">
            <label for="nomEpreuve">Nom de l'Épreuve :</label>
            <input type="text" name="nomEpreuve" id="nomEpreuve" required>

            <label for="dateEpreuve">Date de l'Épreuve :</label>
            <input type="date" name="dateEpreuve" id="dateEpreuve" required>

            <label for="heureEpreuve">Heure de l'Épreuve :</label>
            <input type="time" name="heureEpreuve" id="heureEpreuve" required>

            <!-- Dropdown for selecting an existing sport -->
            <label for="existingSport">Sport existant :</label>
            <select name="existingSport" id="existingSport">
                <?php
                // Fetch existing sports from the database and populate the dropdown
                $queryExistingSports = "SELECT id_sport, nom_sport FROM sport";
                $statementExistingSports = $connexion->query($queryExistingSports);
                while ($rowSport = $statementExistingSports->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value=\"" . $rowSport['id_sport'] . "\">" . $rowSport['nom_sport'] . "</option>";
                }
                ?>
            </select>

            <!-- Dropdown for selecting an existing location (lieu) -->
            <label for="existingLieu">Lieu existant :</label>
            <select name="existingLieu" id="existingLieu">
                <?php
                // Fetch existing locations from the database and populate the dropdown
                $queryExistingLieux = "SELECT id_lieu, nom_lieu FROM lieu";
                $statementExistingLieux = $connexion->query($queryExistingLieux);
                while ($rowLieu = $statementExistingLieux->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value=\"" . $rowLieu['id_lieu'] . "\">" . $rowLieu['nom_lieu'] . "</option>";
                }
                ?>
            </select>

            <input type="submit" value="Ajouter l'Événement">
        </form>


        <p class="paragraph-link">
            <a class="link-home" href="manage-events.php">Retour à la gestion des événements</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
</body>

</html>