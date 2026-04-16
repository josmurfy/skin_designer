// Original: shopmanager/fast_add_form.js
function checkFormStatus() {
    var categoryChecked = document.querySelector('input[name="category_id"]').value;
    var conditionChecked = document.querySelector('input[name="condition_id"]:checked');
    var product_id = document.querySelector('input[name="product_id"]').value;
    var upc = document.querySelector('input[name="upc"]').value;

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
        alert(TEXT_INVALID_UPC);
    }
});

// Gardez lautocomplétion existante
$('input[name="category"]').autocomplete({
   

    'source': function(request, response) {
        var user_token = document.querySelector('input[name="user_token"]').value;
        $.ajax({
            url: 'index.php?route=shopmanager/catalog/category.autocomplete&user_token=' + user_token + '&filter_name=' +  encodeURIComponent(request),
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

        // Charger les conditions et mettre à jour le category_id
        var user_token = document.querySelector('input[name="user_token"]').value;
        loadCategoryDetails(item['value'], user_token);
    }
});

// Nouvelle fonctionnalité pour lentrée manuelle dun category_id
$('input[name="category"]').on('change', function() {
    var user_token = document.querySelector('input[name="user_token"]').value;
 
    var category_id = $(this).val().trim();

    if ($.isNumeric(category_id)) {
      //  alert(category_id);
      loadCategoryDetails(category_id, user_token);
      
    }
});



// Gestion de la suppression des catégories
$('#product-category').on('click', '.fa-minus-circle', function() {
    $(this).parent().remove();
});


});

document.addEventListener('DOMContentLoaded', function () {
    const upcInput = document.getElementById('input-upc');
    var user_token = document.querySelector('input[name="user_token"]').value;

    if (upcInput) {
        upcInput.addEventListener('change', function () {
            const upc = upcInput.value.trim();

            if (!upc) return;

            var fetchUrl = 'index.php?route=shopmanager/ebay.getCategoryId&upc=' + encodeURIComponent(upc) + '&user_token=' + user_token;

            fetch(fetchUrl)
                .then(response => response.json())
                .then(json => {
                        if (json.success) {
                            const category_id = json.category_id;
                            const currentCategoryId = $('#category_id').val();
                            document.querySelector('input[name="category_id"]').value = '';

                            if (currentCategoryId != category_id) {
                                checkFormStatus();
                                ChangeCategory(category_id);
                                loadCategoryDetails(category_id, user_token);
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

function ChangeCategory(category_id) {
    var user_token = document.querySelector('input[name="user_token"]').value;
    //var category_id = category_id.trim(); // Corrected to use the passed parameter directly

    if ($.isNumeric(category_id)) {
        $.ajax({
            url: 'index.php?route=shopmanager/catalog/category.getDetails&category_id=' + encodeURIComponent(category_id) + '&user_token=' + user_token,
            dataType: 'json',
            success: function(json) {
                if (!json.erreur) {
                    // Update the category name field
                    $('input[name="category"]').val(json.name + ' (' + category_id + ')');
                    $('input[name="category_specific_names_json"]').val(
                        JSON.stringify(json.category_specific_names)
                    );
                    $('input[name="category_name"]').val(json.name);
                    $('#category_id').val(category_id);

                    // Clear current categories
                    $('#product-category').empty();

                    // Use the full path provided by the JSON
                    var fullPath = json.path + ' &gt; ' + json.name;
                    fullPath += '<input type="hidden" name="product_category[]" value="' + category_id + '" />';
                    $.each(json.parents, function(index, parent) {
                        fullPath += '<input type="hidden" name="product_category[]" value="' + parent.id + '" />';
                    });
                    $('#product-category').append('<div id="product-category' + category_id + '">' + fullPath + '</div>');


                } else {
                    console.error('Category not found or path is missing.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    } else {
        console.warn('Invalid category ID:', category_id);
    }
}

function loadCategoryDetails(category_id, user_token) {
    $.ajax({
        url: 'index.php?route=shopmanager/catalog/category.getDetails&category_id=' + encodeURIComponent(category_id) + '&user_token=' + user_token,
        dataType: 'json',
        success: function(json) {
            if (!json.erreur) {
                // Met à jour le champ texte avec le nom de la catégorie
                $('input[name="category"]').val(json.name + ' (' + category_id + ')');
                $('#category_id').val(category_id);

                // Supprime les catégories actuelles
                $('#product-category').empty();

                // Construit le chemin complet
                var fullPath = json.path + ' &gt; ' + json.name;
                fullPath += '<input type="hidden" name="product_category[]" value="' + category_id + '" />';
                
                $.each(json.parents, function(index, parent) {
                    fullPath += '<input type="hidden" name="product_category[]" value="' + parent.id + '" />';
                });

                $('#product-category').append('<div id="product-category' + category_id + '">' + fullPath + '</div>');
                $('#conditions-group').empty();
                getConditionDetails(category_id);
                checkFormStatus();
            } else {
                alert(TEXT_CATEGORY_NOT_FOUND);
                $('#conditions-group').empty();
            }
        }
    });
}

