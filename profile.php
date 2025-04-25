<?php
session_start();

if (!isset($_SESSION['alias'])) {
    header("Location: login.php");
    exit();
}

include_once 'includes/bdd.php';

$alias = $_SESSION['alias'];
$error = "";
$success = "";

try {
    // Récupération des infos utilisateur
    $sql = "SELECT * FROM USER WHERE alias = :alias";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['alias' => $alias]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = "Utilisateur non trouvé.";
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des données : " . $e->getMessage();
}

// Mise à jour des infos personnelles
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile']) && !$error) {
    $prenom = htmlspecialchars($_POST['prenom']);
    $nom = htmlspecialchars($_POST['nom']);
    $mail = htmlspecialchars($_POST['mail']);

    if (empty($prenom) || empty($nom) || empty($mail)) {
        $error = "Tous les champs doivent être remplis.";
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse e-mail invalide.";
    } else {
        try {
            $updateSql = "UPDATE USER SET prenom = :prenom, nom = :nom, mail = :mail WHERE alias = :alias";
            $stmt = $pdo->prepare($updateSql);
            $stmt->execute([
                'prenom' => $prenom,
                'nom' => $nom,
                'mail' => $mail,
                'alias' => $alias
            ]);
            $_SESSION['prenom'] = $prenom;
            $_SESSION['nom'] = $nom;
            $success = "Informations mises à jour avec succès.";
            $user['prenom'] = $prenom;
            $user['nom'] = $nom;
            $user['mail'] = $mail;
        } catch (PDOException $e) {
            $error = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}

// Mise à jour du mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password']) && !$error) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if (empty($old_password) || empty($new_password) || empty($confirm_new_password)) {
        $error = "Tous les champs de changement de mot de passe doivent être remplis.";
    } elseif (!password_verify($old_password, $user['mdp'])) {
        $error = "Ancien mot de passe incorrect.";
    } elseif ($new_password !== $confirm_new_password) {
        $error = "Les nouveaux mots de passe ne correspondent pas.";
    } elseif (strlen($new_password) < 8 || strlen($new_password) > 40) {
        $error = "Le nouveau mot de passe doit comporter entre 8 et 40 caractères.";
    } else {
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        try {
            $updatePassSql = "UPDATE USER SET mdp = :new_password WHERE alias = :alias";
            $stmt = $pdo->prepare($updatePassSql);
            $stmt->execute([
                'new_password' => $new_hashed_password,
                'alias' => $alias
            ]);
            $success = "Mot de passe mis à jour avec succès.";

            $scriptPath = "/var/www/html/ModifUtilisateur.sh";
            $command = "sudo " . escapeshellcmd($scriptPath) . " " . escapeshellarg($alias) . " " . escapeshellarg($new_password);
            shell_exec($command);

        } catch (PDOException $e) {
            $error = "Erreur lors de la mise à jour du mot de passe : " . $e->getMessage();
        }
    }
    
}
//$siteSize = shell_exec("sudo /var/www/html/taille_site.sh " . escapeshellarg($alias));
//$siteSize = trim($siteSize);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil utilisateur</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .profile-header {
      background-color: #007bff;
      color: white;
      padding: 20px;
      text-align: center;
    }
    .profile-header h2 {
      margin: 0;
    }
    .nav-tabs .nav-link.active {
      background-color: #007bff;
      color: white;
    }
    /* Centrer le contenu de la carte */
    .card {
      margin: auto;
    }
  </style>
</head>
<body>
  <?php include 'includes/header.php'; ?>

  <div class="container mt-4">
    <?php if ($error): ?>
      <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
      <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="card mx-auto" style="max-width: 800px;">
      <div class="card-header profile-header">
         <h2>Profil de <?php echo htmlspecialchars($user['prenom'] ?? 'Inconnu') . " " . htmlspecialchars($user['nom'] ?? ''); ?></h2>
         <p>Alias : <?php echo htmlspecialchars($user['alias']); ?></p>
      </div>
      <div class="mb-3">
      <?php
      $dossier="/home/".$alias."/public_html";
$taille = shell_exec("du -sh " . escapeshellarg($dossier) . " 2>/dev/null | cut -f1");

echo "Taille du site : " . trim($taille);?>
</div>
      <div class="card-body">
         <!-- Onglets -->
         <ul class="nav nav-tabs" id="profileTab" role="tablist">
           <li class="nav-item" role="presentation">
             <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">Mes Informations</button>
           </li>
           <li class="nav-item" role="presentation">
             <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">Changer de Mot de Passe</button>
           </li>
         </ul>
         <div class="tab-content mt-3" id="profileTabContent">
           <!-- Onglet Mes Informations -->
           <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
             <form action="" method="POST">
               <input type="hidden" name="update_profile" value="1">
               <div class="mb-3">
                 <label for="prenom" class="form-label">Prénom :</label>
                 <input type="text" id="prenom" name="prenom" class="form-control" value="<?php echo htmlspecialchars($user['prenom'] ?? ''); ?>" required>
               </div>
               <div class="mb-3">
                 <label for="nom" class="form-label">Nom :</label>
                 <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($user['nom'] ?? ''); ?>" required>
               </div>
               <div class="mb-3">
                 <label for="mail" class="form-label">Adresse mail :</label>
                 <input type="email" id="mail" name="mail" class="form-control" value="<?php echo htmlspecialchars($user['mail'] ?? ''); ?>" required>
               </div>
               <button type="submit" class="btn btn-primary">Mettre à jour</button>
             </form>
           </div>
           <!-- Onglet Changer de Mot de Passe -->
           <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
             <form action="" method="POST" id="passwordForm">
               <input type="hidden" name="update_password" value="1">
               <div class="mb-3">
                 <label for="old_password" class="form-label">Ancien mot de passe :</label>
                 <input type="password" id="old_password" name="old_password" class="form-control" required maxlength="40">
               </div>
               <div class="mb-3">
                 <label for="new_password" class="form-label">Nouveau mot de passe :</label>
                 <div class="input-group">
                   <input type="password" id="new_password" name="new_password" class="form-control" required maxlength="40">
                   <span class="input-group-text" id="toggleNewPassword" style="cursor: pointer;">
                     <i class="bi bi-eye"></i>
                   </span>
                 </div>
                 <div id="newPasswordConstraint" class="text-muted mt-1">
                   8 à 40 caractères avec minuscule, majuscule, chiffre et caractère spécial.
                 </div>
               </div>
               <div class="mb-3">
                 <label for="confirm_new_password" class="form-label">Confirmer le nouveau mot de passe :</label>
                 <div class="input-group">
                   <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control" required maxlength="40">
                   <span class="input-group-text" id="toggleConfirmNewPassword" style="cursor: pointer;">
                     <i class="bi bi-eye"></i>
                   </span>
                 </div>
                 <div id="newPasswordError" class="text-danger mt-1"></div>
               </div>
               <button type="submit" class="btn btn-primary" id="updatePasswordBtn">Changer de mot de passe</button>
             </form>
           </div>
         </div>
         <!-- Bouton Supprimer le compte -->
         <div class="mt-4 text-center">
           <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Supprimer le compte</button>
         </div>
      </div>
    </div>
  </div>

  <!-- Modal de confirmation pour la suppression du compte -->
  <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de suppression</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <form action="delete_account.php" method="POST" class="d-inline">
            <button type="submit" class="btn btn-danger">Supprimer mon compte</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <footer class="text-center py-3 bg-primary text-white mt-4">
    &copy; 2024 HebergeX. Tous droits réservés.
  </footer>

  <!-- Bootstrap JS -->
  <script src="js/bootstrap.bundle.min.js"></script>
  <!-- Script de validation du nouveau mot de passe -->
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const newPasswordField = document.getElementById('new_password');
    const confirmNewPasswordField = document.getElementById('confirm_new_password');
    const newPasswordError = document.getElementById('newPasswordError');
    const updatePasswordBtn = document.getElementById('updatePasswordBtn');
    const toggleNewPassword = document.getElementById('toggleNewPassword');
    const toggleConfirmNewPassword = document.getElementById('toggleConfirmNewPassword');

    // Toggle pour le nouveau mot de passe
    toggleNewPassword.addEventListener('click', function () {
      const type = newPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
      newPasswordField.setAttribute('type', type);
      this.firstElementChild.classList.toggle('bi-eye');
      this.firstElementChild.classList.toggle('bi-eye-slash');
    });

    // Toggle pour confirmer le nouveau mot de passe
    toggleConfirmNewPassword.addEventListener('click', function () {
      const type = confirmNewPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
      confirmNewPasswordField.setAttribute('type', type);
      this.firstElementChild.classList.toggle('bi-eye');
      this.firstElementChild.classList.toggle('bi-eye-slash');
    });

    // Regex pour valider les contraintes du nouveau mot de passe
    function validateNewPasswordConstraints(password) {
      const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,40}$/;
      return regex.test(password);
    }

    function checkNewPasswords() {
      if (!validateNewPasswordConstraints(newPasswordField.value)) {
        newPasswordError.textContent = "Le mot de passe doit comporter 8 à 40 caractères avec minuscule, majuscule, chiffre et caractère spécial.";
        updatePasswordBtn.disabled = true;
        return;
      }
      if (newPasswordField.value !== confirmNewPasswordField.value) {
        newPasswordError.textContent = "Les mots de passe ne correspondent pas.";
        updatePasswordBtn.disabled = true;
      } else {
        newPasswordError.textContent = "";
        updatePasswordBtn.disabled = false;
      }
    }

    newPasswordField.addEventListener('input', checkNewPasswords);
    confirmNewPasswordField.addEventListener('input', checkNewPasswords);
  });
  </script>
</body>
</html>
