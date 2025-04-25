#!/bin/bash

# ─── CONFIGURATION ────────────────────────────────────────────────────────────
PHP_ENDPOINT="http://localhost/modifier_utilisateur.php" # Modifier l'URL si besoin

# ─── Dépendance requise : curl ───────────────────────────────────────────────
if ! command -v curl &> /dev/null; then
    echo "Erreur : curl n'est pas installé. Veuillez l'installer."
    exit 1
fi

# ─── Lecture des informations utilisateur ─────────────────────────────────────
echo "--- Modification d'un utilisateur ---"
read -p "Alias de l'utilisateur : " alias
read -p "Prénom : " prenom
read -p "Nom : " nom
read -p "Adresse e-mail : " mail
read -s -p "Nouveau mot de passe : " password

echo -e "\nEnvoi des données en cours..."

# ─── Envoi via POST à modifier_utilisateur.php ───────────────────────────────
RESPONSE=$(curl -s -X POST "$PHP_ENDPOINT" \
  -d "alias=$alias" \
  -d "prenom=$prenom" \
  -d "nom=$nom" \
  -d "mail=$mail" \
  -d "password=$password")

# ─── Affichage du résultat ───────────────────────────────────────────────────
echo "\n--- Réponse du serveur ---"
echo "$RESPONSE"

exit 0
