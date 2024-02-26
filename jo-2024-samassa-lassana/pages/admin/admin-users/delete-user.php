<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'identifiant du genre à supprimer est présent dans l'URL
if (!isset($_GET['id_genre']) || empty($_GET['id_genre'])) {
    $_SESSION['error'] = "Identifiant du genre manquant.";
    header("Location: manage-gender.php");
    exit();
}

$idGenre = $_GET['id_genre'];

try {
    // Vérifiez si le genre existe
    $queryCheck = "SELECT id_genre FROM GENRE WHERE id_genre = :idGenre";
    $statementCheck = $connexion->prepare($queryCheck);
    $statementCheck->bindParam(":idGenre", $idGenre, PDO::PARAM_INT);
    $statementCheck->execute();

    if ($statementCheck->rowCount() === 0) {
        $_SESSION['error'] = "Le genre n'existe pas.";
        header("Location: manage-gender.php");
        exit();
    }

    // Requête pour supprimer un genre
    $queryDelete = "DELETE FROM GENRE WHERE id_genre = :idGenre";
    $statementDelete = $connexion->prepare($queryDelete);
    $statementDelete->bindParam(":idGenre", $idGenre, PDO::PARAM_INT);

    // Exécutez la requête
    if ($statementDelete->execute()) {
        $_SESSION['success'] = "Le genre a été supprimé avec succès.";
        header("Location: manage-gender.php");
        exit();
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression du genre.";
        header("Location: manage-gender.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-gender.php");
    exit();
}
?>
