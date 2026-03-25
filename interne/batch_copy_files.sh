#!/bin/bash

# Définir les répertoires source et destination
BASE_DIR="/home/n7f9655/public_html/canuship/catalog"
BASE_DIR_TO="/home/n7f9655/public_html/phoenixsupplies"

# Demander le nom du fichier sans l'extension
read -p "Entrez le nom du fichier sans l'extension : " FILENAME

# Fonction pour copier un fichier uniquement s'il existe
copy_if_exists() {
    if [ -f "$1" ]; then
        cp "$1" "$2"
        echo "✅ Copie de $(basename "$1") -> $(basename "$2")"
    else
        echo "⚠️ Fichier non trouvé : $1"
    fi
}

# Copier les fichiers des différents répertoires
echo "📂 Début de la copie des fichiers..."

copy_if_exists "$BASE_DIR/controller/shipper/$FILENAME.php" "$BASE_DIR_TO/admin/controller/shopmanager/"
copy_if_exists "$BASE_DIR/language/en/shipper/$FILENAME.php" "$BASE_DIR_TO/admin/language/en/shopmanager/"
copy_if_exists "$BASE_DIR/language/fr/shipper/$FILENAME.php" "$BASE_DIR_TO/admin/language/fr/shopmanager/"
copy_if_exists "$BASE_DIR/model/shipper/$FILENAME.php" "$BASE_DIR_TO/admin/model/shopmanager/"

copy_if_exists "$BASE_DIR/view/theme/default/template/shipper/$FILENAME.tpl" "$BASE_DIR_TO/admin/view/template/shopmanager/"
copy_if_exists "$BASE_DIR/view/theme/default/javascript/shipper/$FILENAME.js" "$BASE_DIR_TO/admin/view/javascript/shopmanager/"
copy_if_exists "$BASE_DIR/view/theme/default/stylesheet/shipper/$FILENAME.css" "$BASE_DIR_TO/admin/view/stylesheet/shopmanager/"

#copy_if_exists "$BASE_DIR/view/template/shipper/$FILENAME.tpl" "$BASE_DIR_TO/admin/view/template/shopmanager/"
#copy_if_exists "$BASE_DIR/view/javascript/shipper/$FILENAME.js" "$BASE_DIR_TO/admin/view/javascript/shopmanager/"
#copy_if_exists "$BASE_DIR/view/stylesheet/shipper/$FILENAME.css" "$BASE_DIR_TO/admin/view/stylesheet/shopmanager/"

echo "✅ Copie terminée."

# Remplacement des occurrences de "Shipper" et "shipper" dans les fichiers copiés
echo "🔍 Modification des fichiers copiés : remplacement de 'Shipper' et 'shipper'..."

FOLDERS=(
    "$BASE_DIR_TO/admin/controller/shopmanager/"
    "$BASE_DIR_TO/admin/language/en/shopmanager/"
    "$BASE_DIR_TO/admin/language/fr/shopmanager/"
    "$BASE_DIR_TO/admin/model/shopmanager/"
    "$BASE_DIR_TO/admin/view/template/shopmanager/"
    "$BASE_DIR_TO/admin/view/javascript/shopmanager/"
    "$BASE_DIR_TO/admin/view/stylesheet/shopmanager/"
   
)

for FOLDER in "${FOLDERS[@]}"; do
    if [ -d "$FOLDER" ]; then
        find "$FOLDER" -type f -exec sed -i 's/Shipper/Shopmanager/g' {} +
        find "$FOLDER" -type f -exec sed -i 's/shipper/shopmanager/g' {} +
        echo "✅ Remplacement terminé dans $FOLDER"
    else
        echo "⚠️ Dossier non trouvé : $FOLDER"
    fi
done

echo "✅ Remplacement global terminé."

# Appel du script batch_copy_vers_phoenixliquidation.sh
echo "🚀 Exécution du script batch_copy_vers_phoenixliquidation.sh..."
/bin/bash batch_copy_vers_phoenixliquidation.sh
echo "✅ Script batch_copy_vers_phoenixliquidation.sh exécuté."
