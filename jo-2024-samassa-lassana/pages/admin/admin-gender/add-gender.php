<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nomGenre = filter_input(INPUT_POST, 'nomGenre', FILTER_SANITIZE_STRING);

    // Vérifiez si le nom du Genre est vide
    if (empty($nomGenre)) {
        $_SESSION['error'] = "Le nom du Genre ne peut pas être vide.";
        header("Location: add-gender.php");
        exit();
    }

    try {
        // Vérifiez si le Genre existe déjà
        $queryCheck = "SELECT id_genre FROM Genre WHERE nom_genre = :nomGenre";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nomGenre", $nomGenre, PDO::PARAM_STR);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "Le Genre existe déjà.";
            header("Location: add-gender.php");
            exit();
        } else {

            // Requête pour ajouter un Genre
            $query = "INSERT INTO Genre (nom_genre) VALUES (:nomGenre)";
            $statement = $connexion->prepare($query);
            $statement->bindParam(":nomGenre", $nomGenre, PDO::PARAM_STR);

            // Exécutez la requête
            if ($statement->execute()) {
                $_SESSION['success'] = "Le Genre a été ajouté avec succès.";
                header("Location: manage-genders.php");
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout du Genre.";
                header("Location: add-gender.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-gender.php");
        exit();
    }
}
// Afficher les erreurs en PHP
// (fonctionne à condition d’avoir activé l’option en local)
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
    <title>Ajouter un Genre - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
    </style>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
            <li><a href="../admin-users/manage-users.php">Gestion Utilisateurs</a></li>
                <li><a href="../admin-sports/manage-sports.php">Gestion Sports</a></li>
                <li><a href="../admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-calendar/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-pays/manage-countries.php">Gestion Pays</a></li>
                <li><a href="manage-gender.php">Gestion Genres</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Ajouter un Genre</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="add-gender.php" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter ce Genre?')"">
            <label for=" nomGenre">Nom du Genre :</label>
            <input type="text" name="nomGenre" id="nomGenre" required>
            <input type="submit" value="Ajouter le Genre">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-genders.php">Retour à la gestion des Genres</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>

</body>

</html>