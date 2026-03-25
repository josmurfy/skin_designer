// ebay.js

function edit() {
    var token = document.querySelector('input[name="token"]').value;
    var productId = document.querySelector('input[name="product_id"]').value;
    var formData = $('#form-product' + productId).serialize();

    $.ajax({
        url: `index.php?route=shopmanager/catalog/product.updateProduct&token=${token}`,
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $.ajax({
                    url: `index.php?route=shopmanager/ebay.edit&token=${token}`,
                    type: 'POST',
                    data: { product_id: productId },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Listing eBay mis à jour avec succès:', response);
                      //  alert('Listing eBay mis à jour avec succès!');
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur lors de la mise à jour du listing eBay:', error);
                        alert('Erreur lors de la mise à jour du listing eBay. Veuillez réessayer.');
                    }
                });
            } else {
                alert('Erreur lors de la mise à jour du produit. Veuillez réessayer.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur lors de la mise à jour du produit:', error);
            alert('Erreur lors de la mise à jour du produit. Veuillez réessayer.');
        }
    });
}


function editEbayPrice(price) {
    var token = document.querySelector('input[name="token"]').value;
    var productId = document.querySelector('input[name="product_id"]').value;
    var marketplace_item_id = document.querySelector('input[name="marketplace_item_id"]').value;
    var countrySelect = document.getElementById('input-made-in-country_id');
    if (countrySelect) {
        var country_id = countrySelect.value;
        console.log("ID du pays sélectionné : ", country_id);
    } else {
        console.error("L'élément select avec id='input-made-in-country_id-" + product_id + "' n'existe pas.");
    }

    var formData = $('#form-product'  + productId).serialize();
      console.log('productId :' + productId);
      console.log('marketplace_item_id :' + marketplace_item_id);
      console.log('price :' + price);

                $.ajax({
                    url: `index.php?route=shopmanager/ebay.editPrice&token=${token}`,
                    type: 'POST',
                    data: { product_id: productId, marketplace_item_id: marketplace_item_id, price: price },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Listing eBay mis à jour avec succès:', response);
                    //    alert('Listing eBay mis à jour avec succès!');
                        handleEbayAddUIUpdate(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur lors de la mise à jour du listing eBay:', error);
                        alert(error);
                    }
                });
           
}

function removeFromEbay(marketplace_item_id) {
    var token = document.querySelector('input[name="token"]').value;
    var productId = document.querySelector('input[name="product_id"]').value;
    var site_id = document.querySelector('input[name="site_id"]').value;


    if (marketplace_item_id && confirm("Are you sure?")) {
        $.ajax({
            url: `index.php?route=shopmanager/ebay.delete&token=${token}`,
            type: 'POST',
            data: { marketplace_item_id: marketplace_item_id, product_id: productId,site_id: site_id },
            dataType: 'json',
            success: function(json) {
                if (json.error) {
                    alert(json.message);
                } else {
                    handleEbayRemovalUIUpdate(json);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert('An error occurred: ' + xhr.responseText);
            }
        });
    }
}

function relistToEbay(marketplace_item_id) {
    var token = document.querySelector('input[name="token"]').value;
    var productId = document.querySelector('input[name="product_id"]').value;
    var site_id = document.querySelector('input[name="site_id"]').value;
    
    if (marketplace_item_id) {
        $.ajax({
            url: `index.php?route=shopmanager/ebay.relist&token=${token}`,
            type: 'POST',
            data: { marketplace_item_id: marketplace_item_id, product_id: productId, site_id: site_id },
            dataType: 'json',
            success: function(json) {
                if (json.error) {
                    alert(json.message);
                } else {
                    handleEbayRelistUIUpdate(json);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert('An error occurred: ' + xhr.responseText);
            }
        });
    }
}


function addToEbay() {
    var token = document.querySelector('input[name="token"]').value;
    var productId = document.querySelector('input[name="product_id"]').value;
    var site_id = document.querySelector('input[name="site_id"]').value;
    var quantity = document.querySelector('input[name="quantity"]').value;
    var unallocated_quantity = document.querySelector('input[name="unallocated_quantity"]').value;
    console.log('productId :' + productId);
    console.log('quantity :' + quantity);
    console.log('unallocated_quantity :' + unallocated_quantity);
        $.ajax({
            url: `index.php?route=shopmanager/ebay.add&token=${token}`,
            type: 'POST',
            data: {  product_id: productId ,quantity: quantity,  unallocated_quantity: unallocated_quantity,site_id:site_id},
            dataType: 'json',
            success: function(json) {
                if (json.error) {
                    alert(json.message);
                } else {
                    handleEbayAddUIUpdate(json);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert('An error occurred: ' + xhr.responseText);
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
    console.log('message :' + JSON.stringify(json));
}

function handleEbayAddUIUpdate(json) {
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
    console.log('message :' + JSON.stringify(json));
}
