// Original: warehouse/popup/marketplace_error.js
function getPopupElement() {
    return document.getElementById('popup-error');
}

function showPopupModal() {
    var modalElement = getPopupElement();
    if (!modalElement) {
        return;
    }

    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
        bootstrap.Modal.getOrCreateInstance(modalElement).show();
    } else if (typeof $ === 'function' && typeof $('#popup-error').modal === 'function') {
        $('#popup-error').modal('show');
    } else {
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
    }
}

function hidePopupModal() {
    var modalElement = getPopupElement();
    if (!modalElement) {
        return;
    }

    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
        var instance = bootstrap.Modal.getInstance(modalElement) || bootstrap.Modal.getOrCreateInstance(modalElement);
        instance.hide();
    } else if (typeof $ === 'function' && typeof $('#popup-error').modal === 'function') {
        $('#popup-error').modal('hide');
    } else {
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
    }
}

function disposePopupModal() {
    var modalElement = getPopupElement();
    if (!modalElement) {
        return;
    }

    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
        var instance = bootstrap.Modal.getInstance(modalElement);
        if (instance) {
            instance.dispose();
        }
    }
}

function showErrorPopup (product_id) {
    console.log('🚀 showErrorPopup STARTED with product_id:', product_id);
    
    var tokenInput = document.querySelector('input[name="user_token"]');
    console.log('🔍 tokenInput found:', tokenInput);
    
    var user_token = tokenInput ? tokenInput.value : '';
    console.log('✅ user_token from input:', user_token);

    if (!user_token) {
        console.log('⚠️ No token from input, trying URL params...');
        try {
            var urlParams = new URLSearchParams(window.location.search);
            user_token = urlParams.get('user_token') || '';
            console.log('✅ user_token from URL:', user_token);
        } catch (e) {
            console.error('❌ URLSearchParams error:', e);
            user_token = '';
        }
    }

    if (!user_token && typeof window.USER_TOKEN !== 'undefined') {
        console.log('⚠️ No token from input/URL, trying window.USER_TOKEN...');
        user_token = window.USER_TOKEN;
        console.log('✅ user_token from window.USER_TOKEN:', user_token);
    }

    if (!user_token) {
        console.error('❌ NO USER TOKEN FOUND!');
        alert('User token not found. Please reload the page.');
        return;
    }

    var ajaxUrl = `index.php?route=warehouse/popup/marketplace_error.index&user_token=${encodeURIComponent(user_token)}&product_id=${encodeURIComponent(product_id)}`;
    var fallbackUrl = `index.php?route=warehouse/popup/marketplace_error&user_token=${encodeURIComponent(user_token)}&product_id=${encodeURIComponent(product_id)}`;
    
    console.log('📡 AJAX URL:', ajaxUrl);
    console.log('📡 FALLBACK URL:', fallbackUrl);

    function renderPopup(html) {
        console.log('🎨 renderPopup called, html length:', html ? html.length : 0);
        disposePopupModal();
        $('#popup-error').remove();
        $('body').append(html);
        console.log('✅ HTML appended to body');
        showPopupModal();
        console.log('✅ showPopupModal called');
    }

    console.log('📤 Sending first AJAX request to:', ajaxUrl);
    $.ajax({
        url: ajaxUrl,
        dataType: 'html',
        success: function(html) {
            console.log('✅ AJAX SUCCESS, html length:', html ? html.length : 0);
            renderPopup(html);
        },
        error: function(xhr, status, error) {
            console.error('❌ AJAX PRIMARY ERROR:', error, 'Status:', status);
            console.log('🔄 Trying fallback URL:', fallbackUrl);
            $.ajax({
                url: fallbackUrl,
                dataType: 'html',
                success: function(html) {
                    console.log('✅ AJAX FALLBACK SUCCESS, html length:', html ? html.length : 0);
                    renderPopup(html);
                },
                error: function(xhr2, status2, error2) {
                    console.error('❌ AJAX PRIMARY ERROR:', error);
                    console.error('❌ XHR:', xhr);
                    console.error('❌ Status:', status);
                    console.error('❌ Response Text:', xhr.responseText);
                    console.error('❌ AJAX FALLBACK ERROR:', error2);
                    console.error('❌ XHR FALLBACK:', xhr2);
                    console.error('❌ Status FALLBACK:', status2);
                    console.error('❌ Response FALLBACK:', xhr2.responseText);
                    alert('Failed to load error popup.');
                }
            });
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
    var user_token = document.querySelector('input[name="user_token"]').value;

    $.ajax({
        url: `index.php?route=warehouse/popup/marketplace_error&user_token=${user_token}&marketplace_item_id=${marketplace_item_id}`,
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
                showPopupModal();
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
            showPopupModal();
        }
    });
}


$(document).on('click', '#popup-error .btn-close', function() {
    hidePopupModal();
});

$(document).on('click', '#popup-error .btn-retry', function() {
    var marketplace_item_id = $('#popup-error').data('marketplace_item_id');
    var marketplace_account_id = $('#popup-error').data('marketplace_account_id');
    var product_id = $('#popup-error').data('product_id');
    var is_products = $('#popup-error').data('is_products');
    handleMarketplaceError(marketplace_account_id, marketplace_item_id, product_id, is_products);
});

$(document).on('click', '#popup-error .btn-fix-issue', function() {
    var product_id = $('#popup-error').data('product-id') || $('#popup-error').data('product_id');
    var user_token = document.querySelector('input[name="user_token"]') 
        ? document.querySelector('input[name="user_token"]').value 
        : (window.USER_TOKEN || '');
    hidePopupModal();
    if (product_id && user_token) {
        window.location.href = 'index.php?route=warehouse/product/product.form&user_token=' + user_token + '&product_id=' + product_id;
    }
});

function handleMarketplaceError(marketplace_account_id, marketplace_item_id , product_id, is_products = false) {
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
        } else if (marketplace_item_id =='' || marketplace_item_id == 0) {
            // Si erreur, garder le bouton onclick avec l'image rouge
           
            spanElement.innerHTML = `
                <input type="hidden" name="url_product_${product_id}${marketplace_account_id}" value="${baseUrl}">
                <a href="javascript:void(0);" onclick="addToMarketplace('${product_id}', '${marketplace_account_id}', '9');">
                    <img src="${newImageUrl}" alt="${marketplaceName}" style="width:50px; height:auto; filter: grayscale(100%);">
                </a>
            `;
        }
        hidePopupModal();
    } else {
        console.warn(`Élément(s) non trouvé(s) pour marketplace_account_id_${product_underscore}_${marketplace_account_id}`);
    }
}