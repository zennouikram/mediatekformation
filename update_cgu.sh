#!/bin/bash

# Script pour mettre à jour l'URL dans la page CGU
# Usage: ./update_cgu.sh votre-url.alwaysdata.net

if [ -z "$1" ]; then
    echo "Usage: ./update_cgu.sh votre-url.alwaysdata.net"
    exit 1
fi

NEW_URL=$1

echo "Mise à jour de la page CGU avec l'URL: $NEW_URL"

sed -i "s|http://www.mediatekformation.fr|https://$NEW_URL|g" templates/pages/cgu.html.twig
sed -i "s|mediatekformation.fr|$NEW_URL|g" templates/pages/cgu.html.twig

echo "Page CGU mise à jour avec succès!"
echo "N'oubliez pas de commit et push les changements:"
echo "git add templates/pages/cgu.html.twig"
echo "git commit -m 'Update CGU with production URL'"
echo "git push"
