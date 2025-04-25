#!/bin/bash

# Vérification des paramètres
if [ -z "$1" ] || [ -z "$2" ]; then
    echo "Usage: $0 <alias_utilisateur> <mot_de_passe>"
    exit 1
fi

# Variables
ALIAS=$1
PASSWORD=$2
HOME_DIR="/home/$ALIAS"
APACHE_CONF_DIR="/etc/apache2/sites-available"
DNS_FILE="/etc/bind/db.heberge3.lan"
DB_NAME="heberge3"
GLPI_DB="glpi"
GLPI_ACTIVE=true  # Passe à false si tu veux désactiver GLPI

echo "Début de la création de l'utilisateur : $ALIAS"

# 1. Création du compte Linux
echo "Création du compte Linux..."
useradd -m -d "$HOME_DIR" -s /bin/bash "$ALIAS"
echo "$ALIAS:$PASSWORD" | chpasswd

mkdir -p "$HOME_DIR/public_html"
touch "$HOME_DIR/public_html/index.html"
chown -R "$ALIAS:$ALIAS" "$HOME_DIR"

# 2. Création de la base de données personnelle
echo "Création de la base de données MySQL..."
mysql -u root -proot -e "CREATE DATABASE $ALIAS;"
mysql -u root -proot -e "GRANT ALL PRIVILEGES ON \`$ALIAS\`.* TO '$ALIAS'@'%' IDENTIFIED BY '$PASSWORD';"

# 3. Configuration Apache
echo "Configuration d'Apache..."
CONF_TEMPLATE="$APACHE_CONF_DIR/XXXX.conf"
CONF_TARGET="$APACHE_CONF_DIR/$ALIAS.conf"

if [ -f "$CONF_TEMPLATE" ]; then
    cp "$CONF_TEMPLATE" "$CONF_TARGET"
    sed -i -e "s/XXXX/$ALIAS/g" "$CONF_TARGET"

    a2ensite "$ALIAS.conf"
    service apache2 reload
else
    echo "⚠️  Le modèle $CONF_TEMPLATE est introuvable !"
fi

# 4. Ajout DNS
echo "Ajout de l'entrée DNS..."
if ! grep -q "$ALIAS IN CNAME" "$DNS_FILE"; then
    echo "$ALIAS IN CNAME SrvWeb.heberge3.lan." >> "$DNS_FILE"
    service bind9 reload
else
    echo "Entrée DNS déjà existante."
fi

# 5. Création utilisateur GLPI
if [ "$GLPI_ACTIVE" = true ]; then
    echo "Ajout de l'utilisateur dans GLPI..."

    HASHED_PASS=$(php -r "echo password_hash('$PASSWORD', PASSWORD_DEFAULT);")

    mysql -u root -proot "$GLPI_DB" <<EOF
INSERT INTO glpi_users (name, password, realname, firstname, is_active, authtype)
VALUES ('$ALIAS', '$HASHED_PASS', '$ALIAS', '$ALIAS', 1, 1);
EOF

    USER_ID=$(mysql -u root -proot -sN -e "SELECT id FROM glpi_users WHERE name='$ALIAS';" "$GLPI_DB")
    mysql -u root -proot "$GLPI_DB" -e "INSERT INTO glpi_profiles_users (users_id, profiles_id) VALUES ('$USER_ID', 1);"
fi

echo "✅ Utilisateur $ALIAS créé avec succès !"
