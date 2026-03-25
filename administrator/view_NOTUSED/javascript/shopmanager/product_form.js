// product_form.js
function switchEntryName(fieldName = 'product',specifics_row) {
    const product_id = document.getElementById('product_id').value;


    var form = document.getElementById('form-product');
    if (!form) {
        console.error('Form element not found');
        return;
    }
    // Obtenir les éléments concernés
    var label = document.getElementById('ai-result-name');
    var labelcount = document.getElementById('ai-result-name-count');
   

    var element = form.querySelector('input[name="' + fieldName + '_description[1][name]"]');
 //   var input=element.value;

   //console.log('input:' + input);
    // Vérifier si le label contient du texte
    if (label && label.style.display !== 'none' && label.innerText.trim() !== "") {
        // Si le label contient du texte, on le copie dans l'input et cache le label
       //console.log('label:' +  label.textContent);
        element.value = label.textContent.trim();
        label.style.display = 'none';
        labelcount.style.display = 'none';
    
      //  translateContentForAllLanguages('product_description_1_'+ specifics_row,'', 'product');
        if (typeof generateInfo === 'function') {
    generateInfo();
}

    } else {
        // Si le label est vide ou caché, on affiche un message dans la console (ou une autre action)
        //console.log('Le label est vide ou caché.');
    }
}


function updateMultiselectSize() {
    $('.multiselect').each(function() {
        // Récupérer toutes les options
        var $options = $(this).children('option');
        var seenValues = {};

        // Traiter les options pour supprimer les doublons et mettre en majuscule la première lettre de chaque mot
        $options.each(function() {
            var value = $(this).val();
            var text = $(this).text();

            // Supprimer les doublons
            if (!seenValues[value]) {
                seenValues[value] = true;

                // Mettre en majuscule la première lettre de chaque mot
                var capitalizedText = text.toLowerCase().replace(/\b\w/g, function(l) {
                    return l.toUpperCase();
                });

                $(this).text(capitalizedText); // Mettre à jour le texte de l'option
            } else {
                $(this).remove(); // Supprimer les doublons
            }
        });

        // Récupérer les options sélectionnées et non sélectionnées
        var $selectedOptions = $(this).children('option:selected');
        var $nonSelectedOptions = $(this).children('option:not(:selected)');

        // Réorganiser les options pour que les sélectionnées soient visibles en premier
        $(this).html(''); // Vider le select
        $(this).append($selectedOptions); // Ajouter d'abord les options sélectionnées
        $(this).append($nonSelectedOptions); // Puis les non sélectionnées

        // Déterminer la taille à appliquer (nombre d'options sélectionnées + 2 lignes supplémentaires)
        var size = Math.max(2, $selectedOptions.length + 1);
        $(this).attr('size', size);

        // Faire défiler le multiselect pour montrer les options sélectionnées en haut
        if ($selectedOptions.length > 0) {
            this.scrollTop = this.querySelector('option[selected]').offsetTop;
        }
    });
}


function updateLabel(languageId, conditionName) {
    var label = document.getElementById('condition-name' + languageId);
    label.textContent = conditionName; // Mettre à jour le texte du label
  //  label.style.display = 'block'; // Afficher le label
}
// Fonction pour mettre à jour tous les labels en fonction de la condition sélectionnée
    // Fonction pour mettre à jour tous les labels en fonction de la condition sélectionnée
function updateAllLabels(selectedConditionId) {
    // Récupérer le JSON des langues à partir du champ caché
   
    var languages = JSON.parse($('#languages_json').val());
  //console.log('languagesJson : ' + $('#languages_json').val());
    // Boucler sur chaque langue
    for (var targetLanguageId in languages) {
        var targetLanguage = languages[targetLanguageId];
      //console.log('targetLanguage ' + targetLanguage);
        // Construire l'ID du champ caché condition_id pour chaque langue
    //    var conditionInput = document.getElementById('condition_id' +targetLanguageId);
      
        // Si le champ caché condition_id existe
        var conditions = JSON.parse($('#conditions_json' + targetLanguageId).val());
      //console.log('conditions : ' + $('#condition_id' +targetLanguageId).val() );
         //   var conditions = JSON.parse(conditionInput.value);
           
            // Vérifier si la condition sélectionnée existe dans le JSON 
            if (conditions[selectedConditionId]) {
                //console.log('conditions : ' + JSON.stringify(conditions));
                //console.log('conditions[selectedConditionId]condition_name : ' + conditions[selectedConditionId]['condition_name']);
                // Mettre à jour le label avec le nom de la condition sélectionnée
                var label = document.getElementById('condition-name' +targetLanguageId);
                label.textContent = conditions[selectedConditionId]['condition_name'];
                if(targetLanguage != 'English'){
                    label.style.display = 'block';
                }
            }
      
    };
}
function getEbayProduct() {
    var token = document.querySelector('input[name="token"]').value;
    var category_id = document.querySelector('input[name="category_id"]').value;
    const inputField = document.getElementById(`ebayValueInput`);
    const marketplace_item_id = inputField ? inputField.value.trim() : '';
    //console.log('marketplace_item_id: ', marketplace_item_id);

    if (!marketplace_item_id) {
        alert('Please enter a value before proceeding.');
        return;
    }

    fetch(`index.php?route=shopmanager/ebay.getProduct&token=${token}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ marketplace_item_id: marketplace_item_id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            //console.log('Data retrieved successfully from eBay.');
            console.table(data.result); // Affiche les résultats sous forme de tableau
           // generateProductSpecifics(category_id, data.result); // Passe les données au générateur de specifics
        } else {
            alert('Error: ' + (data.error || 'Unknown error occurred.'));
        }
    })
    .catch(error => {
        console.error('Error fetching eBay values:', error);
        //alert('Failed to fetch eBay values.');
    });
}



function generateProductSpecifics(categoryId, source_value) {

    var specifics_row =window.specifics_row_specific;
    var token = document.querySelector('input[name="token"]').value;
    var product_id = document.querySelector('input[name="product_id"]').value;
    var marketplace_item_id = document.querySelector('input[name="marketplace_item_id"]').value;
 // Ouvrir la modale avant de lancer l'appel AJAX
 $('#loadingModal').modal('show');

    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product_search/generateProductSpecifics&product_id=' + product_id + '&category_id=' + categoryId + '&marketplace_item_id=' + marketplace_item_id + '&token=' + token,
        type: 'GET',
        dataType: 'json',
        success: function (response) {

                updateProductSpecificsTable(response, specifics_row);
                  // Fermer la modale lorsque la réponse est reçue
         

        },
        error: function (xhr, status, error) {
            console.error("Erreur lors de la récupération des spécificités : " + error);
             // Fermer la modale même en cas d'erreur
          
        }
    });
    $('#loadingModal').modal('hide');
}

function AllunconfimSource() {

    var languages = JSON.parse($('#languages_json').val());
   
    for (var language_id in languages) {
   
        var rows = document.querySelectorAll(`[id^="specifics_${language_id}_"]`);

        rows.forEach(row => {
        
            // Extraire l'ID de la ligne en supprimant le préfixe "specifics1-row"
            var rowId = row.id.replace(`specifics_${language_id}_`, '');
         //console.log('rowId ' + rowId);
            // Sélectionner l'élément VerifiedSource correspondant
            var verifiedSourceElement = document.getElementById(`verified_source_${language_id}_${rowId}`);

            // Si l'élément existe et que sa valeur n'est pas "yes", appeler la fonction verifySpecific
            if (verifiedSourceElement && verifiedSourceElement.value.toLowerCase() === 'yes') {
                unconfirmSource(language_id,rowId);
            }
        });
    }
}

function AllconfirmSource() {

    var languages = JSON.parse($('#languages_json').val());
   
    for (var language_id in languages) {
        var rows = document.querySelectorAll(`[id^="specifics_${language_id}_"]`);
        rows.forEach(row => {
        
            // Extraire l'ID de la ligne en supprimant le préfixe "specifics1-row"
            var rowId = row.id.replace(`specifics_${language_id}_`, '');
          //console.log('rowId ' + rowId);
            // Sélectionner l'élément VerifiedSource correspondant
            var verifiedSourceElement = document.getElementById(`verified_source_${language_id}_${rowId}`);

            // Si l'élément existe et que sa valeur n'est pas "yes", appeler la fonction verifySpecific
            if (verifiedSourceElement && verifiedSourceElement.value.toLowerCase() !== 'yes') {
                confirmSource(language_id,rowId);
            }
        });
    }
}

function unconfirmSource(language_id,rowId) {
    // Sélectionner la ligne de spécificité correspondant à rowId
    const rowElement = document.querySelector(`#specifics_${language_id}_${rowId}`);
    //console.log('rowElement ' + rowElement);
    // Sélectionner les boutons correspondants dans la ligne
    const confirmButton = rowElement.querySelector("button[data-original-title='Confirm Source']");
    const unconfirmButton = rowElement.querySelector("button[data-original-title='Unconfirm Source']");
    const verifyButton = rowElement.querySelector("button[data-original-title='Verify Value']");

    // Afficher ou masquer les boutons selon la logique définie
    confirmButton.style.display = 'inline-block';   // Afficher le bouton "Confirm"
    unconfirmButton.style.display = 'none';         // Masquer le bouton "Unconfirm"
    verifyButton.style.display = 'inline-block';    // Afficher le bouton "Verify"

    // Sélectionner les champs de saisie (input, select, textarea) pour réinitialiser la valeur d'origine
    const VerifiedSourceInput = document.getElementById(`verified_source_${language_id}_${rowId}`);

    VerifiedSourceInput.value  = '';

    // Rétablir la classe d'origine de la ligne à partir de l'attribut data-original-class
    if (rowElement.hasAttribute('data-original-class')) {
        const originalClass = rowElement.getAttribute('data-original-class');
        rowElement.className = originalClass; // Remettre la classe d'origine
    } else {
        // Si aucune classe d'origine n'est définie, retirer toutes les classes
        rowElement.className = '';
    }
}

function confirmSource(language_id,rowId) {
    // Sélectionner la ligne de spécificité correspondant à rowId
    const rowElement = document.querySelector(`#specifics_${language_id}_${rowId}`);
    
    // Sélectionner les boutons correspondants dans la ligne
    const confirmButton = rowElement.querySelector("button[data-original-title='Confirm Source']");
    const unconfirmButton = rowElement.querySelector("button[data-original-title='Unconfirm Source']");
    const verifyButton = rowElement.querySelector("button[data-original-title='Verify Value']");

    // Afficher ou masquer les boutons selon la logique définie
    confirmButton.style.display = 'none';   // Afficher le bouton "Confirm"
    unconfirmButton.style.display = 'inline-block';         // Masquer le bouton "Unconfirm"
    verifyButton.style.display = 'none';    // Afficher le bouton "Verify"

    // Sélectionner les champs de saisie (input, select, textarea) pour réinitialiser la valeur d'origine
   
    const VerifiedSourceInput = document.getElementById(`verified_source_${language_id}_${rowId}`);

    VerifiedSourceInput.value  = 'yes';

    // Rétablir la classe d'origine de la ligne à partir de l'attribut data-original-class
    if (rowElement.hasAttribute('data-original-class')) {
        const originalClass = rowElement.getAttribute('data-original-class');
        rowElement.className = originalClass; // Remettre la classe d'origine
    } else {
        // Si aucune classe d'origine n'est définie, retirer toutes les classes
        rowElement.className = 'bg-info-dark text-white';
    }
}


function updateProductSpecificsTable(data, specifics_row_received) {
    var specifics_row = specifics_row_received;
    var fragment = document.createDocumentFragment();

    for (var languageId in data) {
        if (data.hasOwnProperty(languageId)) {
            var specifics_row = specifics_row_received;
            var specifics = data[languageId];
            var tableBody = document.querySelector('#specifics_' + languageId + ' tbody');
            tableBody.innerHTML = ''; // Vider le tableau existant

            for (var specificName in specifics) {
                if (specifics.hasOwnProperty(specificName)) {
                    var specific = specifics[specificName];

                    // Vérifiez si 'specific_info' existe avant d'accéder à 'aspectConstraint' et 'aspectValues'
                    var constraint = (specific.specific_info && specific.specific_info.aspectConstraint) ? specific.specific_info.aspectConstraint : {};
                    var values = (specific.specific_info && specific.specific_info.aspectValues) ? specific.specific_info.aspectValues : [];
                    var required = constraint.aspectRequired ? 'required' : '';
                    var current_name = specific.Name || specific.specific_info.localizedAspectName;
                    var current_value = specific.Value || '';
                    var VerifiedSource = (specific && typeof specific.VerifiedSource !== 'undefined') ? specific.VerifiedSource : '';
                    var actual_value = specific.Actual_value || '';
                    //console.log('current_name ' + current_name);
                    //console.log('current_value ' + current_value);
                    // Traiter la valeur de `current_value` pour s'assurer qu'elle est sous forme de tableau
                    if (typeof current_value === 'string') {
                        current_value = (specificName !== 'Region Code' && current_value.includes('@@')) ? current_value.split('@@ ') : [current_value];
                    }
                    if (!Array.isArray(current_value)) {
                        current_value = [current_value];
                    }
                    //console.log('current_valueAFTER ' + current_value);
                    //current_value = current_value.map(value => value.substring(0, 65));

                    // Handle the case where current_value contains commas or semicolons
                    if (constraint.aspectMode === 'FREE_TEXT' && constraint.itemToAspectCardinality === 'MULTI') {
                        current_value = current_value.flatMap(value => value.split(/[;,]/).map(v => v.trim()));
                    }

                    values.sort(function(a, b) {
                        if (current_value.includes(a.localizedValue)) return -1;
                        if (current_value.includes(b.localizedValue)) return 1;
                        return a.localizedValue.localeCompare(b.localizedValue);
                    });

                    // Construire le champ d'entrée en fonction du type de contrainte
                    var inputField = '';

                    if (constraint.aspectMode === 'SELECTION_ONLY') {
                        //console.log('selectSELECTION_ONLY ' + current_value);
                        inputField = '<select id="product_description_' + languageId + '_' + specifics_row + '" name="product_description[' + languageId + '][specifics][' + specificName + '][Value]" class="form-control" ' + required + ' >';
                        inputField += '<option value=""></option>';
                        values.forEach(function(value) {
                            inputField += '<option value="' + value.localizedValue + '"' + (current_value.includes(value.localizedValue) ? ' selected' : '') + '>' + value.localizedValue + '</option>';
                        });
                        inputField += '</select>';
                    } else if (constraint.aspectMode === 'FREE_TEXT' && constraint.itemToAspectCardinality === 'MULTI') {
                        //console.log('selectMULTI ' + current_value);
                        inputField = '<select id="product_description_' + languageId + '_' + specifics_row + '" name="product_description[' + languageId + '][specifics][' + specificName + '][Value][]" class="form-control multiselect" multiple ' + required + ' size="' + Math.max(2, values.length + 2) + '" >';
                        let foundValues = [];
                        current_value.forEach(function(current) {
                            let found = values.find(function(value) {
                                return value.localizedValue === current;
                            });
                            if (found) {
                                inputField += '<option value="' + found.localizedValue + '" selected>' + found.localizedValue + '</option>';
                                foundValues.push(found.localizedValue);
                            }
                        });
                        values.forEach(function(value) {
                            if (!foundValues.includes(value.localizedValue)) {
                                inputField += '<option value="' + value.localizedValue + '">' + value.localizedValue + '</option>';
                            }
                        });
                        current_value.forEach(function(current) {
                            if (!foundValues.includes(current)) {
                                inputField += '<option value="' + current + '" selected>' + current + '</option>';
                            }
                        });
                        inputField += '</select>';
                     
                    } else if (constraint.aspectMode === 'FREE_TEXT' && constraint.itemToAspectCardinality === 'SINGLE') {
                        inputField = '<input type="text" id="product_description_' + languageId + '_' + specifics_row + '" name="product_description[' + languageId + '][specifics][' + specificName + '][Value]" placeholder="" class="form-control" ' + required + ' value="' + current_value.join(', ') + '"  />';
                        //console.log('current_value.join ' + current_value.join(', '));
                    } else {
                        inputField = '<input type="text" id="product_description_' + languageId + '_' + specifics_row + '" name="product_description[' + languageId + '][specifics][' + specificName + '][Value]" placeholder="" class="form-control" ' + required + ' value="' + current_value.join(', ') + '"  />';
                        //console.log('current_value.join ELSE ' + current_value.join(', '));
                    }

                    // Créer la ligne du tableau
                    var rowHtml = document.createElement('tr');
                    rowHtml.id = 'specifics_' + languageId + '_' + specifics_row;

                    // Appliquer la classe de style en fonction de la présence de VerifiedSource
                    var rowClass = VerifiedSource ? 'bg-info-dark text-white' : ''; // Applique la classe 'bg-info-dark' si VerifiedSource est défini
                    rowHtml.className = rowClass; // Applique la classe CSS à la ligne

                    rowHtml.innerHTML = 
                    ' <td class="text-center text-nowrap" style="width: auto;">' + 
                        '<button type="button" onclick="removeSpecificsRow(' + specifics_row + ',' + specifics_row + ', \'' + specificName + '\');" data-toggle="tooltip" title="Supprimer" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i></button> ' +
                        '</td>' +
                        '<td class="text-left">' + 

                            '<div id="product_description_' + languageId + '_specifics_Name_' + specifics_row + '" >' + 
                            current_name + '</div>' +
                            '<input type="hidden" name="product_description[' + languageId + '][specifics][' + specificName + '][Name]" value="' + specificName + '" class="form-control" />' +
                            '<input type="hidden" id="verified_source_' + languageId + '_' + specifics_row + '" name="product_description[' + languageId + '][specifics][' + specificName + '][VerifiedSource]" value="' + VerifiedSource + '" class="form-control" />' +
                        '</td>' +
                        '<td class="text-left">' +
                            '<input type="hidden" id="hidden_original_value_' + languageId + '_' + specifics_row + '" value="" />' +
                            '<div id="original_value_' + languageId + '_' + specifics_row + '" >' + 
                            (Array.isArray(actual_value) ? actual_value.join(',') : actual_value) + '</div>' +
                        
                        
                        (actual_value ? 
                       
                        '<input type="hidden" id="actual_value_' + languageId + '_' + specifics_row + '" name="actual_value_' + languageId + '_' + specifics_row + '" value="' + (Array.isArray(actual_value) ? actual_value.join(',') : actual_value) + '" />' +
                        '<button type="button" id="btTrf' + specifics_row + '" onclick="trfValue(' + specifics_row + ')" data-toggle="tooltip" title="Transfer Actual Value" class="btn btn-success"><i class="fa-regular fa-circle-right"></i></button>' +
                        '<button type="button" id="btUndo' + specifics_row + '" onclick="undoValue(' + specifics_row + ')" data-toggle="tooltip" title="Undo Transfer" class="btn btn-warning" style="display:none;"><i class="fa  fa-undo"></i></button>' 
                       
                        : '<button type="button" id="btUndo' + specifics_row + '" onclick="undoValue(' + specifics_row + ')" data-toggle="tooltip" title="Undo Transfer" class="btn btn-warning" style="display:none;"><i class="fa  fa-undo"></i></button>' )+
                         '</td>' +
                        '<td class="text-left">' +
                        '<div id="response_product_description_' + languageId + '_' + specifics_row + '"></div>' +
                            inputField +
                        '</td>' +
                        
                        '<td class="text-center">' +
                            // Cacher le bouton de vérification si VerifiedSource est défini
                            '<button type="button" onclick="confirmSource(' + languageId + ',' + specifics_row + ');" data-toggle="tooltip" title="" class="btn btn-success" data-original-title="Confirm Source" style="display: ' + (VerifiedSource ? 'none' : 'inline-block') + '"><i class="fa-regular fa-circle-check"></i></button> ' +
                            '<button type="button" onclick="unconfirmSource(' + languageId + ',' + specifics_row + ');" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="Unconfirm Source" style="display: ' + (VerifiedSource ? 'inline-block' : 'none') + '"><i class="fa-regular fa-circle-xmark"></i></button> ' +
                            '<button type="button" onclick="verifySpecific(' + specifics_row + ', \'true\');" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="Verify Value" style="display: ' + (VerifiedSource ? 'none' : 'inline-block') + '"><i class="fa-regular fa-circle-question"></i></button> ' +
                        '</td>';


                    fragment.appendChild(rowHtml);
                    specifics_row++; // Incrémenter le compteur de lignes
                }
            }
            tableBody.appendChild(fragment); // Ajouter tout le fragment en une seule opération
        }
    }

    updateMultiselectSize();
    
    // Met à jour la variable globale après traitement
    window.specifics_row = specifics_row;
}



function getEbayValueOLD(specifics_row, language_id) {
    var token = document.querySelector('input[name="token"]').value;
    const inputField = document.getElementById(`ebayValueInput_${specifics_row}`);
    const inputValue = inputField ? inputField.value.trim() : '';

    if (!inputValue) {
        alert('Please enter a value before proceeding.');
        return;
    }

    fetch(`index.php?route=shopmanager/ebay.getProduct&token=${token}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ specifics_row, language_id, value: inputValue })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateProductSpecificsTable(data.result, specifics_row);
            alert('eBay values updated successfully!');
        } else {
            alert('Error: ' + (data.error || 'Unknown error occurred.'));
        }
    })
    .catch(error => {
        console.error('Error fetching eBay values:', error);
        //alert('Failed to fetch eBay values.');
    });
}


function getRowID() {
    // Assuming you have a way to determine the correct row ID, implement this logic
    var rowElem = document.querySelector('[name="product_description[1][description]"]');
    if (rowElem) {
        var idParts = rowElem.id.split('_');
        return idParts[idParts.length - 1]; 
    }
    return null;
}
  function htmlspecialchars(str) {
      return str.replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
  }
function htmlspecialchars_decode(str) {
  return str.replace(/&amp;/g, '&')
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>')
            .replace(/&quot;/g, '"')
            .replace(/&#039;/g, "'");
}
  function ucwords(str) {
      return str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
          return letter.toUpperCase();
      });
  }

  function addslashes(str) {
      return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
  }

  function doubleval(val) {
      return parseFloat(val) || 0;
  }

function generateInfo(){
    var languages = JSON.parse($('#languages_json').val());
   
         for (var targetLanguageId in languages) {
            generateMetaTag(targetLanguageId);
            generateDescription(targetLanguageId);
           

            var inputElement = document.querySelector('input[name="product_description[' + targetLanguageId +'][name]"]');
            updateCharacterCount(inputElement,'char-count-' + targetLanguageId +'-0');


           // updateCharacterCount(targetLanguageId);
        }


}
function generateMetaTag(language_id) {
    var upc= document.getElementById("input-upc").value;
    var nameInput = document.querySelector('input[name="product_description['+ language_id +'][name]"]');
    var metaTagTitle = document.querySelector('input[name="product_description[' + language_id + '][meta_title]"]');
    var conditionnameElement = document.getElementById("condition-name" + language_id);
    var metaTagDescription = document.querySelector('textarea[name="product_description[' + language_id + '][meta_description]"]');
    var nameDescriptionElement = document.querySelector('textarea[name="product_description[' + language_id + '][description_supp]"]');
    var metaTagKeyword = document.querySelector('textarea[name="product_description[' + language_id + '][meta_keyword]"]');
    var tag = document.querySelector('input[name="product_description[' + language_id + '][tag]"]');
    var keyword = document.querySelector('input[name="product_description[' + language_id + '][keyword]"]');


    var name = nameInput.value;
    var conditionname = conditionnameElement.textContent.trim();
    var additionalDescriptionHtml = nameDescriptionElement.value.trim();
    var additionalDescriptionText = additionalDescriptionHtml
      .replace(/<\/?[^>]+(>|$)/g, "") // Supprime les balises HTML
      .replace(/&nbsp;/g, ' ') // Remplace les espaces insécables HTML par des espaces
      .replace(/\s+/g, ' '); // Remplace les espaces multiples par un seul espace

    metaTagTitle.value =  '(' + conditionname + ') ' + name + ' UPC: ' + upc;
    metaTagDescription.value = additionalDescriptionText;
    var tagkeywords = conditionname + ' ' + name + ' ' + upc;
    tagkeywords = tagkeywords.replace(/[.,;:'"\{\}\[\]\(\)@%$&\-]/g, '');
    var tagkeywords = tagkeywords.split(/\s+/).join(','); // Sépare les mots et les joint avec des virgules
    if (tagkeywords.endsWith(',')) {
      tagkeywords = tagkeywords.slice(0, -1);
    }

    metaTagKeyword.value = tagkeywords;
    tag.value = tagkeywords;


    var keywords = conditionname + ' ' + name;
    keywords = keywords.replace(/[.,;:'"\{\}\[\]\(\)@%$&\-]/g, '');
    var keywords = keywords.split(/\s+/).join('-'); // Sépare les mots et les joint avec des virgules
    // Supprimer le dernier caractère sil est un tiret
    if (keywords.endsWith('-')) {
      keywords = keywords.slice(0, -1);
    }

    keyword.value = keywords;
}
function generateDescription(language_id) {
    const product_id = document.getElementById('product_id').value;

    var form = document.getElementById('form-product');
    var nameElem = form.querySelector(`input[name="product_description[${language_id}][name]"]`);
    var name = nameElem ? nameElem.value.trim() : '';

    var description = `<style>
        .secondary-list-item {
            list-style-type: none;
            padding-left: 3em;
            text-indent: -1em;
        }
    </style>`;

    description += `<h1>${htmlspecialchars(name)}</h1>`;

    var conditionElem = document.getElementById(`condition-name${language_id}`);
    var conditionName = conditionElem ? conditionElem.textContent.trim() : 'Unknown Condition';
    description += `<h3 style="color: darkblue;"><b>Condition:</b> <b style="color: black;">${conditionName}</b></h3>`;

    // Fonction pour ajouter une section si le contenu est valide
    function addSection(title, fieldName) {
        var fieldElem = form.querySelector(`textarea[name="product_description[${language_id}][${fieldName}]"]`);
        if (fieldElem) {
            var fieldValue = fieldElem.value.trim();
            if (fieldValue && fieldValue !== '<p><br></p>') {
                description += `<h3 style="color: darkblue;"><b>${title}:</b></h3>`;
                description += fieldValue;
            }
        }
    }

    addSection('Additional Conditions', 'condition_supp');
    addSection('Included Accessories', 'included_accessories');
    addSection('Description', 'description_supp');

    // Ajout des Specific Features
    description += '<h3 style="color: darkblue;"><b>Specific Features:</b></h3><ul>';
    let specificsRows = form.querySelectorAll(`[id^="specifics_${language_id}"]`);

    specificsRows.forEach(function(row) {
        let nameElem = row.querySelector(`div[id^="product_description_${language_id}_specifics"][id$="_Name"]`);
        let valueElem = row.querySelector('input[name*="[Value]"], select[name*="[Value][]"], select[name*="[Value]"]');

        if (nameElem && valueElem) {
            let name = nameElem.textContent.trim();
            let values = [];

            if (valueElem.tagName.toLowerCase() === 'select' && valueElem.multiple) {
                values = Array.from(valueElem.selectedOptions)
                              .map(option => option.value.trim())
                              .filter(value => value !== ''); // Supprime les valeurs vides
            } else {
                let value = valueElem.value.trim();
                if (value !== '') {
                    values.push(value);
                }
            }

            // Ajouter uniquement si des valeurs valides existent
            if (values.length > 0) {
                description += `<li><b>${name}:</b> ${values.map(ucwords).join(', ')}</li>`;
            }
        }
    });

    description += '</ul>';

    // Définition des termes en fonction de la langue
    let labels = language_id == 1 ? {
        Model: 'Model:',
        Dimension: 'Package Dimension:',
        Weight: 'Package Weight:',
        Lbs: ' Lbs',
        Inch: ' Inch'
    } : {
        Model: 'Modèle:',
        Dimension: 'Dimensions du colis:',
        Weight: 'Poids du colis:',
        Lbs: ' Livres',
        Inch: ' Pouces'
    };

    // Ajout des informations produit
    function getInputValue(name) {
        var elem = form.querySelector(`input[name="${name}"]`);
        return elem ? parseFloat(elem.value) || 'N/A' : 'N/A';
    }

    description += `<p><b>${labels.Model}</b> ${ucwords(htmlspecialchars(getInputValue("model")))}</p>`;
    description += `<p><b>${labels.Dimension}</b> ${getInputValue("length")}x${getInputValue("width")}x${getInputValue("height")}${labels.Inch}</p>`;
    description += `<p><b>${labels.Weight}</b> ${getInputValue("weight")}${labels.Lbs}</p>`;

    // Gestion des images
    var imageElem = document.getElementById('thumb-image');
    if (imageElem) {
        var imgTag = imageElem.querySelector('img');
        if (imgTag) {
            var imageUrl = imgTag.src.replace('-100x100.', '.').replace('cache/', '');
            description += '<h3 style="color: darkblue;"><b>Photos:</b></h3>';
            description += '<table bgcolor="FFFFFF" style="width: 500px;" border="1" cellspacing="1" cellpadding="5" align="center"><tbody>';
            description += `<tr><td style="text-align: center;" align="center" valign="middle"><img src="${imageUrl}" width="450"></td></tr>`;

            var imageElements = document.querySelectorAll('[id^="image-row"] img');
            imageElements.forEach(function(img) {
                var imageUrl = img.src.replace('-100x100.', '.').replace('cache/', '');
                description += `<tr><td style="text-align: center;" align="center" valign="middle"><img src="${imageUrl}" width="450"></td></tr>`;
            });

            description += '</tbody></table>';
        }
    }

    // Mise à jour du champ description et affichage
    var descriptionTextarea = document.querySelector(`textarea[name="product_description[${language_id}][description]"]`);
    if (descriptionTextarea) {
        descriptionTextarea.value = description;
        var displayDivId = descriptionTextarea.id.replace('product_description_', 'display_product_description_');
        var displayDiv = document.getElementById(displayDivId);
        if (displayDiv) {
            displayDiv.innerHTML = description;
        }
    }
}
function generateDescriptionOLD(language_id) {
  const product_id = document.getElementById('product_id').value;

  var form = document.getElementById('form-product');
  var name = form.querySelector('input[name="product_description['+ language_id +'][name]"]').value;
  var description = '<style>';
      description += '.secondary-list-item {';
      description += 'list-style-type: none;';
      description += 'padding-left: 3em; /* Adjust the padding as needed */';
      description += 'text-indent: -1em;';
      description += ' }';
      description += '</style>' ;
      description += '<h1>' + htmlspecialchars(name) + '</h1>';

  var conditionname = document.getElementById("condition-name" + language_id);

  description += '<h3 style="color: darkblue;"><b>Condition:</b> <b style="color: black;">' + (conditionname.textContent) + '</b></h3>';

  var additionalConditions = form.querySelector('textarea[name="product_description[' + language_id +'][condition_supp]"]').value.trim();
  if (additionalConditions && additionalConditions != '<p><br></p>') {
      description += '<h4 style="color: red;"><b>Additional Conditions:</b></h4>';
      description += additionalConditions;
  }
  var includedAccessories = form.querySelector('textarea[name="product_description['+ language_id +'][included_accessories]"]').value.trim();
  if (includedAccessories && includedAccessories != '<p><br></p>') {
      description += '<h3 style="color: darkblue;"><b>Included Accessories:</b></h3>';
      description += includedAccessories;
  } 

  var additionalDescription = form.querySelector('textarea[name="product_description['+ language_id +'][description_supp]"]').value.trim();
  if (additionalDescription && additionalDescription != '<p><br></p>') {
      description += '<h3 style="color: darkblue;"><b>Description:</b></h3>';
      description += additionalDescription;
  }


     description +=  '<h3 style="color: darkblue;"><b>Specific Features:</b></h3><ul>';

    let specificsRows = form.querySelectorAll(`[id^="specifics_${language_id}"]`);

    specificsRows.forEach(function(row) {
        let nameElem = row.querySelector(`div[id^="product_description_${language_id}_specifics"][id$="_Name"]`);
        let valueElem = row.querySelector('input[name*="[Value]"], select[name*="[Value][]"], select[name*="[Value]"]');

        if (nameElem && valueElem) {
            let name = nameElem.textContent.trim();
            let values = [];

            if (valueElem.tagName.toLowerCase() === 'select' && valueElem.multiple) {
                values = Array.from(valueElem.selectedOptions)
                              .map(option => option.value.trim())
                              .filter(value => value !== ''); // Supprime les valeurs vides
            } else {
                let value = valueElem.value.trim();
                if (value !== '') {
                    values.push(value);
                }
            }

            // Ajouter uniquement si des valeurs valides existent
            if (values.length > 0) {
                description += `<li><b>${name}:</b> ${values.map(ucwords).join(', ')}</li>`;
            }
        }
    });

    description += '</ul>';


/**
 * Fonction ucwords pour mettre en majuscule la première lettre de chaque mot
 */


if (language_id ==1){
    var Model='Model:';
    var Dimension='Package Dimension:';
    var Weight='Package Weight:';
    var Lbs= ' Lbs';
    var Inch= ' Inch';
}else{
    var Model = 'Modèle: ';
    var Dimension = 'Dimensions du colis: ';
    var Weight = 'Poids du colis: ';
    var Lbs= ' Livres';
    var Inch= ' Pouces';
}
  description += '<p><b>' + Model +'</b> ' + ucwords(htmlspecialchars(form.querySelector('input[name="model"]').value)) + '</p>';
  description += '<p><b>' + Dimension + '</b> ' + doubleval(form.querySelector('input[name="length"]').value) + 'x' + doubleval(form.querySelector('input[name="width"]').value) + 'x' + doubleval(form.querySelector('input[name="height"]').value) + Inch + '</p>';
  description += '<p><b>' + Weight + '</b> ' + doubleval(form.querySelector('input[name="weight"]').value) + Lbs +'</p>';

  var imageUrl = document.getElementById('thumb-image').querySelector('img').src
      .replace('-100x100.', '.')
      .replace('cache/', '');

  description += '<h3 style="color: darkblue;"><b>Photos:</b></h3>';
  description += '<table bgcolor="FFFFFF" style="width: 500px;" border="1" cellspacing="1" cellpadding="5" align="center"><tbody>';
  description += '<tr><td style="text-align: center;" align="center" valign="middle"><img src="' + imageUrl + '" width="450"></td></tr>';

  var imageElements = document.querySelectorAll('[id^="image-row"] img');

  imageElements.forEach(function(img) {
      var imageUrl = img.src.replace('-100x100.', '.').replace('cache/', '');
      description += '<tr><td style="text-align: center;" align="center" valign="middle"><img src="' + imageUrl + '" width="450"></td></tr>';
  });

  description += '</tbody></table>';

  var descriptionTextarea = document.querySelector('textarea[name="product_description[' + language_id +'][description]"]');
  if (descriptionTextarea) {
      descriptionTextarea.value = description;
      // Met à jour le div correspondant pour l'affichage
      var displayDivId = descriptionTextarea.id.replace('product_description_', 'display_product_description_');
      var displayDiv = document.getElementById(displayDivId);
      if (displayDiv) {
          displayDiv.innerHTML = description;
      }
  }
}

function htmlspecialchars(str) {
    return str.replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#039;');
}

function extractRowIdFromElementId(elementId) {
    var parts = elementId.split('_');
    return parts[parts.length - 1];
}

// Fonction pour calculer les frais d'expédition
function calculateShippingCost() {
   
    // Récupération des valeurs des champs de dimensions et de poids
    var length = parseFloat(document.getElementById('input-length').value);
    var width = parseFloat(document.getElementById('input-width').value);
    var height = parseFloat(document.getElementById('input-height').value);
    var weight = parseFloat(document.getElementById('input-weight').value);

    // Vérification que toutes les valeurs sont supérieures à 0
    if (length > 0 && width > 0 && height > 0 && weight > 0) {
        // Récupération du product_id depuis le champ caché
        var product_id = document.querySelector('input[name="product_id"]').value;
        var token = document.querySelector('input[name="token"]').value;

        // Création de l'objet de données
        var data = {
            product_id: product_id,
            length: length,
            width: width,
            height: height,
            weight: weight
        };

        // Envoi de la requête AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'index.php?route=shopmanager/shipping.get_shipping&token=' + token, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function () {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Mise à jour des champs de coût et de transporteur
                    document.getElementById('input-shipping-cost').value = response.message['shipping_cost'];
                    document.getElementById('input-shipping-carrier').value = response.message['shipping_carrier'];
                    document.getElementById('shipping-carrier').textContent = response.message['shipping_carrier'];

                    // Mise à jour du prix avec les frais d'expédition
                    updatePriceWithShipping();
                } else {
                    console.error(response.message);
                }
            } else {
                console.error('Erreur lors de la requête AJAX.');
            }
        };
        xhr.send(JSON.stringify(data));
    } else {
        console.warn('Les valeurs de poids et de dimensions doivent être supérieures à 0.');
    }
}


// Fonction pour mettre à jour le prix avec les frais d'expédition
function updatePriceWithShipping() {
    var marketplaceItemInputs = document.querySelectorAll('[id^="marketplace_items_"]');

    var priceInput = document.getElementById('input-price');
    var shippingCostInput = document.getElementById('input-shipping-cost');
    var priceWithShippingInput = document.getElementById('input-price-with-shipping');
    var price = parseFloat(priceInput.value) || 0;
    var shippingCost = parseFloat(shippingCostInput.value) || 0;

    priceWithShippingInput.value = (price + shippingCost).toFixed(2);
    //console.log('priceWithShippingInput2: ' + priceWithShippingInput.value);
    if (shippingCost === 0) {
        calculateShippingCost();
    }

    marketplaceItemInputs.forEach(function(input) {
        // Récupération de la valeur de l'input
      //  var marketplace_item_id = input.value.trim();
    
        // Conversion de la valeur en nombre
   //     var itemIdNumber = Number(marketplace_item_id);
        var encodedJson = input.value.trim();
        var decodedJson = decodeHtmlEntities(encodedJson);
        var jsonObject = JSON.parse(decodedJson);

        // Extraire la propriété marketplace_item_id
        var marketplace_item_id = jsonObject.marketplace_item_id;
        // Vérification si la conversion est réussie et si l'ID est supérieur à zéro
        if (marketplace_item_id) {
            //console.log('marketplace_item_id: ' + marketplace_item_id);
            // Appel de la fonction editEbayPrice avec l'ID de l'article et le prix formaté
            editEbayPrice(marketplace_item_id, price.toFixed(2));
        } else {
            console.warn('Valeur non valide pour marketplace_item_id:', marketplace_item_id);
        }
    });
}

// Fonction pour mettre à jour le prix de base en fonction du prix avec expédition
function updatePrice() {
    var marketplaceItemInputs = document.querySelectorAll('[id^="marketplace_items_"]');

    var priceWithShippingInput = document.getElementById('input-price-with-shipping');
    var shippingCostInput = document.getElementById('input-shipping-cost');
    var priceInput = document.getElementById('input-price');
    var discountInput = document.getElementById('input-discount');

    var priceWithShipping = parseFloat(priceWithShippingInput.value) || 0;
    var shippingCost = parseFloat(shippingCostInput.value) || 0;

    priceInput.value = (priceWithShipping - shippingCost).toFixed(2);

    if (discountInput && discountInput.value>0) {
        var discount = parseFloat(1-(discountInput.value/100)) || 0;
        priceInput.value = (priceInput.value/discount).toFixed(2);
        updatePriceWithShipping();
    }
    marketplaceItemInputs.forEach(function(input) {
        // Récupération de la valeur de l'input
        var encodedJson = input.value.trim();
        var decodedJson = decodeHtmlEntities(encodedJson);
        var jsonObject = JSON.parse(decodedJson);

        // Extraire la propriété marketplace_item_id
        var marketplace_item_id = jsonObject.marketplace_item_id;
    
        // Vérification si la conversion est réussie et si l'ID est supérieur à zéro
        if (marketplace_item_id) {
            //console.log('marketplace_item_id: ' + marketplace_item_id);
                        // Appel de la fonction editEbayPrice avec l'ID de l'article et le prix formaté
            editEbayPrice(marketplace_item_id, priceInput.value);
        } else {
            console.warn('Valeur non valide pour marketplace_item_id:', marketplace_item_id);
        }
    });
}





// Fonction pour mettre à jour le poids en onces
function updateWeightOz() {
    var weightInput = document.getElementById('input-weight');
    var weightOzInput = document.getElementById('input-weight-oz');

    var weight = parseFloat(weightInput.value) || 0;
    var weightOz = Math.round(weight * 16);

    weightOzInput.value = weightOz;
    calculateShippingCost();
}

// Fonction pour mettre à jour le poids en livres
function updateWeight() {
    var weightOzInput = document.getElementById('input-weight-oz');
    var weightInput = document.getElementById('input-weight');

    var weightOz = parseFloat(weightOzInput.value) || 0;
    var weight = (weightOz / 16).toFixed(3);

    weightInput.value = weight;
    calculateShippingCost();
}


function trfValue(specificsRow) {
   //console.log('trfValue specificsRow ' + specificsRow);
    // Récupérer la valeur actuelle du champ caché
    var hiddenValue = $('#actual_value_1_' + specificsRow).val();

    // Sélectionner le champ de saisie cible
    var targetElement = $('#product_description_1_' + specificsRow);

    // Stocker la valeur initiale
    var originalValue;
    if (targetElement.is('select[multiple]')) {
        var selectedValues = targetElement.val();
        originalValue = selectedValues ? selectedValues.join(',') : ''; // Vérification si 'selectedValues' n'est pas null
    } else {
        originalValue = targetElement.val();
    }
    $('#hidden_original_value_1_' + specificsRow).val(originalValue);

    // Vérifier si le champ cible est un select avec l'attribut multiple
    if (targetElement.is('select[multiple]')) {
        var values = hiddenValue.split(',');

        // Ajouter les valeurs manquantes au début du select
        values.forEach(function(value) {
            if (targetElement.find('option[value="' + value + '"]').length === 0) {
                targetElement.prepend('<option value="' + value + '">' + value + '</option>');
            }
        });

        // Mettre à jour la valeur du select
      //  targetElement.val(values).trigger('change');
    } 
    // Vérifier si le champ cible est un select sans l'attribut multiple
    else if (targetElement.is('select')) {
        var value = hiddenValue;

        // Ajouter la valeur manquante au début du select
        if (targetElement.find('option[value="' + value + '"]').length === 0) {
            targetElement.prepend('<option value="' + value + '">' + value + '</option>');
        }

        // Mettre à jour la valeur du select
        targetElement.val(value).trigger('change');
    } 
    // Vérifier si le champ cible est un textarea
    else if (targetElement.is('textarea')) {
        targetElement.val(hiddenValue.replace(/,/g, '\n'));
    } 
    // Sinon, il s'agit d'un input de type texte
    else if (targetElement.is('input')) {
        targetElement.val(hiddenValue);
    }

    // Masquer le bouton Transfer et afficher le bouton Undo
    $('#btTrf' + specificsRow).hide();
    $('#btUndo' + specificsRow).show();
}

function undoValue(specificsRow) {
   //console.log('undoValuespecificsRow ' + specificsRow);
    // Récupérer la valeur originale stockée
    var originalValue = $('#hidden_original_value_1_' + specificsRow).val();

    // Sélectionner le champ de saisie cible
    var targetElement = $('#product_description_1_' + specificsRow);

    // Vérifier si le champ cible est un select avec l'attribut multiple
    if (targetElement.is('select[multiple]')) {
        var values = originalValue.split(',');

        // Mettre à jour la valeur du select
      //  targetElement.val(values).trigger('change');
    } 
    // Vérifier si le champ cible est un select sans l'attribut multiple
    else if (targetElement.is('select')) {
        targetElement.val(originalValue).trigger('change');
    } 
    // Vérifier si le champ cible est un textarea
    else if (targetElement.is('textarea')) {
        targetElement.val(originalValue.replace(/,/g, '\n'));
    } 
    // Sinon, il s'agit d'un input de type texte
    else if (targetElement.is('input')) {
        targetElement.val(originalValue);
    }

    // Masquer le bouton Undo et afficher le bouton Transfer
    $('#btUndo' + specificsRow).hide();
    $('#btTrf' + specificsRow).show();
}


// Initialiser les écouteurs d'événements après le chargement complet de la page
window.addEventListener('load', function() {
    const product_id = document.getElementById('product_id').value;

    const inputField = document.getElementById('product_description_1_0');

    // Vérifier si le champ n'est pas vide
    if (inputField.value.trim() !== '') {
      //console.log('Le champ n\'est pas vide');
        // Vous pouvez ajouter ici le code que vous souhaitez exécuter si le champ n'est pas vide
        if (typeof generateInfo === 'function') {
    generateInfo();
}

       
    }
   

    var form = document.getElementById('form-product');
    var cancelLink = document.querySelector('a.btn-default[data-toggle="tooltip"]'); // Ajustez le sélecteur ici
    var isFormModified = false;

    if (!form) {
        console.error('Form element not found');
        return;
    }

    if (!cancelLink) {
        console.error('Cancel link not found');
        return;
    }

    var elements = form.querySelectorAll('input, select, textarea');

    elements.forEach(function(element) {
        if (element.tagName.toLowerCase() === 'textarea') {
            if (element.classList.contains('summernote')) {
                $(element).on('summernote.blur', function() {
                    isFormModified = true;
                });
            } else {
                element.addEventListener('blur', function() {
                    isFormModified = true;
                });
            }
        } else {
            element.addEventListener('change', function() {
                isFormModified = true;
            });
        }
    });

   /* cancelLink.addEventListener('click', function(event) {
        // Vérifie que l'utilisateur clique bien sur le lien d'annulation
        if (isFormModified) {
            var confirmLeave = confirm('Des modifications ont été effectuées. Si vous quittez cette page, les modifications seront perdues. Voulez-vous continuer ?');
            if (!confirmLeave) {
                event.preventDefault();
            }
        }
    });*/

   /* form.addEventListener('submit', function() {
      //  targetElement.val(values).trigger('change');
        // Réinitialise isFormModified à false lorsque le formulaire est soumis
        
        isFormModified = false;
    });*/





    var form = document.getElementById('form-product');
    var isFormModified = false;

    if (!form) {
        console.error('Form element not found');
        return;
    }

    var elements = form.querySelectorAll('input, select, textarea');
    //console.log('elements ' + JSON.stringify(elements));
    elements.forEach(function(element) {
       
        if (element.tagName.toLowerCase() === 'textarea' && element.classList.contains('summernote')) {
            $(element).on('summernote.blur', function() {
                isFormModified = true;
                if (typeof generateInfo === 'function') {
    generateInfo();
}

            });
        } else {
            element.addEventListener('change', function() {
                isFormModified = true;
                if (typeof generateInfo === 'function') {
    generateInfo();
}

            });
        }
    });
   
    var lengthInput = document.getElementById('input-length');
    if (lengthInput) {
        lengthInput.addEventListener('change', calculateShippingCost);
    }

    var widthInput = document.getElementById('input-width');
    if (widthInput) {
        widthInput.addEventListener('change', calculateShippingCost);
    }

    var heightInput = document.getElementById('input-height');
    if (heightInput) {
        heightInput.addEventListener('change', calculateShippingCost);
    }

    var weightInput = document.getElementById('input-weight');
    if (weightInput) {
        weightInput.addEventListener('change', calculateShippingCost);
    }

    var priceInput = document.getElementById('input-price');
    if (priceInput) {
        priceInput.addEventListener('change', updatePriceWithShipping);
    }

    var shippingCostInput = document.getElementById('input-shipping-cost');
    if (shippingCostInput) {
        shippingCostInput.addEventListener('change', updatePriceWithShipping);
    }

    var priceWithShippingInput = document.getElementById('input-price-with-shipping');
    if (priceWithShippingInput) {
        priceWithShippingInput.addEventListener('change', updatePrice);
    }
    
    document.getElementById('convertButton').addEventListener('click', function() {
        const textarea = document.querySelector('textarea.summernote');
        if (textarea) {
            let text = textarea.value;

            // Diviser le texte par la virgule et envelopper chaque segment dans <li>
            let formattedText = '<ul>' + text.split(',').map(line => `<li>${line.trim()}</li>`).join('') + '</ul>';

            // Injecter le texte formaté dans le textarea
            textarea.value = formattedText;

            // Si Summernote est activé, mettre à jour son contenu
            if ($(textarea).hasClass('summernote')) {
                $(textarea).summernote('code', formattedText);
            }
        } else {
            console.error('Textarea non trouvé');
        }
    });


    
    $(document).on('change', '.multiselect', function() {
        updateMultiselectSize();
    });
    
    $(document).ready(function() {
        updateMultiselectSize();
    });
  /*  window.addEventListener('beforeunload', function(event) {
        if (isFormModified) {
            var message = 'Des modifications ont été effectuées. Si vous quittez cette page, les modifications seront perdues. Voulez-vous continuer ?';
            event.returnValue = message; // Standard pour certains navigateurs
            return message; // Standard pour d'autres navigateurs
        }
    });*/

});

document.addEventListener('DOMContentLoaded', function () {
    console.log('load 1336');
    initializeDragAndDrop();
    const locationInput = document.getElementById('input-location');
    const quantityInput = document.getElementById('input-quantity');
    const saveButton = document.getElementById('saveButton');
    const cancelButton = document.getElementById('cancelButton'); // Correction ici
    const form = document.getElementById('form-product');

    // Création du message d'erreur
    const messageElement = document.createElement('span');
    messageElement.style.color = 'red';
    messageElement.textContent = 'Location cannot be empty when quantity is greater than 0';
    messageElement.style.display = 'none';
    locationInput.parentElement.appendChild(messageElement);

    function validateForm() {
        const isLocationEmpty = locationInput.value.trim() === '';
        const quantityValue = parseFloat(quantityInput.value) || 0;

        if (quantityValue > 0 && isLocationEmpty) {
            //console.log('NOLocationEmpty', quantityValue);
            messageElement.style.display = 'inline';
            saveButton.disabled = true;
            saveButton.style.opacity = '0.5'; // Griser le bouton
        } else {
            //console.log('isLocationEmpty', quantityValue);
            messageElement.style.display = 'none';
            saveButton.disabled = false;
            saveButton.style.opacity = '1';
        }
        //console.log('validateForm', validateForm);
    }

    function removeCancelButton() {
        //console.log('cancelButton', cancelButton);
        if (cancelButton) {
            cancelButton.remove(); // Supprime le bouton du DOM
        }
    }

    // Désactiver le bouton save au chargement
    saveButton.disabled = true;
    saveButton.style.opacity = '0.5';

    // Écouter les événements de modification pour valider et supprimer le bouton cancel
    form.addEventListener('input', function () {
        validateForm();
        removeCancelButton();
    });

    form.addEventListener('change', function () {
        validateForm();
        removeCancelButton();
        //handleTranslationAndModal('form-product');
    });

    form.addEventListener('select', function () {
        validateForm();
        removeCancelButton();
        //handleTranslationAndModal('form-product');
    });

    // Vérification au chargement de la page
    //validateForm();
});




document.addEventListener('DOMContentLoaded', function() {
    console.log('load 1405');
    var token = document.querySelector('input[name="token"]').value;
    const sku = document.getElementById('input-sku').value;
    const upc= document.querySelector('#input-upc').value;
    const quantityInput = document.getElementById('input-quantity');
    const unallocatedQuantityInput = document.getElementById('input-unallocated-quantity');

    let initialQuantity = parseInt(quantityInput.value, 10);
    let initialUnallocatedQuantity = parseInt(unallocatedQuantityInput.value, 10);


   //console.log('SKU', sku);
  //console.log('upc', upc);



    function checkQuantityChange() {
        const currentQuantity = parseInt(quantityInput.value, 10);
        const currentUnallocatedQuantity = parseInt(unallocatedQuantityInput.value, 10);
    

        let quantityToPrint = 0;

        if (currentQuantity > initialQuantity) {
            quantityToPrint = currentQuantity - initialQuantity;
        }

        if (currentUnallocatedQuantity > initialUnallocatedQuantity) {
            quantityToPrint += currentUnallocatedQuantity - initialUnallocatedQuantity;
        }

        if (quantityToPrint > 0) {
            openPrintLabel(sku, upc, quantityToPrint, '','no');
        }

        initialQuantity = currentQuantity;
        initialUnallocatedQuantity = currentUnallocatedQuantity;

    }

    quantityInput.addEventListener('change', checkQuantityChange);
    unallocatedQuantityInput.addEventListener('change', checkQuantityChange);

    $('#input-image-principal').on('change', toggleUploadButton);
    $('#input-images-secondary').on('change', toggleUploadButton);
    $('#input-sourcecode').on('input', toggleUploadButton); // Écouteur pour la modification du contenu de la source


    // Initialiser l'état du bouton lors du chargement de la page
    toggleUploadButton();

    
// Fonction pour gérer l'état du bouton "Upload Images"
    function toggleUploadButton() {
        // Récupérer les fichiers sélectionnés dans les champs de fichier
        var primaryImageFile = $('#input-image-principal')[0] ? $('#input-image-principal')[0].files[0] : null;
        var secondaryImageFiles = $('#input-images-secondary')[0] ? $('#input-images-secondary')[0].files : [];
        var sourcescode = $('#input-sourcecode').val();
     //console.log('primaryImageFile : ' + primaryImageFile);
     //console.log('secondaryImageFiles.length : ' + secondaryImageFiles.length);
        // Activer ou désactiver le bouton de téléchargement en fonction de la sélection de fichiers
        $('#upload-images-btn').prop('disabled', !(secondaryImageFiles.length > 0));
        $('#upload-image-btn').prop('disabled', !(primaryImageFile));
        $('#sourcescode-btn').prop('disabled', !sourcescode || sourcescode.trim() === ''); // Désactiver si sourcescode est vide
    }
});


document.addEventListener("DOMContentLoaded", function () {
    console.log('load 1498');
    // Sélectionner tous les champs de texte dans le formulaire
    var textFields = document.querySelectorAll('form input[type="text"]');

    textFields.forEach(function (field) {
        // Exclure les champs avec des valeurs numériques et les champs ayant "category" dans leur nom
        if (!isNaN(field.value) || field.name.toLowerCase().includes('category') || field.name.toLowerCase().includes('location')) {
            return; // Passer au prochain champ s'il est numérique ou contient "category"
        }

        // Créer un conteneur div pour le champ et le bouton
        var containerDiv = document.createElement('div');
        containerDiv.className = 'input-group-custom'; // Ajout de classe Bootstrap pour aligner le champ et le bouton

        // Insérer le conteneur juste avant le champ actuel
        field.parentNode.insertBefore(containerDiv, field);

        // Déplacer le champ dans le conteneur
        containerDiv.appendChild(field);

        // Créer un bouton de mise en majuscule
        var capitalizeButton = document.createElement('button');
        capitalizeButton.type = 'button';
        capitalizeButton.className = 'btn btn-warning'; // Ajout de classes Bootstrap pour le style
        capitalizeButton.title = 'UpperCase';
        capitalizeButton.innerHTML = '<i class="fa fa-text-height"></i>'; // Utilisation de FontAwesome

        // Ajouter l'événement pour mettre en majuscule la première lettre de chaque mot
        capitalizeButton.addEventListener('click', function () {
            // Récupérer la valeur du champ de texte
            var text = field.value;
        //console.log('Texte original : ' + text);
            // Vérifier si le texte est en majuscules
            if (text === text.toUpperCase()) {
                // Si tout le texte est en majuscules, le convertir en minuscules
                text = text.toLowerCase();
            }
           // Mettre la première lettre de chaque mot en majuscule, en préservant les mots existants avec des apostrophes et en conservant les majuscules originales
           var capitalizedText = text.replace(/\b([a-zA-ZÀ-ÿ][a-zA-ZÀ-ÿ']*)\b/g, function (match, word) {
            // Si le mot contient une apostrophe ou est déjà capitalisé correctement, on le laisse tel quel
            if (word === word.charAt(0).toUpperCase() + word.slice(1)) {
                return word;
            }
            // Sinon, on met en majuscule la première lettre et on garde le reste tel quel
            return word.charAt(0).toUpperCase() + word.slice(1);
        });

     //console.log('Texte capitalisé : ' + capitalizedText);
            // Mettre à jour le champ de texte avec le texte formaté
            field.value = capitalizedText;
        });

        // Insérer le bouton dans le conteneur à gauche du champ de texte
        containerDiv.insertBefore(capitalizeButton, field);
    });
});

document.addEventListener("DOMContentLoaded", function () {
    console.log('load 1556');
    // Sélectionner le champ de texte "location"
    var locationInput = document.querySelector('input[name="location"]');

    if (locationInput) {
        // Forcer la valeur à être en majuscule lors du chargement de la page
        locationInput.value = locationInput.value.toUpperCase();

        // Ajouter un écouteur d'événement pour chaque changement de valeur dans le champ
        locationInput.addEventListener('input', function () {
            // Mettre à jour la valeur en majuscule
            this.value = this.value.toUpperCase();
        });

        // Forcer la valeur à être en majuscule lorsque le champ perd le focus
        locationInput.addEventListener('blur', function () {
            this.value = this.value.toUpperCase();
        });

        // Forcer la valeur à être en majuscule lorsque le champ est modifié par un script
        var observer = new MutationObserver(function () {
            locationInput.value = locationInput.value.toUpperCase();
        });

        observer.observe(locationInput, {
            attributes: true,
            attributeFilter: ['value'],
            childList: true,
            subtree: true
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    console.log('load 1590');

    // Récupérer les éléments
    var tokenElement = document.querySelector('input[name="token"]');
    var skuElement = document.querySelector('#input-sku');
    var upcElement = document.querySelector('#input-upc');
    var locationInput = document.getElementById('input-location');
    var printLabelButton = document.getElementById('btn-print-label');
    var printQuantityLabelButton = document.getElementById('btn-print-quantity-label');

    // Vérification des éléments requis
    if (!tokenElement || !skuElement || !locationInput || !printLabelButton || !printQuantityLabelButton) {
        console.error('Un ou plusieurs éléments nécessaires sont manquants.');
        return;
    }

    var token = tokenElement.value;

    // Fonction d'ouverture de la fenêtre d'impression
   

    // Impression à partir de la location
    printLabelButton.addEventListener('click', function () {
        var location = locationInput.value.trim();
        if (location) {
            openPrintLabel(null,null,1,location, 'yes');
        } else {
            alert('Veuillez saisir une location pour imprimer l\'étiquette.');
        }
    });

    // Impression à partir du SKU
    printQuantityLabelButton.addEventListener('click', function () {
        var sku = skuElement.value.trim();
        var upc = upcElement ? upcElement.value.trim() : null;

        if (sku) {
            openPrintLabel(sku, upc, 1, '', 'yes');
        } else {
            alert('Aucun SKU détecté pour l\'impression.');
        }
    });
});





document.addEventListener("DOMContentLoaded", function () {
    console.log('load 1660');
    // Fonction pour ajouter un nouveau fabricant
    document.getElementById("btn-search-manufacturer").addEventListener("click", function () {
        var manufacturerName = document.querySelector('input[name="manufacturer"]').value;
        suggestManufactuerNAME();
        
    });
    document.getElementById("btn-add-manufacturer").addEventListener("click", function () {
        var manufacturerName = document.querySelector('input[name="manufacturer"]').value;
        var token = document.querySelector('input[name="token"]').value;

        if (manufacturerName) {
            $.ajax({
                url: 'index.php?route=shopmanager/manufacturer.add&ajax=true&token=' + token,
                type: 'post',
                data: { name: manufacturerName },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Le fabricant a été ajouté avec succès !');
                        document.querySelector('input[name="manufacturer_id"]').value = response.manufacturer_id;
                    } else {
                        alert('Erreur : ' + response.error);
                    }
                },
                error: function (xhr, status, error) {
                    alert('Erreur lors de l\'ajout du fabricant : ' + error);
                }
            });
        } else {
            alert('Veuillez entrer un nom de fabricant.');
        }
    });

    // Fonction pour modifier un fabricant existant
    document.getElementById("btn-edit-manufacturer").addEventListener("click", function () {
        var manufacturerId = document.querySelector('input[name="manufacturer_id"]').value;
        var manufacturerName = document.querySelector('input[name="manufacturer"]').value;
        var token = document.querySelector('input[name="token"]').value;

        if (manufacturerId && manufacturerName) {
            $.ajax({
                url: 'index.php?route=shopmanager/manufacturer.edit&ajax=true&token=' + token + '&manufacturer_id=' + manufacturerId,
                type: 'post',
                data: { name: manufacturerName },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Le fabricant a été modifié avec succès !');
                    } else {
                        alert('Erreur : ' + response.error);
                    }
                },
                error: function (xhr, status, error) {
                    alert('Erreur lors de la modification du fabricant : ' + error);
                }
            });
        } else {
            alert('Veuillez sélectionner un fabricant et entrer un nom.');
        }
    });

    // Fonction pour supprimer un fabricant existant
    document.getElementById("btn-delete-manufacturer").addEventListener("click", function () {
        var manufacturerId = document.querySelector('input[name="manufacturer_id"]').value;
        var token = document.querySelector('input[name="token"]').value;

        if (manufacturerId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce fabricant ?')) {
                $.ajax({
                    url: 'index.php?route=shopmanager/manufacturer.delete&ajax=true&token=' + token + '&manufacturer_id=' + manufacturerId,
                    type: 'post',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert('Le fabricant a été supprimé avec succès !');
                            document.querySelector('input[name="manufacturer"]').value = '';
                            document.querySelector('input[name="manufacturer_id"]').value = '';
                        } else {
                            alert('Erreur : ' + response.error);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('Erreur lors de la suppression du fabricant : ' + error);
                    }
                });
            }
        } else {
            alert('Veuillez sélectionner un fabricant à supprimer.');
        }
    });
});


function checkFormStatus() {
    var categoryChecked = document.querySelector('input[name^="product_category"]:checked');
    var conditionChecked = document.querySelector('input[name="condition_id"]:checked');
    var priceChecked = document.querySelector('input[name^="price_ebay"]:checked');
    var product_id = document.querySelector('input[name="product_id"]').value;

  //console.log('product_id: ' + product_id);
    var saveButton = document.getElementById('saveButton');
    
    if ((categoryChecked && conditionChecked && priceChecked )|| product_id) {
     // saveButton.disabled = false;
    } else {
    //  saveButton.disabled = true;
    }
  }
   // Fonction qui met à jour le bouton radio save-data-radio basé sur la sélection de condition
    function updateCondition(conditionMarketplaceItemId,selectedConditionId) {
        // Désélectionner tous les boutons radio save-data-radio
        const radios = document.querySelectorAll('.save-data-radio');
        
        radios.forEach(function(radio) {
            radio.checked = false; // Décocher tous les boutons
        });
    
        // Sélectionner le bouton radio correspondant à conditionMarketplaceItemId
        const targetRadio = document.querySelector(`#condition_marketplace_item_id${conditionMarketplaceItemId}`);
        
        if (targetRadio) {
          
            targetRadio.checked = true; // Cocher le bouton radio correspondant
            updateAllLabels(selectedConditionId);
        }
    }
  // Fonction pour ajouter un champ texte et calculer le prix manquant
function removeUnnecessaryConditions() {
    const rows = document.querySelectorAll('#conditionsTable tbody tr');
    let prices = [];
    $('#loadingModal').modal('show');
    // Étape 1 : Extraire les prix existants et condition_marketplace_item_id pour chaque ligne
    rows.forEach(row => {
        const radioInput = row.querySelector('.save-data-radio');
        if (radioInput) {
            const conditionMarketplaceItemId = parseInt(radioInput.id.match(/\d+/)[0]);
            const priceCell = row.cells[3]; // Cellule du prix

            // Vérifier s'il y a un radio button correspondant (relatedRadio)
            const relatedRadio = document.querySelector(`#condition-${conditionMarketplaceItemId}`);
            if (relatedRadio) {
                // Vérifier s'il existe un prix
                const priceValue = parseFloat(radioInput.value) || null;  // Convertir en nombre ou laisser null

                prices.push({
                    conditionMarketplaceItemId: conditionMarketplaceItemId,
                    price: priceValue, 
                    row: row
                });
            } else {
                // Si aucun input associé n'est trouvé, on peut supprimer la ligne
                row.remove();
            }
        }
      
    });

    // Étape 2 : Parcourir les prix et calculer les valeurs manquantes
    prices.forEach((item, index) => {
        if (item.price === null) {
            let previousPrice = null;
            let nextPrice = null;

            // Trouver le prix précédent non vide
            for (let i = index - 1; i >= 0; i--) {
                if (prices[i].price !== null) {
                    previousPrice = prices[i];
                    break;
                }
            }

            // Trouver le prix suivant non vide
            for (let i = index + 1; i < prices.length; i++) {
                if (prices[i].price !== null) {
                    nextPrice = prices[i];
                    break;
                }
            }

            // Si on trouve des prix avant et après, on calcule la moyenne pondérée
            let calculatedPrice;
            if (previousPrice && nextPrice) {
                let distPrev = item.conditionMarketplaceItemId - previousPrice.conditionMarketplaceItemId;
                let distNext = nextPrice.conditionMarketplaceItemId - item.conditionMarketplaceItemId;

                // Calculer une moyenne pondérée en fonction de la distance
                calculatedPrice = ((distNext * previousPrice.price) + (distPrev * nextPrice.price)) / (distPrev + distNext);
            } else if (previousPrice && !nextPrice) {
                // Si c'est la dernière valeur qui est vide, utiliser 50% du prix précédent
                calculatedPrice = previousPrice.price * 0.5;
            }

            // Ajouter le symbole $ s'il n'est pas déjà présent et éviter de dupliquer le champ texte
            const priceCell = item.row.cells[3]; // La cellule contenant le prix
            if (!priceCell.querySelector('.manual-input')) {
                // Ajouter le symbole $ avant le champ texte
                priceCell.innerHTML = `$ `;

                const textInput = document.createElement('input');
                textInput.type = 'text';
                textInput.placeholder = 'Entrez une valeur';
                textInput.className = 'manual-input';
                textInput.value = calculatedPrice.toFixed(2);

                // Ajouter l'événement pour mettre à jour la valeur du bouton radio
                textInput.addEventListener('input', function() {
                    item.row.querySelector('.save-data-radio').value = textInput.value; // Mettre à jour la valeur du radio dans le tableau
                });

                // Ajouter le champ texte après le symbole $
                priceCell.appendChild(textInput);
            }

            // Mettre à jour la valeur du bouton radio
            item.row.querySelector('.save-data-radio').value = calculatedPrice.toFixed(2);
        }
        $('#loadingModal').modal('hide');
    });
}

function changeProduct(url) {
    // Définir l'URL de redirection avec le token et l'ID du produit
     
    // Redirection vers l'URL spécifiée
    window.location.href = url;
}
function initializeDragAndDrop() {
  //console.log("Initializing drag-and-drop functionality");

    // Gestion de la zone de dépôt pour l'image principale
    if (document.getElementById('drop-area-principal')) {
      //console.log("Configuring drag-and-drop for principal area");

        document.getElementById('drop-area-principal').addEventListener('click', function() {
       //console.log("Principal area clicked - opening file input");
            document.getElementById('file-input-principal').click();
        });

        document.getElementById('drop-area-principal').addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '#f0f0f0';
     //console.log("Dragging over principal drop area");
        });

        document.getElementById('drop-area-principal').addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '';
      //console.log("Drag leave from principal drop area");
        });

        document.getElementById('drop-area-principal').addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '';
            var files = e.dataTransfer.files;
      //console.log("Files dropped in principal area:", files);
            uploadFiles(files, 'principal');
        });
    }

    // Gestion de la zone de dépôt pour les images supplémentaires
    if (document.getElementById('drop-area-secondary')) {
     //console.log("Configuring drag-and-drop for secondary area");

        document.getElementById('drop-area-secondary').addEventListener('click', function() {
     //console.log("Secondary area clicked - opening file input");
            document.getElementById('file-input-secondary').click();
        });

        document.getElementById('drop-area-secondary').addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '#f0f0f0';
     //console.log("Dragging over secondary drop area");
        });

        document.getElementById('drop-area-secondary').addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '';
        //console.log("Drag leave from secondary drop area");
        });

        document.getElementById('drop-area-secondary').addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '';
            var files = e.dataTransfer.files;
        //console.log("Files dropped in secondary area:", files);
            uploadFiles(files, 'secondary');
        });
    }

    // Gestion de l'input de fichier pour l'image principale
    if (document.getElementById('file-input-principal')) {
        document.getElementById('file-input-principal').addEventListener('change', function(e) {
            var files = this.files;
        //console.log("Files selected in principal file input:", files);
            uploadFiles(files, 'principal');
        });
    }

    // Gestion de l'input de fichier pour les images supplémentaires
    if (document.getElementById('file-input-secondary')) {
        document.getElementById('file-input-secondary').addEventListener('change', function(e) {
            var files = this.files;
         //console.log("Files selected in secondary file input:", files);
            uploadFiles(files, 'secondary');
        });
    }
}

// Appel de la fonction d'initialisation après le chargement de la page

function uploadFiles(files, type) {
    var token = document.querySelector('input[name="token"]').value;
    var product_id = document.querySelector('input[name="product_id"]').value;
  //console.log("Starting upload for:", type, "files:", files);

    var formData = new FormData();
    for (var i = 0; i < files.length; i++) {
        if (type === 'principal') {
            formData.append('imageprincipal', files[i]);
     //console.log("Appending principal image to formData:", files[i].name);
        } else {
            formData.append('imagesecondary[]', files[i]);
     //console.log("Appending secondary image to formData:", files[i].name);
        }
    }

     // Log pour inspecter le contenu de FormData
 //console.log("Inspecting FormData content:");
     for (var pair of formData.entries()) {
     //console.log(pair[0]+ ', ' + pair[1].name); // pair[1].name affichera le nom du fichier
     }

    var url = 'index.php?route=shopmanager/tools.uploadImagesFiles&product_id=' + product_id + '&token=' + token;

    // Déterminer l'URL d'upload selon le type d'image
    url  += (type === 'principal') ? '&type=pri'  : '&type=sec';
    //console.log("Uploading to URL:", url);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            //console.log("Server response:", response);

            if (response.success) {
                /*if (type === 'principal') {
                    displayUploadedImagePrincipal(response.image);
                } else {
                    displayUploadedImages(response.images);
                }*/
                    updateProductImagesUI(response.product_images);
            } else {
                console.error("Upload error:", response.error);
                alert('<?php echo $error_upload; ?>');
            }
        } else {
            console.error("XHR error - status:", xhr.status);
        }
    };
    xhr.send(formData);
    //console.log("Upload request sent to server");
}

function displayUploadedImagePrincipal(image) {
    var thumb = document.getElementById('thumb-image');
    thumb.src = image.thumb;
    document.getElementById('input-image').value = image.path;
    //console.log("Displaying uploaded principal image:", image);
}

function displayUploadedImages(images) {
    var tbody = document.getElementById('uploaded-images');
    images.forEach(function(image, index) {
        var row = document.createElement('tr');
        row.id = 'image-row' + index;
        row.innerHTML = `
            <td class="col-sm-1 text-left"><img src="${image.thumb}" alt="" class="img-thumbnail" /></td>
            <td class="col-sm-1 text-left">
                <button type="button" onclick="removeImage('${image.image}', 'sec', '#image-row${index}');" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa-solid fa-minus-circle"></i></button>
            </td>
            <td class="col-sm-4 text-right"><input type="text" name="product_image[${index}][sort_order]" value="${image.sort_order}" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>
        `;
        tbody.appendChild(row);
        //console.log("Displaying uploaded secondary image:", image);
    });
}


function uploadProductImages() {
    // Récupérer les valeurs de `product_id` et `token`
    var product_id = document.querySelector('input[name="product_id"]').value;
    var token = document.querySelector('input[name="token"]').value;
    var sourcecode = document.querySelector('textarea[name="sourcecode"]').value; // Assuming `sourcecode` is in a textarea

    // Récupérer les fichiers image du formulaire
    var formData = new FormData();
    var primaryImageFile = $('#input-image-principal')[0] ? $('#input-image-principal')[0].files[0] : null;
    var secondaryImageFiles = $('#input-images-secondary')[0] ? $('#input-images-secondary')[0].files : [];
    sourcecode = cleanHTML(sourcecode);
    //console.log("sourcecode:" + sourcecode);
    // Ajouter `product_id` et `sourcecode` aux données du formulaire
    formData.append('product_id', product_id);
    formData.append('sourcecode', sourcecode);
    //console.log('formData : ' + JSON.stringify(formData));
    // Ajouter l'image principale si elle est définie
    if (primaryImageFile) {
        formData.append('imageprincipal', primaryImageFile);
    }

    // Ajouter les images secondaires si elles sont définies
    if (secondaryImageFiles.length > 0) {
        for (var i = 0; i < secondaryImageFiles.length; i++) {
            if (secondaryImageFiles[i]) {
                formData.append('imagesecondary[]', secondaryImageFiles[i]);
            }
        }
    } else {
       //console.log("Aucune image secondaire sélectionnée.");
    }
 
    //console.log('formData : ' + JSON.stringify(formData));

    // Envoyer les données au contrôleur via AJAX
    $.ajax({
        url: 'index.php?route=shopmanager/tools.uploadImagesFiles&token=' + token,
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateProductImagesUI(response.product_images);
                $('#input-image-principal').val('');  // Efface l'image principale
                $('#input-image-secondary').val('');   // Efface les images secondaires
                $('#input-sourcecode').val('');             // Efface le code source
                $('#upload-images-btn').prop('disabled');
                $('#upload-image-btn').prop('disabled');
                $('#sourcescode-btn').prop('disabled'); // Désactiver si sourcescode est vide
            } else if (response.error) {
                alert('Erreur : ' + response.error);
            }
        },
        error: function(xhr, status, error) {
            console.error("Erreur lors du téléchargement des images : " + xhr.responseText);
            alert('Erreur lors du téléchargement des images : ' + xhr.responseText);
        }
    });
}


// Fonction pour mettre à jour l'interface avec les nouvelles images
function updateProductImagesUI(productImages) {
   
    // Mettre à jour l'image principale
    if (productImages.primary) {
        $('#thumb-image img').attr('src', productImages.primary.thumb);
        $('#thumb-image img').attr('data-placeholder', productImages.primary.thumb);
        $('#input-image').val(productImages.primary.image);
    }

    // Mettre à jour les images secondaires
   // $('#images tbody').empty(); // Vider les images existantes
    var image_row = window.image_row;
    productImages.secondary.forEach(function(imageData) {
        var html = '<tr id="image-row' + image_row + '">';
        html += '  <td class="text-center"><a href="" id="thumb-image' + image_row + '" data-toggle="image" class="img-thumbnail"><img src="' + imageData.thumb + '" alt="" title="" data-placeholder="' + imageData.thumb + '" /></a><input type="hidden" name="product_image[' + image_row + '][image]" value="' + imageData.image + '" id="input-image' + image_row + '" /></td>';
        html += '  <td class="col-sm-1 text-left"> <button type="button" onclick="removeImage(\'' + imageData.image +'\', \'sec\', \'#image-row' + image_row + '\');" data-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';

        html += '  <td class="text-right"><input type="text" name="product_image[' + image_row + '][sort_order]" value="' + imageData.sort_order + '" placeholder="Sort Order" class="form-control" /></td>';

  
        html += '</tr>';

        $('#images tbody').append(html);
        image_row++;
    });
    if (typeof generateInfo === 'function') {
    generateInfo();
}

}

function removeImage(image, type, elementSelector) {

    var product_id = document.querySelector('input[name="product_id"]').value;
    var token = document.querySelector('input[name="token"]').value;

 //console.log('image: ' + image);
  //console.log('type: ' + type);
 //console.log('elementSelector: ' + elementSelector);
  //console.log('product_id: ' + product_id);
 //console.log('token: ' + token);

    $.ajax({
        url: 'index.php?route=shopmanager/tools.deleteProductImage&token=' + token,
        type: 'post',
        data: { 
            product_id: product_id, 
            image: image, 
            type: type 
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Supprime l'élément HTML si l'opération est réussie
                $(elementSelector).remove();
             //   alert('Image supprimée avec succès.');
            } else if (response.error) {
                alert('Erreur : ' + response.error);
            }
        },
        error: function(xhr, status, error) {
            alert('Erreur lors de la suppression de l\'image : ' + error);
        }
    });
}


window.addEventListener("load", function() {
    // Maintenant, tout est complètement chargé, y compris les scripts et les images
    var product_id = document.querySelector('input[name="product_id"]').value;
    
    if (product_id && product_id === '') {
        // Soumet automatiquement le formulaire s'il n'y a pas de `product_id`
       // alert(product_idInput.value);

        document.querySelector('button[form="form-product" + product_id]').click();
    }
});

 

function editMadeInCountry(product_id) {
    var token = document.querySelector('input[name="token"]').value;
    var countrySelect = document.getElementById('input-made-in-country-id');
        if (countrySelect) {
            var made_in_country_id = countrySelect.value;
            //console.log("ID du pays sélectionné : ", made_in_country_id);
        } else {
            console.error("L'élément select avec id='input-made-in-country-id-" + product_id + "' n'existe pas.");
        }
    // var made_in_country_id = mySelect.val()
    //console.log('token: ' + token);
    //console.log('product_id: ' + product_id);
    //console.log('made_in_country_id: ' + made_in_country_id);
    //console.log('marketplace_item_id: ' + marketplace_item_id);
  //console.log('quantity: ' + quantity);
	//document.getElementByName("product[" + product_row +"][made_in_country_id]").value;
	//alert (item_id +"selected " + mySelect.val());
 	 $.ajax({
          url: 'index.php?route=shopmanager/catalog/product.editMadeInCountry&token=' + token,

		   method: "POST",
		  data: {product_id:product_id,
				made_in_country_id:made_in_country_id,
             //   marketplace_item_id:marketplace_item_id,
           //     quantity:quantity
           },
		dataType: 'json',
		crossDomain: true,
           success:function(json) {
       
			 //alert(json['succes']);
             if(made_in_country_id > 0){
                document.getElementById('check_made_in_country_id').style.backgroundColor='green';
               }else{
                document.getElementById('check_made_in_country_id').style.backgroundColor='red';
               }
            },
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	  });
}


function addToMarketplace(product_id,marketplace_account_id,marketplace_id, is_products = true) {
    var token = document.querySelector('input[name="token"]').value;
  

  
  //  var quantity = document.querySelector('input[name="quantity"]').value;
  //  var unallocated_quantity = document.querySelector('input[name="unallocated_quantity"]').value;
    //console.log('product_id :' + product_id);
    //console.log('marketplace_account_id :' + marketplace_account_id);
  //console.log('quantity :' + quantity);
  //console.log('unallocated_quantity :' + unallocated_quantity);
        $.ajax({
            url: `index.php?route=shopmanager/marketplace.addToMarketplace&token=${token}`,
            type: 'POST',
            data: {  product_id: product_id ,marketplace_account_id:marketplace_account_id,marketplace_id: marketplace_id},
            dataType: 'json',
            success: function(json) {
                if (json.error) {
                    alert(json.message);
                } else {
                    handleMarketplaceAddUIUpdate(json,marketplace_account_id,product_id,is_products);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert('An error occurred: ' + xhr.responseText);
            }
        });
    
}
function handleMarketplaceAddUIUpdate(json, marketplace_account_id, product_id, is_products = false) {
    if(is_products === true) {
        var product_underscore = product_id + '_' ;
    }else{
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

    //console.log('Message:', JSON.stringify(json));
}




// Call the function to translate all fields and show modal when the page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('load 2336');


    function update_language_id_row() {
        let totalElements = 0;
        let processedCount = 0;
        var languages = JSON.parse($('#languages_json').val());
        var sourceLanguageId = '1'; // Supposons que '1' est l'ID pour l'anglais

        document.querySelectorAll("input[id^='product_description_" + sourceLanguageId + "'], textarea[id^='product_description_" + sourceLanguageId + "'], select[id^='product_description_" + sourceLanguageId + "']")
            .forEach(function(element) {
                // Exclure les champs ayant certains mots-clés dans leur name ou id
                let elementName = element.getAttribute('name'); // Correction ici
                let elementID = element.id; // `.id` est une propriété native

                const excludedKeywords = ['[description]', '[meta_title]', '[meta_description]', '[tag]', '[meta_keyword]', 'response_', 'display_','_specifics_Name'];

                if (elementName && excludedKeywords.some(keyword => elementName.includes(keyword))) {
                    //console.log(`Skipping element: ${elementID || 'no ID'} (excluded due to name filter)`);
                    return;
                }

                if (elementID && excludedKeywords.some(keyword => elementID.includes(keyword))) {
                    //console.log(`Skipping element: ${elementID} (excluded due to ID filter)`);
                    return;
                }

                totalElements++; // Incrémenter le nombre d'éléments à traiter

                const processEvent = (event) => {
                    updateToTranslate(event);
                    processedCount++; // Compter les champs traités

                    // Vérifier si tous les champs ont été traités
                    if (processedCount >= totalElements) {
                        let tabExists = document.getElementById("tab-product_search") !== null;
    
                        if (tabExists) {
                            //console.log("All updates finished, executing handleTranslationAndModal...");
                            setTimeout(() => {
                                handleTranslationAndModal('form-product', true);
                            }, 100);
                        }
                    }
                };

                element.addEventListener("change", processEvent);
                element.addEventListener("blur", processEvent);
                element.addEventListener("select", processEvent);
            });

    
        // Cas où aucun champ n'a été trouvé à traiter
        //if (totalElements === 0) {
            //console.log("No valid elements found, executing handleTranslationAndModal immediately.");
            let tabExists = document.getElementById("tab-product-search") !== null;
    
            if (tabExists) {
                //console.log("All updates finished, executing handleTranslationAndModal...");
                setTimeout(() => {
                    handleTranslationAndModal('form-product', true);
                }, 100);
            }
            generateInfo();
        //}
    }
    
    

    function updateToTranslate(event) {
        let fieldId = event.target.id;
        let hiddenInput = document.querySelector("input#to_translate");

        if (!hiddenInput) {
            hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.id = "to_translate";
            hiddenInput.name = "to_translate";
            hiddenInput.value = "";
            document.body.appendChild(hiddenInput);
        }

        let currentValue = hiddenInput.value.trim();
        let translationData = {};

        if (currentValue) {
            try {
                translationData = JSON.parse(currentValue);
            } catch (error) {
                console.error("Erreur de parsing JSON:", error);
            }
        }

        if (!translationData[fieldId]) {
            translationData[fieldId] = true;
            hiddenInput.value = JSON.stringify(translationData);
        }
    }

   update_language_id_row();
  



// Attendre un court instant pour s'assurer que le DOM est mis à jour avant de continuer


    // Existing code for form initialization
    const product_id = document.getElementById('product_id').value;
    const inputField = document.getElementById('product_description_1_0');

    if (inputField.value.trim() !== '') {
        if (typeof generateInfo === 'function') {
            generateInfo();
        }
    }

    var form = document.getElementById('form-product');
    var cancelLink = document.querySelector('a.btn-default[data-toggle="tooltip"]');
    var isFormModified = false;

    if (!form) {
        console.error('Form element not found');
        return;
    }

    if (!cancelLink) {
        console.error('Cancel link not found');
        return;
    }

    var elements = form.querySelectorAll('input, select, textarea');

    elements.forEach(function(element) {
        if (element.tagName.toLowerCase() === 'textarea') {
            if (element.classList.contains('summernote')) {
                $(element).on('summernote.blur', function() {
                    isFormModified = true;
                });
            } else {
                element.addEventListener('blur', function() {
                    isFormModified = true;
                });
            }
        } else {
            element.addEventListener('change', function() {
                isFormModified = true;
            });
        }
    });

  /*  cancelLink.addEventListener('click', function(event) {
        if (isFormModified) {
            var confirmLeave = confirm('Des modifications ont été effectuées. Si vous quittez cette page, les modifications seront perdues. Voulez-vous continuer ?');
            if (!confirmLeave) {
                event.preventDefault();
            }
        }
    });*/

    form.addEventListener('submit', function(event) {
        // Prevent default form submission
        event.preventDefault();

        // Call the function to handle translation and modal display
        handleTranslationAndModal('form-product',false).then(() => {
            // Submit the form after handleTranslationAndModal completes
            form.submit();
        }).catch((error) => {
            console.error('Error during translation:', error);
        });
    });

   /* form.addEventListener('submit', function() {
        // Réinitialise isFormModified à false lorsque le formulaire est soumis
        isFormModified = false;
    });*/
});


document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('table_image');
    const tableBody = document.getElementById('tbody_image');  // Cibler l'élément avec l'ID tbody_images
    let rowsData = [];

    // Fonction pour vérifier et traiter la résolution de chaque image
    function checkResolution(thumbnail, checkbox, hiddenInput, imageContainer, resolutionMessage) {
        const width = thumbnail.naturalWidth;
        const height = thumbnail.naturalHeight;
        const resolution = width * height;
        const dimensions = width + 'x' + height;

        // Créer les données de l'image
        const imageData = {
            url: thumbnail.src,
            resolution: resolution
        };
        checkbox.value = JSON.stringify(imageData);
        hiddenInput.value = resolution;

        // Si l'image respecte les dimensions minimales, on affiche la résolution et on change la couleur de fond
        if (width >= 400 && height >= 600) {
            imageContainer.style.backgroundColor = 'green';
            resolutionMessage.textContent = dimensions;
        } else {  imageContainer.style.backgroundColor = 'red';
            // Si l'image ne respecte pas les dimensions, on supprime complètement la colonne Bootstrap associée
            const col = imageContainer.closest('.col-md-2');
            if (imageData.url.includes("phoenix")) {
                imageContainer.style.backgroundColor = 'red';
                resolutionMessage.textContent = dimensions;
             
            }else if (col) {
               col.remove(); // Supprimer la colonne complète avec l'image et la case à cocher
            }
            return; // On quitte la fonction car l'image a été supprimée
        }
        resolutionMessage.style.display = 'block';

        // Ajouter la ligne (div.col-md-2) avec sa résolution dans rowsData
        rowsData.push({
            row: imageContainer.closest('.col-md-2'),
            resolution: resolution
        });
    }

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

    // Fonction pour gérer les images et leur résolution
    function handleImages() {
        const thumbnails = document.querySelectorAll('.thumbnail-image');
        rowsData = []; // Réinitialiser le tableau

        thumbnails.forEach(thumbnail => {
            const fullsize = thumbnail.nextElementSibling;
            const resolutionMessage = thumbnail.parentElement.querySelector('.resolution-message');
            const imageContainer = thumbnail.closest('.image-container');
            const checkbox = imageContainer ? imageContainer.closest('.thumbnail').querySelector('.save-data-checkbox') : null;
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'image_dimensions[]';
            imageContainer.appendChild(hiddenInput);

            // Vérification de la résolution
            if (thumbnail.complete) {
                checkResolution(thumbnail, checkbox, hiddenInput, imageContainer, resolutionMessage);
            } else {
                thumbnail.addEventListener('load', function() {
                    checkResolution(thumbnail, checkbox, hiddenInput, imageContainer, resolutionMessage);
                });
            }

            // Ajout des événements sur la miniature
            addImageEvents(thumbnail, fullsize);
        });

        // Trier et reconstruire les lignes après calcul des résolutions
        Promise.all(Array.from(thumbnails).map(thumbnail => thumbnail.complete ? Promise.resolve() : new Promise(resolve => thumbnail.addEventListener('load', resolve))))
        .then(() => {
            rowsData.sort((a, b) => b.resolution - a.resolution);
            rebuildTable();
        });

        // Ajout des boutons de suppression
        addDeleteButtons();
    }



   
    // Fonction pour ajouter des boutons de suppression
    function addDeleteButtons() {
        const checkboxes = document.querySelectorAll('.save-data-checkbox');
        checkboxes.forEach(checkbox => {
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.setAttribute('data-toggle', 'tooltip');
            deleteBtn.setAttribute('title', 'Delete');
            deleteBtn.className = 'btn btn-danger';
            deleteBtn.innerHTML = '<i class="fa fa-trash-o" aria-hidden="true"></i>';

            deleteBtn.addEventListener('click', function() {
                const col = checkbox.closest('.col-md-2'); // Colonne de Bootstrap
                if (col) {
                    col.remove(); // Supprimer la colonne complète
                    refreshTable(); // Rafraîchir le tableau après suppression
                }
            });

            const cell = checkbox.closest('div.thumbnail');
            if (cell) {
                const spacer = document.createElement('span');
                spacer.innerHTML = '&nbsp;'; // Ajoute un espace non sécable
                cell.appendChild(spacer);    // Ajoute l'espace dans la cellule
                cell.appendChild(deleteBtn);
            }
        });
    }

    // Fonction pour reconstruire le tableau après suppression ou modification
    function rebuildTable() {
        tableBody.innerHTML = ''; // Vider le tableau avant de le reconstruire
        rowsData.forEach(data => {
            tableBody.appendChild(data.row);
        });
    }

    // Fonction pour rafraîchir le tableau après suppression d'une image
    function refreshTable() {
        const remainingRows = document.querySelectorAll('.col-sm-2, .col-md-2'); // Sélectionner les colonnes Bootstrap
        tableBody.innerHTML = ''; // Vider le tableau avant de le reconstruire
    
        let rowIndex = 0;
        let currentRow = document.createElement('div'); // Créer un div pour regrouper les colonnes dans une ligne
        currentRow.classList.add('row'); // Ajouter la classe 'row' Bootstrap
    
        remainingRows.forEach(col => {
            currentRow.appendChild(col); // Ajouter chaque colonne à la ligne
    
            rowIndex++;
    
            // Si on atteint 4 colonnes (Bootstrap divise l'écran en 12 colonnes, donc 3x4), on ajoute une nouvelle ligne
            if (rowIndex % 6 === 0) {
                const newRowWrapper = document.createElement('tr');
                const newCell = document.createElement('td');
                newCell.colSpan = 3; // Étendre la cellule sur toute la largeur
                newCell.appendChild(currentRow); // Ajouter la ligne Bootstrap à la cellule
                newRowWrapper.appendChild(newCell);
                tableBody.appendChild(newRowWrapper); // Ajouter la nouvelle ligne au tableau
    
                // Réinitialiser pour une nouvelle ligne
                currentRow = document.createElement('div');
                currentRow.classList.add('row');
            }
        });
    
        // Si des colonnes restent après la boucle, les ajouter dans une nouvelle ligne
        if (currentRow.children.length > 0) {
            const newRowWrapper = document.createElement('tr');
            const newCell = document.createElement('td');
            newCell.colSpan = 3; // Étendre la cellule sur toute la largeur
            newCell.appendChild(currentRow); // Ajouter la ligne Bootstrap à la cellule
            newRowWrapper.appendChild(newCell);
            tableBody.appendChild(newRowWrapper); // Ajouter la nouvelle ligne au tableau
        }
    }
    
    

    // Appel initial pour gérer les images
    handleImages();
   
});

document.addEventListener('DOMContentLoaded', function() {


    const images = document.querySelectorAll('.thumbnail-actual-image');
  
    // Fonction pour vérifier la résolution des images
    function checkImageResolution(imageElement) {

        const resolutionMessage = imageElement.parentElement.querySelector('.resolution-message');
        const imageContainer = imageElement.closest('.actual-image-container');
        const width = imageElement.naturalWidth;
        const height = imageElement.naturalHeight;
        const resolutionText = `${width}x${height}`;
        
        // Définir les dimensions minimales requises (400x600)
        if (width >= 400 && height >= 600) {
            imageContainer.style.backgroundColor = 'green';  // Image OK, couleur verte
            resolutionMessage.textContent = `${resolutionText}`;
        } else {
            imageContainer.style.backgroundColor = 'red';  // Image incorrecte, couleur rouge
            resolutionMessage.textContent = `${resolutionText}`;
        }
        resolutionMessage.style.display = 'block';
    }
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
    // Parcourir chaque image et vérifier la résolution après chargement
    images.forEach(imageElement => {
        console.log('imageElement:', imageElement);
        const fullsize = imageElement.nextElementSibling;
        if (imageElement.complete) {
            checkImageResolution(imageElement);
        } else {
            imageElement.addEventListener('load', function() {
                checkImageResolution(imageElement);
            });
        }
        addImageEvents(imageElement, fullsize);
    });

      
});

function openPrintLabel(sku = '', upc = '', quantity = 1, location = '', force = 'no') {
    const token = document.querySelector('input[name="token"]').value;
    const condition_id = document.querySelector('input[name="condition_id"]').value;

    console.log('[DEBUG] SKU:', sku);
    console.log('[DEBUG] UPC:', upc);
    console.log('[DEBUG] Quantity:', quantity);
    console.log('[DEBUG] Location:', location);
    console.log('[DEBUG] Token:', token);
    console.log('[DEBUG] Condition ID:', condition_id);

    // Si le SKU est identique au UPC, on ignore le UPC
    if (sku === upc) {
        console.log('[DEBUG] SKU and UPC are identical, ignoring UPC');
       //upc = '';
    }

    const url = 'index.php?route=shopmanager/tools.create_label' +
        '&sku=' + encodeURIComponent(sku) +
        '&upc=' + encodeURIComponent(upc) +
        '&quantity=' + encodeURIComponent(quantity) +
        '&location=' + encodeURIComponent(location) +
        '&token=' + encodeURIComponent(token);

    console.log('[DEBUG] URL to open:', url);
    console.log('[DEBUG] force:', force);
    console.log('[DEBUG] condition_id:', condition_id);
    console.log('[DEBUG] upc:', upc);

    if ((condition_id && condition_id !== '1000') || upc == '' || force == 'yes') {
        console.log('[DEBUG] Opening print window...');
        window.open(url, 'printWindow', 'width=288,height=96');
    } else {
        console.warn('[DEBUG] Print window not opened: condition_id is missing or equals 1000');
    }
}



