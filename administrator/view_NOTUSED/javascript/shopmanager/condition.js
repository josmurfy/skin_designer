// Fonction pour récupérer les détails de condition selon le category_id
function getConditionDetails(categoryId) {

    var token = document.querySelector('input[name="token"]').value;

    //console.log('categoryId ' + JSON.stringify(categoryId));

    if ($.isNumeric(categoryId)) {
        $.ajax({
            url: 'index.php?route=shopmanager/condition.getConditionDetails&category_id=' + encodeURIComponent(categoryId) + '&token=' + token,
            dataType: 'json',
            success: function(json) {
                if (!json.erreur) {
                    // Supprimez les conditions actuelles dans #conditions-group
                  

                    // Créez le contenu dynamique pour les conditions
                    var conditionsHtml = '<div class="form-group"><label class="col-sm-2 control-label">Conditions</label><div class="col-sm-10"><div class="row">';

                    $.each(json.conditions, function(index, condition) {
                        conditionsHtml += '<div class="col-sm-5">';
                        conditionsHtml += '<label for="condition-' + condition.condition_marketplace_item_id + '">';
                        conditionsHtml += '<input type="radio" name="condition_id" class="save_data-radio" id="condition-' + condition.condition_marketplace_item_id + '" value="' + condition.condition_id + '" ';
                        
                        // Vérifiez si cette condition est déjà sélectionnée
                        if (json.selected_condition_id == condition.condition_id) {
                            conditionsHtml += ' checked ';
                        }
                        conditionsHtml += ' onclick="checkFormStatus();"';
                        conditionsHtml += '>';
                        conditionsHtml += condition.condition_name;
                        conditionsHtml += '</label></div>';
                    });

                    conditionsHtml += '</div></div></div>';
            //console.log('json ' + JSON.stringify(json));
                    // Ajoutez le nouveau contenu au #conditions-group
                    $('#conditions-group').append(conditionsHtml);
                } else {
                    alert('Conditions non trouvées pour cette catégorie.');
                }
          //console.log('json ' + JSON.stringify(json));
            }
        });
    }
}
