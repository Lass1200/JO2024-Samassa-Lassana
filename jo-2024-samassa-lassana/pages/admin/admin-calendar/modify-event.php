<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID de l'événement est fourni dans l'URL
if (!isset($_GET['id_epreuve'])) {
    $_SESSION['error'] = "ID de l'événement manquant.";
    header("Location: manage-events.php");
    exit();
}

$id_epreuve = filter_input(INPUT_GET, 'id_epreuve', FILTER_VALIDATE_INT);

// Vérifiez si l'ID de l'événement est un entier valide
if (!$id_epreuve && $id_epreuve !== 0) {
    $_SESSION['error'] = "ID de l'événement invalide.";
    header("Location: manage-events.php");
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nomEpreuve = filter_input(INPUT_POST, 'nomEpreuve', FILTER_SANITIZE_STRING);
    $dateEpreuve = filter_input(INPUT_POST, 'dateEpreuve', FILTER_SANITIZE_STRING);
    $heureEpreuve = filter_input(INPUT_POST, 'heureEpreuve', FILTER_SANITIZE_STRING);

    // Vérifiez si les champs sont vides
    if (empty($nomEpreuve) || empty($dateEpreuve) || empty($heureEpreuve)) {
        $_SESSION['error'] = "Tous les champs doivent être remplis.";
        header("Location: modify-event.php?id_epreuve=$id_epreuve");
        exit();
    }

    try {
        // Vérifiez si l'événement existe déjà
        $queryCheck = "SELECT id_epreuve FROM epreuve WHERE nom_epreuve = :nomEpreuve AND id_epreuve <> :idEpreuve";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nomEpreuve", $nomEpreuve, PDO::PARAM_STR);
        $statementCheck->bindParam(":idEpreuve", $id_epreuve, PDO::PARAM_INT);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "L'événement existe déjà.";
            header("Location: modify-event.php?id_epreuve=$id_epreuve");
            exit();
        }

        // Requête pour mettre à jour l'événement
        $query = "UPDATE epreuve SET nom_epreuve = :nomEpreuve, date_epreuve = :dateEpreuve, heure_epreuve = :heureEpreuve WHERE id_epreuve = :idEpreuve";
        $statement = $connexion->prepare($query);
        $statement->bindParam(":nomEpreuve", $nomEpreuve, PDO::PARAM_STR);
        $statement->bindParam(":dateEpreuve", $dateEpreuve, PDO::PARAM_STR);
        $statement->bindParam(":heureEpreuve", $heureEpreuve, PDO::PARAM_STR);
        $statement->bindParam(":idEpreuve", $id_epreuve, PDO::PARAM_INT);

        // Exécutez la requête
        if ($statement->execute()) {
            $_SESSION['success'] = "L'événement a été modifié avec succès.";
            header("Location: manage-events.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la modification de l'événement.";
            header("Location: modify-event.php?id_epreuve=$id_epreuve");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: modify-event.php?id_epreuve=$id_epreuve");
        exit();
    }
}

// Récupérez les informations de l'événement pour affichage dans le formulaire
try {
    $queryEvent = "SELECT nom_epreuve, date_epreuve, heure_epreuve FROM epreuve WHERE id_epreuve = :idEpreuve";
    $statementEvent = $connexion->prepare($queryEvent);
    $statementEvent->bindParam(":idEpreuve", $id_epreuve, PDO::PARAM_INT);
    $statementEvent->execute();

    if ($statementEvent->rowCount() > 0) {
        $event = $statementEvent->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['error'] = "Événement non trouvé.";
        header("Location: manage-events.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-events.php");
    exit();
}
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
    <title>Modifier un Événement - Jeux Olympiques 2024</title>
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
                <li><a href="../admin-users/manage-users.php">Gestion Utilisateurs</a></li>
                <li><a href="../admin-sports/manage-sports.php">Gestion Sports</a></li>
                <li><a href="../admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-calendar/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-countries/manage-countries.php">Gestion Pays</a></li>
                <li><a href="../admin-gender/manage-gender.php">Gestion Genres</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Modifier un Événement</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="modify-event.php?id_epreuve=<?php echo $id_epreuve; ?>" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir modifier cet événement?')">
            <label for="nomEpreuve">Nom de l'Épreuve :</label>
            <input type="text" name="nomEpreuve" id="nomEpreuve"
                value="<?php echo htmlspecialchars($event['nom_epreuve']); ?>" required>
            <label for="dateEpreuve">Date de l'Épreuve :</label>
            <input type="date" name="dateEpreuve" id="dateEpreuve"
                value="<?php echo htmlspecialchars($event['date_epreuve']); ?>" required>
            <label for="heureEpreuve">Heure de l'Épreuve :</label>
            <input type="time" name="heureEpreuve" id="heureEpreuve"
                value="<?php echo htmlspecialchars($event['heure_epreuve']); ?>" required>
            <input type="submit" value="Modifier l'Événement">
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
