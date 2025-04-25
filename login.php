<?php
session_start();
ob_start();
include 'includes/bdd.php';
include 'includes/header.php';

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // On récupère l'identifiant, qui peut être une adresse e-mail ou un alias
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Vérification côté serveur des longueurs
    if (strlen($login) > 255) {
        $error = "L'identifiant ne doit pas dépasser 255 caractères.";
    }
    if (strlen($password) < 8 || strlen($password) > 40) {
        $error = "Le mot de passe doit comporter entre 8 et 40 caractères.";
    }
    
    if (!empty($login) && !empty($password) && empty($error)) {
        try {
            // Rechercher un utilisateur dont l'adresse e-mail ou l'alias correspond au login fourni
            $query = $pdo->prepare("SELECT * FROM USER WHERE mail = ? OR alias = ?");
            $query->execute([$login, $login]);
            $user = $query->fetch();

            if ($user && password_verify($password, $user['mdp'])) {
                // Définit les variables de session
                $_SESSION['user'] = $user;
                $_SESSION['alias'] = $user['alias'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['nom'] = $user['nom'];

                $message = "Bonjour " . htmlspecialchars($user['prenom']) . " " . htmlspecialchars($user['nom']) . " !";
                header("refresh:2;url=index.php");
                exit();
            } else {
                $error = "Email/alias ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $error = "Erreur de connexion à la base : " . $e->getMessage();
        }
    } else {
        if (empty($error)) {
            $error = "Veuillez remplir tous les champs.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
    <h2 class="text-center text-primary mb-4">Connexion</h2>

    <!-- Affichage des messages -->
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php elseif (!empty($message)): ?>
      <div class="alert alert-success">
        <?php echo htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="POST" id="loginForm">
      <div class="mb-3">
        <label for="login" class="form-label">Email ou Alias</label>
        <input type="text" id="login" name="login" class="form-control" placeholder="Entrez votre email ou alias" required maxlength="255">
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <div class="input-group">
          <input type="password" id="password" name="password" class="form-control" placeholder="Entrez votre mot de passe" required maxlength="40">
          <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
            <i class="bi bi-eye"></i>
          </span>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100" id="submitBtn">Se connecter</button>
    </form>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="js/bootstrap.bundle.min.js"></script>
<!-- Script de vérification en temps réel et de bascule de visibilité -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const loginField = document.getElementById('login');
  const passwordField = document.getElementById('password');
  const submitBtn = document.getElementById('submitBtn');
  const togglePassword = document.getElementById('togglePassword');

  // Toggle pour afficher/masquer le mot de passe
  togglePassword.addEventListener('click', function () {
    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordField.setAttribute('type', type);
    this.firstElementChild.classList.toggle('bi-eye');
    this.firstElementChild.classList.toggle('bi-eye-slash');
  });

  // Vérification en temps réel
  function checkLoginForm() {
    let valid = true;
    if (loginField.value.length > 255) {
      valid = false;
    }
    if (passwordField.value.length < 8 || passwordField.value.length > 40) {
      valid = false;
    }
    submitBtn.disabled = !valid;
  }

  loginField.addEventListener('input', checkLoginForm);
  passwordField.addEventListener('input', checkLoginForm);
});
</script>
</body>
</html>
