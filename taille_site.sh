#!/bin/bash

# ───────────────────────────────────────────────────────
# Script : taille_site.sh
# Objectif : Calculer la taille totale du site web d’un utilisateur
# Utilisation : ./taille_site.sh <alias_utilisateur>
# Ex : ./taille_site.sh alves71
# ───────────────────────────────────────────────────────

# Vérifie que l'alias est fourni en argument
if [ -z "$1" ]; then
    echo "Erreur : alias manquant. Utilisation : $0 <alias_utilisateur>"
    exit 1
fi

ALIAS="$1"
SITE_DIR="/home/$ALIAS/public_html"

# Vérifie que le dossier du site existe
if [ ! -d "$SITE_DIR" ]; then
    echo "Inexistant"
    exit 0
fi

# Calcule la taille totale du dossier (tous les fichiers, sous-dossiers inclus)
TAILLE=$(du -sh "$SITE_DIR" 2>/dev/null | cut -f1)

# Si le résultat est vide, on renvoie une erreur
if [ -z "$TAILLE" ]; then
    echo "Erreur de calcul"
    exit 1
fi

# Affiche la taille (ex : 12K, 3.4M, 1.2G...)
echo "$TAILLE"
exit 0
