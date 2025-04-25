#!/bin/bash

# Vérification si un alias utilisateur est passé en argument
if [ -z "$1" ]; then
    echo "Usage: $0 <alias_utilisateur>"
    exit 1
fi

# Variables
ALIAS=$1
APACHE_CONF_DIR="/etc/apache2/sites-available"
DNS_FILE="/etc/bind/db.heberge3.lan"
HOME_DIR="/home/$ALIAS"

echo "Début de la suppression de l'utilisateur : $ALIAS"

# 1. Suppression de l’enregistrement dans la base de données principale
echo "Suppression de l'utilisateur dans la base de données principale..."
mysql -u root -proot -e "DELETE FROM USER WHERE alias = '$ALIAS';" hebergeX

# 2. Suppression du compte Linux
echo "Suppression du compte Linux..."
sudo userdel -r "$ALIAS" 2>/dev/null || echo "Compte Linux non trouvé ou déjà supprimé."

# 3. Suppression des fichiers de stockage
echo "Suppression des fichiers de stockage..."
sudo rm -rf "$HOME_DIR" || echo "Aucun répertoire personnel trouvé."

# 4. Suppression de l'utilisateur GLPI
echo "Suppression de l'utilisateur GLPI..."
mysql -u root -proot -e "DELETE FROM glpi_profiles_users WHERE users_id = (SELECT id FROM glpi_users WHERE name = '$ALIAS');" glpi
mysql -u root -proot -e "DELETE FROM glpi_users WHERE name = '$ALIAS';" glpi

# 5. Suppression de la configuration Apache
echo "Suppression de la configuration Apache..."
if [ -f "$APACHE_CONF_DIR/$ALIAS.conf" ]; then
    sudo rm "$APACHE_CONF_DIR/$ALIAS.conf"
    sudo a2dissite "$ALIAS.conf"
    sudo service apache2 reload
else
    echo "Fichier de configuration Apache non trouvé."
fi

# 6. Suppression de l’entrée DNS
echo "Suppression de l'entrée DNS..."
if grep -q "$ALIAS IN CNAME" "$DNS_FILE"; then
    sudo perl -ni.orig -e "print unless (/$ALIAS IN CNAME/)" "$DNS_FILE"
    sudo service bind9 reload
else
    echo "Entrée DNS non trouvée."
fi

echo "Utilisateur $ALIAS supprimé avec succès !"
