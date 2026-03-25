#!/bin/bash

# Répertoires de base
BASE_DIR="/home/n7f9655/public_html/phoenixsupplies"
BASE_DIR_TO="/home/n7f9655/public_html/phoenixliquidation"
# Fonction pour copier les répertoires
copy_dir() {
    src="$1"
    dest="$2"
    if [ -d "$src" ]; then
        yes | cp -rf "$src" "$dest"
        echo "Copié : $src -> $dest"
    else
        echo "Source introuvable : $src"
    fi
}

# Copier les répertoires Admin
copy_dir "$BASE_DIR/admin/controller/common/" "$BASE_DIR_TO/admin/controller/"
copy_dir "$BASE_DIR/admin/language/en/common/" "$BASE_DIR_TO/admin/language/en/"
copy_dir "$BASE_DIR/admin/language/fr/common/" "$BASE_DIR_TO/admin/language/fr/"
copy_dir "$BASE_DIR/admin/controller/shopmanager/" "$BASE_DIR_TO/admin/controller/"
copy_dir "$BASE_DIR/admin/language/en/shopmanager/" "$BASE_DIR_TO/admin/language/en/"
copy_dir "$BASE_DIR/admin/language/fr/shopmanager/" "$BASE_DIR_TO/admin/language/fr/"
copy_dir "$BASE_DIR/admin/model/shopmanager/" "$BASE_DIR_TO/admin/model/"

copy_dir "$BASE_DIR/admin/view/stylesheet/shopmanager/" "$BASE_DIR_TO/admin/view/stylesheet/"
copy_dir "$BASE_DIR/admin/view/template/shopmanager/" "$BASE_DIR_TO/admin/view/template/"
copy_dir "$BASE_DIR/admin/view/javascript/shopmanager/" "$BASE_DIR_TO/admin/view/javascript/"


# Copier les répertoires Image
copy_dir "$BASE_DIR/image/data/category/" "$BASE_DIR_TO/image/data/"
copy_dir "$BASE_DIR/image/data/category/" "$BASE_DIR_TO/image/data/"

echo "Toutes les copies sont terminées."
