// Original: warehouse/product/publish.js
// Assurez-vous que le DOM est prêt
document.addEventListener('DOMContentLoaded', function () {
    
    var user_token = document.querySelector('input[name="user_token"]').value;
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

        checkedCheckboxes.forEach(function(checkbox) {
            selectedProducts.push(checkbox.value); // Ajouter l'ID produit au tableau
        });

        // Vérifier s'il y a des produits sélectionnés
        if (selectedProducts.length === 0) {
            alert(TEXT_SELECT_PRODUCT);
            $('#loadingModal').modal('hide');
            return;
        }

        // Diviser les produits sélectionnés en groupes de 3
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

            return fetch('index.php?route=warehouse/product/publish.product_source_info_feed&user_token=' + user_token, {
                method: 'POST', // Utiliser POST pour envoyer les données
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data) // Envoyer les produits sélectionnés sous forme JSON
            })
            .then(response => response.json())
            .then(data => {
                // Traitement de la réponse

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

        // Envoyer les produits par groupes de 3
        sendProductChunksSequentially();
    });
});
function transferUnallocatedQuantity(productId) {

    var user_token = document.querySelector('input[name="user_token"]').value;

    // Récupérer les valeurs actuelles
    var quantity = parseInt($('tr[data-product-id="' + productId + '"] .quantity span').text());
    var unallocatedQuantity = parseInt($('tr[data-product-id="' + productId + '"] .unallocated_quantity span').text());
    var location = $('tr[data-product-id="' + productId + '"] .location').text().trim();


    // Si quantity est 0, il faut valider la location
    if (quantity === 0) {
     //   alert('La quantité est 0, veuillez valider la localisation.');

        // Créer un champ texte pour modifier la localisation
        var html = '<input type="text" id="location_input' + productId + '" class="form-control location_input" value="' + location + '" placeholder="' + TEXT_LOCATION_PLACEHOLDER + '" />';
        $('#location' + productId).html(html);

        // Donner le focus au champ texte et sélectionner tout le texte
        $('#location_input' + productId).focus().select();

        // Gérer l'événement 'keydown' sur le champ texte
        $('#location_input' + productId).on('keydown', function(event) {
            if (event.key === 'Enter' || event.keyCode === 13 || event.which === 13) {
                event.preventDefault(); // Empêche l'action par défaut (comme le rechargement de la page)
                event.stopPropagation(); // Empêche la propagation de l'événement

                var newLocation = $('#location_input' + productId).val().trim();

                // Si la nouvelle localisation est vide, afficher une alerte
                if (newLocation === '') {
                    alert(TEXT_LOCATION_EMPTY);
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
                            url: 'index.php?route=warehouse/product/product.trfUnallocatedQuantity&user_token=' + user_token,
                            type: 'post',
                            data: {
                                product_id: productId,
                                unallocated_quantity: unallocatedQuantity,
                                quantity: quantity,
                                location: newLocation
                            },
                            success: function(response) {
                                if (response.success) {
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
                               //     $('#quantity' + productId).html(finalQuantity);
                                    var quantitySpan = '<span id="quantity' + productId + '" class="pedit-quantity ' + quantityClass + '" rel="' + productId + '" rel1="' + newQuantity + '">' + newQuantity + '</span>';
                                    $('#quantity' + productId).replaceWith(quantitySpan);

                                } else {
                                    alert(TEXT_ERROR_UPDATE_PREFIX + response);
                                }
                            },
                            error: function() {
                                alert(TEXT_ERROR_API);
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
            if (confirm(TEXT_CONFIRM_QTY_TRANSFER)) {

                // Mettre à jour l'affichage des quantités et vider le unallocated_quantity
                $('#quantity' + productId + ' span').text(quantity);
                $('#unallocated_quantity' + productId + ' span').text(0);

                // Envoyer les données via AJAX pour mettre à jour la base de données
                $.ajax({
                    url: 'index.php?route=warehouse/product/product.trfUnallocatedQuantity&user_token=' + user_token,
                    type: 'post',
                    data: {
                        product_id: productId,
                        unallocated_quantity: unallocatedQuantity,
                        quantity: quantity,
                        location: location
                    },
                    success: function(response) {
                        if (response.success) {

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
                       //     $('#quantity' + productId).html(finalQuantity);
                            var quantitySpan = '<span id="quantity' + productId + '" class="pedit-quantity ' + quantityClass + '" rel="' + productId + '" rel1="' + newQuantity + '">' + newQuantity + '</span>';
                            $('#quantity' + productId).replaceWith(quantitySpan);
                        
                        } else {
                            alert(TEXT_ERROR_UPDATE);
                        }
                    },
                    error: function() {
                        alert(TEXT_ERROR_API);
                    }
                });
            }
        } else {
            alert(TEXT_LOCATION_EMPTY);
        }
    }

}




// Remplacer unallocated_quantity par un champ texte lors du clic
$(document).on('click', '.pedit-unallocated-quantity', function(event) {
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
    inputElement.on('change', function() {
     //   if (event.key === 'Enter' || event.keyCode === 13 || event.which === 13) {
      //      event.preventDefault(); // Empêcher l'action par défaut (comme le rechargement de la page)
     //       event.stopPropagation(); // Empêche la propagation de l'événement

            var newUnallocatedQuantity = $(this).val(); // Utiliser la valeur actuelle du champ d'entrée

            // Appeler la fonction pour confirmer la nouvelle quantité
            confirmUnallocatedQuantity(rel, newUnallocatedQuantity, quantity, marketplace_item_id);
    //    }
    });

     // Mettre à jour l'attribut 'rel1' en temps réel lors de la modification
     inputElement.on('input', function() {
        $(this).closest('.pedit-unallocated-quantity').attr('rel1', $(this).val());
    });
});



// Remplacer location par un champ texte lors du clic
$(document).on('click', '.pedit-location', function(event) {
    event.preventDefault(); // Empêcher le comportement par défaut lors du clic

    var rel = $(this).attr('rel'); // Product ID
    var rel1 = $(this).attr('rel1'); // Current location value

    // Créer un champ texte avec la valeur existante de location
    var html = '<input type="text" id="location_input' + rel + '" class="form-control location_input" value="' + rel1 + '" />';
    $('#location' + rel).html(html);

    // Autofocus et sélectionner tout le texte
    $('#location_input' + rel).focus().select();

    // Détecter l'appui sur la touche Enter dans le champ texte
    $('#location_input' + rel).on('keydown', function(event) {
        if (event.key === 'Enter' || event.keyCode === 13 || event.which === 13) {
            event.preventDefault(); // Empêche l'action par défaut (comme le rechargement de la page)
            event.stopPropagation(); // Empêche la propagation de l'événement

            var newLocation = $(this).val().trim(); // Récupérer la valeur entrée dans le champ

            // Appeler la fonction de confirmation pour la location avec la nouvelle valeur
            confirmLocation(rel, newLocation);
        }
    });
     // Mettre à jour l'attribut 'rel1' en temps réel lors de la modification
     $('#location_input' + rel).on('input', function() {
        $(this).closest('.pedit-location').attr('rel1', $(this).val());
    });
});


// Remplacer quantity par un champ texte lors du clic
$(document).on('click', '.pedit-quantity', function(event) {
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
    $('#quantity_input' + rel).on('change', function() {
     //   if (event.key === 'Enter' || event.keyCode === 13 || event.which === 13) {
   //         event.preventDefault(); // Empêche l'action par défaut (comme le rechargement de la page)
       //     event.stopPropagation(); // Empêche la propagation de l'événement

            var finalQuantity = $(this).val(); // Récupérer la valeur saisie dans le champ
            confirmQuantity(rel, finalQuantity, unallocatedQuantity, marketplace_item_id); // Appeler la fonction de confirmation avec les nouvelles valeurs
     //   }
    });
    $('#quantity_input' + rel).on('input', function() {
        $(this).closest('.pedit-quantity').attr('rel1', $(this).val());
    });
});



function confirmUnallocatedQuantity(productId, newUnallocatedQuantity, currentQuantity, marketplace_item_id) {
    var user_token = document.querySelector('input[name="user_token"]').value;

    // Validation de la nouvelle quantité non allouée
    if (newUnallocatedQuantity === '' || isNaN(newUnallocatedQuantity)) {
        alert(TEXT_INVALID_UNALLOCATED_QTY);
        return;
    }

    // Calculer la nouvelle quantity en ajoutant la unallocated_quantity à la quantity actuelle
   // var finalQuantity = parseInt(newUnallocatedQuantity) + parseInt(currentQuantity);

    // Mise à jour de l'affichage après confirmation
 //  
   // $('#quantity' + productId).html(finalQuantity);

    // Appel AJAX pour sauvegarder les données sur le serveur
    $.ajax({
        url: 'index.php?route=warehouse/product/product.updateUnallocatedQuantity&user_token=' + user_token,
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
          //      $('#unallocated_quantity' + productId).html(newUnallocatedQuantity);
                var unallocatedSpan = '<span id="unallocated_quantity' + productId + '" class="pedit-unallocated-quantity ' + unallocatedClass + '" rel="' + productId + '" rel1="' + newUnallocatedQuantity + '">' + newUnallocatedQuantity + '</span>';
                $('#unallocated_quantity' + productId).replaceWith(unallocatedSpan);
//
          //      alert('Quantité non allouée et quantité totale mises à jour avec succès.');
            } else {
                alert(TEXT_ERROR_QTY_UPDATE);
            }
        },
        error: function() {
            alert(TEXT_ERROR_API);
        }
    });
}


function confirmLocation(productId, newLocation) {
    var user_token = document.querySelector('input[name="user_token"]').value;

    // Validation de la nouvelle localisation
    if (newLocation === '') {
        alert(TEXT_LOCATION_EMPTY);
        return;
    }

    // Mise à jour de l'affichage après confirmation
  

    // Appel AJAX pour sauvegarder les données sur le serveur si nécessaire
    $.ajax({
        url: 'index.php?route=warehouse/product/product.updateLocation&user_token=' + user_token,
        type: 'post',
        data: {
            product_id: productId,
            location: newLocation
        },
        success: function(response) {
            if (response.success) {
            //    $('#location' + productId).html(newLocation);
                var locationSpan = '<span id="location' + productId + '" class="pedit-location" rel="' + productId + '" rel1="' + newLocation + '">' + newLocation + '</span>';
                $('#location' + productId).replaceWith(locationSpan);

          //      alert('Localisation mise à jour avec succès');
            } else {
                alert(TEXT_ERROR_LOCATION_UPDATE);
            }
        },
        error: function() {
            alert(TEXT_ERROR_API);
        }
    });
}

function confirmQuantity(productId, finalQuantity, unallocatedQuantity, marketplace_item_id) {

    var user_token = document.querySelector('input[name="user_token"]').value;

    // Validation de la nouvelle quantité
    if (finalQuantity === '' || isNaN(finalQuantity)) {
        alert(TEXT_INVALID_QTY);
        return;
    }

    // Mise à jour de l'affichage après confirmation
  
    // Appel AJAX pour sauvegarder les données sur le serveur
    $.ajax({
        url: 'index.php?route=warehouse/product/product.updateQuantity&user_token=' + user_token,
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
           //     $('#quantity' + productId).html(finalQuantity);
                var quantitySpan = '<span id="quantity' + productId + '" class="pedit-quantity ' + quantityClass + '" rel="' + productId + '" rel1="' + finalQuantity + '">' + finalQuantity + '</span>';
                $('#quantity' + productId).replaceWith(quantitySpan);

             //   alert('Quantité mise à jour avec succès');
            } else {
                alert(response + TEXT_ERROR_QTY_UPDATE);
            }
        },
        error: function() {
            alert(TEXT_ERROR_API);
        }
    });
}



function handleDelete(productId) {
    checkProduct(productId);
    if (confirm(confirmMessage)) {
        $('#form-product').submit();
    }
}

function handleEnable(productId) {
    if(productId){
    checkProduct(productId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-product');
        form.action = enableUrl;
        form.submit();
    }
}

function handleDisable(productId) {
    if(productId){
    checkProduct(productId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-product');
        form.action = disableUrl;
        form.submit();
    }
}

function handleCopy(productId) {
    if(productId){
    checkProduct(productId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-product');
        form.action = copyUrl;
        form.submit();
    }
}

function checkProduct(productId) {
    // Sélectionner la case à cocher directement par son sélecteur d'attribut
    var checkbox = $('input[name="selected[' + productId + ']"]');
    checkbox.prop('checked', !checkbox.prop('checked'));
}




document.addEventListener("DOMContentLoaded", function () {

        // Ajouter un écouteur d'événement sur les champs de recherche
        $('#search-form input, #search-form select').on('change', function() {
            var currentInput = $(this);
            
            // Réinitialiser tous les champs sauf celui qui vient de changer et ceux à ne pas modifier
            $('#search-form input, #search-form select').each(function() {
                if ($(this).attr('name') !== currentInput.attr('name') 
                    && $(this).attr('id') !== 'input-status' 
                    && $(this).attr('id') !== 'input-image' 
                    && $(this).attr('id') !== 'input-limit') {
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
        var user_token = document.querySelector('input[name="user_token"]').value;

        var $inputSku = $('#input-sku');

        // Vérifier que l'élément existe et qu'il a une valeur avant d'appliquer select()
        if ($inputSku.length && $inputSku.val()) {
            $inputSku.focus().select();
        }
   


        $('input[name=\'' + inputName + '\']').autocomplete({
        
            'source': function(request, response) {
                $.ajax({
                    url: 'index.php?route=warehouse/product/product.autocomplete&user_token=' + user_token + '&' + filterType + '=' + encodeURIComponent(request),
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
                        }).filter(Boolean)); // Remove null values
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

    // Initialiser l'autocomplétion pour chaque champ
    initAutocomplete('filter_sku', 'filter_sku');
    initAutocomplete('filter_product_id', 'filter_product_id');
    initAutocomplete('filter_name', 'filter_name');
    initAutocomplete('filter_category_id', 'filter_category_id');
    initAutocomplete('filter_model', 'filter_model');
});


