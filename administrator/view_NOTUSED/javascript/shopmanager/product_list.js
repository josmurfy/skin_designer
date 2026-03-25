// Define formatImage at the top of the file
function formatImage(imageUrl, altText) {
    if (!imageUrl) {
        // Return a placeholder image if no image URL is provided
        return '<img src="https://via.placeholder.com/50" alt="No Image" style="width:100; height:auto;">';
    }
    return `<img src="${imageUrl}" alt="${altText}" style="width:100px; height:auto;">`;
}

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

       
        html += '<span>' + data.has_specifics + '</span><br>';
        

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
    //console.log('[DEBUG] madeInCountryId:', madeInCountryId);
    //console.log('[DEBUG] productId:', productId);
    //console.log('[DEBUG] options (raw):', options);

    if (!madeInCountryId || madeInCountryId === '0') {
        console.warn('[DEBUG] No madeInCountryId provided, returning original options.');
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

    // Sauvegarde l'ancienne sélection
    optionList.forEach(option => {
        if (option.selected) {
            oldSelectedValue = option.value;
        }
    });

    optionList.forEach(option => {
        if (option.value === String(madeInCountryId)) {
            option.setAttribute('selected', 'selected');
            found = true;
            console.log('[DEBUG] Matching option found and selected:', option.textContent);
        } else {
            option.removeAttribute('selected');
        }
    });

    // Changer la couleur du <td> associé
    if (productId) {
        const td = document.getElementById('check_made_in_country_id_' + productId);
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
        console.warn('[DEBUG] No matching option found, returning original options.');
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

function transferUnallocatedQuantity(productId) {

    var token = document.querySelector('input[name="token"]').value;

    // Récupérer les valeurs actuelles
    var quantity = parseInt($('tr[data-product-id="' + productId + '"] .quantity span').text());
    var unallocatedQuantity = parseInt($('tr[data-product-id="' + productId + '"] .unallocated_quantity span').text());
    var location = $('tr[data-product-id="' + productId + '"] .location').text().trim();

    console.log('quantity: ' + quantity);
    console.log('unallocatedQuantity: ' + unallocatedQuantity);
    console.log('location: ' + location);

    // Si quantity est 0, il faut valider la location
    if (quantity === 0) {
        //   alert('La quantité est 0, veuillez valider la localisation.');

        // Créer un champ texte pour modifier la localisation
        var html = '<input type="text" id="location_input' + productId + '" class="form-control location_input" value="' + location + '" placeholder="Entrez la localisation" />';
        $('#location' + productId).html(html);

        // Donner le focus au champ texte et sélectionner tout le texte
        $('#location_input' + productId).focus().select();

        // Gérer l'événement 'keydown' sur le champ texte
        $('#location_input' + productId).on('keydown', function (event) {
            if (event.key === 'Enter' || event.keyCode === 13 || event.which === 13) {
                event.preventDefault(); // Empêche l'action par défaut (comme le rechargement de la page)
                event.stopPropagation(); // Empêche la propagation de l'événement

                var newLocation = $('#location_input' + productId).val().trim();

                // Si la nouvelle localisation est vide, afficher une alerte
                if (newLocation === '') {
                    alert('La localisation ne peut pas être vide.');
                } else {
                    // Demander confirmation pour la nouvelle localisation et le transfert
                    //     if (confirm('Confirmer la nouvelle localisation et le transfert ?')) {
                    // Transférer la quantité non allouée vers la quantité

                    // Mettre à jour l'affichage
                    $('#quantity' + productId + ' span').text(quantity);
                    $('#unallocated_quantity' + productId + ' span').text(0);
                    $('#location' + productId).html('<span class="pedit-location" rel="' + productId + '" rel1="' + newLocation + '">' + newLocation + '</span>');

                    // Envoyer les nouvelles données via AJAX pour mettre à jour la base de données
                    $.ajax({
                        url: 'index.php?route=shopmanager/catalog/product.trfUnallocatedQuantity&token=' + token,
                        type: 'post',
                        data: {
                            product_id: productId,
                            unallocated_quantity: unallocatedQuantity,
                            quantity: quantity,
                            location: newLocation
                        },
                        success: function (response) {
                            if (response.success) {
                                console.log('Transfert effectué avec succès.');
                                var unallocatedClass = 'label label-warning';

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
                                //     $('#quantity' + productId).html(finalQuantity);
                                var quantitySpan = '<span id="quantity' + productId + '" class="pedit-quantity ' + quantityClass + '" rel="' + productId + '" rel1="' + newQuantity + '">' + newQuantity + '</span>';
                                $('#quantity' + productId).replaceWith(quantitySpan);

                            } else {
                                alert('Erreur lors de la mise à jour : ' + response);
                            }
                        },
                        error: function () {
                            alert('Erreur lors de l\'appel à l\'API');
                        }
                    });
                    // } else {
                    // Si l'utilisateur annule, remettre l'ancienne valeur dans l'affichage
                    //      $('#location' + productId).html(location);
                    //   }
                }
            }
        });
    } else {
        // Si quantity est > 0 et location est valide, faire le transfert normalement
        if (location !== '') {
            if (confirm('Confirmer le transfert de quantité ?')) {

                // Mettre à jour l'affichage des quantités et vider le unallocated_quantity
                $('#quantity' + productId + ' span').text(quantity);
                $('#unallocated_quantity' + productId + ' span').text(0);

                // Envoyer les données via AJAX pour mettre à jour la base de données
                $.ajax({
                    url: 'index.php?route=shopmanager/catalog/product.trfUnallocatedQuantity&token=' + token,
                    type: 'post',
                    data: {
                        product_id: productId,
                        unallocated_quantity: unallocatedQuantity,
                        quantity: quantity,
                        location: location
                    },
                    success: function (response) {
                        if (response.success) {
                            console.log('Transfert effectué avec succès.');

                            var unallocatedClass = 'label label-warning';

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
                            //     $('#quantity' + productId).html(finalQuantity);
                            var quantitySpan = '<span id="quantity' + productId + '" class="pedit-quantity ' + quantityClass + '" rel="' + productId + '" rel1="' + newQuantity + '">' + newQuantity + '</span>';
                            $('#quantity' + productId).replaceWith(quantitySpan);

                        } else {
                            alert('Erreur lors de la mise à jour.');
                        }
                    },
                    error: function () {
                        alert('Erreur lors de l\'appel à l\'API');
                    }
                });
            }
        } else {
            alert('La localisation ne peut pas être vide.');
        }
    }

}




// Remplacer unallocated_quantity par un champ texte lors du clic
$(document).on('click', '.pedit-unallocated-quantity', function (event) {
    event.preventDefault(); // Empêcher le comportement par défaut lors du clic

    // Empêcher la soumission du formulaire lorsqu'on appuie sur "Enter" dans le champ input
    /*  $('form').on('submit', function(e) {
          e.preventDefault(); // Empêche la soumission de tous les formulaires
      });*/

    var rel = $(this).attr('rel'); // Product ID
    var rel1 = $(this).attr('rel1'); // Current unallocated quantity
    var quantity = parseInt($('tr[data-product-id="' + rel + '"] .quantity span').text());
    var marketplace_item_id = parseInt($('tr[data-product-id="' + rel + '"] .marketplace_item_id span').text());
    // Retirer la classe 'label label-danger' de l'élément cliqué
    $(this).removeClass('label label-danger');

    // Créer un champ texte avec la valeur existante de unallocated_quantity
    var html = '<input type="text" id="unallocated_quantity_input' + rel + '" class="form-control unallocated_quantity_input" value="' + rel1 + '" />';
    $('#unallocated_quantity' + rel).html(html);

    var inputElement = $('#unallocated_quantity_input' + rel);
    inputElement.focus().select();

    // Détecter l'appui sur la touche Enter dans le champ texte
    // inputElement.on('keydown', function(event) {
    inputElement.on('change', function () {
        //   if (event.key === 'Enter' || event.keyCode === 13 || event.which === 13) {
        //      event.preventDefault(); // Empêcher l'action par défaut (comme le rechargement de la page)
        //       event.stopPropagation(); // Empêche la propagation de l'événement

        var newUnallocatedQuantity = $(this).val(); // Utiliser la valeur actuelle du champ d'entrée

        // Appeler la fonction pour confirmer la nouvelle quantité
        confirmUnallocatedQuantity(rel, newUnallocatedQuantity, quantity, marketplace_item_id);
        //    }
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
    var html = '<input type="text" id="location_input' + rel + '" class="form-control location_input" value="' + rel1 + '" />';
    $('#location' + rel).html(html);

    // Autofocus et sélectionner tout le texte
    $('#location_input' + rel).focus().select();

    // Détecter l'appui sur la touche Enter dans le champ texte
    $('#location_input' + rel).on('keydown', function (event) {
        if (event.key === 'Enter' || event.keyCode === 13 || event.which === 13) {
            event.preventDefault(); // Empêche l'action par défaut (comme le rechargement de la page)
            event.stopPropagation(); // Empêche la propagation de l'événement

            var newLocation = $(this).val().trim(); // Récupérer la valeur entrée dans le champ

            // Appeler la fonction de confirmation pour la location avec la nouvelle valeur
            confirmLocation(rel, newLocation);
        }
    });
    // Mettre à jour l'attribut 'rel1' en temps réel lors de la modification
    $('#location_input' + rel).on('input', function () {
        $(this).closest('.pedit-location').attr('rel1', $(this).val());
    });
});


// Remplacer quantity par un champ texte lors du clic
$(document).on('click', '.pedit-quantity', function (event) {
    event.preventDefault(); // Empêcher le comportement par défaut lors du clic

    var rel = $(this).attr('rel'); // Product ID
    var rel1 = $(this).attr('rel1'); // Current quantity
    var unallocatedQuantity = parseInt($('tr[data-product-id="' + rel + '"] .unallocated_quantity span').text());
    var marketplace_item_id = parseInt($('tr[data-product-id="' + rel + '"] .marketplace_item_id span').text());

    // Calculer la nouvelle quantité (quantity + unallocated_quantity)
    var quantity = parseInt(rel1);

    // Créer un champ texte avec la nouvelle quantité
    var html = '<input type="text" id="quantity_input' + rel + '" class="form-control quantity_input" value="' + quantity + '" />';
    $('#quantity' + rel).html(html);

    // Autofocus et sélectionner tout le texte
    $('#quantity_input' + rel).focus().select();

    // Détecter l'appui sur la touche Enter dans le champ texte
    // $('#quantity_input' + rel).on('keydown', function(event) {
    $('#quantity_input' + rel).on('change', function () {
        //   if (event.key === 'Enter' || event.keyCode === 13 || event.which === 13) {
        //         event.preventDefault(); // Empêche l'action par défaut (comme le rechargement de la page)
        //     event.stopPropagation(); // Empêche la propagation de l'événement

        var finalQuantity = $(this).val(); // Récupérer la valeur saisie dans le champ
        confirmQuantity(rel, finalQuantity, unallocatedQuantity, marketplace_item_id); // Appeler la fonction de confirmation avec les nouvelles valeurs
        //   }
    });
    $('#quantity_input' + rel).on('input', function () {
        $(this).closest('.pedit-quantity').attr('rel1', $(this).val());
    });
});



function confirmUnallocatedQuantity(productId, newUnallocatedQuantity, currentQuantity, marketplace_item_id) {
    var token = document.querySelector('input[name="token"]').value;

    // Validation de la nouvelle quantité non allouée
    if (newUnallocatedQuantity === '' || isNaN(newUnallocatedQuantity)) {
        alert('Veuillez entrer une quantité non allouée valide.');
        return;
    }

    // Calculer la nouvelle quantity en ajoutant la unallocated_quantity à la quantity actuelle
    // var finalQuantity = parseInt(newUnallocatedQuantity) + parseInt(currentQuantity);

    // Mise à jour de l'affichage après confirmation
    //  
    // $('#quantity' + productId).html(finalQuantity);

    console.log('product_id: ' + productId);
    console.log('unallocated_quantity: ' + newUnallocatedQuantity);
    console.log('quantity: ' + currentQuantity);
    console.log('marketplace_item_id: ' + marketplace_item_id);
    // Appel AJAX pour sauvegarder les données sur le serveur
    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product.updateUnallocatedQuantity&token=' + token,
        type: 'post',
        data: {
            product_id: productId,
            unallocated_quantity: newUnallocatedQuantity,
            quantity: currentQuantity,
            marketplace_item_id: marketplace_item_id
        },
        success: function (response) {
            if (response.success) {
                var unallocatedClass = '';
                if (newUnallocatedQuantity <= 0) {
                    unallocatedClass = 'label label-warning';
                } else if (newUnallocatedQuantity <= 5) {
                    unallocatedClass = 'label label-danger';
                } else {
                    unallocatedClass = 'label label-success';
                }
                //      $('#unallocated_quantity' + productId).html(newUnallocatedQuantity);
                var unallocatedSpan = '<span id="unallocated_quantity' + productId + '" class="pedit-unallocated-quantity ' + unallocatedClass + '" rel="' + productId + '" rel1="' + newUnallocatedQuantity + '">' + newUnallocatedQuantity + '</span>';
                $('#unallocated_quantity' + productId).replaceWith(unallocatedSpan);
                //
                //      alert('Quantité non allouée et quantité totale mises à jour avec succès.');
            } else {
                alert('Erreur lors de la mise à jour de la quantité.');
            }
        },
        error: function () {
            alert('Erreur lors de l\'appel à l\'API.');
        }
    });
}


function confirmLocation(productId, newLocation) {
    var token = document.querySelector('input[name="token"]').value;

    // Validation de la nouvelle localisation
    if (newLocation === '') {
        alert('La localisation ne peut pas être vide.');
        return;
    }

    // Mise à jour de l'affichage après confirmation


    console.log('product_id: ' + productId);
    console.log('location: ' + newLocation);
    // Appel AJAX pour sauvegarder les données sur le serveur si nécessaire
    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product.updateProductLocation&token=' + token,
        type: 'post',
        data: {
            product_id: productId,
            location: newLocation
        },
        success: function (response) {
            if (response.success) {
                //    $('#location' + productId).html(newLocation);
                var locationSpan = '<span id="location' + productId + '" class="pedit-location" rel="' + productId + '" rel1="' + newLocation + '">' + newLocation + '</span>';
                $('#location' + productId).replaceWith(locationSpan);

                //      alert('Localisation mise à jour avec succès');
            } else {
                alert('Erreur lors de la mise à jour de la localisation.');
            }
        },
        error: function () {
            alert('Erreur lors de l\'appel à l\'API.');
        }
    });
}

function confirmQuantity(productId, finalQuantity, unallocatedQuantity, marketplace_item_id) {

    var token = document.querySelector('input[name="token"]').value;

    // Validation de la nouvelle quantité
    if (finalQuantity === '' || isNaN(finalQuantity)) {
        alert('Veuillez entrer une quantité valide.');
        return;
    }

    // Mise à jour de l'affichage après confirmation

    console.log('product_id: ' + productId);
    console.log('unallocated_quantity: ' + unallocatedQuantity);
    console.log('quantity: ' + finalQuantity);
    console.log('marketplace_item_id: ' + marketplace_item_id);
    // Appel AJAX pour sauvegarder les données sur le serveur
    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product.updateQuantity&token=' + token,
        type: 'post',
        data: {
            product_id: productId,
            quantity: finalQuantity,
            unallocated_quantity: unallocatedQuantity,
            marketplace_item_id: marketplace_item_id
        },
        success: function (response) {
            if (response.success) {
                var quantityClass = '';
                if (finalQuantity <= 0) {
                    quantityClass = 'label label-warning';
                } else if (finalQuantity <= 5) {
                    quantityClass = 'label label-danger';
                } else {
                    quantityClass = 'label label-success';
                }
                //     $('#quantity' + productId).html(finalQuantity);
                var quantitySpan = '<span id="quantity' + productId + '" class="pedit-quantity ' + quantityClass + '" rel="' + productId + '" rel1="' + finalQuantity + '">' + finalQuantity + '</span>';
                $('#quantity' + productId).replaceWith(quantitySpan);

                //   alert('Quantité mise à jour avec succès');
            } else {
                alert(response + 'Erreur lors de la mise à jour de la quantité.');
            }
        },
        error: function () {
            alert('Erreur lors de l\'appel à l\'API.');
        }
    });
}



function handleDelete(productId) {
    checkProduct(productId);
    if (confirm(confirmMessage)) {
        $('#form-product' + productId).submit();
    }
}

function handleEnable(productId) {
    if (productId) {
        checkProduct(productId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-product');
        form.action = enableUrl;
        form.submit();
    }
}

function handleDisable(productId) {
    if (productId) {
        checkProduct(productId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-product');
        form.action = disableUrl;
        form.submit();
    }
}

function handleCopy(productId) {
    if (productId) {
        checkProduct(productId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-product');
        form.action = copyUrl;
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
    // console.log('DOM is ready');

    // Ajouter un écouteur d'événement sur les champs de recherche
    $('#search-form input, #search-form select').on('change', function () {
        var currentInput = $(this);

        // Réinitialiser tous les champs sauf celui qui vient de changer et ceux à ne pas modifier
        $('#search-form input, #search-form select').each(function () {
            if ($(this).attr('name') !== currentInput.attr('name')
                && $(this).attr('id') !== 'input-status'
                && $(this).attr('id') !== 'input-image'
                && $(this).attr('id') !== 'input-category-id'
                && $(this).attr('id') !== 'input-marketplace-account-id'
                && $(this).attr('id') !== 'input-marketplace'
                && $(this).attr('id') !== 'input-name'
                && $(this).attr('id') !== 'input-specifics'
                && $(this).attr('id') !== 'input-sources'
                && $(this).attr('id') !== 'input-model'
                && $(this).attr('id') !== 'input-price'
                && $(this).attr('id') !== 'input-quantity'
                && $(this).attr('id') !== 'input-limit'
            ) {

                $(this).val('');
            }
        });

        // Cliquer sur le bouton de filtre
        $('#button-filter').click();
    });


});
document.addEventListener('DOMContentLoaded', function () {
    const select = document.querySelector('#input-limit');
    select.addEventListener('change', function () {
        const selectedValue = select.value;
        const newUrl = limitLink.replace('{page}', '1').replace('&limit=', '&limit=' + selectedValue);
        // Rediriger vers l'URL avec les paramètres appropriés
        window.location.href = newUrl;
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // État initial des éléments supplémentaires (cachés par défaut)
    let showMore = false;

    // Gestion de l'affichage des champs supplémentaires au clic sur le bouton
    document.getElementById('toggle-button').addEventListener('click', function () {
        const button = this;
        const additionalFields = document.getElementById('additional-fields');

        // Basculer l'état d'affichage
        showMore = !showMore;

        // Afficher ou masquer les champs en fonction de l'état
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

        // Vérifier que l'élément existe et qu'il a une valeur avant d'appliquer select()
        if ($inputSku.length && $inputSku.val()) {
            $inputSku.focus().select();
        }



        $('input[name=\'' + inputName + '\']').autocomplete({

            'source': function (request, response) {
                //   console.log('inputName: ' + inputName);
                //     console.log('filterType: ' + filterType);
                //   console.log('request: ' + request);
                $.ajax({
                    url: 'index.php?route=shopmanager/catalog/product.autocomplete&token=' + token + '&' + filterType + '=' + encodeURIComponent(request),
                    dataType: 'json',
                    success: function (json) {
                        //      console.log('Response received from server for:', inputName);
                        //console.log('Server Response:', json);

                        response($.map(json, function (item) {
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
                        }).filter(Boolean)); // Remove null values
                    }
                });
            },
            'select': function (item) {
                if (item && item.label) {
                    console.log('Selected item:', item);
                    $('input[name=\'' + inputName + '\']').val(item['label']);
                }
            }
        });
    }

    // Initialiser l'autocomplétion pour chaque champ
    initAutocomplete('filter_sku', 'filter_sku');
    initAutocomplete('filter_product_id', 'filter_product_id');
    initAutocomplete('filter_name', 'filter_name');
    initAutocomplete('filter_category_id', 'filter_category_id');
    initAutocomplete('filter_model', 'filter_model');
});


function editMadeInCountry(product_id) {
    var token = document.querySelector('input[name="token"]').value;
    var countrySelect = document.getElementById('input-made-in-country-id-' + product_id);
    if (countrySelect) {
        var made_in_country_id = countrySelect.value;
        console.log("ID du pays sélectionné : ", made_in_country_id);
    } else {
        console.error("L'élément select avec id='input-made-in-country-id-" + product_id + "' n'existe pas.");
    }
    // var made_in_country_id = mySelect.val()
    console.log('token: ' + token);
    console.log('product_id: ' + product_id);
    console.log('made_in_country_id: ' + made_in_country_id);
    //    console.log('marketplace_item_id: ' + marketplace_item_id);
    //   console.log('quantity: ' + quantity);
    //document.getElementByName("product[" + product_row +"][made_in_country_id]").value;
    //alert (item_id +"selected " + mySelect.val());
    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product.editMadeInCountry&token=' + token,

        method: "POST",
        data: {
            product_id: product_id,
            made_in_country_id: made_in_country_id,
            //   marketplace_item_id:marketplace_item_id,
            //    quantity:quantity
        },
        dataType: 'json',
        crossDomain: true,
        success: function (json) {

            //alert(json['succes']);
            if (made_in_country_id > 0) {
                document.getElementById('check_made_in_country_id_' + product_id).style.backgroundColor = 'green';
            } else {
                document.getElementById('check_made_in_country_id_' + product_id).style.backgroundColor = 'red';
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
}

function addToMarketplace(product_id, marketplace_account_id, marketplace_id, is_products = true) {
    var token = document.querySelector('input[name="token"]').value;



    //  var quantity = document.querySelector('input[name="quantity"]').value;
    //  var unallocated_quantity = document.querySelector('input[name="unallocated_quantity"]').value;
    console.log('product_id :' + product_id);
    console.log('marketplace_account_id :' + marketplace_account_id);
    //  console.log('quantity :' + quantity);
    //  console.log('unallocated_quantity :' + unallocated_quantity);
    $.ajax({
        url: `index.php?route=shopmanager/marketplace.addToMarketplace&token=${token}`,
        type: 'POST',
        data: { product_id: product_id, marketplace_account_id: marketplace_account_id, marketplace_id: marketplace_id },
        dataType: 'json',
        success: function (json) {
            if (json.error) {
                alert(json.message);
            } else {
                handleMarketplaceAddUIUpdate(json, marketplace_account_id, product_id, is_products);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('An error occurred: ' + xhr.responseText);
        }
    });

}
function handleMarketplaceAddUIUpdate(json, marketplace_account_id, product_id, is_products = false) {
    if (is_products === true) {
        var product_underscore = product_id + '_';
    } else {
        var product_underscore = '';
    }
    // Construire dynamiquement les IDs des éléments cachés et du span
    var spanElement = document.getElementById(`marketplace_account_id_${product_underscore}${marketplace_account_id}`);
    var urlProductInput = document.querySelector(`input[name="url_product_${product_underscore}${marketplace_account_id}"]`);
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

    console.log('Message:', JSON.stringify(json));
}

function handleList() {
    // Sélectionner tous les liens <a> contenant addToMarketplace
    $('#loadingModal').modal('show');
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
            console.log("Déjà listé : ", img.alt);
            return; // Passer cet élément
        }

        console.log("Listing en cours :", img ? img.alt : "Produit inconnu");
        element.click(); // Simule le clic sur le lien
    });
    $('#loadingModal').modal('hide');
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
    let tokenElement = document.querySelector("input[name='token']");
    let token = tokenElement ? tokenElement.value : '';

    if (!token) {
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

    showLoadingPopup('Mise à jour des produits');
    appendLoadingMessage('🛠 Recherche d\'info : ' + productIds.join(', '));

    function processNextProduct(index) {
        if (index >= productIds.length) {
            appendLoadingMessage('✅ Tous les produits ont été mis à jour !');
            finishLoadingPopup();
            return;
        }

        let productId = productIds[index];

        $.ajax({
            url: 'index.php?route=shopmanager/catalog/product_search/getProductSearchData&token=' + token,
            type: "POST",
            data: {
                product_id: productId
            },
            dataType: "json",
            success: function (data) {
                appendLoadingMessage(`✅ Réponse reçue pour product_id ${productId}`);

                if (data[productId]) {
                    let row = $(`tr[data-product-id="${productId}"]`);
                    const selectElement = document.getElementById('input-made-in-country-id-' + productId);
                    const originalSelectHtml = selectElement ? selectElement.outerHTML : null;

                    if (row.length) {
                        updateCell(row, '.text-center:nth-child(3)', formatImage(data[productId].image, data[productId].name));
                        updateCell(row, '.text-center:nth-child(6)', data[productId].name);
                        updateCell(row, '.text-center:nth-child(7)', formatPrice(data[productId].price, data[productId].special));
                        if (originalSelectHtml) {
                            updateCell(
                                row,
                                '.text-center:nth-child(8)',
                                formatMadeinCountryid(
                                    data[productId].made_in_country_id,
                                    originalSelectHtml,
                                    productId
                                )
                            );
                        } else {
                            appendLoadingMessage(`[WARNING] Select HTML introuvable pour product_id ${productId}`, 'warn');
                        }
                        updateCell(row, '.text-center:nth-child(13)', data[productId].has_sources);
                        updateCell(row, '.text-center:nth-child(14)', formatSpecifics(data[productId]));
                    }
                }
                processNextProduct(index + 1);
            },
            error: function (xhr, status, error) {
                appendLoadingMessage(`❌ Erreur AJAX pour product_id ${productId}: ${xhr.responseText}`, 'error');
                processNextProduct(index + 1);
            }
        });
    }

    processNextProduct(0);
}
function handleFeedListOLD(productId) {
    let tokenElement = document.querySelector("input[name='token']");
    let token = tokenElement ? tokenElement.value : '';

    if (!token) {
        console.error("❌ Token non trouvé !");
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
        alert("⚠ Aucun produit sélectionné !");
        return;
    }

    console.log("🛠 Envoi des product_id un par un :", productIds);



    function processNextProduct(index) {
        if (index >= productIds.length) {
            alert("✅ Tous les produits ont été mis à jour !");
            return;
        }

        let productId = productIds[index];


        $.ajax({
            url: 'index.php?route=shopmanager/catalog/product_search/getProductSearchData&token=' + token,
            type: "POST",
            data: {
                product_id: productId
            },
            dataType: "json",
            success: function (data) {
                console.log(`✅ Réponse JSON pour product_id ${productId}:`, data);

                if (data[productId]) {
                    let row = $(`tr[data-product-id="${productId}"]`);
                    const selectElement = document.getElementById('input-made-in-country-id-' + productId);
                    const originalSelectHtml = selectElement ? selectElement.outerHTML : null;
                   
                    if (row.length) {
                        updateCell(row, '.text-center:nth-child(3)', formatImage(data[productId].image, data[productId].name));
                        updateCell(row, '.text-center:nth-child(6)', data[productId].name);
                        updateCell(row, '.text-center:nth-child(7)', formatPrice(data[productId].price, data[productId].special));
                        if (originalSelectHtml) {
                            updateCell(
                                row,
                                '.text-center:nth-child(8)',
                                formatMadeinCountryid(
                                    data[productId].made_in_country_id,
                                    originalSelectHtml,
                                    productId
                                )
                            );
                        } else {
                            console.warn(`[WARNING] Select HTML introuvable pour product_id ${productId}`);
                        }
                        updateCell(row, '.text-center:nth-child(13)', data[productId].has_sources);
                        updateCell(row, '.text-center:nth-child(14)', formatSpecifics(data[productId])); // Mettre à jour la colonne "Specifics"
                    }
                }

                processNextProduct(index + 1);
            },
            error: function (xhr, status, error) {
                console.error(`❌ Erreur AJAX pour product_id ${productId}:`,  xhr.responseText);
             //   alert(`❌ Erreur AJAX pour product_id ${productId}:`,  xhr.responseText);
            }
        });
    }

    // 🚀 Lancer le traitement avec la première requête
    processNextProduct(0);
}


function editQuantityToMarketplaceBulk() {
    $('#loadingModal').modal('show');
    var elements = document.querySelectorAll('[id^="marketplace_account_id_"]');

    if (elements.length === 0) {
        console.warn("Aucun produit à updater.");
        $('#loadingModal').modal('hide');
        return;
    }

    let processIndex = 0;
    let successCount = 0;
    let failureCount = 0;
    let report = [];

    function processNext() {
        if (processIndex >= elements.length) {
            console.log("Tous les produits ont été traités.");
            $('#loadingModal').modal('hide');

            let reportMessage = `Mise à jour terminée :\nSuccès : ${successCount}\nÉchecs : ${failureCount}\n\nDétails:\n` + report.join("\n");
            alert(reportMessage);
            return;
        }

        let element = elements[processIndex];
        let idParts = element.id.match(/marketplace_account_id_(\d+)_(\d+)/);
        let productId = idParts ? idParts[1] : null;
        let marketplaceAccountId = idParts ? idParts[2] : "1";
        let tokenElement = document.querySelector("input[name='token']");
        let token = tokenElement ? tokenElement.value : "";

        // Ne traiter que les produits avec marketplaceAccountId = 1
        if (marketplaceAccountId !== "1") {
            console.log("Produit ignoré, marketplace_account_id != 1:", productId);
            processIndex++;
            processNext();
            return;
        }

        console.log("Traitement du produit:", { productId, marketplaceAccountId, token });

        if (!productId || !marketplaceAccountId || !token) {
            console.warn("Données manquantes pour le produit", { productId, marketplaceAccountId, token });
            processIndex++;
            processNext();
            return;
        }

        console.log("Envoi de la mise à jour pour le produit:", productId);

        $.ajax({
            url: "index.php?route=shopmanager/marketplace.editQuantityToMarketplace&token=" + token,
            type: "POST",
            data: {
                product_id: productId,
                marketplace_account_id: marketplaceAccountId
            },
            dataType: "json",
            success: function (data) {
                console.log("Réponse reçue:", data);
                if (data.success) {
                    console.log("Mise à jour réussie pour le produit:", productId);
                    successCount++;
                    report.push(`Produit ${productId}: Succès`);
                } else {
                    console.error("Échec de la mise à jour pour le produit", productId, data.message);
                    failureCount++;
                    report.push(`Produit ${productId}: Échec - ${data.message}`);
                }
                processIndex++;
                processNext();
            },
            error: function (error) {
                console.error("Erreur lors de la requête pour le produit", productId, error);
                failureCount++;
                report.push(`Produit ${productId}: Erreur de requête`);
                processIndex++;
                processNext();
            }
        });
    }

    processNext();
}

/*
document.addEventListener('DOMContentLoaded', function () {

    var token = document.querySelector('input[name="token"]').value;
    // Sélectionner le bouton avec la classe btn-warning et l'icône fa-search
    const searchButton = document.querySelector('.btn.btn-warning i.fa-search').parentNode;

    // Ajouter un écouteur d'événement au clic
    searchButton.addEventListener('click', function (event) {
        $('#loadingModal').modal('show');
        // Empêcher le comportement par défaut du lien
        event.preventDefault();

        // Récupérer toutes les cases à cocher cochées
        const checkedCheckboxes = document.querySelectorAll('input[type="checkbox"][name^="selected"]:checked');

        // Créer un tableau pour stocker les product_ids sélectionnés
        let selectedProducts = [];

        checkedCheckboxes.forEach(function (checkbox) {
            selectedProducts.push(checkbox.value); // Ajouter l'ID produit au tableau
        });

        // Vérifier s'il y a des produits sélectionnés
        if (selectedProducts.length === 0) {
            alert('Veuillez sélectionner au moins un produit.');
            $('#loadingModal').modal('hide');
            // Soumettre le formulaire comme d'habitude
            window.location.href = searchButton.href;
            return;
        }

        // Diviser les produits sélectionnés en groupes de 5
        const chunkSize = 5;
        let productChunks = [];
        for (let i = 0; i < selectedProducts.length; i += chunkSize) {
            productChunks.push(selectedProducts.slice(i, i + chunkSize));
        }

        // Fonction pour envoyer un groupe de produits
        function sendProductChunk(productChunk) {
            let data = {
                product_ids: productChunk
            };
            console.log('data:', JSON.stringify(data));
            return fetch('index.php?route=shopmanager/catalog/product_search.product_source_info_feed&token=' + token, {
                method: 'POST', // Utiliser POST pour envoyer les données
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data) // Envoyer les produits sélectionnés sous forme JSON
            })
                .then(response => response.json())
                .then(data => {
                    // Traitement de la réponse
                    console.log('Réponse pour les produits:', productChunk);
                    console.log(data);

                    // Vous pouvez faire des actions supplémentaires avec les données reçues
                })
                .catch(error => {
                    console.error('Erreur lors de l\'appel AJAX:', error);
                });
        }

        // Fonction pour envoyer les groupes de produits de manière séquentielle
        async function sendProductChunksSequentially() {
            for (let chunk of productChunks) {
                await sendProductChunk(chunk); // Attendre que chaque groupe soit envoyé avant de passer au suivant
            }
            $('#loadingModal').modal('hide'); // Cacher la modal lorsque toutes les requêtes sont terminées
            // Recharger la page
            location.reload();
        }

        // Envoyer les produits par groupes de 5
        sendProductChunksSequentially();
    });
    // Fonction pour ajouter les événements liés à l'image
 function addImageEvents(thumbnail, fullsize) {
    thumbnail.addEventListener('mouseover', function() {
        fullsize.style.display = 'block';
    });

    thumbnail.addEventListener('mousemove', function(event) {
        const rect = thumbnail.getBoundingClientRect();
        fullsize.style.top = (event.clientY - rect.top) + 'px';
        fullsize.style.left = (event.clientX - rect.left) + 'px';
    });

    thumbnail.addEventListener('mouseout', function() {
        fullsize.style.display = 'none';
    });
}
function handleImages() {
    const thumbnails = document.querySelectorAll('.img-thumbnail');
    rowsData = []; // Réinitialiser le tableau

    thumbnails.forEach(thumbnail => {
        const fullsize = thumbnail.nextElementSibling;
        const resolutionMessage = thumbnail.parentElement.querySelector('.resolution-message');
        const imageContainer = thumbnail.closest('.image-container');
        //const checkbox = imageContainer ? imageContainer.closest('.thumbnail').querySelector('.save-data-checkbox') : null;
        //const hiddenInput = document.createElement('input');
        //hiddenInput.type = 'hidden';
        //hiddenInput.name = 'image_dimensions[]';
        //imageContainer.appendChild(hiddenInput);

        

        // Ajout des événements sur la miniature
        addImageEvents(thumbnail, fullsize);
    });

    // Trier et reconstruire les lignes après calcul des résolutions
 //   Promise.all(Array.from(thumbnails).map(thumbnail => thumbnail.complete ? Promise.resolve() : new Promise(resolve => thumbnail.addEventListener('load', resolve))))
  //  .then(() => {
  //      rowsData.sort((a, b) => b.resolution - a.resolution);
 //       rebuildTable();
 //   });

    // Ajout des boutons de suppression
  
}
handleImages();
});*/
document.addEventListener('DOMContentLoaded', function () {
    var token = document.querySelector('input[name="token"]').value;
    const searchButton = document.querySelector('.btn.btn-warning i.fa-search').parentNode;

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

        function sendProductChunk(productChunk) {
            let data = {
                product_ids: productChunk
            };

            appendLoadingMessage(`📦 Traitement des produits : ${productChunk.join(', ')}`);

            return fetch('index.php?route=shopmanager/catalog/product_search.product_source_info_feed&token=' + token, {
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

        async function sendProductChunksSequentially() {
            for (let chunk of productChunks) {
                await sendProductChunk(chunk);
            }
            finishLoadingPopup('✅ Analyse terminée. Rechargement...');
            setTimeout(() => location.reload(), 1500);
        }

        sendProductChunksSequentially();
    });

    function addImageEvents(thumbnail, fullsize) {
        thumbnail.addEventListener('mouseover', function () {
            fullsize.style.display = 'block';
        });

        thumbnail.addEventListener('mousemove', function (event) {
            const rect = thumbnail.getBoundingClientRect();
            fullsize.style.top = (event.clientY - rect.top) + 'px';
            fullsize.style.left = (event.clientX - rect.left) + 'px';
        });

        thumbnail.addEventListener('mouseout', function () {
            fullsize.style.display = 'none';
        });
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

function formatImage(imageUrl, altText) {
    if (!imageUrl) {
        // Return a placeholder image if no image URL is provided
        return '<img src="https://via.placeholder.com/50" alt="No Image" style="width:100px; height:auto;">';
    }
    return `<img src="${imageUrl}" alt="${altText}" style="width:100px; height:auto;">`;
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.actual-image-container').forEach(function (container) {
      const fileInput = container.querySelector('input[type="file"]');
      const previewImage = container.querySelector('img.img-thumbnail');
      const fullImage = container.querySelector('.fullsize-actual-image');
      const productId = container.id.replace('drop-', '');
      const token = document.querySelector('input[name="token"]').value;
  
      container.addEventListener('dragover', function (event) {
        event.preventDefault();
        event.stopPropagation();
        container.style.borderColor = '#007bff'; // Highlight border on dragover
        console.log('[DRAGOVER] sur', container.id);
      });
  
      container.addEventListener('dragleave', function () {
        container.style.borderColor = '#ccc';
        console.log('[DRAGLEAVE] sur', container.id);
      });
  
      container.addEventListener('drop', function (event) {
        event.preventDefault();
        event.stopPropagation();
        container.style.borderColor = '#ccc';
        console.log('[DROP] sur', container.id);
  
        if (event.dataTransfer.files.length > 0) {
          const file = event.dataTransfer.files[0];
          console.log('[DROP] Fichier:', file);
  
          // Affecte le fichier au champ input
          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(file);
          fileInput.files = dataTransfer.files;
  
          // Mise à jour de l'image visuelle
          const reader = new FileReader();
          reader.onload = function (e) {
            previewImage.src = e.target.result;
            if (fullImage) fullImage.src = e.target.result;
          };
          reader.readAsDataURL(file);
  
          // Envoi AJAX
          const formData = new FormData();
          formData.append('product_id', productId);
          formData.append('sourcecode', '');
          formData.append('imageprincipal', file);
  
          console.log('[AJAX] Envoi du fichier imageprincipal pour product_id =', productId);
  
          $.ajax({
            url: 'index.php?route=shopmanager/tools.uploadImagesFiles&type=pri&token=' + token,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
              console.log('[AJAX] Réponse:', response);
              if (response.success) {
                updateProductImagesUI(response.product_images);
              } else if (response.error) {
                alert('Erreur : ' + response.error);
              }
            },
            error: function (xhr) {
              console.error('[AJAX] Erreur :', xhr.responseText);
              alert('Erreur upload : ' + xhr.responseText);
            }
          });
        } else {
          console.warn('[DROP] Aucun fichier détecté');
        }
      });
  
      // Pour le clic sur la boîte
      container.addEventListener('click', () => fileInput.click());
  
      // Pour les fichiers choisis manuellement
      fileInput.addEventListener('change', function () {
        if (fileInput.files.length > 0) {
          const file = fileInput.files[0];
          console.log('[CHANGE] Fichier sélectionné manuellement :', file);
          container.dispatchEvent(new DragEvent('drop', {
            dataTransfer: new DataTransfer()
          }));
        }
      });
    });
  
    // Globale : empêcher Chrome d'ouvrir l'image en drag/drop
    ['dragover', 'drop'].forEach(eventName => {
      window.addEventListener(eventName, function (e) {
        e.preventDefault();
      }, false);
    });
  });

  function updateProductImagesUI(productImages) {
    if (productImages.primary && productImages.primary.thumb) {
        const thumb = productImages.primary.thumb;
        const imageField = document.querySelector('.preview-' + productImages.product_id);
        if (imageField) {
            imageField.src = thumb;
        }
    }

    console.log('[updateProductImagesUI] Images mises à jour :', productImages);
}

  