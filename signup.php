<?php
session_start();
include 'includes/bdd.php';
include 'includes/header.php';

$message = "";
$erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nettoyage des données
    $alias = htmlspecialchars($_POST['alias']);
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $passwordInput = $_POST['password'];
    $confirmPasswordInput = $_POST['confirm_password'];

    // Vérification des longueurs
    if (strlen($alias) > 255 || strlen($nom) > 255 || strlen($prenom) > 255 || strlen($email) > 255) {
        $erreur = "Les champs texte ne doivent pas dépasser 255 caractères.";
    }

    // Vérification du mot de passe
    if (strlen($passwordInput) < 8 || strlen($passwordInput) > 40) {
        $erreur = "Le mot de passe doit comporter entre 8 et 40 caractères.";
    }

    // Regex de sécurité (majuscule, minuscule, chiffre, caractère spécial)
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,40}$/', $passwordInput)) {
        $erreur = "Le mot de passe doit comporter au moins une majuscule, une minuscule, un chiffre et un caractère spécial.";
    }

    // Vérification de la confirmation
    if ($passwordInput !== $confirmPasswordInput) {
        $erreur = "Les mots de passe ne correspondent pas.";
    }

    if (empty($erreur)) {
        try {
            // Vérifier si alias ou mail déjà pris
            $sql_check = "SELECT COUNT(*) FROM USER WHERE alias = :alias OR mail = :email";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->bindParam(':alias', $alias);
            $stmt_check->bindParam(':email', $email);
            $stmt_check->execute();
            $count = $stmt_check->fetchColumn();

            if ($count > 0) {
                $erreur = "Un compte existe déjà avec cet alias ou cet e-mail. Veuillez vous connecter.";
            } else {
                // Hachage du mot de passe
                $password = password_hash($passwordInput, PASSWORD_DEFAULT);

                // Insertion en base
                $sql = "INSERT INTO USER (alias, nom, prenom, mail, mdp) 
                        VALUES (:alias, :nom, :prenom, :email, :password)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':alias', $alias);
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':prenom', $prenom);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                $stmt->execute();

                // Exécution du script Bash pour création système
                $command = "sudo /var/www/html/CreeUtilisateur.sh " . escapeshellarg($alias) . " " . escapeshellarg($passwordInput);
                $output = shell_exec($command);

                if ($output === null || str_contains($output, "Erreur")) {
                    $erreur = "Erreur lors de la création de l'utilisateur sur le serveur Linux.";
                } else {
                    // Connexion automatique
                    $_SESSION['user'] = [
                        'alias' => $alias,
                        'prenom' => $prenom,
                        'nom' => $nom,
                        'email' => $email
                    ];
                    $_SESSION['alias'] = $alias;
                    $_SESSION['prenom'] = $prenom;
                    $_SESSION['nom'] = $nom;

                    $message = "Bienvenue " . htmlspecialchars($prenom) . " " . htmlspecialchars($nom) . " ! Vous êtes maintenant connecté. L'utilisateur Linux a été créé avec succès.";
                    header("refresh:3;url=index.php");
                    exit();
                }
            }
        } catch (PDOException $e) {
            $erreur = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>

  <!DOCTYPE html>
  <html lang="fr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  </head>
  <body class="bg-light">

  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-4" style="max-width: 500px; width: 100%;">
      <h2 class="text-center text-primary mb-4">Inscription</h2>

      <!-- Affichage des messages -->
      <?php if (!empty($erreur)): ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <?php echo htmlspecialchars($erreur); ?>
        </div>
      <?php elseif (!empty($message)): ?>
        <div class="alert alert-success d-flex align-items-center" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i>
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <form action="" method="POST" id="signupForm">
        <div class="mb-4">
          <label for="alias" class="form-label">Alias</label>
          <input type="text" id="alias" name="alias" class="form-control" required maxlength="255">
        </div>

        <div class="mb-4">
          <label for="nom" class="form-label">Nom</label>
          <input type="text" id="nom" name="nom" class="form-control" required maxlength="255">
        </div>

        <div class="mb-4">
          <label for="prenom" class="form-label">Prénom</label>
          <input type="text" id="prenom" name="prenom" class="form-control" required maxlength="255">
        </div>

        <div class="mb-4">
          <label for="email" class="form-label">E-mail</label>
          <input type="email" id="email" name="email" class="form-control" required maxlength="255">
        </div>

        <!-- Champ mot de passe avec bouton pour afficher/masquer -->
        <div class="mb-4">
          <label for="password" class="form-label">Mot de passe</label>
          <div class="input-group">
            <input type="password" id="password" name="password" class="form-control" required maxlength="40">
            <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
              <i class="bi bi-eye"></i>
            </span>
          </div>
          <div id="passwordConstraint" class="text-muted mt-1">
            8 à 40 caractères avec minuscule, majuscule, chiffre et caractère spécial.
          </div>
        </div>

        <!-- Champ confirmer mot de passe avec bouton pour afficher/masquer -->
        <div class="mb-4">
          <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
          <div class="input-group">
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required maxlength="40">
            <span class="input-group-text" id="toggleConfirmPassword" style="cursor: pointer;">
              <i class="bi bi-eye"></i>
            </span>
          </div>
          <div id="passwordError" class="text-danger mt-1"></div>
        </div>

        <button type="submit" class="btn btn-primary w-100" id="submitBtn">S'inscrire</button>
      </form>
    </div>
  </div>



  <!-- Bootstrap JS -->
  <script src="js/bootstrap.bundle.min.js"></script>
  <!-- Script de vérification en temps réel des contraintes et bascule de visibilité -->
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');
    const passwordError = document.getElementById('passwordError');
    const submitBtn = document.getElementById('submitBtn');

    // Toggle pour le champ "password"
    const togglePassword = document.getElementById('togglePassword');
    togglePassword.addEventListener('click', function () {
      const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordField.setAttribute('type', type);
      this.firstElementChild.classList.toggle('bi-eye');
      this.firstElementChild.classList.toggle('bi-eye-slash');
    });

    // Toggle pour le champ "confirm_password"
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    toggleConfirmPassword.addEventListener('click', function () {
      const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
      confirmPasswordField.setAttribute('type', type);
      this.firstElementChild.classList.toggle('bi-eye');
      this.firstElementChild.classList.toggle('bi-eye-slash');
    });

    // Regex pour valider les contraintes de mot de passe (8 à 40 caractères, minuscule, majuscule, chiffre, caractère spécial)
    function validatePasswordConstraints(password) {
      const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,40}$/;
      return regex.test(password);
    }

    // Vérification des champs et désactivation du bouton si conditions non respectées
    function checkForm() {
      // Vérifier la longueur des champs texte (déjà limitée par maxlength mais on le contrôle aussi en JS)
      const textFields = document.querySelectorAll('#alias, #nom, #prenom, #email');
      let validText = true;
      textFields.forEach(function(field) {
        if (field.value.length > 255) {
          validText = false;
        }
      });
      
      if (!validText) {
        passwordError.textContent = "Les champs texte ne doivent pas dépasser 255 caractères.";
        submitBtn.disabled = true;
        return;
      }
      
      // Vérifier la contrainte du mot de passe
      if (!validatePasswordConstraints(passwordField.value)) {
        passwordError.textContent = "Le mot de passe doit comporter 8 à 40 caractères avec minuscule, majuscule, chiffre et caractère spécial.";
        submitBtn.disabled = true;
        return;
      }
      
      // Vérifier que les deux mots de passe correspondent
      if (passwordField.value !== confirmPasswordField.value) {
        passwordError.textContent = "Les mots de passe ne correspondent pas.";
        submitBtn.disabled = true;
      } else {
        passwordError.textContent = "";
        submitBtn.disabled = false;
      }
    }

    passwordField.addEventListener('input', checkForm);
    confirmPasswordField.addEventListener('input', checkForm);
    
    const textFields = document.querySelectorAll('#alias, #nom, #prenom, #email');
    textFields.forEach(function(field) {
      field.addEventListener('input', checkForm);
    });
  });
  </script>
  </body>
  </html>
