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
        alert('SKU not found');
    }
}

function filterInventory() {
    var token = document.querySelector('input[name="token"]').value;

    let url = 'index.php?route=shopmanager/inventory&token=' + token;
    let sku = document.getElementById('input-sku').value;
    
    if (sku) {
        url += '&filter_sku=' + encodeURIComponent(sku);
    }
    location.href = url;
}

function confirmDelete() {
    if (confirm("<?php echo $text_confirm; ?>")) {
        document.getElementById('form-inventory').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Sélectionne le champ `new_location` et le bouton `button-submit`
    const newLocationInput = document.getElementById('input-new-location');
    const submitButton = document.getElementById('button-submit');

    // Fonction pour activer ou désactiver le bouton en fonction de la valeur du champ
    function toggleSubmitButton() {
        // Vérifie si le champ `new_location` est vide
        if (newLocationInput.value.trim() === "") {
            submitButton.disabled = true; // Désactive le bouton si le champ est vide
        } else {
            submitButton.disabled = false; // Active le bouton si le champ contient du texte
        }
    }

    // Écoute les changements dans le champ `new_location`
    newLocationInput.addEventListener('input', toggleSubmitButton);

    // Vérifie l'état initial au chargement de la page
    toggleSubmitButton();
});

// Remplacer unallocated_quantity par un champ texte lors du clic
$(document).on('click', '.pedit-unallocated-quantity', function(event) {
    event.preventDefault(); // Empêcher le comportement par défaut lors du clic

    // Empêcher la soumission du formulaire lorsqu'on appuie sur "Enter" dans le champ input
    $('form').on('submit', function(e) {
        e.preventDefault(); // Empêche la soumission de tous les formulaires
    });

    var rel = $(this).attr('rel'); // Product ID
    var rel1 = $(this).attr('rel1'); // Current unallocated quantity
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
        updateQuantity('unallocated_quantity', rel, newUnallocatedQuantity);
        updateTotalQuantity(rel);
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
        updateQuantity('quantity', rel, newQuantity);
        updateTotalQuantity(rel);
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

    // Déterminer la classe à appliquer en fonction de la valeur de totalQuantity
    if (totalQuantity <= 0) {
        spanClass = 'label label-warning';
    } else if (totalQuantity <= 5) {
        spanClass = 'label label-danger';
    } else {
        spanClass = 'label label-success';
    }

    // Mettre à jour le contenu et les classes de #total_quantity sans superposition
    $('#total_quantity' + rel)
        .removeClass('label label-danger label-success label-warning label-primary label-info')
        .html('<span class="' + spanClass + '">' + totalQuantity + '</span>');
}

function updateQuantity(element, rel, quantity) {
    // Récupérer les valeurs de quantity et unallocated_quantity en convertissant en nombre
    var spanClass = '';
    // Calculer la nouvelle valeur pour total_quantity

    // Déterminer la classe à appliquer en fonction de la valeur de totalQuantity
    if (quantity <= 0) {
        spanClass = 'label label-warning';
    } else if (quantity <= 5) {
        spanClass = 'label label-danger';
    } else {
        spanClass = 'label label-success';
    }

    // Mettre à jour le contenu et les classes de #total_quantity sans superposition
    $('#' + element + rel)
        .removeClass('label label-danger label-success label-warning label-primary label-info')
        .html('<span class="' + spanClass + '">' + quantity + '</span>');
    $('#' + element + '_hid' + rel).val(quantity);
}

$(document).ready(function() {
    $('#input-sku').on('input', function() {
        var inputSku = $(this).val().trim(); // Récupère la valeur entrée dans le champ SKU

        // Parcourir les lignes du tableau pour vérifier la correspondance avec le SKU
        $('tr[data-product-id]').each(function() {
            var row = $(this);
            var productSku = row.find('td').eq(2).text().trim(); // Récupère le SKU dans la 3ème cellule de la ligne

            // Vérifie si le SKU correspond à l'entrée
            if (productSku === inputSku) {
                var productId = row.data('product-id'); // Récupère le product_id de la ligne correspondante

                // Déplace la ligne trouvée en haut du tableau avant de cocher
                row.prependTo(row.closest('tbody'));

                // Cocher la case de sélection
                row.find('input[type="checkbox"][name^="product_id"]').prop('checked', true);

                // Mettre à jour les classes de quantité et unallocated_quantity
                var quantitySpan = $('#quantity' + productId);
                var unallocatedQuantitySpan = $('#unallocated_quantity' + productId);

                // Récupère les valeurs de quantity et unallocated_quantity
                var unallocatedQuantity = parseInt(unallocatedQuantitySpan.text()) || 0;
                var quantity = parseInt(quantitySpan.text()) || 0;
                var newQuantity = quantity + 1;
                var newUnallocatedQuantity = unallocatedQuantity - 1;

                updateQuantity('quantity', productId, newQuantity);
                updateQuantity('unallocated_quantity', productId, newUnallocatedQuantity);
                updateTotalQuantity(productId);

                // Met à jour la classe de la ligne en fonction de newUnallocatedQuantity
                row.removeClass('table-success table-warning table-danger'); // Enlève les classes existantes

                if (newUnallocatedQuantity === 0) {
                    row.css('background-color', '#d4edda').css('color', '#155724'); // Vert
                } else if (newUnallocatedQuantity > 0) {
                    row.css('background-color', '#fff3cd').css('color', '#856404'); // Jaune
                } else {
                    row.css('background-color', '#f8d7da').css('color', '#721c24'); // Rouge
                }

                // Réinitialise le champ de recherche
                $('#input-sku').val('');
            }
        });
    });
});
