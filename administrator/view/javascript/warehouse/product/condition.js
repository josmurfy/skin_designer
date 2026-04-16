// Original: warehouse/product/condition.js
// Fonction pour récupérer les détails de condition selon le category_id
function getConditionDetails(categoryId) {

    var user_token = document.querySelector('input[name="user_token"]').value;


    if ($.isNumeric(categoryId)) {
        $.ajax({
            url: 'index.php?route=warehouse/product/condition.getConditionDetails&category_id=' + encodeURIComponent(categoryId) + '&user_token=' + user_token,
            dataType: 'json',
            success: function(json) {
                if (!json.erreur) {
                    // Supprimez les conditions actuelles dans #conditions-group
                  

                    // Créez le contenu dynamique pour les conditions
                    var conditionsHtml = '<div class="row mb-3"><label class="col-sm-2 col-form-label">Conditions</label><div class="col-sm-10"><div class="row">';

                    $.each(json.conditions, function(index, condition) {
                        conditionsHtml += '<div class="col-sm-4"><div class="form-check">';
                        conditionsHtml += '<input type="radio" name="condition_id" class="form-check-input save_data-radio" id="condition-' + condition.condition_marketplace_item_id + '" value="' + condition.condition_id + '" ';
                        
                        // Vérifiez si cette condition est déjà sélectionnée
                        if (json.selected_condition_id == condition.condition_id) {
                            conditionsHtml += ' checked ';
                        }
                        conditionsHtml += ' onclick="checkFormStatus();"';
                        conditionsHtml += '>';
                        conditionsHtml += '<label class="form-check-label" for="condition-' + condition.condition_marketplace_item_id + '">';
                        conditionsHtml += condition.condition_name;
                        conditionsHtml += '</label></div></div>';
                    });

                    conditionsHtml += '</div></div></div>';
                    // Ajoutez le nouveau contenu au #conditions-group
                    $('#conditions-group').append(conditionsHtml);
                } else {
                    alert('Conditions non trouvées pour cette catégorie.');
                }
            }
        });
    }
}
