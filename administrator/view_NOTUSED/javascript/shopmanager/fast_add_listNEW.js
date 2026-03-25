// Assurez-vous que le DOM est prêt
document.addEventListener('DOMContentLoaded', function () {
    var token = document.querySelector('input[name="token"]').value;
    const searchButton = document.querySelector('.btn.btn-warning i.fa-search').parentNode;

    searchButton.addEventListener('click', function (event) {
        $('#loadingModal').modal('show');
        event.preventDefault();

        const checkedCheckboxes = document.querySelectorAll('input[type="checkbox"][name^="selected"]:checked');
        let selectedProducts = [];

        checkedCheckboxes.forEach(function(checkbox) {
            selectedProducts.push(checkbox.value);
        });

        if (selectedProducts.length === 0) {
            alert('Veuillez sélectionner au moins un produit.');
            $('#loadingModal').modal('hide');
            return;
        }

        const chunkSize = 5;
        let productChunks = [];
        for (let i = 0; i < selectedProducts.length; i += chunkSize) {
            productChunks.push(selectedProducts.slice(i, i + chunkSize));
        }

        function sendProductChunk(productChunk) {
            let data = {
                product_ids: productChunk
            };

            return fetch('index.php?route=shopmanager/fast_add/product_source_info_feed&token=' + token, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Réponse pour les produits:', productChunk);
                console.log(data);
            })
            .catch(error => {
                console.error('Erreur lors de l\'appel AJAX:', error);
            });
        }

        async function sendProductChunksSequentially() {
            for (let chunk of productChunks) {
                await sendProductChunk(chunk);
            }
            $('#loadingModal').modal('hide');
            location.reload();
        }

        sendProductChunksSequentially();
    });
});

function transferUnallocatedQuantity(productId) {
    var token = document.querySelector('input[name="token"]').value;
    var quantity = parseInt($('tr[data-product-id="' + productId + '"] .quantity span').text());
    var unallocatedQuantity = parseInt($('tr[data-product-id="' + productId + '"] .unallocated_quantity span').text());
    var location = $('tr[data-product-id="' + productId + '"] .location').text().trim();

    console.log('quantity: ' + quantity);
    console.log('unallocatedQuantity: ' + unallocatedQuantity);
    console.log('location: ' + location);

    if (quantity === 0) {
        var html = '<input type="text" id="location_input' + productId + '" class="form-control location_input" value="' + location + '" placeholder="Entrez la localisation" />';
        $('#location' + productId).html(html);

        $('#location_input' + productId).focus().select();

        $('#location_input' + productId).on('keydown', function(event) {
            if (event.key === 'Enter' || event.keyCode === 13 || event.which === 13) {
                event.preventDefault();
                event.stopPropagation();

                var newLocation = $('#location_input' + productId).val().trim();

                if (newLocation === '') {
                    alert('La localisation ne peut pas être vide.');
                } else {
                    $('#quantity' + productId + ' span').text(quantity);
                    $('#unallocated_quantity' + productId + ' span').text(0);
                    $('#location' + productId).html('<span class="pedit-location" rel="' + productId + '" rel1="' + newLocation + '">' + newLocation + '</span>');

                    $.ajax({
                        url: 'index.php?route=shopmanager/catalog/product.trfUnallocatedQuantity&token=' + token,
                        type: 'post',
                        data: {
                            product_id: productId,
                            unallocated_quantity: unallocatedQuantity,
                            quantity: quantity,
                            location: newLocation
                        },
                        success: function(response) {
                            if (response.success) {
                                console.log('Transfert effectué avec succès.');
                                var unallocatedClass  = 'label label-warning';
            
                                var unallocatedSpan = '<span id="unallocated_quantity' + productId + '" class="pedit-unallocated-quantity ' + unallocatedClass + '" rel="' + productId + '" rel1="' + 0 + '">' + 0 + '</span>';
                                $('#unallocated_quantity' + productId).replaceWith(unallocatedSpan);
                                var quantityClass = '';
                                newQuantity = unallocatedQuantity + quantity
                                if (newQuantity <= 0) {
                                    quantityClass = 'label label-warning';
                                } else if (newQuantity <= 5) {
                                    quantityClass = 'label label-danger';
                                } else {
                                    quantityClass = 'label label-success';
                                }
                                var quantitySpan = '<span id="quantity' + productId + '" class="pedit-quantity ' + quantityClass + '" rel="' + productId + '" rel1="' + newQuantity + '">' + newQuantity + '</span>';
                                $('#quantity' + productId).replaceWith(quantitySpan);

                            } else {
                                alert('Erreur lors de la mise à jour : ' + response);
                            }
                        },
                        error: function() {
                            alert('Erreur lors de l\'appel à l\'API');
                        }
                    });
                }
            }
        });
    } else {
        if (location !== '') {
            if (confirm('Confirmer le transfert de quantité ?')) {
                $('#quantity' + productId + ' span').text(quantity);
                $('#unallocated_quantity' + productId + ' span').text(0);

                $.ajax({
                    url: 'index.php?route=shopmanager/catalog/product.trfUnallocatedQuantity&token=' + token,
                    type: 'post',
                    data: {
                        product_id: productId,
                        unallocated_quantity: unallocatedQuantity,
                        quantity: quantity,
                        location: location
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Transfert effectué avec succès.');

                            var unallocatedClass  = 'label label-warning';
            
                            var unallocatedSpan = '<span id="unallocated_quantity' + productId + '" class="pedit-unallocated-quantity ' + unallocatedClass + '" rel="' + productId + '" rel1="' + 0 + '">' + 0 + '</span>';
                            $('#unallocated_quantity' + productId).replaceWith(unallocatedSpan);

                            var newQuantity = unallocatedQuantity + quantity;
                            var quantityClass = '';
                            if (newQuantity <= 0) {
                                quantityClass = 'label label-warning';
                            } else if (newQuantity <= 5) {
                                quantityClass = 'label label-danger';
                            } else {
                                quantityClass = 'label label-success';
                            }
                            var quantitySpan = '<span id="quantity' + productId + '" class="pedit-quantity ' + quantityClass + '" rel="' + productId + '" rel1="' + newQuantity + '">' + newQuantity + '</span>';
                            $('#quantity' + productId).replaceWith(quantitySpan);
                        
                        } else {
                            alert('Erreur lors de la mise à jour.');
                        }
                    },
                    error: function() {
                        alert('Erreur lors de l\'appel à l\'API');
                    }
                });
            }
        } else {
            alert('La localisation ne peut pas être vide.');
        }
    }
}

$(document).on('click', '.pedit-unallocated-quantity', function(event) {
    event.preventDefault();

    var rel = $(this).attr('rel');
    var rel1 = $(this).attr('rel1');
    var quantity = parseInt($('tr[data-product-id="' + rel + '"] .quantity span').text());
    var marketplace_item_id = parseInt($('tr[data-product-id="' + rel + '"] .marketplace_item_id span').text());
    $(this).removeClass('label label-danger');

    var html = '<input type="text" id="unallocated_quantity_input' + rel + '" class="form-control unallocated_quantity_input" value="' + rel1 + '" />';
    $('#unallocated_quantity' + rel).html(html);

    var inputElement = $('#unallocated_quantity_input' + rel);
    inputElement.focus().select();

    inputElement.on('change', function() {
        var newUnallocatedQuantity = $(this).val();
        confirmUnallocatedQuantity(rel, newUnallocatedQuantity, quantity, marketplace_item_id);
    });

    inputElement.on('input', function() {
        $(this).closest('.pedit-unallocated-quantity').attr('rel1', $(this).val());
    });
});

$(document).on('click', '.pedit-location', function(event) {
    event.preventDefault();

    var rel = $(this).attr('rel');
    var rel1 = $(this).attr('rel1');

    var html = '<input type="text" id="location_input' + rel + '" class="form-control location_input" value="' + rel1 + '" />';
    $('#location' + rel).html(html);

    $('#location_input' + rel).focus().select();

    $('#location_input' + rel).on('keydown', function(event) {
        if (event.key === 'Enter' || event.keyCode === 13 || event.which === 13) {
            event.preventDefault();
            event.stopPropagation();

            var newLocation = $(this).val().trim();
            confirmLocation(rel, newLocation);
        }
    });

    $('#location_input' + rel).on('input', function() {
        $(this).closest('.pedit-location').attr('rel1', $(this).val());
    });
});

$(document).on('click', '.pedit-quantity', function(event) {
    event.preventDefault();

    var rel = $(this).attr('rel');
    var rel1 = $(this).attr('rel1');
    var unallocatedQuantity = parseInt($('tr[data-product-id="' + rel + '"] .unallocated_quantity span').text());
    var marketplace_item_id = parseInt($('tr[data-product-id="' + rel + '"] .marketplace_item_id span').text());

    var quantity = parseInt(rel1);
    var html = '<input type="text" id="quantity_input' + rel + '" class="form-control quantity_input" value="' + quantity + '" />';
    $('#quantity' + rel).html(html);
    
    $('#quantity_input' + rel).focus().select();
    
    $('#quantity_input' + rel).on('change', function() {
        var finalQuantity = $(this).val();
        confirmQuantity(rel, finalQuantity, unallocatedQuantity, marketplace_item_id);
    });

    $('#quantity_input' + rel).on('input', function() {
        $(this).closest('.pedit-quantity').attr('rel1', $(this).val());
    });
});

function confirmUnallocatedQuantity(productId, newUnallocatedQuantity, currentQuantity, marketplace_item_id) {
    var token = document.querySelector('input[name="token"]').value;

    if (newUnallocatedQuantity === '' || isNaN(newUnallocatedQuantity)) {
        alert('Veuillez entrer une quantité non allouée valide.');
        return;
    }

    console.log('product_id: ' + productId);
    console.log('unallocated_quantity: ' + newUnallocatedQuantity);
    console.log('quantity: ' + currentQuantity);
    console.log('marketplace_item_id: ' + marketplace_item_id);

    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product.updateUnallocatedQuantity&token=' + token,
        type: 'post',
        data: {
            product_id: productId,
            unallocated_quantity: newUnallocatedQuantity,
            quantity: currentQuantity,
            marketplace_item_id: marketplace_item_id
        },
        success: function(response) {
            if (response.success) {
                var unallocatedClass = '';
                if (newUnallocatedQuantity <= 0) {
                    unallocatedClass = 'label label-warning';
                } else if (newUnallocatedQuantity <= 5) {
                    unallocatedClass = 'label label-danger';
                } else {
                    unallocatedClass = 'label label-success';
                }
                var unallocatedSpan = '<span id="unallocated_quantity' + productId + '" class="pedit-unallocated-quantity ' + unallocatedClass + '" rel="' + productId + '" rel1="' + newUnallocatedQuantity + '">' + newUnallocatedQuantity + '</span>';
                $('#unallocated_quantity' + productId).replaceWith(unallocatedSpan);
            } else {
                alert('Erreur lors de la mise à jour de la quantité.');
            }
        },
        error: function() {
            alert('Erreur lors de l\'appel à l\'API.');
        }
    });
}

function confirmLocation(productId, newLocation) {
    var token = document.querySelector('input[name="token"]').value;

    if (newLocation === '') {
        alert('La localisation ne peut pas être vide.');
        return;
    }

    console.log('product_id: ' + productId);
    console.log('location: ' + newLocation);

    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product.updateProductLocation&token=' + token,
        type: 'post',
        data: {
            product_id: productId,
            location: newLocation
        },
        success: function(response) {
            if (response.success) {
                var locationSpan = '<span id="location' + productId + '" class="pedit-location" rel="' + productId + '" rel1="' + newLocation + '">' + newLocation + '</span>';
                $('#location' + productId).replaceWith(locationSpan);
            } else {
                alert('Erreur lors de la mise à jour de la localisation.');
            }
        },
        error: function() {
            alert('Erreur lors de l\'appel à l\'API.');
        }
    });
}

function confirmQuantity(productId, finalQuantity, unallocatedQuantity, marketplace_item_id) {
    var token = document.querySelector('input[name="token"]').value;

    if (finalQuantity === '' || isNaN(finalQuantity)) {
        alert('Veuillez entrer une quantité valide.');
        return;
    }

    console.log('product_id: ' + productId);
    console.log('unallocated_quantity: ' + unallocatedQuantity);
    console.log('quantity: ' + finalQuantity);
    console.log('marketplace_item_id: ' + marketplace_item_id);

    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product.updateQuantity&token=' + token,
        type: 'post',
        data: {
            product_id: productId,
            quantity: finalQuantity,
            unallocated_quantity : unallocatedQuantity,
            marketplace_item_id: marketplace_item_id
        },
        success: function(response) {
            if (response.success) {
                var quantityClass = '';
                if (finalQuantity <= 0) {
                    quantityClass = 'label label-warning';
                } else if (finalQuantity <= 5) {
                    quantityClass = 'label label-danger';
                } else {
                    quantityClass = 'label label-success';
                }
                var quantitySpan = '<span id="quantity' + productId + '" class="pedit-quantity ' + quantityClass + '" rel="' + productId + '" rel1="' + finalQuantity + '">' + finalQuantity + '</span>';
                $('#quantity' + productId).replaceWith(quantitySpan);
            } else {
                alert(response + 'Erreur lors de la mise à jour de la quantité.');
            }
        },
        error: function() {
            alert('Erreur lors de l\'appel à l\'API.');
        }
    });
}

function handleDelete(productId) {
    checkProduct(productId);
    if (confirm(confirmMessage)) {
        $('#form-product'  + productId).submit();
    }
}

function handleEnable(productId) {
    if(productId){
    checkProduct(productId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-product' + productId);
        form.action = enableUrl;
        form.submit();
    }
}

function handleDisable(productId) {
    if(productId){
    checkProduct(productId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-product' + productId);
        form.action = disableUrl;
        form.submit();
    }
}

function handleCopy(productId) {
    if(productId){
    checkProduct(productId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-product' + productId);
        form.action = copyUrl;
        form.submit();
    }
}

function checkProduct(productId) {
    var checkbox = $('input[name="selected[' + productId + ']"]');
    checkbox.prop('checked', !checkbox.prop('checked'));
}

document.addEventListener("DOMContentLoaded", function () {
    $('#search-form input, #search-form select').on('change', function() {
        var currentInput = $(this);
        
        $('#search-form input, #search-form select').each(function() {
            if ($(this).attr('name') !== currentInput.attr('name') 
                && $(this).attr('id') !== 'input-status' 
                && $(this).attr('id') !== 'input-image' 
                && $(this).attr('id') !== 'input-limit') {
                $(this).val('');
            }
        });
        
        $('#button-filter').click();
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const select = document.querySelector('#input-limit');
    select.addEventListener('change', function () {
        const selectedValue = select.value;
        const newUrl = limitLink.replace('{page}', '1').replace('&limit=', '&limit=' + selectedValue);
        window.location.href = newUrl;
    });
});

document.addEventListener("DOMContentLoaded", function () {
    let showMore = false;

    document.getElementById('toggle-button').addEventListener('click', function () {
        const button = this;
        const additionalFields = document.getElementById('additional-fields');
        
        showMore = !showMore;

        if (showMore) {
            additionalFields.classList.add('show');
            button.innerHTML = '<i class="fa fa-chevron-up"></i> Show Less';
        } else {
            additionalFields.classList.remove('show');
            button.innerHTML = '<i class="fa fa-chevron-down"></i> Show More';
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    function initAutocomplete(inputName, filterType) {
        var token = document.querySelector('input[name="token"]').value;

        var $inputSku = $('#input-sku');

        if ($inputSku.length && $inputSku.val()) {
            $inputSku.focus().select();
        }

        $('input[name=\'' + inputName + '\']').autocomplete({
            'source': function(request, response) {
                $.ajax({
                    url: 'index.php?route=shopmanager/catalog/product.autocomplete&token=' + token + '&' + filterType + '=' + encodeURIComponent(request),
                    dataType: 'json',
                    success: function(json) {
                        response($.map(json, function(item) {
                            if (item.hasOwnProperty('name') && item.hasOwnProperty('product_id')) {
                                return {
                                    label: item['name'],
                                    value: item['product_id']
                                };
                            } else if (item.hasOwnProperty('model') && item.hasOwnProperty('product_id')) {
                                return {
                                    label: item['model'],
                                    value: item['product_id']
                                };
                            } else {
                                return null;
                            }
                        }).filter(Boolean));
                    }
                });
            },
           'select': function(item) {
                if (item && item.label) {
                    $('input[name=\'' + inputName + '\']').val(item['label']);
                }
            }
        });
    }

    initAutocomplete('filter_sku', 'filter_sku');
    initAutocomplete('filter_product_id', 'filter_product_id');
    initAutocomplete('filter_name', 'filter_name');
    initAutocomplete('filter_category_id', 'filter_category_id');
    initAutocomplete('filter_model', 'filter_model');
});


