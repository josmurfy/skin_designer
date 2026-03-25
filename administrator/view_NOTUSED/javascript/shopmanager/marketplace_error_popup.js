function showErrorPopup (product_id) {
    var token = document.querySelector('input[name="token"]').value;

    $.ajax({
        url: `index.php?route=shopmanager/marketplace_error_popup&token=${token}&product_id=${product_id}`,
        dataType: 'html',
        success: function(html) {
            // Supprime la modale précédente s’il y en a une
            $('#popup-error').remove();

            // Ajoute la nouvelle modale au DOM
            $('body').append(html);

            // Affiche la popup
            $('#popup-error').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Failed to load popup:', error);
            alert('Failed to load error popup.');
        }
    });
}


function safeJsonParse(badJson) {
    try {
        return JSON.parse(badJson);
    } catch (e) {
        console.warn('Invalid JSON. Attempting repair...');

        // Corrige : remplace les guillemets à l'intérieur des chaînes
        let repaired = badJson.replace(/:\s*"([^"]*?)"(?=\s*[,\}])/g, function(match, p1) {
            return ': "' + p1.replace(/"/g, '\\"') + '"';
        });

        try {
            return JSON.parse(repaired);
        } catch (e2) {
            console.error('JSON irreparable:', e2.message);
            return null;
        }
    }
}

function showErrorPopupOLD(marketplace_account_id, marketplace_item_id, error, product_id, is_products = false) {
    var token = document.querySelector('input[name="token"]').value;

    $.ajax({
        url: `index.php?route=shopmanager/marketplace_error_popup&token=${token}&marketplace_item_id=${marketplace_item_id}`,
        dataType: 'html',
        success: function(html) {
            $('body').append(html);

            let errorData;

            try {
                errorData = JSON.parse(error);
            } catch (e) {
                console.error("JSON parse error:", e.message);
                console.warn("Invalid JSON string received:", error);
                $('#popup-error .modal-body').html('<div class="alert alert-danger">Unable to parse error details.</div>');
                $('#popup-error').modal('show');
                return;
            }

            let tableHtml = '<div class="table-responsive"><table class="table table-bordered table-hover"><thead><tr><th>Key</th><th>Value</th></tr></thead><tbody>';
            tableHtml += generateTableRows(errorData);
            tableHtml += '</tbody></table></div>';

            $('#popup-error .modal-body').html(tableHtml);
            $('#popup-error').data('marketplace_item_id', marketplace_item_id);
            $('#popup-error').data('marketplace_account_id', marketplace_account_id);
            $('#popup-error').data('product_id', product_id);
            $('#popup-error').data('is_products', is_products);
            $('#popup-error').modal('show');
        }
    });
}


$(document).on('click', '#popup-error .btn-close', function() {
    $('#popup-error').modal('hide');
});

$(document).on('click', '#popup-error .btn-retry', function() {
    var marketplace_item_id = $('#popup-error').data('marketplace_item_id');
    var marketplace_account_id = $('#popup-error').data('marketplace_account_id');
    var product_id = $('#popup-error').data('product_id');
    var is_products = $('#popup-error').data('is_products');
    handleMarketplaceError(marketplace_account_id, marketplace_item_id, product_id, is_products);
});

$(document).on('click', '#popup-error .btn-fix-issue', function() {
    var marketplace_item_id = $('#popup-error').data('marketplace_item_id');
    var marketplace_account_id = $('#popup-error').data('marketplace_account_id');
    var product_id = $('#popup-error').data('product_id');
    var is_products = $('#popup-error').data('is_products');
    handleMarketplaceError(marketplace_account_id, marketplace_item_id, product_id, is_products);
    // Implement fix issue logic here
});

function handleMarketplaceError(marketplace_account_id, marketplace_item_id , product_id, is_products = false) {
    var token = document.querySelector('input[name="token"]').value;
    if (is_products === true) {
        var product_underscore = product_id + '_';
    } else {
        var product_underscore = '';
    }
    // Construire dynamiquement les IDs des éléments cachés et du span
    var spanElement = document.getElementById(`marketplace_account_id_${product_underscore}${marketplace_account_id}`);
    if (!spanElement) {
        console.warn(`Élément non trouvé pour marketplace_account_id_${product_underscore}${marketplace_account_id}`);
        return;
    }
    var urlProductInput = document.querySelector(`input[name="url_product_${product_underscore}${marketplace_account_id}"]`);
    var thumbInput = spanElement.querySelector("img"); // Sélectionne l'image dans le <span>

    if (spanElement && urlProductInput && thumbInput) {
        // Récupérer le nom de la marketplace depuis l'attribut alt de l'image
        var marketplaceName = thumbInput.alt || "Marketplace";
        var baseUrl = urlProductInput.value || "https://www.example.com/itm/{item_id}";
        var defaultImage = thumbInput.src || "https://phoenixsupplies.ca/image/cache/catalog/marketplace/default_grey-25x25.png";

        // Déterminer l'état de l'image (grey, green, red)
        var imageStatus = "grey"; // Par défaut en gris
        if (marketplace_item_id > 0 && marketplace_item_id !== "") {
            imageStatus = "green"; // Succès
        } else if (json.error) {
            imageStatus = "red"; // Erreur
        }
        console.log('imageStatus:', imageStatus);
        // Déterminer la marketplace dynamiquement depuis l'URL de l'image existante
        var imageParts = defaultImage.split("_red-"); // Séparer en tableau avec "_grey-"
        if (imageParts.length > 1) {
            var newImageUrl = imageParts.join(`_${imageStatus}-`);
        } else {
            var newImageUrl = defaultImage.replace("-red", `-${imageStatus}`);
        }

        // Mise à jour des champs cachés avec les valeurs dynamiques
        var newProductUrl = (marketplace_item_id > 0 && marketplace_item_id !== "") ? baseUrl.replace("{item_id}", marketplace_item_id) : baseUrl;

        // Modifier dynamiquement le contenu du <span> selon le succès ou l'erreur
        if (marketplace_item_id > 0 && marketplace_item_id !== "") {
            
            // Si succès, remplacer par un lien fonctionnel avec image verte
            spanElement.innerHTML = `
                <a href="${newProductUrl}" target="marketplace_item_id">
                    <img src="${newImageUrl}" alt="${marketplaceName}" style="width:50px; height:auto;">
                </a>
            `;
            console.log(`
                <a href="${newProductUrl}" target="marketplace_item_id">
                    <img src="${newImageUrl}" alt="${marketplaceName}" style="width:50px; height:auto;">
                </a>
            `);
        } else if (marketplace_item_id =='' || marketplace_item_id == 0) {
            // Si erreur, garder le bouton onclick avec l'image rouge
           
            spanElement.innerHTML = `
                <input type="hidden" name="url_product_${product_id}${marketplace_account_id}" value="${baseUrl}">
                <a href="javascript:void(0);" onclick="addToMarketplace('${product_id}', '${marketplace_account_id}', '9');">
                    <img src="${newImageUrl}" alt="${marketplaceName}" style="width:50px; height:auto; filter: grayscale(100%);">
                </a>
            `;
            console.log(`
                <input type="hidden" name="url_product_${product_id}${marketplace_account_id}" value="${baseUrl}">
                <a href="javascript:void(0);" onclick="addToMarketplace('${product_id}', '${marketplace_account_id}', '9');">
                    <img src="${newImageUrl}" alt="${marketplaceName}" style="width:50px; height:auto; filter: grayscale(100%);">
                </a>
            `);
        }
        $('#popup-error').modal('hide');
    } else {
        console.warn(`Élément(s) non trouvé(s) pour marketplace_account_id_${product_underscore}_${marketplace_account_id}`);
    }

    //console.log('marketplace_item_id:', marketplace_item_id);
}