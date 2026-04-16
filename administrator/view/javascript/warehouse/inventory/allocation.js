// Original: warehouse/inventory/allocation.js
// ============================================
// GLOBAL VARIABLES
// ============================================
var autoAcceptAICountry = window.autoAcceptAICountry || false;
var hasScannedProducts = false;

// ============================================
// PAGINATION & AJAX LOADING
// ============================================
$(document).ready(function() {
    // AJAX pagination for inventory list
    $('#inventory').on('click', 'thead a, .pagination a', function(e) {
        e.preventDefault();
        $('#inventory').load(this.href);
    });
    
    // Reinit relative times after list loads
    $('#inventory').on('DOMSubtreeModified', function() {
        setTimeout(updateRelativeTimes, 100);
    });
});

// ============================================
// PREVENT ACCIDENTAL PAGE LEAVE
// ============================================
window.addEventListener('beforeunload', function(e) {
    if (hasScannedProducts) {
        e.preventDefault();
        e.returnValue = TEXT_UNSAVED_CHANGES;
        return TEXT_UNSAVED_CHANGES;
    }
});

// ============================================
// SELECTION FUNCTIONS
// ============================================
function selectAll() {
    document.querySelectorAll('input[name="product_select[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function unselectAll() {
    document.querySelectorAll('input[name="product_select[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function checkedSku() {
    let sku = document.getElementById('sku').value;
    let checkbox = document.getElementById(sku);
    if (checkbox) {
        checkbox.checked = true;
        document.getElementById('sku').value = '';
    } else {
        alert(TEXT_SKU_NOT_FOUND + ': ' + sku);
    }
}
function filterInventory() {
    var user_token = document.querySelector('input[name="user_token"]').value;

    let url = 'index.php?route=warehouse/inventory/allocation&user_token=' + user_token;
    let sku = document.getElementById('input-sku').value;
    
    if (sku) {
        url += '&filter_sku=' + encodeURIComponent(sku);
    }
    location.href = url;
}

function confirmDelete() {
    if (confirm(TEXT_CONFIRM || 'Are you sure?')) {
        document.getElementById('form-inventory').submit();
    }
}

// Fonction globale pour activer ou désactiver le bouton submit
function toggleSubmitButton() {
    const newLocationInput = document.getElementById('input-new-location');
    const submitButton = document.getElementById('button-submit');
    
    if (newLocationInput && submitButton) {
        const checkedCount = document.querySelectorAll('input[type="checkbox"][name^="product_id"]:checked').length;
        
        if (newLocationInput.value.trim() === "" || checkedCount === 0) {
            submitButton.disabled = true;
        } else {
            submitButton.disabled = false;
        }
    }
}


document.addEventListener('DOMContentLoaded', function() {
    // Initialize relative time updates
    updateRelativeTimes();
    setInterval(updateRelativeTimes, 60000); // Update every minute
    
    // Sélectionne le champ `new_location` et le bouton `button-submit`
    const newLocationInput = document.getElementById('input-new-location');
    const submitButton = document.getElementById('button-submit');
    const skuInput = document.getElementById('input-sku');

    // Intercepter la soumission pour n'envoyer que les lignes cochées
    submitButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        const form = document.getElementById('form-inventory');
        const container = document.getElementById('hidden-inputs-container');
        const newLocation = newLocationInput.value.trim();
        
        // Vider le conteneur
        container.innerHTML = '';
        
        // Récupérer toutes les lignes cochées
        const checkedRows = document.querySelectorAll('input[type="checkbox"][name^="product_id"]:checked');
        
        if (checkedRows.length === 0) {
            alert(TEXT_SELECT_AT_LEAST_ONE);
            return;
        }
        
        // Pour chaque ligne cochée, ajouter les champs hidden nécessaires
        checkedRows.forEach(function(checkbox) {
            const productId = checkbox.value;
            const row = checkbox.closest('tr');
            
            // Ajouter product_id
            const inputProductId = document.createElement('input');
            inputProductId.type = 'hidden';
            inputProductId.name = 'product_id[' + productId + ']';
            inputProductId.value = productId;
            container.appendChild(inputProductId);
            
            // Ajouter quantity
            const quantity = document.getElementById('quantity_hid' + productId).value;
            const inputQuantity = document.createElement('input');
            inputQuantity.type = 'hidden';
            inputQuantity.name = 'quantity[' + productId + ']';
            inputQuantity.value = quantity;
            container.appendChild(inputQuantity);
            
            // Ajouter unallocated_quantity
            const unallocatedQuantity = document.getElementById('unallocated_quantity_hid' + productId).value;
            const inputUnallocated = document.createElement('input');
            inputUnallocated.type = 'hidden';
            inputUnallocated.name = 'unallocated_quantity[' + productId + ']';
            inputUnallocated.value = unallocatedQuantity;
            container.appendChild(inputUnallocated);
            
            // Ajouter old_location
            const oldLocation = document.getElementById('old_location' + productId).value;
            const inputOldLocation = document.createElement('input');
            inputOldLocation.type = 'hidden';
            inputOldLocation.name = 'old_location[' + productId + ']';
            inputOldLocation.value = oldLocation;
            container.appendChild(inputOldLocation);
        });
        
        // Reset flag before submitting (allow navigation after submit)
        hasScannedProducts = false;
        
        // Soumettre le formulaire
        form.submit();
    });

    // Écoute les changements dans le champ `new_location`
    newLocationInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
        toggleSubmitButton();
    });
    
    // Écoute les changements sur les checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.matches('input[type="checkbox"][name^="product_id"]')) {
            toggleSubmitButton();
        }
    });

    // Vérifie l'état initial au chargement de la page
    toggleSubmitButton();
    
    // ============================================
    // PATTERN #4: AUTO-SCAN CHECKBOX WITH COUNTRY VALIDATION
    // ============================================
    
    // Select-all checkbox avec popups séquentiels
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    if (selectAllCheckbox) {
        selectAllCheckbox.onclick = function() {
            const isChecked = this.checked;
            
            if (isChecked) {
                // ✅ VALIDATION GLOBALE
                const itemsWithoutRequiredField = [];
                document.querySelectorAll('input[type="checkbox"][name^="product_id"]').forEach(function(checkbox) {
                    const productId = checkbox.value;
                    const requiredField = document.getElementById('input-made-in-country-id-' + productId);
                    const requiredValue = requiredField ? requiredField.value : '0';
                    
                    if (!requiredValue || requiredValue === '0') {
                        itemsWithoutRequiredField.push({
                            checkbox: checkbox,
                            productId: productId,
                            row: checkbox.closest('tr')
                        });
                    }
                });
                
                // Si items sans champ requis: popups séquentiels
                if (itemsWithoutRequiredField.length > 0) {
                    selectAllCheckbox.checked = false;
                    
                    let currentIndex = 0;
                    
                    function processNextItem() {
                        if (currentIndex >= itemsWithoutRequiredField.length) {
                            // Tous validés: re-clic select-all après délai
                            setTimeout(function() {
                                selectAllCheckbox.click();
                            }, 500);
                            return;
                        }
                        
                        const item = itemsWithoutRequiredField[currentIndex];
                        currentIndex++;
                        
                        // Affiche popup validation
                        showCountryPopupForScan(item.productId, item.row, function() {
                            // Attendre avant prochain popup
                            setTimeout(function() {
                                processNextItem();
                            }, 300);
                        });
                    }
                    
                    processNextItem();
                    return;
                }
            }
            
            // Select-all normal (tous champs validés)
            document.querySelectorAll('input[type="checkbox"][name^="product_id"]').forEach(function(checkbox) {
            const productId = checkbox.value;
            const row = checkbox.closest('tr');
            const unallocatedQuantity = parseInt(document.getElementById('unallocated_quantity' + productId).textContent) || 0;
            
            checkbox.checked = isChecked;
            
            if (isChecked) {
                    autoScanProduct(row, productId, unallocatedQuantity);
                } else {
                    // Reset - RESTORE ORIGINAL VALUES
                    const originalQuantity = parseInt(row.dataset.originalQuantity);
                    const originalUnallocated = parseInt(row.dataset.originalUnallocated);
                    
                    if (!isNaN(originalQuantity) && !isNaN(originalUnallocated)) {
                        // Restore to original state
                        updateQuantity('quantity', productId, originalQuantity);
                        updateQuantity('unallocated_quantity', productId, originalUnallocated);
                    }
                    
                    updateTotalQuantity(productId);
                    
                    // Clear stored values
                    delete row.dataset.addedQuantity;
                    delete row.dataset.originalQuantity;
                    delete row.dataset.originalUnallocated;
                    
                    row.style.backgroundColor = '';
                    row.style.color = '';
                    row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
                        td.style.backgroundColor = '';
                        td.style.color = '';
                    });
                }
            });
            
            // Check if any products are still scanned
            if (!isChecked) {
                hasScannedProducts = false;
            }
            
            // Play sound ONCE after all checkboxes processed
            if (isChecked) {
                playSuccessSound();
            }
            
            toggleSubmitButton();
        };
    }
    
    // Individual checkbox with popup AI country
    document.addEventListener('change', function(e) {
        if (e.target.matches('input[type="checkbox"][name^="product_id"]')) {
            const checkbox = e.target;
            const isChecked = checkbox.checked;
            const row = checkbox.closest('tr');
            const productId = checkbox.value;
            const unallocatedQuantity = parseInt(document.getElementById('unallocated_quantity' + productId).textContent) || 0;
            
            if (isChecked) {
                // ✅ VALIDATION CHAMP REQUIS (pays)
                const requiredField = document.getElementById('input-made-in-country-id-' + productId);
                const requiredValue = requiredField ? requiredField.value : '0';
                
                if (!requiredValue || requiredValue === '0') {
                    checkbox.checked = false;
                    
                    // Affiche popup validation (avec AI si applicable)
                    showCountryPopupForScan(productId, row, function() {
                        checkbox.checked = true;
                        autoScanProduct(row, productId, unallocatedQuantity);
                    });
                    return;
                }
                
                autoScanProduct(row, productId, unallocatedQuantity);
                playSuccessSound(); // Son pour checkbox individuel
            } else {
                // Reset on uncheck - RESTORE ORIGINAL VALUES
                const originalQuantity = parseInt(row.dataset.originalQuantity);
                const originalUnallocated = parseInt(row.dataset.originalUnallocated);
                
                
                if (!isNaN(originalQuantity) && !isNaN(originalUnallocated)) {
                    // Restore to original state
                    updateQuantity('quantity', productId, originalQuantity);
                    updateQuantity('unallocated_quantity', productId, originalUnallocated);
                } else {
                    console.error('No original values found for product', productId);
                }
                
                updateTotalQuantity(productId);
                
                // Clear stored values
                delete row.dataset.addedQuantity;
                delete row.dataset.originalQuantity;
                delete row.dataset.originalUnallocated;
                
                // Reset row style
                row.style.backgroundColor = '';
                row.style.color = '';
                row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
                    td.style.backgroundColor = '';
                    td.style.color = '';
                });
                
                // Check if any products are still scanned
                const anyScanned = Array.from(document.querySelectorAll('tr[data-product-id]')).some(function(r) {
                    const pid = r.dataset.productId;
                    const qty = parseInt(document.getElementById('quantity' + pid).textContent) || 0;
                    return qty > 0;
                });
                hasScannedProducts = anyScanned;
            }
            
            toggleSubmitButton();
            
            // Update select-all state
            updateSelectAllState();
        }
    });
    
    // Fonction pour mettre à jour l'état du select-all
    function updateSelectAllState() {
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        if (selectAllCheckbox) {
            const allCheckboxes = document.querySelectorAll('input[type="checkbox"][name^="product_id"]');
            const checkedCheckboxes = document.querySelectorAll('input[type="checkbox"][name^="product_id"]:checked');
            
            if (allCheckboxes.length > 0) {
                selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
            }
        }
    }

    var scanTimeout = null;
    var lastScannedSku = '';

    // Prevent Enter from submitting
    if (skuInput) {
        skuInput.onkeydown = function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                return false;
            }
        };
    }

    // Process SKU scan function
    function processSKUScan() {
        var inputSku = skuInput.value.trim();
        if (inputSku === '' || inputSku === lastScannedSku) {
            return;
        }
        
        lastScannedSku = inputSku;
        var skuFound = false;


        // Parcourir les lignes du tableau pour vérifier la correspondance avec le SKU
        $('tr[data-product-id]').each(function() {
            var row = $(this);
            var productSku = row.find('td').eq(2).text().trim(); // Récupère le SKU dans la 3ème cellule de la ligne

            // Vérifie si le SKU correspond à l'entrée
            if (productSku === inputSku) {
                skuFound = true; // SKU trouvé
                var productId = row.data('product-id'); // Récupère le product_id de la ligne correspondante


                // Vider le champ SKU
                skuInput.value = '';

                // Check if made_in_country_id is set
                const countrySelect = document.getElementById('input-made-in-country-id-' + productId);
                const madeInCountryId = countrySelect ? countrySelect.value : '0';
                
                
                if (!madeInCountryId || madeInCountryId === '0' || madeInCountryId === '') {
                    // Country not set - show popup first
                    showCountryPopupForScan(productId, row[0], function() {
                        processScanAllocation(row, productId);
                        // Débloquer pour le prochain scan
                        lastScannedSku = '';
                        skuInput.focus();
                    });
                    return false;
                }
                
                // Country is set - process the allocation directly
                processScanAllocation(row, productId);
                
                // Débloquer pour le prochain scan IMMÉDIATEMENT
                lastScannedSku = '';
                skuInput.focus();
                
                return false; // Exit the each loop
            }
        });
        
        // Si le SKU n'a pas été trouvé et que l'input n'est pas vide
        if (!skuFound && inputSku !== '') {
            playErrorSound();
            alert(TEXT_SKU_NOT_FOUND + ': ' + inputSku);
            skuInput.value = '';
            lastScannedSku = '';
        }
    }

    // SKU scan on input with debounce
    if (skuInput) {
        skuInput.oninput = function() {
            var inputSku = this.value.trim();
            if (inputSku === lastScannedSku) return;
            
            clearTimeout(scanTimeout);
            scanTimeout = setTimeout(function() {
                processSKUScan();
            }, 100);
        };
        
        // SKU scan on blur
        skuInput.onblur = function() {
            clearTimeout(scanTimeout);
            processSKUScan();
        };
    }
});

// Function to process the allocation scan (called after country is validated or set)
function processScanAllocation(row, productId) {
    
    // Récupère les valeurs de quantity et unallocated_quantity
    var unallocatedQuantity = parseInt($('#unallocated_quantity' + productId).text()) || 0;
    var quantity = parseInt($('#quantity' + productId).text()) || 0;
    var currentLocation = $('#old_location' + productId).val() || '';
    
    
    // Validation: Si location existe ET quantity > 0, interdire l'ajout
    if (currentLocation !== '' && quantity > 0) {
        alert(TEXT_CANNOT_ADD_PRODUCT.replace('%s', currentLocation));
        playErrorSound();
        return;
    }

    // Déplace la ligne trouvée en haut du tableau avant de cocher
    row.prependTo(row.closest('tbody'));

    // Cocher la case de sélection
    row.find('input[type="checkbox"][name^="product_id"]').prop('checked', true);
    
    var newQuantity, newUnallocatedQuantity;
    var isManualAdd = false; // Flag pour distinguer les ajouts manuels
    
    // Si unallocated est déjà à 0 ou moins, demander confirmation
    if (unallocatedQuantity <= 0) {
        if (confirm(TEXT_ALREADY_SCANNED)) {
            // Ajouter seulement à quantity sans changer unallocated
            newQuantity = quantity + 1;
            newUnallocatedQuantity = unallocatedQuantity; // Reste inchangé
            isManualAdd = true; // C'est un ajout manuel
        } else {
            // Annuler - ne rien faire
            return;
        }
    } else {
        // Transfert normal: quantity +1, unallocated -1
        newQuantity = quantity + 1;
        newUnallocatedQuantity = unallocatedQuantity - 1;
    }

    updateQuantity('quantity', productId, newQuantity);
    updateQuantity('unallocated_quantity', productId, newUnallocatedQuantity);
    updateTotalQuantity(productId);

    // Si quantity devient 0, effacer la location pour permettre une nouvelle
    if (newQuantity === 0) {
        row.find('td').eq(7).text(''); // Vider l'affichage de la location (8ème colonne)
        $('#old_location' + productId).val(''); // Vider le champ hidden
    }

    // Met à jour la couleur de la ligne en fonction de newUnallocatedQuantity
    if (isManualAdd) {
        // Ajout manuel accepté par l'utilisateur = ORANGE
        row.css({'background-color': '#fd7e14 !important', 'color': '#fff !important'}); // Orange - Ajout manuel
        row.find('td').css({'background-color': '#fd7e14', 'color': '#fff'});
    } else if (newUnallocatedQuantity === 0) {
        row.css({'background-color': '#d4edda !important', 'color': '#155724 !important'}); // Vert - Complet
        row.find('td').css({'background-color': '#d4edda', 'color': '#155724'});
    } else if (newUnallocatedQuantity > 0) {
        row.css({'background-color': '#fff3cd !important', 'color': '#856404 !important'}); // Jaune - Partiel
        row.find('td').css({'background-color': '#fff3cd', 'color': '#856404'});
    } else {
        row.css({'background-color': '#f8d7da !important', 'color': '#721c24 !important'}); // Rouge - Négatif
        row.find('td').css({'background-color': '#f8d7da', 'color': '#721c24'});
    }

    // Mettre à jour l'état du bouton submit
    toggleSubmitButton();
    
    // Mark that we have scanned products (enable beforeunload warning)
    hasScannedProducts = true;
    
    // Play success sound
    playSuccessSound();
    
    // Return focus to SKU input
    $('#input-sku').focus();
}

// ============================================
// HELPER: AUTO-SCAN PRODUCT (PATTERN #4)
// ============================================
function autoScanProduct(row, productId, unallocatedQuantity) {
    // Get current quantity
    const currentQuantity = parseInt(document.getElementById('quantity' + productId).textContent) || 0;
    
    // Store ORIGINAL values before modification (in hidden inputs for persistence)
    if (!row.dataset.originalQuantity) {
        row.dataset.originalQuantity = currentQuantity;
        row.dataset.originalUnallocated = unallocatedQuantity;
    }
    
    // ADD unallocated to existing quantity (not replace)
    const newQuantity = currentQuantity + unallocatedQuantity;
    updateQuantity('quantity', productId, newQuantity);
    updateQuantity('unallocated_quantity', productId, 0);
    updateTotalQuantity(productId);
    
    // Store the added quantity in row dataset for uncheck
    row.dataset.addedQuantity = unallocatedQuantity;
    
    // Visual feedback - green (complete)
    row.style.backgroundColor = '#d4edda';
    row.style.color = '#155724';
    row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
        td.style.backgroundColor = '#d4edda';
        td.style.color = '#155724';
    });
    
    hasScannedProducts = true;
    toggleSubmitButton();
    // Note: Pas de son ici pour éviter 100+ AudioContext lors du select-all
}

// Remplacer unallocated_quantity par un champ texte lors du clic
$(document).on('click', '.pedit-unallocated-quantity', function(event) {
    event.preventDefault(); // Empêcher le comportement par défaut lors du clic

    // Empêcher la soumission du formulaire lorsqu'on appuie sur "Enter" dans le champ input
    $('form').on('submit', function(e) {
        e.preventDefault(); // Empêche la soumission de tous les formulaires
    });

    var rel = $(this).attr('rel'); // Product ID
    var rel1 = $(this).attr('rel1'); // Current unallocated quantity
  //  var quantity = parseInt($('tr[data-product-id="' + rel + '"] .quantity span').text());
 //   var marketplace_item_id = parseInt($('tr[data-product-id="' + rel + '"] .marketplace_item_id span').text());
    // Retirer la classe 'label label-danger' de l'élément cliqué
   $(this).removeClass('label label-danger label-success label-warning label-primary label-info');


    // Créer un champ texte avec la valeur existante de unallocated_quantity
    var html = '<input type="text" id="unallocated_quantity_input' + rel + '" class="form-control unallocated_quantity_input" value="' + rel1 + '" />';
    $('#unallocated_quantity' + rel).html(html);

    var inputElement = $('#unallocated_quantity_input' + rel);
    inputElement.focus().select();

    // Détecter l'appui sur la touche Enter dans le champ texte
    inputElement.on('blur', function() {
        var newUnallocatedQuantity = $(this).val(); // Récupérer la nouvelle valeur
   //     var spanClass = '';

        // Vérifier la valeur et appliquer les classes appropriées
     /*   if (newUnallocatedQuantity <= 0) {
            spanClass = 'label label-danger';
        } else if (newUnallocatedQuantity <= 5) {
            spanClass = 'label label-warning';
        } else {
            spanClass = 'label label-success';
        }

        // Remplacer l'input par un span avec la nouvelle valeur et la classe appropriée
        $('#unallocated_quantity' + rel).html('<span class="' + spanClass + '">' + newUnallocatedQuantity + '</span>');
        $('#unallocated_quantity_hid' + rel).val(newUnallocatedQuantity);*/
        updateQuantity('unallocated_quantity',rel,newUnallocatedQuantity) ;
        updateTotalQuantity(rel);
        // Appeler la fonction pour confirmer la nouvelle quantité si besoin
      //  confirmUnallocatedQuantity(rel, newUnallocatedQuantity, quantity, marketplace_item_id);
    });

     // Mettre à jour l'attribut 'rel1' en temps réel lors de la modification
     inputElement.on('input', function() {
        $(this).closest('.pedit-unallocated-quantity').attr('rel1', $(this).val());
    });
});


// Remplacer quantity par un champ texte lors du clic
$(document).on('click', '.pedit-quantity', function(event) {
    event.preventDefault(); // Empêcher le comportement par défaut lors du clic

    // Empêcher la soumission du formulaire lorsqu'on appuie sur "Enter" dans le champ input
    $('form').on('submit', function(e) {
        e.preventDefault(); // Empêche la soumission de tous les formulaires
    });

    var rel = $(this).attr('rel'); // Product ID
    var rel1 = $(this).attr('rel1'); // Current unallocated quantity
  //  var quantity = parseInt($('tr[data-product-id="' + rel + '"] .quantity span').text());
  //  var marketplace_item_id = parseInt($('tr[data-product-id="' + rel + '"] .marketplace_item_id span').text());
    // Retirer la classe 'label label-danger' de l'élément cliqué
   $(this).removeClass('label label-danger label-success label-warning label-primary label-info');


    // Créer un champ texte avec la valeur existante de quantity
    var html = '<input type="text" id="quantity_input' + rel + '" class="form-control quantity_input" value="' + rel1 + '" />';
    $('#quantity' + rel).html(html);

    var inputElement = $('#quantity_input' + rel);
    inputElement.focus().select();

    // Détecter l'appui sur la touche Enter dans le champ texte
    inputElement.on('blur', function() {
        var newQuantity = $(this).val(); // Récupérer la nouvelle valeur
    //    var spanClass = '';

        // Vérifier la valeur et appliquer les classes appropriées
      /*  if (newQuantity <= 0) {
            spanClass = 'label label-danger';
        } else if (newQuantity <= 5) {
            spanClass = 'label label-warning';
        } else {
            spanClass = 'label label-success';
        }*/

        // Remplacer l'input par un span avec la nouvelle valeur et la classe appropriée
       
     //   $('#quantity' + rel).html('<span class="' + spanClass + '">' + newQuantity + '</span>');
      

        updateQuantity('quantity',rel,newQuantity);
        updateTotalQuantity(rel);
        // Appeler la fonction pour confirmer la nouvelle quantité si besoin
      //  confirmUnallocatedQuantity(rel, newQuantity, quantity, marketplace_item_id);
    });

     // Mettre à jour l'attribut 'rel1' en temps réel lors de la modification
     inputElement.on('input', function() {
        $(this).closest('.pedit-quantity').attr('rel1', $(this).val());
    });
});

function updateTotalQuantity(rel) {
    // Récupérer les valeurs de quantity et unallocated_quantity en convertissant en nombre
    var quantity = parseInt($('#quantity' + rel).text()) || 0;
    var unallocatedQuantity = parseInt($('#unallocated_quantity' + rel).text()) || 0;

    var spanClass = '';
    // Calculer la nouvelle valeur pour total_quantity
    var totalQuantity = quantity + unallocatedQuantity;

    // Déterminer la classe à appliquer en fonction de la valeur de totalQuantity (Bootstrap 5)
    if (totalQuantity <= 0) {
        spanClass = 'badge bg-warning';
    } else if (totalQuantity <= 5) {
        spanClass = 'badge bg-danger';
    } else {
        spanClass = 'badge bg-success';
    }

    // Mettre à jour le contenu et les classes de #total_quantity sans superposition
    $('#total_quantity' + rel)
        .removeClass('badge bg-danger bg-success bg-warning bg-primary bg-info')
        .html('<span class="' + spanClass + '">' + totalQuantity + '</span>');
}

function updateQuantity(element, rel, quantity) {
    var spanClass = '';

    // Déterminer la classe à appliquer en fonction de la valeur de totalQuantity (Bootstrap 5)
    if (quantity <= 0) {
        spanClass = 'badge bg-warning';
    } else if (quantity <= 5) {
        spanClass = 'badge bg-danger';
    } else {
        spanClass = 'badge bg-success';
    }

    // Mettre à jour le contenu et les classes sans superposition
    $('#' + element + rel)
        .removeClass('badge bg-danger bg-success bg-warning bg-primary bg-info')
        .html('<span class="' + spanClass + '">' + quantity + '</span>');
     $('#' + element + '_hid' + rel).val(quantity);
}

// ============================================
// AI COUNTRY DETECTION FUNCTIONS
// ============================================

function showCountryPopupForScan(productId, row, callback) {
    const productName = row.querySelector('td:nth-child(4)').textContent.trim();
    const productSku = row.querySelector('td:nth-child(3)').textContent.trim(); // 3ème colonne = SKU
    
    // Get the select from the row
    const countrySelect = document.getElementById('input-made-in-country-id-' + productId);
    if (!countrySelect) {
        console.error('Country select not found for product ' + productId);
        return;
    }
    
    // If auto-accept is enabled, don't show modal - wait for AI and auto-apply
    if (autoAcceptAICountry) {
        // Store callback
        window.scanCallback = callback;
        
        // Call AI to get country suggestion and auto-apply
        
        fetch('index.php?route=warehouse/tools/ai.getMadeInCountry&user_token=' + USER_TOKEN, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            
            // Check if AI returned success with country data
            if (data.success && data.success.country_id && data.success.country_id > 0) {
                
                // Auto-apply the country
                countrySelect.value = data.success.country_id;
                
                // Save to database via AJAX
                fetch('index.php?route=warehouse/product/product.editMadeInCountry&user_token=' + USER_TOKEN, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId + '&made_in_country_id=' + data.success.country_id
                })
                .then(response => response.json())
                .then(json => {
                    if (json.success) {
                        const cell = document.getElementById('check-made-in-country-id-' + productId);
                        if (cell) {
                            cell.style.setProperty('background-color', 'green', 'important');
                        }
                        
                        // Call callback to continue scan
                        if (window.scanCallback) {
                            window.scanCallback();
                            window.scanCallback = null;
                        }
                        
                        playSuccessSound();
                    } else {
                        console.error('Error saving country:', json.error);
                        playErrorSound();
                    }
                })
                .catch(error => {
                    console.error('Error saving country:', error);
                    playErrorSound();
                });
            } else {
                // AI couldn't determine - show modal anyway
                showModalWithAI(productId, row, callback, productName, productSku, countrySelect, null);
            }
        })
        .catch(error => {
            console.error('Error calling AI:', error);
            // Show modal on error
            showModalWithAI(productId, row, callback, productName, productSku, countrySelect, null);
        });
        
        return; // Exit early in auto-accept mode
    }
    
    // Normal mode: show modal with AI suggestion
    showModalWithAI(productId, row, callback, productName, productSku, countrySelect, null);
}

// Helper function to show the modal with AI loading
function showModalWithAI(productId, row, callback, productName, productSku, countrySelect, preloadedData) {
    // Play warning sound
    playWarningSound();
    
    // Clone the select options for the modal
    const modalSelect = document.getElementById('scan-country-select');
    if (modalSelect) {
        modalSelect.innerHTML = countrySelect.innerHTML;
        modalSelect.value = '0'; // Reset to default
    }
    
    // Update modal content
    document.getElementById('scan-product-info').innerHTML = '<strong>SKU:</strong> ' + productSku + '<br><strong>Name:</strong> ' + productName;
    
    // Hide AI result and show loader
    document.getElementById('scan-ai-result').style.display = 'none';
    document.getElementById('scan-ai-loader').style.display = 'block';
    
    // Store product info in modal data
    const modal = document.getElementById('scanCountryModal');
    modal.dataset.productId = productId;
    modal.dataset.callback = 'scanCallback';
    
    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Store callback
    window.scanCallback = callback;
    
    // Call AI to get country suggestion
    
    fetch('index.php?route=warehouse/tools/ai.getMadeInCountry&user_token=' + USER_TOKEN, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => {
        return response.json();
    })
    .then(data => {
        
        // Hide loader
        document.getElementById('scan-ai-loader').style.display = 'none';
        
        // Check if AI returned success with country data
        if (data.success && data.success.country_id && data.success.country_id > 0) {
            // AI found a country
            modalSelect.value = data.success.country_id;
            
            // Show AI result with reasoning
            const aiResult = document.getElementById('scan-ai-result');
            const aiReasoning = document.getElementById('scan-ai-reasoning');
            aiReasoning.innerHTML = data.success.reasoning || 'AI determined this country based on product information.';
            aiResult.style.display = 'block';
        } else {
            // AI couldn't determine or error
        }
    })
    .catch(error => {
        console.error('Error calling AI:', error);
        document.getElementById('scan-ai-loader').style.display = 'none';
    });
}

// ============================================
// RELATIVE TIME FORMATTING
// ============================================
function formatRelativeTime(timestamp) {
    if (!timestamp) return '-';
    
    const now = new Date();
    const date = new Date(timestamp);
    const diffMs = now - date;
    const diffSec = Math.floor(diffMs / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffHour = Math.floor(diffMin / 60);
    const diffDay = Math.floor(diffHour / 24);
    
    if (diffSec < 60) {
        return TEXT_JUST_NOW;
    } else if (diffMin < 60) {
        return TEXT_MINUTES_AGO.replace('%d', diffMin).replace('%s', diffMin > 1 ? 's' : '');
    } else if (diffHour < 24) {
        return TEXT_HOURS_AGO.replace('%d', diffHour).replace('%s', diffHour > 1 ? 's' : '');
    } else if (diffDay < 7) {
        return TEXT_DAYS_AGO.replace('%d', diffDay).replace('%s', diffDay > 1 ? 's' : '');
    } else if (diffDay < 30) {
        const weeks = Math.floor(diffDay / 7);
        return TEXT_WEEKS_AGO.replace('%d', weeks).replace('%s', weeks > 1 ? 's' : '');
    } else if (diffDay < 365) {
        const months = Math.floor(diffDay / 30);
        return TEXT_MONTHS_AGO.replace('%d', months).replace('%s', months > 1 ? 's' : '');
    } else {
        const years = Math.floor(diffDay / 365);
        return TEXT_YEARS_AGO.replace('%d', years).replace('%s', years > 1 ? 's' : '');
    }
}

function updateRelativeTimes() {
    document.querySelectorAll('.relative-time').forEach(function(elem) {
        const timestamp = elem.dataset.timestamp;
        if (timestamp) {
            elem.textContent = formatRelativeTime(timestamp);
        }
    });
}

// ============================================
// HANDLE COUNTRY CHANGE WITH AJAX SAVE (from Twig)
// ============================================
function editMadeInCountry(product_id) {
    var selectedCountry = document.getElementById('input-made-in-country-id-' + product_id).value;
    
    if (!selectedCountry || selectedCountry == '0' || selectedCountry == '') {
        return; // Don't save empty selection
    }
    
    $.ajax({
        url: 'index.php?route=warehouse/product/product.editMadeInCountry&user_token=' + USER_TOKEN,
        method: 'POST',
        data: {
            product_id: product_id,
            made_in_country_id: selectedCountry
        },
        dataType: 'json',
        success: function (json) {
            if (json['success']) {
                var cell = document.getElementById('check-made-in-country-id-' + product_id);
                if (cell) {
                    cell.style.setProperty('background-color', 'green', 'important');
                }
                playSuccessSound();
            } else {
                alert(TEXT_AJAX_ERROR.replace('%s', json['error'] || 'Unknown error'));
                playErrorSound();
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.error('AJAX Error:', xhr.responseText);
            alert(TEXT_ERROR_OCCURRED.replace('%s', thrownError));
            playErrorSound();
        }
    });
}

// ============================================
// HANDLE APPLY BUTTON IN COUNTRY MODAL (from Twig)
// ============================================
$(document).ready(function() {
    $('#scan-apply-country').on('click', function() {
        var selectedCountry = $('#scan-country-select').val();
        var modal = document.getElementById('scanCountryModal');
        var productId = modal.dataset.productId;
        
        if (!selectedCountry || selectedCountry == '0') {
            alert(TEXT_PLEASE_SELECT_COUNTRY);
            return;
        }
        
        // Check if auto-accept checkbox is checked and update session variable
        var autoAcceptCheckbox = document.getElementById('auto-accept-ai-country');
        if (autoAcceptCheckbox && autoAcceptCheckbox.checked) {
            window.autoAcceptAICountry = true;
        }
        
        // Update the country select in the row
        var countrySelect = document.getElementById('input-made-in-country-id-' + productId);
        if (countrySelect) {
            countrySelect.value = selectedCountry;
            
            // Save to database via AJAX
            $.ajax({
                url: 'index.php?route=warehouse/product/product.editMadeInCountry&user_token=' + USER_TOKEN,
                method: 'POST',
                data: {
                    product_id: productId,
                    made_in_country_id: selectedCountry
                },
                dataType: 'json',
                success: function (json) {
                    if (json['success']) {
                        var cell = document.getElementById('check-made-in-country-id-' + productId);
                        if (cell) {
                            cell.style.setProperty('background-color', 'green', 'important');
                        }
                        
                        // Close modal
                        var bsModal = bootstrap.Modal.getInstance(modal);
                        bsModal.hide();
                        
                        // Call callback to continue scan
                        if (window.scanCallback) {
                            window.scanCallback();
                            window.scanCallback = null;
                        }
                        
                        // Return focus to SKU input
                        var skuInput = document.getElementById('input-sku');
                        if (skuInput) {
                            setTimeout(function() {
                                skuInput.focus();
                            }, 300);
                        }
                    } else {
                        alert(TEXT_AJAX_ERROR.replace('%s', json['error'] || 'Unknown error'));
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.error('AJAX Error:', xhr.responseText);
                    alert(TEXT_ERROR_OCCURRED.replace('%s', thrownError));
                }
            });
        }
    });
    
    // Handle country select change with event delegation
    $(document).on('change', '.made-in-country-select', function() {
        var productId = $(this).data('product-id');
        editMadeInCountry(productId);
    });
});
