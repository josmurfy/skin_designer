

// ============================================
// UPDATE LIST/FEED/END BUTTONS BASED ON SELECTION STATE
// ============================================
function updateListFeedButton() {
    var feedBtn    = document.getElementById('button-feed-selected');
    var listBtn    = document.getElementById('button-list-selected');
    var endBtn     = document.getElementById('button-end-selected');
    var updateBtn  = document.getElementById('button-update-selected');
    var syncQtyBtn = document.getElementById('button-sync-qty-all');

    // Masquer tous les boutons dynamiques
    if (feedBtn)    feedBtn.classList.add('d-none');
    if (listBtn)    listBtn.classList.add('d-none');
    if (endBtn)     endBtn.classList.add('d-none');
    if (updateBtn)  updateBtn.classList.add('d-none');
    if (syncQtyBtn) syncQtyBtn.classList.add('d-none');

    // Récupérer les IDs des produits sélectionnés
    var selectedIds = [];
    document.querySelectorAll("input[name^='selected']:checked").forEach(function(cb) {
        if (cb.value) selectedIds.push(cb.value);
    });

    if (selectedIds.length === 0) return;

    var allEnabled  = true;   // tous status=1 (aucun opacity-50)
    var allListed   = true;   // tous ont icône verte
    var allFed      = true;   // tous ont btn-sources-fed
    var allNotListed = true;  // aucun n'est listé
    var allHasStock = true;   // tous ont qty+unalloc > 0

    selectedIds.forEach(function(pid) {
        var row = document.querySelector('tr[data-product-id="' + pid + '"]');

        // Status : opacity-50 = disabled
        if (!row || row.classList.contains('opacity-50')) {
            allEnabled = false;
        }

        // Listed : span marketplace-account-id-{pid}-* avec img _green-
        var listed = false;
        document.querySelectorAll('[id^="marketplace-account-id-' + pid + '-"]').forEach(function(span) {
            var img = span.querySelector('img');
            if (img && img.getAttribute('src') && img.getAttribute('src').indexOf('_green-') !== -1) {
                listed = true;
            }
        });
        if (!listed) allListed = false;
        else         allNotListed = false;

        // Fed : bouton avec classe btn-sources-fed
        if (!row || !row.querySelector('.btn-sources-fed')) {
            allFed = false;
        }

        // Stock : qty + unallocated > 0
        var qtyEl     = document.getElementById('quantity-' + pid);
        var unallocEl = document.getElementById('unallocated-quantity-' + pid);
        var qty       = qtyEl     ? parseInt(qtyEl.getAttribute('rel1')     || 0) : 0;
        var unalloc   = unallocEl ? parseInt(unallocEl.getAttribute('rel1') || 0) : 0;
        if (qty + unalloc <= 0) allHasStock = false;
    });

    // Règle 1 : au moins 1 disabled → rien
    if (!allEnabled) return;

    // Règle 2 : tous enabled → Feed toujours visible
    if (feedBtn) feedBtn.classList.remove('d-none');

    // Règle 3 : tous feedés + tous non listés + tous ont stock → List
    if (allFed && allNotListed && allHasStock) {
        if (listBtn) listBtn.classList.remove('d-none');
    }

    // Règle 4 : tous listés → End + Update + SyncQty
    if (allListed) {
        if (endBtn)     endBtn.classList.remove('d-none');
        if (updateBtn)  updateBtn.classList.remove('d-none');
        if (syncQtyBtn) syncQtyBtn.classList.remove('d-none');
    }
}

// ============================================
// HANDLE END LIST (End Selected Listings)
// ============================================
function handleEndList() {
    var tokenElement = document.querySelector("input[name='user_token']");
    var user_token   = tokenElement ? tokenElement.value : '';
    if (!user_token) {
        alert('Token not found');
        return;
    }

    var selectedIds = [];
    document.querySelectorAll("input[name^='selected']:checked").forEach(function(cb) {
        if (cb.value) selectedIds.push(cb.value);
    });

    if (selectedIds.length === 0) {
        alert(window.TEXT_UPDATE_MARKETPLACE_NO_SELECTION || 'No product selected');
        return;
    }

    showLoadingPopup(window.TEXT_END_SELECTED || 'Ending selected listings...');
    var currentIndex = 0;

    function processNext() {
        if (currentIndex >= selectedIds.length) {
            finishLoadingPopup();
            updateListFeedButton();
            return;
        }

        var productId = selectedIds[currentIndex];

        // Récupérer les comptes marketplace actifs (icône verte) pour ce produit
        var targets = [];
        document.querySelectorAll('[id^="marketplace-account-id-' + productId + '-"]').forEach(function(span) {
            var img = span.querySelector('img');
            if (!img) return;
            var src = img.getAttribute('src') || '';
            if (src.indexOf('_green-') === -1) return; // seulement les listés verts

            // Extraire l'account_id depuis l'id du span : marketplace-account-id-{pid}-{account_id}
            var idParts = span.id.split('-');
            var marketplace_account_id = idParts[idParts.length - 1];

            // Récupérer marketplace_item_id depuis le href du lien (ex: .../itm/296605947039)
            var link = span.querySelector('a');
            var marketplace_item_id = '';
            if (link) {
                var href = link.getAttribute('href') || '';
                var match = href.match(/\/(\d{10,})/);
                if (match) marketplace_item_id = match[1];
            }

            if (marketplace_item_id) {
                targets.push({ marketplace_account_id: marketplace_account_id, marketplace_item_id: marketplace_item_id });
            }
        });

        if (targets.length === 0) {
            appendLoadingMessage('⚠️ Produit #' + productId + ' : aucun listing actif trouvé', 'warning');
            currentIndex++;
            processNext();
            return;
        }

        // Traitement séquentiel de chaque compte marketplace du produit
        var subIndex = 0;
        function processSubTarget() {
            if (subIndex >= targets.length) {
                currentIndex++;
                processNext();
                return;
            }
            var t = targets[subIndex];
            appendLoadingMessage('🛑 End listing #' + t.marketplace_item_id + ' (produit #' + productId + ')', 'info');

            $.ajax({
                url: 'index.php?route=shopmanager/ebay.endListing&user_token=' + user_token,
                type: 'POST',
                data: {
                    product_id: productId,
                    marketplace_item_id: t.marketplace_item_id,
                    marketplace_account_id: t.marketplace_account_id
                },
                dataType: 'json',
                success: function(json) {
                    if (json.success) {
                        setMarketplaceListingState(productId, t.marketplace_account_id, 'grey', '');
                        appendLoadingMessage('✅ Listing #' + t.marketplace_item_id + ' terminé', 'success');
                    } else {
                        appendLoadingMessage('❌ Erreur produit #' + productId + ' : ' + (json.message || 'Erreur inconnue'), 'error');
                    }
                    subIndex++;
                    processSubTarget();
                },
                error: function(xhr, opts, err) {
                    appendLoadingMessage('❌ Erreur AJAX produit #' + productId + ' : ' + (err || 'Erreur réseau'), 'error');
                    subIndex++;
                    processSubTarget();
                }
            });
        }
        processSubTarget();
    }

    processNext();
}

// ============================================
// LOADING POPUP UTILITY
// ============================================
function showLoadingPopup(title = "Chargement en cours...") {
    var loadingTitle = document.getElementById("loading-title");
    var loadingMessages = document.getElementById("loading-messages");
    var loadingPopup = document.getElementById("loading-popup");
    var closeLoadingBtn = document.getElementById("close-loading-btn");
    
    if (!loadingTitle || !loadingMessages || !loadingPopup) {
        console.warn('Loading popup elements not found in DOM');
        return;
    }
    
    loadingTitle.textContent = title;
    loadingMessages.innerHTML = '';
    loadingPopup.style.display = 'block';
    if (closeLoadingBtn) {
        closeLoadingBtn.style.display = 'none';
    }
}

function appendLoadingMessage(message, type = 'info') {
    const container = document.getElementById("loading-messages");
    
    if (!container) {
        console.warn('Loading messages container not found in DOM');
        return;
    }
    
    const color = {
        info: '#007bff',
        success: '#28a745',
        warning: '#ffc107',
        error: '#dc3545'
    }[type] || '#000';

    const icon = {
        info: 'ℹ️',
        success: '✅',
        warning: '⚠️',
        error: '❌'
    }[type] || '';

    const line = document.createElement('div');
    line.innerHTML = `<span style="color:${color}">${icon} ${message}</span>`;
    container.appendChild(line);
    container.scrollTop = container.scrollHeight;
}

function finishLoadingPopup(message = '✅ Terminé !') {
    appendLoadingMessage(message, 'info');
    const closeBtn = document.getElementById('close-loading-btn');
    if (closeBtn) {
        closeBtn.style.display = 'inline-block';
    }
}

function hideLoadingPopup() {
    const popup = document.getElementById("loading-popup");
    if (popup) {
        popup.style.display = 'none';
    }
}

function debugAjaxSuccess(action, response) {
}

function debugAjaxError(action, error) {
}



/**
 * Transfer Unallocated Quantity to Quantity
 * Fonction pour transférer la quantité non allouée vers la quantité principale
 */
function transferUnallocatedQuantity(productId) {
   
    var user_token = document.querySelector('input[name="user_token"]').value;

    // Récupérer les valeurs actuelles avec les bons sélecteurs (tirets)
    var quantity = parseInt($('#quantity-' + productId).text()) || 0;
    var unallocatedQuantity = parseInt($('#unallocated-quantity-' + productId).attr('rel1')) || 0;
    var location = $('#location-' + productId).text().trim();

    // Si quantity est 0, il faut valider la location
    if (quantity === 0) {
        // Créer un champ texte pour modifier la localisation
        var html = '<input type="text" id="location_input' + productId + '" class="form-control location_input" value="' + location + '" placeholder="' + TEXT_LOCATION_PLACEHOLDER + '" />';
        $('#location-' + productId).html(html);

        // Donner le focus au champ texte et sélectionner tout le texte
        $('#location_input' + productId).focus().select();

        // Gérer l'événement 'keydown' sur le champ texte
        $('#location_input' + productId).on('keydown', function(event) {
            if (event.key === 'Enter' || event.keyCode === 13 || event.which === 13) {
                event.preventDefault();
                event.stopPropagation();

                var newLocation = $('#location_input' + productId).val().trim();

                if (newLocation === '') {
                    alert(TEXT_LOCATION_EMPTY);
                } else {
                    // Envoyer les nouvelles données via AJAX
                    $.ajax({
                        url: 'index.php?route=shopmanager/catalog/product.trfUnallocatedQuantity&user_token=' + user_token,
                        type: 'post',
                        data: {
                            product_id: productId,
                            unallocated_quantity: unallocatedQuantity,
                            quantity: quantity,
                            location: newLocation
                        },
                        success: function(response) {
                            debugAjaxSuccess('trfUnallocatedQuantity', response);
                            if (response.success) {
                                // Mettre à jour unallocated_quantity à 0
                                $('#unallocated-quantity-' + productId).attr('rel1', '0').text('0')
                                    .removeClass('badge bg-danger badge bg-warning')
                                    .addClass('badge bg-warning');
                                
                                // Calculer nouvelle quantité
                                var newQuantity = unallocatedQuantity + quantity;
                                var quantityClass = '';
                                if (newQuantity <= 0) {
                                    quantityClass = 'badge bg-warning';
                                } else if (newQuantity <= 5) {
                                    quantityClass = 'badge bg-danger';
                                } else {
                                    quantityClass = 'badge bg-success';
                                }
                                
                                // Mettre à jour quantity
                                $('#quantity-' + productId).attr('rel1', newQuantity).text(newQuantity)
                                    .removeClass('badge bg-warning badge bg-danger badge bg-success')
                                    .addClass(quantityClass);
                                
                                // Mettre à jour location
                                $('#location-' + productId).html('<span class="pedit-location" rel="' + productId + '" rel1="' + newLocation + '">' + newLocation + '</span>');
                            } else {
                                alert(TEXT_ERROR_UPDATE + ': ' + response);
                            }
                        },
                        error: function() {
                            alert(TEXT_ERROR_API_CALL);
                        }
                    });
                }
            }
        });
    } else {
        // Si quantity est > 0 et location est valide, faire le transfert normalement
        if (location !== '') {
            if (confirm(TEXT_CONFIRM_TRANSFER)) {
                $.ajax({
                    url: 'index.php?route=shopmanager/catalog/product.trfUnallocatedQuantity&user_token=' + user_token,
                    type: 'post',
                    data: {
                        product_id: productId,
                        unallocated_quantity: unallocatedQuantity,
                        quantity: quantity,
                        location: location
                    },
                    success: function(response) {
                        if (response.success) {
                            // Mettre à jour unallocated_quantity à 0
                            $('#unallocated-quantity-' + productId).attr('rel1', '0').text('0')
                                .removeClass('badge bg-danger badge bg-warning')
                                .addClass('badge bg-warning');

                            // Calculer nouvelle quantité
                            var newQuantity = unallocatedQuantity + quantity;
                            var quantityClass = '';
                            if (newQuantity <= 0) {
                                quantityClass = 'badge bg-warning';
                            } else if (newQuantity <= 5) {
                                quantityClass = 'badge bg-danger';
                            } else {
                                quantityClass = 'badge bg-success';
                            }
                            
                            // Mettre à jour quantity
                            $('#quantity-' + productId).attr('rel1', newQuantity).text(newQuantity)
                                .removeClass('badge bg-warning badge bg-danger badge bg-success')
                                .addClass(quantityClass);
                        } else {
                            alert(TEXT_ERROR_UPDATE);
                        }
                    },
                    error: function() {
                        alert(TEXT_ERROR_API_CALL);
                    }
                });
            }
        } else {
            alert(TEXT_LOCATION_EMPTY);
        }
    }
}

/**
 * Centralized Image Resolution Check
 */
function checkImageResolution(imageElement, forceRecheck = false) {
    const container = imageElement.closest('.actual-image-container');
    if (!container) return;

    const fullsizeImg = container.querySelector('.fullsize-actual-image, .actual-image-preview');
    const imgToCheck = fullsizeImg && fullsizeImg.src && fullsizeImg.src !== '' ? fullsizeImg : imageElement;

    if (imgToCheck.hasAttribute('data-image-path')) {
        const imagePath = imgToCheck.getAttribute('data-image-path');
        if (imagePath === '' || imagePath === 'undefined') {
            console.warn('Empty data-image-path, skipping resolution check for:', imgToCheck);
            return;
        }
    }

    const srcAttr = imgToCheck.getAttribute('src');
    if (!srcAttr || srcAttr === '' || srcAttr === window.location.href || srcAttr.endsWith('image/') || srcAttr.includes('undefined')) {
        console.warn('Invalid image src, skipping resolution check:', srcAttr);
        return;
    }

    if (!forceRecheck && imgToCheck.dataset.resolutionChecked === 'true') return;

    if (!imgToCheck.complete) {
        imgToCheck.addEventListener('load', function() {
            setImageResolutionData(imgToCheck, container);
        }, { once: true });
    } else {
        setImageResolutionData(imgToCheck, container);
    }
}

function setImageResolutionData(img, container) {
    const width = img.naturalWidth;
    const height = img.naturalHeight;
    
    if (width === 0 || height === 0) {
        console.warn('Image has 0x0 resolution, skipping:', img.src);
        return;
    }
    
    const resolutionText = `${width}x${height}`;
    img.dataset.resolutionChecked = 'true';
    img.setAttribute('data-resolution', resolutionText);
    
    if (width >= 400 && height >= 600) {
        container.style.border = '3px solid #28a745';
    } else {
        container.style.border = '3px solid #dc3545';
    }
    
    const overlay = container.querySelector('.fullsize-resolution-overlay');
    if (overlay) {
        overlay.textContent = resolutionText;
        if (width < 400 || height < 600) {
            overlay.classList.remove('good-res');
            overlay.classList.add('low-res');
        } else {
            overlay.classList.remove('low-res');
            overlay.classList.add('good-res');
        }
    }
}

function initImageResolutionCheck() {
    document.querySelectorAll('.actual-image-container').forEach(function(container) {
        const thumbnail = container.querySelector('.img-thumbnail, .thumbnail-actual-image');
        if (thumbnail) {
            checkImageResolution(thumbnail);
        }
    });
}

function initImagePreview() {
    document.querySelectorAll('.actual-image-container').forEach(function(container) {
        if (container.dataset.previewInitialized === 'true') return;
        container.dataset.previewInitialized = 'true';

        const thumbnail = container.querySelector('.img-thumbnail, .thumbnail-actual-image');
        const wrapper = container.querySelector('.fullsize-actual-image-wrapper');
        const fullsizeImg = container.querySelector('.fullsize-actual-image, .actual-image-preview');
        const resolutionOverlay = container.querySelector('.fullsize-resolution-overlay');

        if (!thumbnail || !wrapper || !fullsizeImg) return;

        if (resolutionOverlay) {
            const resolution = fullsizeImg.getAttribute('data-resolution');
            if (resolution) {
                const parts = resolution.split('x');
                const width = parseInt(parts[0]);
                const height = parseInt(parts[1]);
                
                if (width < 400 || height < 600) {
                    resolutionOverlay.classList.add('low-res');
                } else {
                    resolutionOverlay.classList.add('good-res');
                }
                resolutionOverlay.textContent = resolution;
            }
        }

        thumbnail.addEventListener('mouseenter', function() {
            // Cloner le wrapper et l'ajouter au body pour éviter l'héritage d'opacity
            const clone = wrapper.cloneNode(true);
            clone.id = 'temp-fullsize-preview';
            clone.style.display = 'block';
            document.body.appendChild(clone);
        });

        thumbnail.addEventListener('mouseleave', function() {
            // Supprimer le clone du body
            const clone = document.getElementById('temp-fullsize-preview');
            if (clone) {
                clone.remove();
            }
        });

        wrapper.addEventListener('mouseenter', function() {
            wrapper.style.display = 'block';
        });

        wrapper.addEventListener('mouseleave', function() {
            wrapper.style.display = 'none';
        });
    });
}

function initImageDragAndDrop(uploadUrl, onSuccess, onError) {
    document.querySelectorAll('.actual-image-container').forEach(function (container) {
        if (container.dataset.dragDropInitialized === 'true') return;
        container.dataset.dragDropInitialized = 'true';

        const fileInput = container.querySelector('input[type="file"]');
        const previewImage = container.querySelector('img.img-thumbnail, .thumbnail-actual-image');
        const fullImage = container.querySelector('.fullsize-actual-image, .actual-image-preview');
        const productId = container.id.replace('drop-', '');
        
        const tokenInput = document.querySelector('input[name="user_token"]');
        if (!tokenInput) {
            console.error('[initImageDragAndDrop] user_token not found');
            return;
        }
        const user_token = tokenInput.value;

        container.addEventListener('dragover', function (event) {
            event.preventDefault();
            event.stopPropagation();
            container.style.borderColor = '#007bff';
        });

        container.addEventListener('dragleave', function () {
            container.style.borderColor = '#ccc';
        });

        container.addEventListener('drop', function (event) {
            event.preventDefault();
            event.stopPropagation();
            container.style.borderColor = '#ccc';

            if (event.dataTransfer.files.length > 0) {
                const file = event.dataTransfer.files[0];

                if (fileInput) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                }

                // Afficher aperçu temporaire pendant l'upload (sera remplacé par URL du serveur)
                const reader = new FileReader();
                reader.onload = function (e) {
                    if (previewImage) {
                        previewImage.src = e.target.result;
                        previewImage.dataset.tempPreview = 'true'; // Marquer comme temporaire
                    }
                    if (fullImage) fullImage.src = e.target.result;
                };
                reader.readAsDataURL(file);

                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('sourcecode', '');
                formData.append('imageprincipal', file);

                $.ajax({
                    url: uploadUrl + '&user_token=' + user_token + '&_=' + Date.now(),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success && onSuccess) {
                            onSuccess(response, container, productId);
                        } else if (response.error) {
                            alert((window.lang && lang.error_occurred ? lang.error_occurred : TEXT_ERROR_API_CALL) + ' : ' + response.error);
                        }
                    },
                    error: function (xhr) {
                        console.error('[AJAX Error]', xhr.responseText);
                        if (onError) {
                            onError(xhr, container, productId);
                        } else {
                            alert((window.lang && lang.error_upload ? lang.error_upload : TEXT_ERROR_API_CALL) + ' : ' + xhr.responseText);
                        }
                    }
                });
            }
        });

        if (fileInput) {
            container.addEventListener('click', function(e) {
                if (e.target === container || e.target.tagName === 'I' || e.target.classList.contains('fa-camera')) {
                    fileInput.click();
                }
            });

            fileInput.addEventListener('change', function () {
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    container.dispatchEvent(new DragEvent('drop', {
                        dataTransfer: new DataTransfer()
                    }));
                }
            });
        }
    });

    window.addEventListener('dragover', function(e) {
        e.preventDefault();
    }, false);
    
    window.addEventListener('drop', function(e) {
        if (!e.target.closest('.actual-image-container')) {
            e.preventDefault();
        }
    }, false);
}

// ============================================
// GLOBAL REINIT FUNCTION FOR AJAX RELOADS
// ============================================
/**
 * Réinitialise tous les event listeners après rechargement AJAX
 * Appelée par product.twig après $('#product').load()
 */
window.reinitImagePreview = function() {
    
    // Reset les flags d'initialisation pour permettre la réinit
    document.querySelectorAll('.actual-image-container').forEach(function(container) {
        container.dataset.previewInitialized = 'false';
        container.dataset.dragDropInitialized = 'false';
    });
    
    // Réinitialiser preview (mouseenter/mouseleave)
    initImagePreview();
    
    // Réinitialiser drag-and-drop
    const uploadUrl = 'index.php?route=shopmanager/catalog/product.editImage';
    initImageDragAndDrop(uploadUrl, function(response, container, productId) {
        // Success callback
        if (response.success && response.thumb && response.popup) {
            const thumbnail = container.querySelector('.img-thumbnail, .thumbnail-actual-image');
            const fullImage = container.querySelector('.fullsize-actual-image, .actual-image-preview');
            
            if (thumbnail) thumbnail.src = response.thumb + '?t=' + Date.now();
            if (fullImage) fullImage.src = response.popup + '?t=' + Date.now();
            
            // Recheck resolution après upload
            if (thumbnail) {
                setTimeout(() => checkImageResolution(thumbnail), 100);
            }
        }
    }, function(xhr, container, productId) {
        // Error callback
        console.error('Upload error for product', productId, xhr.responseText);
    });
    
};

/**
 * Expose initImageResolutionCheck globalement pour product.twig
 */
window.initImageResolutionCheck = initImageResolutionCheck;

// ============================================
// END DUPLICATED FUNCTIONS FROM TOOLS.JS
// ============================================

// Define formatImage at the top of the file
function formatImage(imageUrl, altText) {
    if (!imageUrl) {
        // Return a placeholder image if no image URL is provided
        return '<img src="https://via.placeholder.com/50" alt="No Image" style="width:50px; height:auto;">';
    }
    return `<img src="${imageUrl}" alt="${altText}" style="width:50px; height:auto;">`;
}
    // Reveal fullsize image above the row to avoid inherited opacity
    var activePreview = null;

    var destroyPreview = function() {
        if (activePreview && activePreview.node && activePreview.node.parentNode) {
            activePreview.node.parentNode.removeChild(activePreview.node);
        }
        activePreview = null;
    };

function formatPrice(price, special) {
    if (special && special < price) {
        return `<span style="text-decoration: line-through; color: red;">${price.toFixed(2)}</span> <span style="color: green;">${special.toFixed(2)}</span>`;
    }
    return `<span>${price.toFixed(2)}</span>`;
}
function formatSpecifics(data) {
    let html = '';

    if (data && typeof data === 'object') {
        const filled = parseInt(data.filled_specifics_count || 0, 10);
        const total = parseInt(data.total_specifics_count || 0, 10);
        const percentage = total > 0 ? Math.round((filled / total) * 100) : 0;

        const red = Math.max(0, Math.min(255, 255 - (percentage * 2.55)));
        const green = Math.max(0, Math.min(255, percentage * 2.55));
        const color = `rgb(${red}, ${green}, 0)`;

       
        html += '<br> ' + data.has_specifics + ' <br>';
        

        // Progress bar
        html += `
            <div style="position: relative; width: 100px; height: 15px; background-color: #ddd; border-radius: 5px; overflow: hidden;">
                <div style="width: ${percentage}%; height: 100%; background-color: ${color};"></div>
            </div>
            (${filled}/${total})
        `;
    } else {
        html = '<span>Not Set</span><br>' +
               `<div style="position: relative; width: 100px; height: 15px; background-color: #ddd; border-radius: 5px; overflow: hidden;">
                    <div style="width: 0%; height: 100%; background-color: rgb(255, 0, 0);"></div>
                </div>
                (0/0)`;
    }

    return html;
}



function formatMadeinCountryid(madeInCountryId, options, productId) {

    if (!madeInCountryId || madeInCountryId === '0') {
        return options;
    }

    const container = document.createElement('div');
    container.innerHTML = options;

    const select = container.querySelector('select');

    if (!select) {
        console.error('[ERROR] No <select> element found in options HTML.');
        return options;
    }

    const optionList = select.querySelectorAll('option');

    let found = false;
    let oldSelectedValue = null;

    optionList.forEach(option => {
        if (option.selected) {
            oldSelectedValue = option.value;
        }
    });

    optionList.forEach(option => {
        if (option.value === String(madeInCountryId)) {
            option.setAttribute('selected', 'selected');
            found = true;
        } else {
            option.removeAttribute('selected');
        }
    });

    // Changer la couleur du <td> associé
    if (productId) {
        const td = document.getElementById('check-made-in-country-id-' + productId);
        if (td) {
            if (!oldSelectedValue) {
                td.style.backgroundColor = ''; // Pas de changement
            } else if (oldSelectedValue === String(madeInCountryId)) {
                td.style.backgroundColor = '#d4edda'; // vert clair
            } else {
                td.style.backgroundColor = '#fff3cd'; // jaune/orange clair
            }
        }
    }

    if (!found) {
    }

    return container.innerHTML;
}



function formatSpecificsOLD(data) {
    if (!data || typeof data !== 'object') {
        return '<span>No specifics available</span>';
    }

    let specifics = [];
    for (let key in data) {
        if (data.hasOwnProperty(key)) {
            specifics.push(`<strong>${key}:</strong> ${data[key]}`);
        }
    }
    return specifics.length ? specifics.join('<br>') : '<span>No specifics available</span>';
}

function formatMadeinCountryidOLD(madeInCountryId, madeInCountryName, options) {
    if (!options || !Array.isArray(options)) {
        return '<span style="color: red;">Invalid options</span>';
    }

    // Build the <select> element with the correct option selected
    let selectHtml = `<select name="made_in_country_id" class="form-control">`;

    options.forEach(option => {
        const isSelected = option.id === madeInCountryId ? 'selected' : '';
        selectHtml += `<option value="${option.id}" ${isSelected}>${option.name}</option>`;
    });

    selectHtml += `</select>`;
    return selectHtml;
}

// Remplacer unallocated_quantity par un champ texte lors du clic
$(document).on('click', '.pedit-unallocated-quantity', function (event) {
    event.preventDefault();

    var rel = $(this).attr('rel'); // Product ID
    var rel1 = $(this).attr('rel1'); // Current unallocated quantity
    var quantity = parseInt($('tr[data-product-id="' + rel + '"] .quantity span').text()) || 0;
    // Retirer la classe 'label label-danger' de l'élément cliqué
    $(this).removeClass('label label-danger');

    // Créer un champ texte avec la valeur existante de unallocated_quantity
    var html = '<input type="text" id="unallocated-quantity-input-' + rel + '" class="form-control unallocated_quantity_input" value="' + rel1 + '" />';
    $('#unallocated-quantity-' + rel).html(html);

    var inputElement = $('#unallocated-quantity-input-' + rel);
    inputElement.focus().select();

    inputElement.on('change', function () {
        var newUnallocatedQuantity = $(this).val();
        confirmUnallocatedQuantity(rel, newUnallocatedQuantity, quantity);
    });

    // Mettre à jour l'attribut 'rel1' en temps réel lors de la modification
    inputElement.on('input', function () {
        $(this).closest('.pedit-unallocated-quantity').attr('rel1', $(this).val());
    });
});



// Remplacer location par un champ texte lors du clic
$(document).on('click', '.pedit-location', function (event) {
    event.preventDefault(); // Empêcher le comportement par défaut lors du clic

    var rel = $(this).attr('rel'); // Product ID
    var rel1 = $(this).attr('rel1'); // Current location value

    // Créer un champ texte avec la valeur existante de location
    var html = '<input type="text" id="location-input-' + rel + '" class="form-control location_input" value="' + rel1 + '" />';
    $('#location-' + rel).html(html);

    // Autofocus et sélectionner tout le texte
    $('#location-input-' + rel).focus().select();

    // Détecter l'appui sur la touche Enter dans le champ texte
    $('#location-input-' + rel).on('keydown', function (event) {
        if (event.key === 'Enter' || event.keyCode === 13 || event.which === 13) {
            event.preventDefault(); // Empêche l'action par défaut (comme le rechargement de la page)
            event.stopPropagation(); // Empêche la propagation de l'événement

            var newLocation = $(this).val().trim(); // Récupérer la valeur entrée dans le champ

            // Appeler la fonction de confirmation pour la location avec la nouvelle valeur
            confirmLocation(rel, newLocation);
        }
    });
    // Mettre à jour l'attribut 'rel1' en temps réel lors de la modification
    $('#location-input-' + rel).on('input', function () {
        $(this).closest('.pedit-location').attr('rel1', $(this).val());
    });
});


// Remplacer quantity par un champ texte lors du clic
$(document).on('click', '.pedit-quantity', function (event) {
    event.preventDefault(); // Empêcher le comportement par défaut lors du clic

    var rel = $(this).attr('rel'); // Product ID
    var rel1 = $(this).attr('rel1'); // Current quantity
    var unallocatedQuantity = parseInt($('tr[data-product-id="' + rel + '"] .unallocated_quantity span').text()) || 0;
    
    
    // Calculer la nouvelle quantité (quantity + unallocated_quantity)
    var quantity = parseInt(rel1);

    // Créer un champ texte avec la nouvelle quantité
    var html = '<input type="text" id="quantity-input-' + rel + '" class="form-control quantity_input" value="' + quantity + '" />';
    $('#quantity-' + rel).html(html);

    $('#quantity-input-' + rel).focus().select();

    $('#quantity-input-' + rel).on('change', function () {
        var finalQuantity = $(this).val();
        confirmQuantity(rel, finalQuantity, unallocatedQuantity);
    });
    $('#quantity-input-' + rel).on('input', function () {
        $(this).closest('.pedit-quantity').attr('rel1', $(this).val());
    });
});



function confirmUnallocatedQuantity(productId, newUnallocatedQuantity, currentQuantity) {
    var user_token = document.querySelector('input[name="user_token"]').value;

    if (newUnallocatedQuantity === '' || isNaN(newUnallocatedQuantity)) {
        alert(lang.alert_valid_unallocated);
        return;
    }

    // Appel AJAX pour sauvegarder les données sur le serveur
    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product.updateUnallocatedQuantity&user_token=' + user_token,
        type: 'post',
        data: {
            product_id: productId,
            unallocated_quantity: newUnallocatedQuantity,
            quantity: currentQuantity
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                var unallocatedClass = '';
                if (newUnallocatedQuantity <= 0) {
                    unallocatedClass = 'badge bg-warning';
                } else if (newUnallocatedQuantity <= 5) {
                    unallocatedClass = 'badge bg-danger';
                } else {
                    unallocatedClass = 'badge bg-success';
                }
                // Restore the span: replace input with text, update classes and attributes
                var $span = $('#unallocated-quantity-' + productId);
                $span.html(newUnallocatedQuantity);
                $span.attr('class', 'pedit-unallocated-quantity ' + unallocatedClass);
                $span.attr('rel1', newUnallocatedQuantity);
                
                // Show success notification
                $('#alert').prepend('<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check"></i> ' + response.success + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                setTimeout(function() { $('#alert .alert').fadeOut(400, function() { $(this).remove(); }); }, 3000);
            } else {
                alert(lang.error_update_quantity);
            }
        },
        error: function () {
            alert(lang.error_api_call);
        }
    });
}


function confirmLocation(productId, newLocation) {
    var user_token = getUserToken();
    if (!user_token) {
        alert(lang.error_token_not_found);
        return;
    }

    var quantity = parseInt($('tr[data-product-id="' + productId + '"] .quantity span').text()) || 0;
    var unallocatedQuantity = parseInt($('tr[data-product-id="' + productId + '"] .unallocated_quantity span').text()) || 0;

    if (newLocation === '') {
        if (quantity !== 0 || unallocatedQuantity !== 0) {
            alert(lang.alert_location_not_empty_if_qty);
            return;
        }
    }

    if (newLocation !== '') {
        newLocation = newLocation.toUpperCase(); 
    }


    // Appel AJAX pour sauvegarder les données sur le serveur
    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product.updateLocation&user_token=' + user_token,
        type: 'post',
        data: {
            product_id: productId,
            location: newLocation
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                var locationSpan = '<span id="location-' + productId + '" class="pedit-location" rel="' + productId + '" rel1="' + newLocation + '">' + newLocation + '</span>';
                $('#location-' + productId).replaceWith(locationSpan);
            } else {
                alert(lang.error_update_location + ': ' + (response.message || response.error));
            }
        },
        error: function (xhr) {
            console.error('Erreur AJAX:', xhr.responseText);
            alert(lang.error_api_call + ': ' + xhr.statusText);
        }
    });
}

function confirmQuantity(productId, finalQuantity, unallocatedQuantity) {
    
    var user_token = document.querySelector('input[name="user_token"]').value;

    if (finalQuantity === '' || isNaN(finalQuantity)) {
        alert(lang.alert_valid_quantity);
        return;
    }

    var ajaxData = {
        product_id: productId,
        quantity: finalQuantity,
        unallocated_quantity: unallocatedQuantity
    };
    
    // Appel AJAX pour sauvegarder les données sur le serveur
    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product.updateQuantity&user_token=' + user_token,
        type: 'post',
        data: {
            product_id: productId,
            quantity: finalQuantity,
            unallocated_quantity: unallocatedQuantity
        },
        dataType: 'json',
        success: function (response) {
            debugAjaxSuccess('updateQuantity', response);
            
            if (response.success) {
                var quantityClass = '';
                if (finalQuantity <= 0) {
                    quantityClass = 'badge bg-warning';
                } else if (finalQuantity <= 5) {
                    quantityClass = 'badge bg-danger';
                } else {
                    quantityClass = 'badge bg-success';
                }
                // Restore the span: replace input with text, update classes and attributes
                var $span = $('#quantity-' + productId);
                $span.html(finalQuantity);
                $span.attr('class', 'pedit-quantity ' + quantityClass);
                $span.attr('rel1', finalQuantity);
                
                // Show success notification
                $('#alert').prepend('<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check"></i> ' + response.success + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                setTimeout(function() { $('#alert .alert').fadeOut(400, function() { $(this).remove(); }); }, 3000);
            } else {
                alert(lang.error_update_quantity);
            }
        },
        error: function (xhr, status, error) {
            debugAjaxError('updateQuantity', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            alert(lang.error_api_call);
        }
    });
}



function handleDelete(productId) {
    if (!confirm(TEXT_CONFIRM_DELETE)) return;

    var user_token = document.querySelector('input[name="user_token"]').value;
    var postData = {};
    postData['selected[' + productId + ']'] = productId;

    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product.delete&user_token=' + user_token,
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(json) {
            if (json.success) {
                location.reload();
            } else if (json.error) {
                alert(json.error);
            }
        },
        error: function() {
            alert(TEXT_ERROR_UPDATE || 'Error deleting product.');
        }
    });
}

function handleDeleteSelected() {
    if (!confirm(TEXT_CONFIRM_DELETE)) return;

    var user_token = document.querySelector('input[name="user_token"]').value;
    var postData = {};
    document.querySelectorAll("input[name^='selected']:checked").forEach(function(cb) {
        postData[cb.name] = cb.value;
    });

    if (Object.keys(postData).length === 0) return;

    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product.delete&user_token=' + user_token,
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(json) {
            if (json.success) {
                location.reload();
            } else if (json.error) {
                alert(json.error);
            }
        },
        error: function() {
            alert(TEXT_ERROR_UPDATE || 'Error deleting product.');
        }
    });
}

function handleEnable(productId) {
    if (productId) {
        checkProduct(productId);
    }

    if (confirm(TEXT_CONFIRM_DELETE)) {
        var form = document.getElementById('form-product');
        form.setAttribute('action', enableUrl);
        form.submit();
    }
}

function handleDisable(productId) {
    if (productId) {
        checkProduct(productId);
    }

    if (confirm(TEXT_CONFIRM_DELETE)) {
        var form = document.getElementById('form-product');
        form.setAttribute('action', disableUrl);
        form.submit();
    }
}

function handleCopy(productId) {
    if (productId) {
        checkProduct(productId);
    }

    if (confirm(TEXT_CONFIRM_DELETE)) {
        var form = document.getElementById('form-product');
        form.setAttribute('action', copyUrl);
        form.submit();
    }
}

function handleFeed(productId) {
    if (productId) {
        handleFeedList(productId);
    }
}

function checkProduct(productId) {
    // Sélectionner la case à cocher directement par son sélecteur d'attribut
    var checkbox = $('input[name="selected[' + productId + ']"]');
    checkbox.prop('checked', !checkbox.prop('checked'));
}



document.addEventListener("DOMContentLoaded", function () {

    // Ajouter un écouteur d'événement sur les champs de recherche
    $('#form-filter input, #form-filter select').on('change', function () {
        var currentInput = $(this);

        // Cliquer sur le bouton de filtre
        $('#button-filter').click();
    });


});

document.addEventListener('DOMContentLoaded', function () {
    const select = document.querySelector('#input-limit');
    if (select) {
        select.addEventListener('change', function () {
            const selectedValue = select.value;
            // Construire l'URL avec le nouveau limit
            const url = new URL(window.location.href);
            url.searchParams.set('limit', selectedValue);
            url.searchParams.set('page', '1'); // Reset à la page 1 quand on change le limit
            window.location.href = url.toString();
        });
    }
});




// ========================================
// ACTIVE FUNCTIONS USED IN TWIG
// ========================================

function editMadeInCountry(product_id) {
    // OpenCart 4.x utilise un token différent
    var user_token = '';
    
    // Méthode 1 : Depuis un input hidden
    var tokenInput = document.querySelector('input[name="user_token"]');
    if (tokenInput) {
        user_token = tokenInput.value;
    } else {
        // Méthode 2 : Depuis l'URL actuelle
        var urlParams = new URLSearchParams(window.location.search);
        user_token = urlParams.get('user_token');
    }
    
    if (!user_token) {
        alert(lang.error_token_not_found);
        return;
    }
    
    var countrySelect = document.getElementById('input-made-in-country-id-' + product_id);
    if (!countrySelect) {
        console.error(lang.error_select_not_exist + " " + product_id);
        alert(lang.error_select_not_exist + " " + product_id);
        return;
    }
    
    var made_in_country_id = countrySelect.value;
    
    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product.editMadeInCountry&user_token=' + user_token,
        method: "POST",
        data: {
            product_id: product_id,
            made_in_country_id: made_in_country_id,
        },
        dataType: 'json',
        beforeSend: function() {
            // Ajouter un loader si nécessaire
        },
        success: function (json) {
            if (json['error']) {
                alert(lang.error_occurred + ': ' + json['error']);
                return;
            }
            
            if (json['success']) {
                // alert('Succès: ' + json['message']);
                if (made_in_country_id > 0) {
                    document.getElementById('check-made-in-country-id-' + product_id).style.backgroundColor = 'green';
                } else {
                    document.getElementById('check-made-in-country-id-' + product_id).style.backgroundColor = 'red';
                }
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.error(lang.error_ajax + ':', xhr.responseText);
            alert(lang.error_occurred + ': ' + thrownError + "\r\nStatus: " + xhr.status + "\r\nMessage: " + xhr.statusText);
        }
    });
}

function addToMarketplace(product_id, marketplace_account_id, marketplace_id, is_products = true) {
    
    var user_token = document.querySelector('input[name="user_token"]').value;

    // Vérifier qty + unallocated_qty > 0 avant de lister
    var qtyEl = document.getElementById('quantity-' + product_id);
    var unallocEl = document.getElementById('unallocated-quantity-' + product_id);
    var qty = qtyEl ? parseInt(qtyEl.getAttribute('rel1') || 0) : 0;
    var unalloc = unallocEl ? parseInt(unallocEl.getAttribute('rel1') || 0) : 0;
    if (qty + unalloc <= 0) {
        alert('Produit #' + product_id + ' : quantité = 0, impossible de lister.');
        return;
    }
    $.ajax({
        url: `index.php?route=shopmanager/marketplace.addToMarketplace&user_token=${user_token}`,
        type: 'POST',
        data: { product_id: product_id, marketplace_account_id: marketplace_account_id, marketplace_id: marketplace_id },
        dataType: 'json',
        success: function (json) {
            if (json.error) {
                console.error(json.error);
                // Parse the error if it's a JSON string
                let errorData = json.error;
                try {
                    if (typeof errorData === 'string') {
                        errorData = JSON.parse(errorData);
                    }
                } catch (e) {
                    // If parsing fails, use the raw error
                }
                
                // Display user-friendly error message
                let errorMessage = 'Failed to list product on marketplace.\n';
                if (errorData && errorData.Errors) {
                    const errors = Array.isArray(errorData.Errors) ? errorData.Errors : [errorData.Errors];
                    errors.forEach(error => {
                        errorMessage += `${error.LongMessage || error.ShortMessage || 'Unknown error'}\n`;
                    });
                } else if (typeof errorData === 'string') {
                    errorMessage += errorData;
                } else if (errorData && errorData.message) {
                    errorMessage += errorData.message;
                }
                alert(errorMessage);
            } else {
                handleMarketplaceAddUIUpdate(json, marketplace_account_id, product_id, is_products);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.error(lang.error_occurred + ': ' + xhr.responseText);
        }
    });

}
function handleMarketplaceAddUIUpdate(json, marketplace_account_id, product_id, is_products = false) {

    // Construire dynamiquement les IDs des éléments cachés et du span
    var spanElement = document.getElementById(`marketplace-account-id-${product_id}-${marketplace_account_id}`);
    var urlProductInput = document.querySelector(`input[name="url_product_${product_id}_${marketplace_account_id}"]`);
    var thumbInput = spanElement.querySelector("img"); // Sélectionne l'image dans le <span>

    if (spanElement && urlProductInput && thumbInput) {
        // Récupérer le nom de la marketplace dynamiquement
        var marketplaceName = json.marketplace_name || "Marketplace";
        var baseUrl = urlProductInput.value || "https://www.example.com/itm/{item_id}";
        var defaultImage = thumbInput.src || "https://phoenixsupplies.ca/image/cache/catalog/marketplace/default_grey-25x25.png";

        // Déterminer l'état de l'image (grey, green, red)
        var imageStatus = "grey"; // Par défaut en gris
        if (json.success && json.marketplace_item_id) {
            imageStatus = "green"; // Succès
        } else if (json.error) {
            imageStatus = "red"; // Erreur
        }

        // Déterminer la marketplace dynamiquement depuis l'URL de l'image existante
        var imageParts = defaultImage.split("_grey-"); // Séparer en tableau avec "_grey-"
        if (imageParts.length > 1) {
            var newImageUrl = imageParts.join(`_${imageStatus}-`);
        } else {
            var newImageUrl = defaultImage.replace("-grey", `-${imageStatus}`);
        }

        // Mise à jour des champs cachés avec les valeurs dynamiques
        var newProductUrl = json.success ? baseUrl.replace("{item_id}", json.marketplace_item_id) : baseUrl;

        // Modifier dynamiquement le contenu du <span> selon le succès ou l'erreur
        if (json.success && json.marketplace_item_id) {
            // Si succès, remplacer par un lien fonctionnel avec image verte
            spanElement.innerHTML = `
                <a href="${newProductUrl}" target="marketplace_item_id">
                    <img src="${newImageUrl}" alt="${marketplaceName}" style="width:25px; height:auto;">
                </a>
            `;
        } else if (json.error) {
            // Si erreur, garder le bouton onclick avec l'image rouge
            spanElement.innerHTML = `
                <input type="hidden" name="url_product_${product_underscore}${marketplace_account_id}" value="${baseUrl}">
                <a href="javascript:void(0);" onclick="addToMarketplace('${product_id}', '${marketplace_account_id}', '9');">
                    <img src="${newImageUrl}" alt="${marketplaceName}" style="width:25px; height:auto; filter: grayscale(100%);">
                </a>
            `;
        }
    } else {
        console.warn(`Élément(s) non trouvé(s) pour marketplace_account_id_${product_id}_${marketplace_account_id}`);
    }

}

function formatMarketplaceText(template, value1, value2) {
    var text = template || '';

    if (typeof value1 !== 'undefined') {
        text = text.replace('%s', value1);
    }

    if (typeof value2 !== 'undefined') {
        text = text.replace('%s', value2);
    }

    return text;
}

function getMarketplaceThumbStateUrl(currentSrc, state) {
    if (!currentSrc) {
        return currentSrc;
    }

    return currentSrc
        .replace('_green-', '_' + state + '-')
        .replace('_red-', '_' + state + '-')
        .replace('_grey-', '_' + state + '-')
        .replace('-green.', '-' + state + '.')
        .replace('-red.', '-' + state + '.')
        .replace('-grey.', '-' + state + '.');
}

function setMarketplaceListingState(productId, marketplaceAccountId, state, marketplaceItemId) {
    var spanElement = document.getElementById('marketplace-account-id-' + productId + '-' + marketplaceAccountId);

    if (!spanElement) {
        return;
    }

    var img = spanElement.querySelector('img');
    var currentSrc = img ? img.getAttribute('src') : '';
    var newImageUrl = getMarketplaceThumbStateUrl(currentSrc, state);
    var width = img && img.style && img.style.width ? img.style.width : '20px';
    var baseUrlInput = document.querySelector('input[name="url_product_' + productId + '_' + marketplaceAccountId + '"]');
    var marketplaceNameInput = document.querySelector('input[name="marketplace_name_' + productId + '_' + marketplaceAccountId + '"]');
    var marketplaceName = marketplaceNameInput ? marketplaceNameInput.value : (img ? img.getAttribute('alt') : 'Marketplace');
    var baseUrl = baseUrlInput ? baseUrlInput.value : '';
    var productUrl = (baseUrl && marketplaceItemId) ? baseUrl.replace('{item_id}', marketplaceItemId) : 'javascript:void(0);';

    var html = '';
    html += '<input type="hidden" name="url_product_' + productId + '_' + marketplaceAccountId + '" value="' + (baseUrl || '') + '" />';
    html += '<input type="hidden" name="marketplace_name_' + productId + '_' + marketplaceAccountId + '" value="' + (marketplaceName || '') + '" />';

    if (state === 'green') {
        html += '<a href="' + productUrl + '" target="marketplace_item_id">';
        html += '<img src="' + newImageUrl + '" alt="' + marketplaceName + '" style="width:' + width + '; height:auto;">';
        html += '</a>';
    } else if (state === 'red') {
        html += '<a href="javascript:void(0);" onclick="showErrorPopup(\'' + productId + '\');">';
        html += '<img src="' + newImageUrl + '" alt="' + marketplaceName + '" style="width:' + width + '; height:auto;">';
        html += '</a>';
    } else if (state === 'grey') {
        var mpIdInput = document.querySelector('input[name="marketplace_id_' + productId + '_' + marketplaceAccountId + '"]');
        var marketplace_id = mpIdInput ? mpIdInput.value : '9';
        html += '<a href="javascript:void(0);" onclick="addToMarketplace(\'' + productId + '\',\'' + marketplaceAccountId + '\',\'' + marketplace_id + '\',true);">';
        html += '<img src="' + newImageUrl + '" alt="' + marketplaceName + '" style="width:' + width + '; height:auto;">';
        html += '</a>';
    }

    spanElement.innerHTML = html;
}

function handleUpdateList() {
    var tokenElement = document.querySelector("input[name='user_token']");
    var user_token = tokenElement ? tokenElement.value : '';

    if (!user_token) {
        alert(lang.error_token_not_found);
        return;
    }

    var selectedProducts = [];
    document.querySelectorAll("input[name^='selected']:checked").forEach(function(checkbox) {
        if (checkbox.value) {
            selectedProducts.push(checkbox.value);
        }
    });

    if (selectedProducts.length === 0) {
        alert(TEXT_UPDATE_MARKETPLACE_NO_SELECTION || lang.alert_select_product || 'Please select at least one product');
        return;
    }

    showLoadingPopup(TEXT_UPDATE_MARKETPLACE_SELECTED || 'Updating selected eBay listings');

    var currentIndex = 0;

    function processNextProduct() {
        if (currentIndex >= selectedProducts.length) {
            finishLoadingPopup(TEXT_UPDATE_MARKETPLACE_DONE || 'Done');
            return;
        }

        var productId = selectedProducts[currentIndex];
        var quantityElement = document.getElementById('quantity-' + productId);
        var quantity = quantityElement ? parseInt(quantityElement.textContent, 10) || 0 : 0;
        var listedMarketplaceSpans = document.querySelectorAll('[id^="marketplace-account-id-' + productId + '-"]');
        var hasListedEbay = false;

        listedMarketplaceSpans.forEach(function(span) {
            var img = span.querySelector('img');
            if (!img) {
                return;
            }

            var src = img.getAttribute('src') || '';
            if (src.indexOf('_green-') !== -1 || src.indexOf('_red-') !== -1) {
                hasListedEbay = true;
            }
        });

        if (quantity <= 0) {
            appendLoadingMessage(formatMarketplaceText(TEXT_UPDATE_MARKETPLACE_SKIP_ZERO_QUANTITY, productId), 'warning');
            currentIndex++;
            processNextProduct();
            return;
        }

        if (!hasListedEbay) {
            appendLoadingMessage(formatMarketplaceText(TEXT_UPDATE_MARKETPLACE_SKIP_NOT_LISTED, productId), 'warning');
            currentIndex++;
            processNextProduct();
            return;
        }

        appendLoadingMessage(formatMarketplaceText(TEXT_UPDATE_MARKETPLACE_PROCESSING, productId), 'info');

        $.ajax({
            url: 'index.php?route=shopmanager/marketplace.updateListedProduct&user_token=' + user_token,
            type: 'POST',
            data: {
                product_id: productId
            },
            dataType: 'json',
            success: function(json) {
                if (json.skipped) {
                    if (json.reason === 'quantity_zero') {
                        appendLoadingMessage(formatMarketplaceText(TEXT_UPDATE_MARKETPLACE_SKIP_ZERO_QUANTITY, productId), 'warning');
                    } else {
                        appendLoadingMessage(formatMarketplaceText(TEXT_UPDATE_MARKETPLACE_SKIP_NOT_LISTED, productId), 'warning');
                    }
                } else if (json.results && json.results.length) {
                    json.results.forEach(function(result) {
                        if (result.status === 'success') {
                            setMarketplaceListingState(productId, result.marketplace_account_id, 'green', result.marketplace_item_id);
                            appendLoadingMessage(formatMarketplaceText(TEXT_UPDATE_MARKETPLACE_SUCCESS, productId), 'success');
                        } else if (result.status === 'error') {
                            setMarketplaceListingState(productId, result.marketplace_account_id, 'red', result.marketplace_item_id);
                            appendLoadingMessage(formatMarketplaceText(TEXT_UPDATE_MARKETPLACE_ERROR, productId, result.message || (lang.error_update_ebay || 'Error')), 'error');
                        }
                    });
                } else if (json.error) {
                    appendLoadingMessage(formatMarketplaceText(TEXT_UPDATE_MARKETPLACE_ERROR, productId, json.error), 'error');
                }

                currentIndex++;
                processNextProduct();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                appendLoadingMessage(formatMarketplaceText(TEXT_UPDATE_MARKETPLACE_ERROR, productId, thrownError || (lang.error_update_ebay || 'Error')), 'error');
                currentIndex++;
                processNextProduct();
            }
        });
    }

    processNextProduct();
}

function handleAddList() {
    // Sélectionner tous les liens <a> contenant addToMarketplace
    showModal('#loadingModal');
    var elements = document.querySelectorAll('a[onclick^="addToMarketplace"]');

    // Vérifier si des éléments ont été trouvés
    if (elements.length === 0) {
        console.warn("Aucun produit à lister.");
        return;
    }

    // Parcourir chaque élément et simuler un clic
    elements.forEach(function (element) {
        var img = element.querySelector("img");

        // Vérifier si l'image est déjà verte (listée)
        if (img && img.src.includes("_green-25x25.png")) {
            return; // Passer cet élément
        }

        // Vérifier qty + unallocated_qty > 0
        var row = element.closest('tr[data-product-id]');
        if (row) {
            var pid = row.getAttribute('data-product-id');
            var qtyEl = document.getElementById('quantity-' + pid);
            var unallocEl = document.getElementById('unallocated-quantity-' + pid);
            var qty = qtyEl ? parseInt(qtyEl.getAttribute('rel1') || 0) : 0;
            var unalloc = unallocEl ? parseInt(unallocEl.getAttribute('rel1') || 0) : 0;
            if (qty + unalloc <= 0) {
                return; // Ignorer ce produit
            }
        }

        element.click(); // Simule le clic sur le lien
    });
    hideModal('#loadingModal');
}

function updateCell(row, selector, newValue) {
    // Convert jQuery object to DOM element if necessary
    if (row instanceof jQuery) {
        row = row[0]; // Or use row.get(0)
    }

    if (row && row.querySelector) {
        let cell = row.querySelector(selector);
        if (cell) {
            cell.innerHTML = newValue;
        } else {
            console.warn(`Cell not found with selector: ${selector}`);
        }
    } else {
        console.error("Row is not a valid DOM element:", row);
    }
}

function handleFeedList(productId) {
    let tokenElement = document.querySelector("input[name='user_token']");
    let user_token = tokenElement ? tokenElement.value : '';

    if (!user_token) {
        showLoadingPopup('Erreur');
        appendLoadingMessage('❌ Token non trouvé !', 'error');
        finishLoadingPopup();
        return;
    }

    let productIds = [];
    if (productId) {
        productIds.push(productId);
    } else {
        document.querySelectorAll("input[name^='selected']:checked").forEach(checkbox => {
            let pid = checkbox.value;
            if (pid) {
                productIds.push(pid);
            }
        });
    }

    if (productIds.length === 0) {
        showLoadingPopup('⚠ Avertissement');
        appendLoadingMessage('⚠ Aucun produit sélectionné !', 'warn');
        finishLoadingPopup();
        return;
    } 

    // ⚠️ CONFIGURATION OPTIMISÉE VPS PUISSANT
    const MAX_PRODUCTS = 1000;             // Limite totale (VPS peut gérer)
    const BATCH_SIZE = 10;                // 10 produits par batch (parallèle)
    const DELAY_BETWEEN_BATCHES = 1000;   // 1s entre batches
    
    if (productIds.length > MAX_PRODUCTS) {
        showLoadingPopup('⚠️ Limite dépassée');
        appendLoadingMessage(`⚠️ Trop de produits sélectionnés (${productIds.length})`, 'warn');
        appendLoadingMessage(`📌 Maximum: ${MAX_PRODUCTS} produits à la fois`, 'warn');
        appendLoadingMessage(`💡 Divisez en plusieurs sélections de ${MAX_PRODUCTS}`, 'info');
        finishLoadingPopup();
        return;
    }

    showLoadingPopup('Mise à jour des produits');
    const totalBatches = Math.ceil(productIds.length / BATCH_SIZE);
    appendLoadingMessage(`🛠 Traitement de ${productIds.length} produit(s) en ${totalBatches} batch(es) de ${BATCH_SIZE}...`);
    appendLoadingMessage(`⚡ Serveur VPS détecté - Mode haute performance`, 'success');

    let processedCount = 0;
    let currentBatch = 0;

    function processNextProduct(index) {
        if (index >= productIds.length) {
            appendLoadingMessage(`✅ Tous les ${productIds.length} produits mis à jour!`, 'success');
            appendLoadingMessage(`📊 Traitement terminé en ${totalBatches} batch(es)`, 'info');
            finishLoadingPopup();
            
            // Empêcher la soumission du formulaire après la fin du traitement
            setTimeout(function() {
                const form = document.getElementById('form-product');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return false;
                    }, { once: true, capture: true });
                }
            }, 100);
            return;
        }

        let productId = productIds[index];
        processedCount++;
        
        // Calculer batch actuel
        const newBatch = Math.floor(index / BATCH_SIZE) + 1;
        if (newBatch > currentBatch) {
            currentBatch = newBatch;
            appendLoadingMessage(`📦 Batch ${currentBatch}/${totalBatches} - Produits ${index + 1}-${Math.min(index + BATCH_SIZE, productIds.length)}...`, 'info');
        }

        $.ajax({
            url: 'index.php?route=shopmanager/catalog/product_search.getSearchData&user_token=' + user_token,
            type: "POST",
            data: {
                product_id: productId
            },
            dataType: "json",
            success: function (data) {
                appendLoadingMessage(`✅ [${processedCount}/${productIds.length}] Product ${productId}`, 'success');

                if (data[productId]) {
                    let row = $(`tr[data-product-id="${productId}"]`);

                    if (row.length) {
                        // Update image
                        updateCell(row, '.text-center:nth-child(2)', data[productId].product_id + formatImage(data[productId].image, data[productId].name));
                        
                        // Update name
                        updateCell(row, '.text-center:nth-child(3)', data[productId].name);
                        
                        // Update price
                        var priceHtml = formatPrice(data[productId].price, data[productId].special);
                        if (data[productId].price_auto_set) {
                            priceHtml += ' <span class="badge bg-warning text-dark" title="Prix calculé automatiquement depuis eBay/Algopix" style="font-size:0.7em;cursor:pointer;" onclick="this.remove()">✨ auto</span>';
                        }
                        updateCell(row, '.text-center:nth-child(5)', priceHtml);
                        
                        // Update made_in_country_id (SELECT + TD background)
                        if (data[productId].made_in_country_id !== undefined) {
                            const selectElement = document.getElementById('input-made-in-country-id-' + productId);
                            
                            if (selectElement) {
                                // Change selected option
                                const options = selectElement.querySelectorAll('option');
                                let countryFound = false;
                                
                                options.forEach(option => {
                                    if (option.value === String(data[productId].made_in_country_id)) {
                                        option.selected = true;
                                        countryFound = true;
                                    } else {
                                        option.selected = false;
                                    }
                                });
                                
                                // Change background color of TD
                                const td = document.getElementById('check-made-in-country-id-' + productId);
                                if (td) {
                                    if (data[productId].made_in_country_id > 0) {
                                        td.style.backgroundColor = 'green';
                                    } else {
                                        td.style.backgroundColor = 'red';
                                    }
                                }
                            }
                        }
                        
                        // Update specifics
                        updateCell(row, '.text-center:nth-child(12)', '<button type="button" data-toggle="tooltip" title="' + data[productId].has_sources + '" class="btn btn-success btn-sm" style="padding:2px 4px; font-size:10px;" onclick="handleFeed(' + data[productId].product_id + ');"><i class="fa-solid fa-rss"></i></button>' + formatSpecifics(data[productId]));
                    }
                }
                
                // Délai entre batches
                if ((index + 1) % BATCH_SIZE === 0 && index + 1 < productIds.length) {
                    setTimeout(() => processNextProduct(index + 1), DELAY_BETWEEN_BATCHES);
                } else {
                    processNextProduct(index + 1);
                }
            },
            error: function (xhr, status, error) {
                appendLoadingMessage(`❌ [${processedCount}/${productIds.length}] Erreur product ${productId}`, 'error');
                
                // Continuer même en cas d'erreur
                if ((index + 1) % BATCH_SIZE === 0 && index + 1 < productIds.length) {
                    setTimeout(() => processNextProduct(index + 1), DELAY_BETWEEN_BATCHES);
                } else {
                    processNextProduct(index + 1);
                }
            }
        });
    }

    processNextProduct(0);
}



function handleSyncQtyAll() {
    var tokenElement = document.querySelector("input[name='user_token']");
    var user_token = tokenElement ? tokenElement.value : '';

    if (!user_token) {
        alert(lang.error_token_not_found);
        return;
    }

    // Opérer uniquement sur les produits sélectionnés
    var selectedProducts = [];
    document.querySelectorAll("input[name^='selected']:checked").forEach(function(checkbox) {
        if (checkbox.value) selectedProducts.push(checkbox.value);
    });

    if (selectedProducts.length === 0) {
        alert(TEXT_UPDATE_MARKETPLACE_NO_SELECTION || 'Please select at least one product');
        return;
    }

    showLoadingPopup('Sync quantities to eBay');

    var currentIndex = 0;

    function processNext() {
        if (currentIndex >= selectedProducts.length) {
            finishLoadingPopup(TEXT_UPDATE_MARKETPLACE_DONE || 'Done');
            return;
        }

        var productId = selectedProducts[currentIndex];

        // Trouver le span marketplace pour account_id=1 (id: marketplace-account-id-{pid}-1)
        var spanEl = document.getElementById('marketplace-account-id-' + productId + '-1');
        if (!spanEl) {
            appendLoadingMessage('Product ' + productId + ': no marketplace span found', 'warning');
            currentIndex++;
            processNext();
            return;
        }

        appendLoadingMessage(formatMarketplaceText(TEXT_UPDATE_MARKETPLACE_PROCESSING, productId), 'info');

        $.ajax({
            url: 'index.php?route=shopmanager/marketplace.editQuantityToMarketplace&user_token=' + user_token,
            type: 'POST',
            data: {
                product_id: productId,
                marketplace_account_id: '1'
            },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    appendLoadingMessage(formatMarketplaceText(TEXT_UPDATE_MARKETPLACE_SUCCESS, productId), 'success');
                } else {
                    appendLoadingMessage(formatMarketplaceText(TEXT_UPDATE_MARKETPLACE_ERROR, productId, data.message || data.error || 'Error'), 'error');
                }
                currentIndex++;
                processNext();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                appendLoadingMessage(formatMarketplaceText(TEXT_UPDATE_MARKETPLACE_ERROR, productId, thrownError || 'Request error'), 'error');
                currentIndex++;
                processNext();
            }
        });
    }

    processNext();
}

document.addEventListener('DOMContentLoaded', function () {
    var user_token = document.querySelector('input[name="user_token"]').value;
    const searchButton = document.querySelector('.btn.btn-warning i.fa-magnifying-glass').parentNode;

    searchButton.addEventListener('click', function (event) {
        event.preventDefault();

        showLoadingPopup('🔍 Analyse des produits sélectionnés...');

        const checkedCheckboxes = document.querySelectorAll('input[type="checkbox"][name^="selected"]:checked');
        let selectedProducts = [];

        checkedCheckboxes.forEach(function (checkbox) {
            selectedProducts.push(checkbox.value);
        });

        if (selectedProducts.length === 0) {
            appendLoadingMessage('⚠ Aucun produit sélectionné. Redirection vers la recherche...');
            setTimeout(() => {
                hideLoadingPopup();
                window.location.href = searchButton.href;
            }, 1500);
            return;
        }

        const chunkSize = 5;
        let productChunks = [];
        for (let i = 0; i < selectedProducts.length; i += chunkSize) {
            productChunks.push(selectedProducts.slice(i, i + chunkSize));
        }

        function sendChunk(productChunk) {
            let data = {
                product_ids: productChunk
            };

            appendLoadingMessage(`📦 Traitement des produits : ${productChunk.join(', ')}`);

            return fetch('index.php?route=shopmanager/catalog/product_search.product_source_info_feed&user_token=' + user_token, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                appendLoadingMessage(`✅ Terminé : ${productChunk.join(', ')}`);
            })
            .catch(error => {
                appendLoadingMessage(`❌ Erreur pour ${productChunk.join(', ')}: ${error.message}`);
            });
        }

        async function sendChunksSequentially() {
            for (let chunk of productChunks) {
                await sendChunk(chunk);
            }
            finishLoadingPopup('✅ Analyse terminée. Rechargement...');
            setTimeout(() => location.reload(), 1500);
        }

        sendChunksSequentially();
    });

    function addImageEvents(thumbnail, fullsize) {
        // Function intentionally left empty - replaced by initImagePreview()
    }

    function handleImages() {
        const thumbnails = document.querySelectorAll('.img-thumbnail');
        rowsData = [];

        thumbnails.forEach(thumbnail => {
            const fullsize = thumbnail.nextElementSibling;
            const resolutionMessage = thumbnail.parentElement.querySelector('.resolution-message');
            const imageContainer = thumbnail.closest('.image-container');

            addImageEvents(thumbnail, fullsize);
        });
    }

    handleImages();
});

// ========================================
// IMAGE DRAG & DROP INITIALIZATION
// ========================================
document.addEventListener('DOMContentLoaded', function () {
    
    // Initialiser resolution check
    if (typeof initImageResolutionCheck === 'function') {
        initImageResolutionCheck();
    }
    
    // Initialiser preview (mouseenter)
    if (typeof initImagePreview === 'function') {
        initImagePreview();
    }
    
    // Initialiser drag & drop
    if (typeof initImageDragAndDrop === 'function') {
        const uploadUrl = 'index.php?route=shopmanager/catalog/product.editImage';
        initImageDragAndDrop(uploadUrl, function(response, container, productId) {
            // Success callback
            if (response.success && response.thumb && response.popup) {
                const thumbnail = container.querySelector('.img-thumbnail, .thumbnail-actual-image');
                const fullImage = container.querySelector('.fullsize-actual-image, .actual-image-preview');
                
                if (thumbnail) thumbnail.src = response.thumb + '?t=' + Date.now();
                if (fullImage) fullImage.src = response.popup + '?t=' + Date.now();
                
                // Recheck resolution après upload
                if (thumbnail) {
                    setTimeout(() => checkImageResolution(thumbnail), 100);
                }
            }
        }, function(xhr, container, productId) {
            // Error callback
            console.error('Upload error for product', productId, xhr.responseText);
        });
    }
    
});

function updateImagesUI(productImages) {
    if (productImages.primary && productImages.primary.thumb) {
        const thumb = productImages.primary.thumb;
        
        // Trouver l'image avec data-temp-preview="true" au lieu d'utiliser product_id
        const imageField = document.querySelector('img[data-temp-preview="true"]');
        
        if (imageField) {
            const container = imageField.closest('.actual-image-container');
            const fullImage = container ? container.querySelector('.fullsize-actual-image') : null;
            
            // Remplacer l'aperçu temporaire base64 par l'URL correcte du serveur
            imageField.src = thumb;
            delete imageField.dataset.tempPreview; // Enlever le marqueur temporaire
            
            // Update fullsize image
            if (fullImage && productImages.primary.fullsize) {
                fullImage.src = productImages.primary.fullsize;
            }
        }
    }
}


function handleSourcesError(product_id) {
     const user_token = document.querySelector('input[name="user_token"]').value;
    alert(lang.error_sources.replace('%s', product_id));
    // Optionally open edit page
    window.open('index.php?route=shopmanager/catalog/product.edit&user_token=' + user_token + '&product_id=' + product_id, '_blank');
}

// ============================================
// HELPER FUNCTION : Get user token
// ============================================
function getUserToken() {
    var tokenInput = document.querySelector('input[name="user_token"]');
    if (tokenInput) {
        return tokenInput.value;
    }
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('user_token') || '';
}

// ============================================
// MUTATION OBSERVER - Réinitialiser les previews après reload de table
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const targetNode = document.querySelector('#form-product') || document.body;
    
    const observer = new MutationObserver(function(mutationsList) {
        for (let mutation of mutationsList) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Vérifier si des images ont été ajoutées
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        if (node.classList && node.classList.contains('actual-image-container') ||
                            node.querySelector && node.querySelector('.actual-image-container')) {
                            // Réinitialiser les previews pour les nouveaux éléments
                            if (typeof initImagePreview === 'function') {
                                initImagePreview();
                            }
                        }
                    }
                });
            }
        }
    });
    
    observer.observe(targetNode, { 
        childList: true, 
        subtree: true 
    });
});


function updateRelativeTimes() {
    document.querySelectorAll('.relative-time').forEach(function(elem) {
        const timestamp = elem.dataset.timestamp;
        if (timestamp) {
            elem.textContent = formatRelativeTime(timestamp);
        }
    });
}

// ============================================
// FIX 403 FORBIDDEN - Filter form data for enable/disable actions
// ============================================
// EVENT DELEGATION: Écouter sur document (ne se reload jamais)
// au lieu d'attacher sur le form (qui se recharge)
(function() {
    // Flag pour éviter d'initialiser plusieurs fois
    if (window._productFormFilterInitialized) {
        return;
    }
    window._productFormFilterInitialized = true;
       
    // Écouter sur document avec event delegation
    document.addEventListener('submit', function(e) {
        // Vérifier si c'est le bon form
        if (e.target.id !== 'form-product') {
            return;
        }
        
        const form = e.target;
        const submitter = e.submitter;
        if (!submitter) return;
        
        const action = submitter.getAttribute('formaction');
        if (!action) return;
        
        // Vérifier si c'est un enable/disable action
        if (action.includes('product.enable') || action.includes('product.disable')) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            
            // Extraire product_id de l'URL (fallback sur data-product-id si absent)
            const urlParams = new URLSearchParams(action.split('?')[1]);
            let productId = urlParams.get('product_id');
            const userToken = urlParams.get('user_token');
            
            if (!productId) {
                productId = submitter.getAttribute('data-product-id');
            }
            
            if (!productId) {
                console.error('❌ No product_id found in action URL or data-product-id');
                return;
            }
            
            // S'assurer que product_id est dans l'URL (nécessaire pour le controller en GET)
            let fetchAction = action;
            if (!urlParams.has('product_id') || !urlParams.get('product_id')) {
                const sep = action.includes('?') ? '&' : '?';
                fetchAction = action + sep + 'product_id=' + encodeURIComponent(productId);
            }
            
            
            // Construire les données minimales
            const formData = new FormData();
            formData.append('product_id', productId);
            
            // Ajouter made_in_country si présent
            const madeInField = form.querySelector('[name="made_in_country_id_' + productId + '"]');
            if (madeInField) {
                formData.append('made_in_country_id_' + productId, madeInField.value);
            }
            
            // Ajouter les URLs marketplace si présentes
            form.querySelectorAll('[name^="url_product_' + productId + '_"]').forEach(function(field) {
                formData.append(field.name, field.value);
            });
            
            // Ajouter marketplace names si présents
            form.querySelectorAll('[name^="marketplace_name_' + productId + '_"]').forEach(function(field) {
                formData.append(field.name, field.value);
            });
            
            
            // Envoyer la requête AJAX
            fetch(fetchAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || data.redirect) {
                    // Recharger la page comme OpenCart 2
                    location.reload();
                } else if (data.error) {
                    alert((window.lang && lang.error_occurred ? lang.error_occurred : TEXT_ERROR_API_CALL) + ': ' + data.error);
                }
            })
            .catch(error => {
                console.error('❌ AJAX Error:', error);
                alert((window.lang && lang.error_occurred ? lang.error_occurred : TEXT_ERROR_API_CALL) + ': ' + error.message);
            });
        }
    }, true); // useCapture=true pour intercepter AVANT common.js
    
    
})();
