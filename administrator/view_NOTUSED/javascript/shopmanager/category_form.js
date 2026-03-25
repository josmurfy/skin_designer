// category_form.js

function updateMultiselectSize() {
    $('.multiselect').each(function() {
        var size = Math.max(2, $(this).children('option:selected').length + 2);
        $(this).attr('size', size);
    });
}

function getRowID() {
    // Assuming you have a way to determine the correct row ID, implement this logic
    var rowElem = document.querySelector('[name="category_description[1][description]"]');
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

  function generateInfo(fieldName = 'category'){
    var languages = JSON.parse($('#languages_json').val());
   
//alert(elementId);
//alert(fieldName);
 
     

        for (var targetLanguageId in languages) {
            generateMetaTag(targetLanguageId);
       //     generateDescription(targetLanguageId);
        //    updateCharacterCount(targetLanguageId);

            //generateDescription(targetLanguageId);

            //var inputElement = document.querySelector('input[name="' + fieldName + '_description[' + targetLanguageId +'][name]"]');
            //updateCharacterCount(inputElement,'char-count-' + targetLanguageId +'-0');
        }


}
function generateMetaTag(language_id) {
    
    var nameInput = document.querySelector('input[name="category_description['+ language_id +'][name]"]');
    var metaTagTitle = document.querySelector('input[name="category_description[' + language_id + '][meta_title]"]');
  
    var metaTagDescription = document.querySelector('textarea[name="category_description[' + language_id + '][meta_description]"]');
    var nameDescriptionElement = document.querySelector('textarea[name="category_description[' + language_id + '][description]"]');
    var metaTagKeyword = document.querySelector('textarea[name="category_description[' + language_id + '][meta_keyword]"]');
   
    var keyword = document.querySelector('input[name="category_description[' + language_id + '][keyword]"]');


    var name = nameInput.value;
    var additionalDescriptionHtml = nameDescriptionElement.value.trim();
    var additionalDescriptionText = additionalDescriptionHtml
      .replace(/<\/?[^>]+(>|$)/g, "") // Supprime les balises HTML
      .replace(/&nbsp;/g, ' ') // Remplace les espaces insécables HTML par des espaces
      .replace(/\s+/g, ' '); // Remplace les espaces multiples par un seul espace

    metaTagTitle.value =  name ;
    metaTagDescription.value = additionalDescriptionText;
    var tagkeywords =  name ;
    tagkeywords = tagkeywords.replace(/[.,;:'"\{\}\[\]\(\)@%$&\-]/g, '');
    var tagkeywords = tagkeywords.split(/\s+/).join(','); // Sépare les mots et les joint avec des virgules
    if (tagkeywords.endsWith(',')) {
      tagkeywords = tagkeywords.slice(0, -1);
    }

    metaTagKeyword.value = tagkeywords;


    var keywords = name;
    keywords = keywords.replace(/[.,;:'"\{\}\[\]\(\)@%$&\-]/g, '');
    var keywords = keywords.split(/\s+/).join('-'); // Sépare les mots et les joint avec des virgules
    // Supprimer le dernier caractère sil est un tiret
    if (keywords.endsWith('-')) {
      keywords = keywords.slice(0, -1);
    }

    keyword.value = keywords;
}

function getCategorySpecifics(categoryId, site_id, specifics_row) {
    var token = document.querySelector('input[name="token"]').value;
    var Button = $('#button-get-specifics');
    $.ajax({
        url: 'index.php?route=shopmanager/ebay.getCategorySpecifics&token=' + token, // Le chemin du contrôleur
        type: 'post',
        dataType: 'json',
        data: { category_id: categoryId,site_id: site_id },
        beforeSend: function() {
            // Optionnel: Affiche un loader pendant la requête
            
           
            Button.prop('disabled', true).text('Receiving...');
           
        },
        success: function(json) {
            if (json.success) {
                // Si la requête est un succès, mettre à jour l'interface
                updateCategorySpecificsTable(json.data,specifics_row);
            } else if (json.error) {
                alert('Erreur: ' + json.message);
            }
        },
        complete: function() {
            // Réactiver le bouton après la requête
          
            Button.removeClass("btn btn-primary").addClass("btn btn-success");

            Button.prop('disabled', false).text('Received');
          
            // Ajouter un délai de 3 secondes (3000 millisecondes)
            setTimeout(function() {
                // Changer à nouveau le texte après 3 secondes
                Button.removeClass("btn btn-success").addClass("btn btn-primary");
                Button.prop('disabled', false).html('<i class="fa-solid fa-robot"></i> <i class="fa-solid fa-question"></i> Get Specifics');
                
            }, 3000); // 3000 millisecondes = 3 secondes
        },
        error: function(xhr, status, error) {
            try {
                console.error('Erreur AJAX: ', error);
                console.error('Status:', status);
                console.error('Response Text:', xhr.responseText);

                // Affiche l'erreur dans le conteneur d'erreurs
                errorContainer.style.display = 'block';
                errorContainer.innerHTML = `<pre class="text-danger"><strong>Erreur AJAX:</strong> ${xhr.responseText}</pre>`;
            } catch (error) {
                console.error('Erreur inattendue lors du traitement de l’erreur AJAX:', error);
                errorContainer.style.display = 'block';
                errorContainer.innerHTML = `<strong>Erreur:</strong> Une erreur inattendue est survenue.`;
            }
        }
    });
}
function updateCategorySpecificsTable(data, specifics_row_received) {
  
    var fragment = document.createDocumentFragment();

    for (var languageId in data) {
        if (data.hasOwnProperty(languageId)) {
            var specifics_row = specifics_row_received;
            var specifics = data[languageId];
            var tableBody = document.querySelector('#specifics' + languageId + ' tbody');
            tableBody.innerHTML = ''; // Vider le tableau existant

            for (var specificName in specifics) {
                if (specifics.hasOwnProperty(specificName)) {
                    var specific = specifics[specificName];
                    var constraint = specific.aspectConstraint || {};
                    var values = specific.aspectValues || [];
                    var required = constraint.aspectRequired ? 'required' : '';
                    var current_value = specific.Value || '';

                    if (typeof current_value === 'string') {
                        current_value = (specificName !== 'Region Code' && current_value.includes('@@')) ? current_value.split('@@ ') : [current_value];
                    }
                    if (!Array.isArray(current_value)) {
                        current_value = [current_value];
                    }

                    values.sort(function(a, b) {
                        if (current_value.includes(a.localizedValue)) return -1;
                        if (current_value.includes(b.localizedValue)) return 1;
                        return a.localizedValue.localeCompare(b.localizedValue);
                    });

                    var inputField = '';

                    if (constraint.aspectMode === 'SELECTION_ONLY') {
                        inputField = '<select id="category_description_' + languageId + '_' + specifics_row + '" name="category_description[' + languageId + '][specifics][' + specificName + '][Value]" class="form-control" ' + required + ' disabled>';
                        inputField += '<option value=""></option>';
                        values.forEach(function(value) {
                            inputField += '<option value="' + value.localizedValue + '"' + (current_value.includes(value.localizedValue) ? ' selected' : '') + '>' + value.localizedValue + '</option>';
                        });
                        inputField += '</select>';
                    } else if (constraint.aspectMode === 'FREE_TEXT' && constraint.itemToAspectCardinality === 'MULTI') {
                        inputField = '<select id="category_description_' + languageId + '_' + specifics_row + '" name="category_description[' + languageId + '][specifics][' + specificName + '][Value][]" class="form-control multiselect" multiple ' + required + ' size="' + Math.max(2, values.length + 2) + '" disabled>';
                        values.forEach(function(value) {
                            inputField += '<option value="' + value.localizedValue + '"' + (current_value.includes(value.localizedValue) ? ' selected' : '') + '>' + value.localizedValue + '</option>';
                        });
                        inputField += '</select>';
                    } else {
                        inputField = '<input type="text" id="category_description_' + languageId + '_' + specifics_row + '" name="category_description[' + languageId + '][specifics][' + specificName + '][Value]" placeholder="Texte libre" class="form-control" ' + required + ' value="' + current_value.join(', ') + '" readonly />';
                    }

                    var rowHtml = document.createElement('tr');
                    rowHtml.id = 'specifics' + languageId + '-row' + specifics_row;
                    rowHtml.innerHTML = 
                        '<td class="text-left">' +
                            '<div>' + specific.localizedAspectName + '</div>' +
                            '<input type="hidden" name="category_description[' + languageId + '][specifics][' + specificName + '][Name]" value="' + specific.localizedAspectName + '" class="form-control" />' +
                        '</td>' +
                        '<td class="text-left">' +
                            inputField +
                        '</td>' +
                        '<td class="text-left">' +
                            '<button type="button" onclick="removeSpecificsRow(' + specifics_row + ',' + specifics_row + ',\'' + specificName + '\');" data-toggle="tooltip" title="Supprimer" class="btn btn-danger">' +
                            '<i class="fa fa-minus-circle"></i>' +
                        '</button>' +
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

async function uploadFromLink(imageUrl) {

    var token = document.querySelector('input[name="token"]').value;
    var Button = $('#ai-suggest-image-btn');
    var ButtonDownload = $('#ai-suggest-image-download-btn');
    var categoryId = document.querySelector('input[name="category_id"]').value;

    console.log('categoryId:', categoryId);
    console.log('token:', token);
    console.log('imageUrl:', imageUrl);

    if (!imageUrl || !categoryId) {
        alert('Please enter a valid image URL and category ID.');
        return;
    }

    var data = {
        category_id: categoryId,
        piclink: imageUrl
    };
    console.log('data:', JSON.stringify(data));
    fetch('index.php?route=shopmanager/catalog/category.uploadFromLink&token=' + token, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
      if (data && data.success) {
        ButtonDownload.prop('disabled', false).text('Downloaded');
        Button.removeClass("btn btn-warning").addClass("btn btn-success");
        var inputElement = document.getElementById('image-url');
        inputElement.value = '';
        inputElement.style.display = "none";
        ButtonDownload.css('display', 'none');  // Utilisation de jQuery pour modifier le style
        ButtonDownload.attr('onclick', '');
        // Ajouter un délai de 3 secondes (3000 millisecondes)
        setTimeout(function() {
            // Changer à nouveau le texte après 3 secondes
            Button.removeClass("btn btn-success").addClass("btn btn-primary");
            Button.prop('disabled', false).html('<i class="fa-solid fa-robot"></i> <i class="fa-solid fa-photo"></i>');
           

           // Button.attr('onclick', "aiSuggestImage('form-category','category');");
        }, 3000); // 3000 millisecondes = 3 secondes 

        var inputElement = document.getElementById('input-image');
        inputElement.value = data.image_url;
   //     alert(data.image_url);

          return (null, data.message);
      } else {
        
          return (data ? data.message : 'Erreur lors de la récupération des données.', null);
      }
  })
  .catch(error => {
  //  alert(data.message);
    console.error('Error fetching correct value:', error);
    return('Erreur de connexion ou réponse invalide du serveur.', null);
});
}

function generateDescriptionOLD(language_id) {
  var form = document.getElementById('form-category');
  var name = form.querySelector('input[name="category_description['+ language_id +'][name]"]').value;
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

  var additionalConditions = form.querySelector('textarea[name="category_description[' + language_id +'][condition_supp]"]').value.trim();
  if (additionalConditions && additionalConditions != '<p><br></p>') {
      description += '<h4 style="color: red;"><b>Additional Conditions:</b></h4>';
      description += additionalConditions;
  }
  var includedAccessories = form.querySelector('textarea[name="category_description['+ language_id +'][included_accessories]"]').value.trim();
  if (includedAccessories && includedAccessories != '<p><br></p>') {
      description += '<h3 style="color: darkblue;"><b>Included Accessories:</b></h3>';
      description += includedAccessories;
  } 

  var additionalDescription = form.querySelector('textarea[name="category_description['+ language_id +'][description_supp]"]').value.trim();
  if (additionalDescription && additionalDescription != '<p><br></p>') {
      description += '<h3 style="color: darkblue;"><b>Description:</b></h3>';
      description += additionalDescription;
  }

  description += '<h3 style="color: darkblue;"><b>Specific Features:</b></h3><ul>';
  var specificsRows = form.querySelectorAll('[id^="specifics1-row"]');
  specificsRows.forEach(function(row) {
      var nameElem = row.querySelector('div[id^="category_description_"][id$="_Name"]');
      var valueElem = row.querySelector('input[name*="[Value]"], select[name*="[Value][]"], select[name*="[Value]"]');

      if (nameElem && valueElem) {
          var name = nameElem.textContent.trim();
          var value = valueElem.value;

          if (value !== '') {
              if (valueElem.tagName.toLowerCase() === 'select' && valueElem.multiple) {
                  var selectedValues = Array.from(valueElem.selectedOptions).map(option => option.value);
                  description += '<li><b>' + name + ':</b></li>';
                  for (var i = 0; i < selectedValues.length; i++) {
                      description += '<li class="secondary-list-item">' + ucwords(selectedValues[i]) + '</li>';
                  }
              } else {
                  description += '<li><b>' + name + ':</b> ' + ucwords(value) + '</li>';
              }
          }
      }
  });

  description += '</ul>';

  description += '<p><b>Model:</b> ' + ucwords(htmlspecialchars(form.querySelector('input[name="model"]').value)) + '</p>';
  description += '<p><b>Package Dimension:</b> ' + doubleval(form.querySelector('input[name="length"]').value) + 'x' + doubleval(form.querySelector('input[name="width"]').value) + 'x' + doubleval(form.querySelector('input[name="height"]').value) + ' Inch</p>';
  description += '<p><b>Package Weight:</b> ' + doubleval(form.querySelector('input[name="weight"]').value) + ' Lbs</p>';

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

  var descriptionTextarea = document.querySelector('textarea[name="category_description[' + language_id +'][description]"]');
  if (descriptionTextarea) {
      descriptionTextarea.value = description;
      // Met à jour le div correspondant pour l'affichage
      var displayDivId = descriptionTextarea.id.replace('category_description_', 'display_category_description_');
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



function trfValue(specificsRow) {
    console.log('trfValue specificsRow ' + specificsRow);
    // Récupérer la valeur actuelle du champ caché
    var hiddenValue = $('#Actual_value' + specificsRow).val();

    // Sélectionner le champ de saisie cible
    var targetElement = $('#category_description_1_' + specificsRow);

    // Stocker la valeur initiale
    var originalValue;
    if (targetElement.is('select[multiple]')) {
        originalValue = targetElement.val().join(',');
    } else {
        originalValue = targetElement.val();
    }
    $('#original_value_' + specificsRow).val(originalValue);

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
        targetElement.val(values).trigger('change');
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
    console.log('undoValuespecificsRow ' + specificsRow);
    // Récupérer la valeur originale stockée
    var originalValue = $('#original_value_' + specificsRow).val();

    // Sélectionner le champ de saisie cible
    var targetElement = $('#category_description_1_' + specificsRow);

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
  
    if (typeof generateInfo === 'function') {
    generateInfo('category');
}


    var form = document.getElementById('form-category');
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

    cancelLink.addEventListener('click', function(event) {
        // Vérifie que l'utilisateur clique bien sur le lien d'annulation
        if (isFormModified) {
            var confirmLeave = confirm('Des modifications ont été effectuées. Si vous quittez cette page, les modifications seront perdues. Voulez-vous continuer ?');
            if (!confirmLeave) {
                event.preventDefault();
            }
        }
    });

    form.addEventListener('submit', function() {
        // Réinitialise isFormModified à false lorsque le formulaire est soumis
        isFormModified = false;
    });


  



    var form = document.getElementById('form-category');
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
    generateInfo('category');
}

            });
        } else {
            element.addEventListener('change', function() {
                isFormModified = true;
               
                if (typeof generateInfo === 'function') {
    generateInfo('category');
}

            });
        }
    });

    
    

    
    $(document).on('change', '.multiselect', function() {
        updateMultiselectSize();
    });
    
    $(document).ready(function() {
        updateMultiselectSize();
    });
    window.addEventListener('beforeunload', function(event) {
        if (isFormModified) {
            var message = 'Des modifications ont été effectuées. Si vous quittez cette page, les modifications seront perdues. Voulez-vous continuer ?';
            event.returnValue = message; // Standard pour certains navigateurs
            return message; // Standard pour d'autres navigateurs
        }
    });

});
