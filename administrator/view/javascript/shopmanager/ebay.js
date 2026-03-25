// ebay.js

function edit() {
    var user_token = document.querySelector('input[name="user_token"]').value;
    const product_id = document.getElementById('product-id').value;

    var formData = $('#form-product' + product_id).serialize();

    $.ajax({
        url: `index.php?route=shopmanager/catalog/product.updateProduct&user_token=${user_token}`,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $.ajax({
                    url: `index.php?route=shopmanager/ebay.edit&user_token=${user_token}`,
                    type: 'POST',
                    data: { product_id: product_id },
                    dataType: 'json',
                    success: function(response) {
                      //  alert('Listing eBay mis à jour avec succès!');
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur lors de la mise à jour du listing eBay:', error);
                        alert(lang.error_update_ebay);
                    }
                });
            } else {
                alert(lang.error_update_product);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur lors de la mise à jour du produit:', error);
            alert(lang.error_update_product);
        }
    });
}


function editEbayPrice(marketplace_item_id,price) {
    var user_token = document.querySelector('input[name="user_token"]').value;
    const product_id = document.getElementById('product-id').value;

//    var marketplace_item_id = document.querySelector('input[name="marketplace_item_id"]').value;
    var countrySelect = document.getElementById('input-made-in-country-id');
    if (countrySelect) {
        var country_id = countrySelect.value;
    } else {
        console.error("L'élément select avec id='input-made-in-country-id-" + product_id + "' n'existe pas.");
    }

    var formData = $('#form-product'  + product_id).serialize();

                $.ajax({
                    url: `index.php?route=shopmanager/ebay.editPrice&user_token=${user_token}`,
                    type: 'POST',
                    data: { product_id: product_id, marketplace_item_id: marketplace_item_id, price: price },
                    dataType: 'json',
                    success: function(response) {
                    //    alert('Listing eBay mis à jour avec succès!');
                        handleEbayAddUIUpdate(response,marketplace_item_id);
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur lors de la mise à jour du listing eBay:', error);
                        alert(lang.error_update_ebay + ': ' + error);
                    }
                });
           
}

function removeFromEbay(marketplace_item_id) {
    var user_token = document.querySelector('input[name="user_token"]').value;
    const product_id = document.getElementById('product-id').value;

    var site_id = document.querySelector('input[name="site_id"]').value;


    if (marketplace_item_id && confirm(lang.confirm_are_you_sure)) {
        $.ajax({
            url: `index.php?route=shopmanager/ebay.endListing&user_token=${user_token}`,
            type: 'POST',
            data: { marketplace_item_id: marketplace_item_id, product_id: product_id,site_id: site_id },
            dataType: 'json',
            success: function(json) {
                if (json.error) {
                    alert(json.message);
                } else {
                    handleEbayRemovalUIUpdate(json);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(lang.error_occurred + ': ' + xhr.responseText);
            }
        });
    }
}

function relistToEbay(marketplace_item_id) {
    var user_token = document.querySelector('input[name="user_token"]').value;
    const product_id = document.getElementById('product-id').value;

    var site_id = document.querySelector('input[name="site_id"]').value;
    
    if (marketplace_item_id) {
        $.ajax({
            url: `index.php?route=shopmanager/ebay.relist&user_token=${user_token}`,
            type: 'POST',
            data: { marketplace_item_id: marketplace_item_id, product_id: product_id, site_id: site_id },
            dataType: 'json',
            success: function(json) {
                if (json.error) {
                    alert(json.message);
                } else {
                    handleEbayRelistUIUpdate(json);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(lang.error_occurred + ': ' + xhr.responseText);
            }
        });
    }
}


function addToEbay() {
    var user_token = document.querySelector('input[name="user_token"]').value;
    const product_id = document.getElementById('product-id').value;

    var site_id = document.querySelector('input[name="site_id"]').value;
    var quantity = document.querySelector('input[name="quantity"]').value;
    var unallocated_quantity = document.querySelector('input[name="unallocated_quantity"]').value;
        $.ajax({
            url: `index.php?route=shopmanager/ebay.add&user_token=${user_token}`,
            type: 'POST',
            data: {  product_id: product_id ,quantity: quantity,  unallocated_quantity: unallocated_quantity,site_id:site_id},
            dataType: 'json',
            success: function(json) {
                if (json.error) {
                    alert(json.message);
                } else {
                    handleEbayAddUIUpdate(json);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(lang.error_occurred + ': ' + xhr.responseText);
            }
        });
    
}

function handleEbayRemovalUIUpdate(json) {
    // Mettre à jour l'interface utilisateur après la suppression d'une annonce eBay
    var hiddenInput = document.getElementById('input-marketplace-account-id-account-id-item-id');
    var valueInput = document.getElementById('label-marketplace-item-id');
    var removeBtn = document.getElementById('remove-from-ebay-btn');
    var addBtn = document.getElementById('add-to-ebay-btn');
    var updateBtn = document.getElementById('update-ebay-btn');
    var relistBtn = document.getElementById('relist-to-ebay-btn');

    if (json.message === 'The auction has already been closed.') {
        alert(json.message);
        if (removeBtn) removeBtn.style.display = 'none';
        if (updateBtn) updateBtn.style.display = 'none';
        if (relistBtn) relistBtn.style.display = 'inline-block';
    } else {
        hiddenInput.value = '';
        valueInput.innerHTML = '';
        valueInput.style.display = 'none';
        if (removeBtn) removeBtn.style.display = 'none';
        if (updateBtn) updateBtn.style.display = 'none';
        if (relistBtn) relistBtn.style.display = 'none';
        if (addBtn) addBtn.style.display = 'inline-block';
    }
}

function handleEbayRelistUIUpdate(json) {
    // Mettre à jour l'interface utilisateur après la remise en ligne d'une annonce eBay
    var hiddenInput = document.getElementById('input-marketplace-account-id-account-id-item-id');
    var valueInput = document.getElementById('label-marketplace-item-id');
    var removeBtn = document.getElementById('remove-from-ebay-btn');
    var addBtn = document.getElementById('add-to-ebay-btn');
    var relistBtn = document.getElementById('relist-to-ebay-btn');
    var updateBtn = document.getElementById('update-ebay-btn');

    if (json.marketplace_item_id) {
        hiddenInput.value = json.marketplace_item_id;
        valueInput.innerHTML = json.marketplace_item_id;
        valueInput.style.display = 'inline-block';
        if (removeBtn) removeBtn.style.display = 'inline-block';
        if (updateBtn) updateBtn.style.display = 'inline-block';
        if (addBtn) addBtn.style.display = 'none';
        if (relistBtn) relistBtn.style.display = 'none';
    }
}

function handleEbayAddUIUpdateOLD(json, marketplace_item_id) {
    // Mettre à jour l'interface utilisateur après la remise en ligne d'une annonce eBay
    var hiddenInput = document.getElementById('input-marketplace-account-id-account-id-item-id');
    var valueInput = document.getElementById('label-marketplace-item-id');
    var removeBtn = document.getElementById('remove-from-ebay-btn');
    var addBtn = document.getElementById('add-to-ebay-btn');
    var relistBtn = document.getElementById('relist-to-ebay-btn');
    var updateBtn = document.getElementById('update-ebay-btn');

    if (json.marketplace_item_id !== marketplace_item_id) {
        hiddenInput.value = json.marketplace_item_id;
        valueInput.innerHTML = json.marketplace_item_id;
        valueInput.style.display = 'inline-block';
        if (removeBtn) removeBtn.style.display = 'inline-block';
        if (updateBtn) updateBtn.style.display = 'inline-block';
        if (addBtn) addBtn.style.display = 'none';
        if (relistBtn) relistBtn.style.display = 'none';
    }
}

function handleEbayAddUIUpdate(json, marketplace_item_id) {
    // Construire dynamiquement l'ID de l'élément en utilisant marketplace_item_id
    var hiddenInputId = 'marketplace_items_' + marketplace_item_id;
    var hiddenInput = document.getElementById(hiddenInputId);

    if (hiddenInput) {
        // Mettre à jour la valeur de l'input caché uniquement si différent
        if (json.marketplace_item_id !== marketplace_item_id) {
            hiddenInput.value = JSON.stringify(json);
        }

        // Sélectionner le bouton de suppression associé
        var removeBtn = hiddenInput.nextElementSibling;
        if (removeBtn && removeBtn.classList.contains('btn-danger')) {
            removeBtn.style.display = 'inline-block';
        }

        // Sélectionner le bouton de mise à jour associé
        var updateBtn = removeBtn.nextElementSibling;
        if (updateBtn && updateBtn.classList.contains('btn-info')) {
            updateBtn.style.display = 'inline-block';
        }

        // Masquer le bouton d'ajout
        var addBtn = updateBtn.nextElementSibling;
        if (addBtn && addBtn.classList.contains('btn-success')) {
            addBtn.style.display = 'none';
        }

        // Masquer le bouton de remise en vente
        var relistBtn = addBtn.nextElementSibling;
        if (relistBtn && relistBtn.classList.contains('btn-info')) {
            relistBtn.style.display = 'none';
        }
    } else {
        console.warn('Élément avec l\'ID', hiddenInputId, 'non trouvé.');
    }

}
