<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/normalize.css">
    <link rel="stylesheet" href="../../../css/styles-computer.css">
    <link rel="stylesheet" href="../../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../../img/favicon-jo-2024.ico" type="image/x-icon">
    <title>Gestion des Événements - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .action-buttons button {
            background-color: #1b1b1b;
            color: #d7c378;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .action-buttons button:hover {
            background-color: #d7c378;
            color: #1b1b1b;
        }
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
        <h1>Gestion des Événements</h1>
        <div class="action-buttons">
            <button onclick="openAddEventForm()">Ajouter un événement</button>
            <!-- Autres boutons... -->
        </div>
        <!-- Tableau des événements -->
        <table>
            <tr>
                <th>Épreuve</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Lieu</th>
                <th>Sport</th>
                <th>Modifier</th>
                <th>Supprimer</th>
            </tr>
            <?php
            session_start();
            require_once("../../../database/database.php");

            try {
                $query = "SELECT e.*, l.nom_lieu, s.nom_sport 
                          FROM epreuve e 
                          INNER JOIN lieu l ON e.id_lieu = l.id_lieu 
                          INNER JOIN sport s ON e.id_sport = s.id_sport 
                          ORDER BY e.date_epreuve, e.heure_epreuve";

                $statement = $connexion->query($query);

                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['nom_epreuve']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_epreuve']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['heure_epreuve']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_lieu']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_sport']) . "</td>";
                    echo "<td><button onclick='openModifyEventForm(" . $row['id_epreuve'] . ")'>Modifier</button></td>";
                    echo "<td><button onclick='deleteEventConfirmation(" . $row['id_epreuve'] . ")'>Supprimer</button></td>";
                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo '<p style="color: red;">Erreur de base de données : ' . $e->getMessage() . '</p>';
            }
            ?>
        </table>
        <p class="paragraph-link">
            <a class="link-home" href="../admin.php">Accueil administration</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
    <script>
        function openAddEventForm() {
            alert('Ajouter un événement');
            // Rediriger vers la page d'ajout d'événement
            window.location.href = 'add-event.php';
        }

        function openModifyEventForm(id_epreuve) {
            alert('ID de l\'événement à modifier : ' + id_epreuve);
            // Rediriger vers la page de modification d'événement avec l'ID correspondant
            window.location.href = 'modify-event.php?id_epreuve=' + id_epreuve;
        }

        function deleteEventConfirmation(id_epreuve) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cet événement?")) {
                alert('Supprimer un événement : ' + id_epreuve);
                // Rediriger vers la page de suppression d'événement avec l'ID correspondant
                window.location.href = 'delete-event.php?id_epreuve=' + id_epreuve;
            }
        }
    </script>
</body>

</html>
