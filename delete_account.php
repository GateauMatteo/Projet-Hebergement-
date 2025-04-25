<?php
session_start();
include_once 'includes/bdd.php'; 

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['alias'])) {
    header("Location: login.php");
    exit();
}

// Récupérer l'alias de l'utilisateur
$alias = $_SESSION['alias'];
$error = "";
$success = "";

// Appel du script Bash pour supprimer Linux + GLPI + fichiers + DNS + Apache
$scriptPath = "/var/www/html/SupprimeUtilisateur.sh"; // 🔁 Change selon ton chemin exact
$command = "sudo " . escapeshellcmd($scriptPath) . " " . escapeshellarg($alias);
$output = shell_exec($command);

// Vérifier le retour du script
if ($output === null || str_contains($output, 'Erreur')) {
    $error = "Erreur lors de la suppression serveur : " . $output;
} else {
    try {
        // Supprimer l'utilisateur de la BDD principale
        $sql = "DELETE FROM USER WHERE alias = :alias";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['alias' => $alias]);

        if ($stmt->rowCount() > 0) {
            $success = "Votre compte a été supprimé avec succès.";

            // Détruire la session
            session_unset();
            session_destroy();

            // Rediriger vers l'accueil avec un message
            header("Location: index.php?message=CompteSupprime");
            exit();
        } else {
            $error = "Le compte était déjà inexistant dans la base.";
        }
    } catch (PDOException $e) {
        $error = "Erreur lors de la suppression en base : " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression de compte</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">HebergeX</div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="login.php">Connexion</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h2>Suppression de compte</h2>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($success): ?>
            <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
    </div>
    <footer>
        &copy; 2024 HebergeX. Tous droits réservés.
    </footer>
</body>
</html>
