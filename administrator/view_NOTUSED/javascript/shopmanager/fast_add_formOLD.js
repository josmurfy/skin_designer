function checkFormStatus() {
    var categoryChecked = document.querySelector('input[name="category_id"]').value;
    var conditionChecked = document.querySelector('input[name="condition_id"]:checked');
    var product_id = document.querySelector('input[name="product_id"]').value;
    var upc = document.querySelector('input[name="upc"]').value;

    //console.log('categoryChecked: ' + categoryChecked);
    //console.log('conditionChecked: ' + conditionChecked);
    //console.log('product_id: ' + product_id);
    //console.log('upc: ' + upc);
    var saveButton = document.getElementById('saveButton');
    
    if ((categoryChecked && conditionChecked && upc )|| product_id) {
      saveButton.disabled = false;
    } else {
     saveButton.disabled = true;
    }
  }

  function isValidUPC(upc, number) {
    // Créer dynamiquement une expression régulière pour vérifier le nombre de chiffres souhaité
    var regex = new RegExp("^\\d{" + number + "}$");
    return regex.test(upc);
}

// Fonction pour vérifier l'état du formulaire
function checkValidUPC() {
    // Récupérer la valeur de l'input UPC
    var upcInput = document.getElementById('input-upc');
    var upcValue = upcInput.value.trim(); // Supprimer les espaces autour

    // Vérifier si c'est un UPC valide
    if (isValidUPC(upcValue,12) ||  isValidUPC(upcValue,13)) {
        upcInput.classList.remove('invalid'); // Supprimer la classe "invalid"
        document.getElementById('error-message').style.display = 'none'; // Cacher le message d'erreur

    } else {
        // Si l'UPC n'est pas valide, afficher une erreur
        upcInput.classList.add('invalid'); // Ajouter la classe "invalid" pour changer le style
        document.getElementById('error-message').style.display = 'block'; // Afficher le message d'erreur
        upcInput.value='';
    }
}


document.addEventListener('DOMContentLoaded', function() {

    checkFormStatus();
// Optionnel : Empêcher la soumission du formulaire si l'UPC n'est pas valide
document.getElementById('form-product').addEventListener('submit', function(event) {
    var upcValue = document.getElementById('input-upc').value.trim();
    if (!isValidUPC(upcValue,12) && !isValidUPC(upcValue,13)) {
        event.preventDefault(); // Empêcher la soumission du formulaire
        alert('Please enter a valid 12 or 13 digit UPC.');
    }
});

// Gardez lautocomplétion existante
$('input[name="category"]').autocomplete({
   

    'source': function(request, response) {
        var token = document.querySelector('input[name="token"]').value;
        $.ajax({
            url: 'index.php?route=shopmanager/catalog/category/autocomplete&token=' + token + '&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['category_id'] 
                    }
                }));
            }
        });
    },
    'select': function(item) {
        $('input[name="category"]').val('');

        $('#product-category' + item['value']).remove();

        $('#product-category').append('<div id="product-category' + item['value'] + '"><i class="fa-solid fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_category[]" value="' + item['value'] + '" /></div>');
    }
});

// Nouvelle fonctionnalité pour lentrée manuelle dun category_id
$('input[name="category"]').on('change', function() {
    var token = document.querySelector('input[name="token"]').value;
 
    var categoryId = $(this).val().trim();

    if ($.isNumeric(categoryId)) {
      //  alert(categoryId);
     
        $.ajax({
            url: 'index.php?route=shopmanager/catalog/category.getCategoryDetails&category_id=' + encodeURIComponent(categoryId) + '&token=' + token,
            dataType: 'json',
            success: function(json) {
                if (!json.erreur) {
                    // Mettez à jour le champ avec le nom de la catégorie
                    $('input[name="category"]').val(json.name + ' (' + categoryId +')');
                    $('#category_id').val(categoryId);

                    // Supprimez les catégories actuelles
                    $('#product-category').empty();

                    // Utilisez le chemin complet fourni par le JSON
                    var fullPath = json.path + ' &gt; ' + json.name;
                    fullPath += '<input type="hidden" name="product_category[]" value="' + categoryId + '" />';
                    $.each(json.parents, function(index, parent) {
                    

                        // Ajoutez le chemin complet jusquà ce parent
                 //       $.each(json.parents.slice(0, index + 1), function(i, p) {
                            fullPath += '<input type="hidden" name="product_category[]" value="' + parent.id + '" />';
                 //       });

                    });
                    $('#product-category').append('<div id="product-category' + categoryId + '">' + fullPath + '</div>');
                    $('#conditions-group').empty();
                    getConditionDetails(categoryId);
                    checkFormStatus();
                } else {
                    alert('Catégorie non trouvée ou le chemin est manquant.');
                    $('#conditions-group').empty();
                }
            }
        });
    }
});



// Gestion de la suppression des catégories
$('#product-category').on('click', '.fa-minus-circle', function() {
    $(this).parent().remove();
});


});

document.addEventListener('DOMContentLoaded', function () {
    const upcInput = document.getElementById('input-upc');
    var token = document.querySelector('input[name="token"]').value;


    if (upcInput) {
        upcInput.addEventListener('change', function () {
            const upc = upcInput.value.trim();

            if (!upc) return;

            fetch('index.php?route=shopmanager/ebay.getCategoryId&upc=' + encodeURIComponent(upc) + '&token=' + token)
                .then(response => response.json())
                .then(json => {
                    if (json.success) {
                        console.log('Category ID:', json.category_id);
                        // Exemple : remplir un champ caché
                        const categoryInput = document.getElementById('input-category-id');
                        if (categoryInput) {
                            categoryInput.value = json.category_id;
                        }
                    } else {
                        console.warn(json.error);
                    }
                })
                .catch(error => {
                    console.error('Error fetching category ID:', error);
                });
        });
    }
});

