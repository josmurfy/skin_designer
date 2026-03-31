// product_form.js - Version OpenCart 4.x (Twig) - IDs avec tirets - Updated: 2025-12-17

// ============================================
// FUNCTIONS DUPLICATED FROM TOOLS.JS (PRODUCTION SAFETY)
// ============================================

function updateCharacterCount(inputElement, counterId) {
    var maxLength = 80;
    var currentLength = inputElement.value.length;
    var counterElement = document.getElementById(counterId);

    counterElement.textContent = currentLength + '/' + maxLength;
  
    if (currentLength > maxLength) {
        counterElement.style.color = 'red';
    } else {
        counterElement.style.color = 'green';
    }
}

function updateSeoCharCount(inputElement, counterId) {
    var maxLength = 64;
    var currentLength = inputElement.value.length;
    var counterElement = document.getElementById(counterId);

    if (counterElement) {
        counterElement.textContent = currentLength + '/' + maxLength;
      
        if (currentLength > maxLength) {
            counterElement.style.color = 'red';
        } else {
            counterElement.style.color = 'green';
        }
    }
}

function htmlspecialchars(str) {
    // Convert to string and handle null/undefined
    if (str === null || str === undefined) {
        return '';
    }
    str = String(str); // Force conversion to string
    
    return str.replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#039;');
}

function ucwords(str) {
    if (str === null || str === undefined) {
        return '';
    }
    str = String(str);
    
    // Ne pas convertir en lowercase pour préserver les caractères Unicode spéciaux
    // Capitaliser seulement le premier caractère de chaque mot
    return str.replace(/\b\w/gu, function(char) {
        return char.toUpperCase();
    });
}

function decodeHtmlEntities(text) {
    var textarea = document.createElement('textarea');
    textarea.innerHTML = text;
    return textarea.value;
}

/**
 * Centralized Image Resolution Check
 * Sets resolution data and applies border colors to image containers
 */
function checkImageResolution(imageElement, forceRecheck = false) {
    const container = imageElement.closest('.actual-image-container');
    if (!container) {
        return;
    }

    // Find the fullsize image for actual resolution
    const fullsizeImg = container.querySelector('.fullsize-actual-image, .actual-image-preview');
    const imgToCheck = fullsizeImg && fullsizeImg.src && fullsizeImg.src !== '' ? fullsizeImg : imageElement;

    // Check data-image-path attribute ONLY if it exists (product_form has it, product_list/report_image don't)
    if (imgToCheck.hasAttribute('data-image-path')) {
        const imagePath = imgToCheck.getAttribute('data-image-path');
        if (imagePath === '' || imagePath === 'undefined') {
            console.warn('Empty data-image-path, skipping resolution check for:', imgToCheck);
            return;
        }
    }

    // Skip if image has no valid src
    const srcAttr = imgToCheck.getAttribute('src');
    if (!srcAttr || srcAttr === '' || srcAttr === window.location.href || srcAttr.endsWith('image/') || srcAttr.includes('undefined')) {
        console.warn('Invalid image src, skipping resolution check:', srcAttr);
        return;
    }

    // Check if already processed
    if (!forceRecheck && imgToCheck.dataset.resolutionChecked === 'true') {
        return;
    }

    // Wait for image to load if not complete
    if (!imgToCheck.complete) {
        imgToCheck.addEventListener('load', function() {
            setImageResolutionData(imgToCheck, container);
        }, { once: true });
    } else {
        setImageResolutionData(imgToCheck, container);
    }
}

function setImageResolutionData(img, container) {
    const width = img.naturalWidth;
    const height = img.naturalHeight;
    
    // Skip if image failed to load (0x0)
    if (width === 0 || height === 0) {
        console.warn('Image has 0x0 resolution, skipping:', img.src);
        return;
    }
    
    const resolutionText = `${width}x${height}`;
    
    // Mark as checked
    img.dataset.resolutionChecked = 'true';
    
    // Set data-resolution attribute for overlay display
    img.setAttribute('data-resolution', resolutionText);
    
    // Apply border color based on resolution (400x600 minimum)
    if (width >= 400 && height >= 600) {
        container.style.border = '3px solid #28a745'; // Green
    } else {
        container.style.border = '3px solid #dc3545'; // Red
    }
    
    // Update overlay if present
    const overlay = container.querySelector('.fullsize-resolution-overlay');
    if (overlay) {
        overlay.textContent = resolutionText;
        if (width < 400 || height < 600) {
            overlay.classList.remove('good-res');
            overlay.classList.add('low-res');
        } else {
            overlay.classList.remove('low-res');
            overlay.classList.add('good-res');
        }
    }
}

/**
 * Initialize all image resolution checks on page load
 * Can be called multiple times for dynamically added images
 */
function initImageResolutionCheck() {
    // LAZY LOADING: Check resolution only on hover instead of immediately
    document.querySelectorAll('.actual-image-container').forEach(function(container) {
        const thumbnail = container.querySelector('.img-thumbnail, .thumbnail-actual-image');
        if (thumbnail) {
            // Add hover listener to check resolution on demand
            container.addEventListener('mouseenter', function() {
                checkImageResolution(thumbnail);
            }, { once: true }); // Only check once per image
        }
    });
}

/**
 * Centralized Image Preview Functionality
 * Handles fullsize image preview on hover for product_form, product_list, and report_image
 */
function initImagePreview() {
    document.querySelectorAll('.actual-image-container').forEach(function(container) {
        // Skip if already initialized
        if (container.dataset.previewInitialized === 'true') {
            return;
        }
        container.dataset.previewInitialized = 'true';

        const thumbnail = container.querySelector('.img-thumbnail, .thumbnail-actual-image');
        const wrapper = container.querySelector('.fullsize-actual-image-wrapper');
        const fullsizeImg = container.querySelector('.fullsize-actual-image, .actual-image-preview');
        const resolutionOverlay = container.querySelector('.fullsize-resolution-overlay');

        if (!thumbnail || !wrapper || !fullsizeImg) {
            return;
        }

        // Set resolution color class if overlay exists
        if (resolutionOverlay) {
            const resolution = fullsizeImg.getAttribute('data-resolution');
            if (resolution) {
                const parts = resolution.split('x');
                const width = parseInt(parts[0]);
                const height = parseInt(parts[1]);
                
                if (width < 400 || height < 600) {
                    resolutionOverlay.classList.add('low-res');
                } else {
                    resolutionOverlay.classList.add('good-res');
                }
                resolutionOverlay.textContent = resolution;
            }
        }

        // Show wrapper on thumbnail hover
        thumbnail.addEventListener('mouseenter', function() {
            wrapper.style.display = 'block';
        });

        thumbnail.addEventListener('mouseleave', function() {
            wrapper.style.display = 'none';
        });

        // Keep wrapper visible when hovering over it
        wrapper.addEventListener('mouseenter', function() {
            wrapper.style.display = 'block';
        });

        wrapper.addEventListener('mouseleave', function() {
            wrapper.style.display = 'none';
        });
    });
}

// ============================================
// END DUPLICATED FUNCTIONS FROM TOOLS.JS
// ============================================

function removeSpecificsRow(specifics_row, specificName) {
    var user_token = document.querySelector('input[name="user_token"]').value;
    var languages = JSON.parse($('#languages-json').val());

    $.ajax({
        url: `index.php?route=shopmanager/catalog/category_specific.excludeSpecific&user_token=${user_token}`,
        type: 'POST',
        data: {
          
            specific_name: specificName
        },
        success: function () {
        },
        error: function (xhr, status, error) {
            console.error('Error excluding specific:', error);
        }
    });

    for (var language_id in languages) {
        if (languages.hasOwnProperty(language_id)) {
            var language_code = languages[language_id];
            var rowId = `#specifics-${language_id}-${specifics_row}`;
            var rowId2 = `#specifics-${language_code}-${specifics_row}`;

            $(rowId).remove();
            $(rowId2).remove();
        }
    }

    specifics_row--;
    
}

function addSpecifics() {
      

    var languages = JSON.parse($('#languages-json').val());
     
                        
    Object.keys(languages).forEach(function(language_id) {
        var html = '<tr id="specifics-' + language_id + '-' + specifics_row + '">';

        html += '<td class="text-start"><div id="product-description-' + language_id + '-specific-' + specifics_row + '-Name"></div>';
        html += '<input type="hidden" id="hidden-product-description-' + language_id + '-specific-' + specifics_row + '-Name" name="product_description[' + language_id + '][specifics][' + specifics_row + '][Name]" placeholder="{{ column_specifics }}" class="form-control" />';
        html += '<input type="text" id="text-product-description-' + language_id + '-specific-' + specifics_row + '-Name" name="product_description[' + language_id + '][specifics][' + specifics_row + '][Name]" placeholder="{{ column_specifics }}" class="form-control" onchange="updateSpecifics(' + specifics_row + ')" />';
        html += '</td>';
        html +='<td class="text-start">';
         html +='<input type="hidden" id="original-value-' + specifics_row + '" value="" />' ;
        html += '</td><td class="text-start"></td>';             
        html += '<td class="text-start">';
        html += '<div id="response-product-description-' + language_id + '-' + specifics_row + '"></div>';
        html += '<input type="text" id="product-description-' + language_id + '-' + specifics_row + '" name="product_description[' + language_id + '][specifics][' + specifics_row + '][Value]" placeholder="{{ entry_text }}" class="form-control" /></td>';
        html += '<td class="text-start">';
        html += '<button type="button" onclick="verifySpecific(\'' + specifics_row + '\', \'true\')" data-bs-toggle="tooltip" title="" class="btn btn-success" data-original-title="Vérification" ><i class="fa-solid fa-check"></i></button> ';
        html += '<button type="button" onclick="removeSpecificsRow(' + specifics_row + ', ' + specifics_row + ',\'' + specifics_row + '\');" data-bs-toggle="tooltip" title="" class="btn btn-danger" data-original-title="{{ button_remove }}"><i class="fa-solid fa-minus-circle"></i></button></td>';
        html += '</tr>';
        
        $('#specifics-' + language_id + ' tbody').append(html);
    });

    specifics_row++;
    
  

}

function updateSpecifics(rowId) {
    var languages = JSON.parse($('#languages-json').val());
    var textInput = document.querySelector(`#text-product-description-1-specific-${rowId}-Name`);
    var newName = textInput.value;
    if (newName == '') {
        var textInput = document.querySelector(`#text-product-description-2-specific-${rowId}-Name`);
        var newName = textInput.value;
    }

    Object.keys(languages).forEach(function(language_id) {
        var divElement = document.querySelector(`#product-description-${language_id}-specific-${rowId}-Name`);
        var hiddenInput = document.querySelector(`#hidden-product-description-${language_id}-specific-${rowId}-Name`);
        var textInput = document.querySelector(`#text-product-description-${language_id}-specific-${rowId}-Name`);
        var valueInput = document.querySelector(`#product-description-${language_id}-${rowId}`);

        if (divElement) {
            divElement.textContent = newName;
        }

        if (hiddenInput) {
            hiddenInput.name = `product_description[${language_id}][specifics][${newName}][Name]`;
            hiddenInput.value = newName;
        }

        if (valueInput) {
            valueInput.name = `product_description[${language_id}][specifics][${newName}][Value]`;
        }

        if (textInput) {
            textInput.style.display = 'none';
            textInput.removeAttribute('name');
        }

        var removeButton = document.querySelector(`#specifics-${language_id}-${rowId} .btn-danger`);
        if (removeButton) {
            removeButton.setAttribute('onclick', `removeSpecificsRow('${newName}', ${rowId}, '${newName}');`);
        }
    });
    
}

function switchEntryName(fieldName = 'product',specifics_row) {


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

    // Vérifier si le label contient du texte
    if (label && label.style.display !== 'none' && label.innerText.trim() !== "") {
        // Si le label contient du texte, on le copie dans l'input et cache le label
        element.value = label.textContent.trim();
        label.style.display = 'none';
        labelcount.style.display = 'none';
    
      //  translateContentForAllLanguages('product_description_1_'+ specifics_row,'', 'product');
        if (typeof generateInfo === 'function') {
            generateInfo();
        }

    } else {
        // Si le label est vide ou caché, on affiche un message dans la console (ou une autre action)
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
    var label = document.getElementById('condition-name-' + languageId);
    if (label) {
        label.textContent = conditionName; // Mettre à jour le texte du label
    }
  //  label.style.display = 'block'; // Afficher le label
}
// Fonction pour mettre à jour tous les labels en fonction de la condition sélectionnée
    // Fonction pour mettre à jour tous les labels en fonction de la condition sélectionnée
function updateAllLabels(selectedConditionId) {
    // Récupérer le JSON des langues à partir du champ caché
    var languagesElement = $('#languages-json');
    if (!languagesElement.length || !languagesElement.val()) {
        console.warn('languages_json element not found or empty');
        return;
    }
    
    try {
        var languages = JSON.parse(languagesElement.val());
    } catch (e) {
        console.error('Error parsing languages_json:', e);
        return;
    }
    // Boucler sur chaque langue
    Object.keys(languages).forEach(function(targetLanguageId) {
        var targetLanguage = languages[targetLanguageId];
        // Construire l'ID du champ caché condition_id pour chaque langue
    //    var conditionInput = document.getElementById('condition_id' +targetLanguageId);
      
        // Si le champ caché condition_id existe
        var conditionsElement = $('#conditions_json' + targetLanguageId);
        if (!conditionsElement.length || !conditionsElement.val()) {
            console.warn('conditions_json not found for language ' + targetLanguageId);
            return;
        }
        
        try {
            var conditions = JSON.parse(conditionsElement.val());
        } catch (e) {
            console.error('Error parsing conditions_json for language ' + targetLanguageId, e);
            return;
        }
         //   var conditions = JSON.parse(conditionInput.value);
           
            // Vérifier si la condition sélectionnée existe dans le JSON 
            if (conditions[selectedConditionId]) {
                // Mettre à jour le label avec le nom de la condition sélectionnée
                var label = document.getElementById('condition-name-' + targetLanguageId);
                if (label) {
                    label.textContent = conditions[selectedConditionId]['condition_name'];
                    if(targetLanguage != 'English'){
                        label.style.display = 'block';
                    }
                }
            }
      
    });
}
function getEbayProduct() {
    var user_token = document.querySelector('input[name="user_token"]').value;
    const inputField = document.getElementById(`ebayValueInput`);
    const marketplace_item_id = inputField ? inputField.value.trim() : '';

    if (!marketplace_item_id) {
        alert(TEXT_ALERT_ENTER_VALUE);
        return;
    }

    fetch(`index.php?route=shopmanager/ebay.getProduct&user_token=${user_token}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ marketplace_item_id: marketplace_item_id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.table(data.result); // Affiche les résultats sous forme de tableau
           // generateSpecifics(category_id, data.result); // Passe les données au générateur de specifics
        } else {
            alert(TEXT_ERROR_OCCURRED + ': ' + (data.error || TEXT_ERROR_UNKNOWN));
        }
    })
    .catch(error => {
        console.error('Error fetching eBay values:', error);
        //alert('Failed to fetch eBay values.');
    });
}



function generateSpecifics(categoryId) {

    var specifics_row =window.specifics_row_specific;
    var user_token = document.querySelector('input[name="user_token"]').value;
    var product_id = document.querySelector('input[name="product_id"]').value;
    var marketplace_item_id = document.querySelector('input[name="marketplace_item_id"]') ? document.querySelector('input[name="marketplace_item_id"]').value : '';
 // Ouvrir la modale avant de lancer l'appel AJAX
 showModal('#loadingModal');

    $.ajax({
        url: 'index.php?route=shopmanager/catalog/product_search.generateSpecifics&product_id=' + product_id + '&category_id=' + categoryId + '&marketplace_item_id=' + marketplace_item_id + '&user_token=' + user_token,
        type: 'GET',
        dataType: 'json',
        success: function (response) {

                updateSpecificsTable(response, specifics_row);
                  // Fermer la modale lorsque la réponse est reçue
         

        },
        error: function (xhr, status, error) {
            console.error("Erreur lors de la récupération des spécificités : " + error);
             // Fermer la modale même en cas d'erreur
          
        }
    });
    hideModal('#loadingModal');
}

function AllunconfimSource() {

    var languages = JSON.parse($('#languages-json').val());
   
    Object.keys(languages).forEach(function(language_id) {
   
        var rows = document.querySelectorAll(`[id^="specifics-${language_id}-"]`);

        rows.forEach(row => {
        
            // Extraire l'ID de la ligne en supprimant le préfixe "specifics1-row"
            var rowId = row.id.replace(`specifics-${language_id}-`, '');
            // Sélectionner l'élément VerifiedSource correspondant
            var verifiedSourceElement = document.getElementById(`verified-source-${language_id}-${rowId}`);

            // Si l'élément existe et que sa valeur n'est pas "yes", appeler la fonction verifySpecific
            if (verifiedSourceElement && verifiedSourceElement.value.toLowerCase() === 'yes') {
                unconfirmSource(language_id,rowId);
            }
        });
    });
}

function AllconfirmSource() {

    var languages = JSON.parse($('#languages-json').val());
   
    Object.keys(languages).forEach(function(language_id) {
        var rows = document.querySelectorAll(`[id^="specifics-${language_id}-"]`);
        rows.forEach(row => {
        
            // Extraire l'ID de la ligne en supprimant le préfixe "specifics1-row"
            var rowId = row.id.replace(`specifics-${language_id}-`, '');
            // Sélectionner l'élément VerifiedSource correspondant
            var verifiedSourceElement = document.getElementById(`verified-source-${language_id}-${rowId}`);

            // Si l'élément existe et que sa valeur n'est pas "yes", appeler la fonction verifySpecific
            if (verifiedSourceElement && verifiedSourceElement.value.toLowerCase() !== 'yes') {
                confirmSource(language_id,rowId);
            }
        });
    });
}

function unconfirmSource(language_id,rowId) {
    // Sélectionner la ligne de spécificité correspondant à rowId
    const rowElement = document.querySelector(`#specifics-${language_id}-${rowId}`);
    // Sélectionner les boutons correspondants dans la ligne
    const confirmButton = rowElement.querySelector("button[data-original-title='Confirm Source']");
    const unconfirmButton = rowElement.querySelector("button[data-original-title='Unconfirm Source']");
    const verifyButton = rowElement.querySelector("button[data-original-title='Verify Value']");

    // Afficher ou masquer les boutons selon la logique définie
    confirmButton.style.display = 'inline-block';   // Afficher le bouton "Confirm"
    unconfirmButton.style.display = 'none';         // Masquer le bouton "Unconfirm"
    verifyButton.style.display = 'inline-block';    // Afficher le bouton "Verify"

    // Sélectionner les champs de saisie (input, select, textarea) pour réinitialiser la valeur d'origine
    const VerifiedSourceInput = document.getElementById(`verified-source-${language_id}-${rowId}`);

    VerifiedSourceInput.value  = '';

    // Retirer les styles de la ligne
    rowElement.className = '';
    rowElement.style.removeProperty('background-color');
    rowElement.style.removeProperty('color');

    // Supprimer tous les styles inline des cellules <td>
    var tdElements = rowElement.querySelectorAll('td');
    tdElements.forEach(function(td) {
        td.style.removeProperty('background-color');
        td.style.removeProperty('color');
    });
}

function confirmSource(language_id,rowId) {
    // Sélectionner la ligne de spécificité correspondant à rowId
    const rowElement = document.querySelector(`#specifics-${language_id}-${rowId}`);
    
    // Sélectionner les boutons correspondants dans la ligne
    const confirmButton = rowElement.querySelector("button[data-original-title='Confirm Source']");
    const unconfirmButton = rowElement.querySelector("button[data-original-title='Unconfirm Source']");
    const verifyButton = rowElement.querySelector("button[data-original-title='Verify Value']");

    // Afficher ou masquer les boutons selon la logique définie
    confirmButton.style.display = 'none';   // Afficher le bouton "Confirm"
    unconfirmButton.style.display = 'inline-block';         // Masquer le bouton "Unconfirm"
    verifyButton.style.display = 'none';    // Afficher le bouton "Verify"

    // Sélectionner les champs de saisie (input, select, textarea) pour réinitialiser la valeur d'origine
   
    const VerifiedSourceInput = document.getElementById(`verified-source-${language_id}-${rowId}`);

    VerifiedSourceInput.value  = 'yes';

    // Appliquer la couleur verte permanente avec !important
    rowElement.className = 'table-info';
    rowElement.style.setProperty('background-color', '#28a745', 'important'); 
    rowElement.style.setProperty('color', 'white', 'important');

    // Appliquer aussi aux cellules <td>
    var tdElements = rowElement.querySelectorAll('td');
    tdElements.forEach(function(td) {
        td.style.setProperty('background-color', '#28a745', 'important');
        td.style.setProperty('color', 'white', 'important');
    });
}


function updateSpecificsTable(data, specifics_row_received) {
    var specifics_row = specifics_row_received;
    var fragment = document.createDocumentFragment();

    for (var languageId in data) {
        if (data.hasOwnProperty(languageId)) {
            var specifics_row = specifics_row_received;
            var specifics = data[languageId];
            var tableBody = document.querySelector('#specifics-' + languageId + ' tbody');
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
                    var to_translate = (specific && typeof specific.to_translate !== 'undefined') ? specific.to_translate : '';
                    var actual_value = specific.Actual_value || '';
                    // Traiter la valeur de `current_value` pour s'assurer qu'elle est sous forme de tableau
                    if (typeof current_value === 'string') {
                        current_value = (specificName !== 'Region Code' && current_value.includes('@@')) ? current_value.split('@@ ') : [current_value];
                    }
                    if (!Array.isArray(current_value)) {
                        current_value = [current_value];
                    }
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
                        inputField = '<select id="product-description-' + languageId + '-' + specifics_row + '" name="product_description[' + languageId + '][specifics][' + specificName + '][Value]" class="form-control" ' + required + ' >';
                        inputField += '<option value=""></option>';
                        values.forEach(function(value) {
                            inputField += '<option value="' + value.localizedValue + '"' + (current_value.includes(value.localizedValue) ? ' selected' : '') + '>' + value.localizedValue + '</option>';
                        });
                        inputField += '</select>';
                    } else if (constraint.aspectMode === 'FREE_TEXT' && constraint.itemToAspectCardinality === 'MULTI') {
                        inputField = '<select id="product-description-' + languageId + '-' + specifics_row + '" name="product_description[' + languageId + '][specifics][' + specificName + '][Value][]" class="form-control multiselect" multiple ' + required + ' size="' + Math.max(2, values.length + 2) + '" >';
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
                        inputField = '<input type="text" id="product-description-' + languageId + '-' + specifics_row + '" name="product_description[' + languageId + '][specifics][' + specificName + '][Value]" placeholder="" class="form-control" ' + required + ' value="' + current_value.join(', ') + '"  />';
                    } else {
                        inputField = '<input type="text" id="product-description-' + languageId + '-' + specifics_row + '" name="product_description[' + languageId + '][specifics][' + specificName + '][Value]" placeholder="" class="form-control" ' + required + ' value="' + current_value.join(', ') + '"  />';
                    }

                    // Créer la ligne du tableau
                    var rowHtml = document.createElement('tr');
                    rowHtml.id = 'specifics-' + languageId + '-' + specifics_row;

                    // Appliquer la classe de style en fonction de la présence de VerifiedSource
                    var rowClass = VerifiedSource ? 'table-info' : ''; // Applique la classe 'table-info' si VerifiedSource est défini
                    rowHtml.className = rowClass; // Applique la classe CSS à la ligne

                    rowHtml.innerHTML = 
                    ' <td class="text-center text-nowrap" style="width: auto;">' + 
                        '<button type="button" onclick="removeSpecificsRow(' + specifics_row + ',' + specifics_row + ', \'' + specificName + '\');" data-bs-toggle="tooltip" title="Supprimer" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i></button> ' +
                        '</td>' +
                        '<td class="text-left">' + 

                            '<div id="product-description-' + languageId + '-specifics-Name-' + specifics_row + '" >' + 
                            current_name + '</div>' +
                            '<input type="hidden" name="product_description[' + languageId + '][specifics][' + specificName + '][Name]" value="' + specificName + '" class="form-control" />' +
                            '<input type="hidden" id="verified-source-' + languageId + '-' + specifics_row + '" name="product_description[' + languageId + '][specifics][' + specificName + '][VerifiedSource]" value="' + VerifiedSource + '" class="form-control" />' +
                            '<input type="hidden" id="to-translate-' + languageId + '-' + specifics_row + '" name="product_description[' + languageId + '][specifics][' + specificName + '][to_translate]" value="' + to_translate + '" class="form-control" />' +
                            '</td>' +
                        '<td class="text-left">' +
                            '<input type="hidden" id="hidden-original-value-' + languageId + '-' + specifics_row + '" value="" />' +
                            '<div id="original-value-' + languageId + '-' + specifics_row + '" >' + 
                            (Array.isArray(actual_value) ? actual_value.join(',') : actual_value) + '</div>' +
                        
                        
                        (actual_value ? 
                       
                        '<input type="hidden" id="actual-value-' + languageId + '-' + specifics_row + '" name="actual-value-' + languageId + '-' + specifics_row + '" value="' + (Array.isArray(actual_value) ? actual_value.join(',') : actual_value) + '" />' +
                        '<button type="button" id="btTrf-' + languageId + '-' + specifics_row + '" class="btTrf-' + specifics_row + '" onclick="trfValue(' + specifics_row + ')" data-bs-toggle="tooltip" title="Transfer Actual Value" class="btn btn-success"><i class="fa-regular fa-circle-right"></i></button>'
                       
                        : '') +
                        '<button type="button" id="btUndo-' + languageId + '-' + specifics_row + '" class="btUndo-' + specifics_row + '" onclick="undoValue(' + specifics_row + ')" data-bs-toggle="tooltip" title="Undo Transfer" class="btn btn-warning" style="display:none;"><i class="fa  fa-undo"></i></button>' +
                         '</td>' +
                        '<td class="text-left">' +
                        '<div id="response-product-description-' + languageId + '-' + specifics_row + '"></div>' +
                            inputField +
                        '</td>' +
                        
                        '<td class="text-center">' +
                            // Cacher le bouton de vérification si VerifiedSource est défini
                            '<button type="button" onclick="confirmSource(' + languageId + ',' + specifics_row + ');" data-bs-toggle="tooltip" title="" class="btn btn-success" data-original-title="Confirm Source" style="display: ' + (VerifiedSource ? 'none' : 'inline-block') + '"><i class="fa-regular fa-circle-check"></i></button> ' +
                            '<button type="button" onclick="unconfirmSource(' + languageId + ',' + specifics_row + ');" data-bs-toggle="tooltip" title="" class="btn btn-danger" data-original-title="Unconfirm Source" style="display: ' + (VerifiedSource ? 'inline-block' : 'none') + '"><i class="fa-regular fa-circle-xmark"></i></button> ' +
                            '<button type="button" onclick="verifySpecific(' + specifics_row + ', \'true\');" data-bs-toggle="tooltip" title="" class="btn btn-primary" data-original-title="Verify Value" style="display: ' + (VerifiedSource ? 'none' : 'inline-block') + '"><i class="fa-regular fa-circle-question"></i></button> ' +
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

    // Appeler generateInfo après chargement initial des specifics (page load)
    if (typeof generateInfo === 'function') {
        generateInfo();
    }

    // Déclencher la synchro COO maintenant que les specifics sont dans le DOM
    if (typeof syncCountryFields === 'function') {
        setTimeout(syncCountryFields, 0);
    }
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

var isGeneratingInfo = false; // Flag pour éviter appels multiples
var generateInfoTimeout = null; // Timeout pour retarder generateInfo

/**
 * Convertit le texte du Summernote ciblé : phrases séparées par virgules → <li>
 */
function convertTextToList(textareaId) {
    var $ta = $('#' + textareaId);
    if (!$ta.length) return;
    var html = $ta.summernote('code') || '';
    // Extraire le texte brut
    var tempDiv = document.createElement('div');
    tempDiv.innerHTML = html;
    var text = (tempDiv.textContent || tempDiv.innerText || '').trim();
    if (!text) return;
    var items = text.split(',').map(function(s) { return s.trim(); }).filter(Boolean);
    if (items.length <= 1) return; // Rien à convertir
    var formatted = '<ul>' + items.map(function(s) { return '<li>' + s + '</li>'; }).join('') + '</ul>';
    $ta.summernote('code', formatted);
}

function generateInfo(){
    if (isGeneratingInfo) {
        return;
    }
    
    isGeneratingInfo = true;
    
    // Add 2-second delay to wait for translations to complete
    if (generateInfoTimeout) {
        clearTimeout(generateInfoTimeout);
    }
    
    generateInfoTimeout = setTimeout(function() {
        try {
            var languagesJson = $('#languages-json').val();
            if (!languagesJson) {
                console.warn('languages_json not found');
                isGeneratingInfo = false;
                return;
            }
            var languages = JSON.parse(languagesJson);
           
            // Utiliser Object.keys() pour itérer uniquement sur les vraies propriétés
            Object.keys(languages).forEach(function(targetLanguageId) {
                if (typeof generateMetaTag === 'function') {
                    generateMetaTag(targetLanguageId);
                }
                if (typeof generateDescription === 'function') {
                    generateDescription(targetLanguageId);
                }

                var inputElement = document.querySelector('input[name="product_description[' + targetLanguageId +'][name]"]');
                if (inputElement && typeof updateCharacterCount === 'function') {
                    updateCharacterCount(inputElement,'char-count-' + targetLanguageId +'-0');
                }
            });
        } catch (error) {
            console.error('Error in generateInfo:', error);
        } finally {
            isGeneratingInfo = false;
        }
    }, 0); // Wait 2 seconds for translations to complete
}
function generateMetaTag(language_id) {
    // Vérifier l'existence de l'élément UPC
    var upcElement = document.getElementById("input-upc");
    var upc = upcElement ? upcElement.value : '';
    
    var nameInput = document.querySelector('input[name="product_description['+ language_id +'][name]"]');
    var metaTagTitle = document.querySelector('input[name="product_description[' + language_id + '][meta_title]"]');
    var conditionnameElement = document.getElementById("condition-name-" + language_id);
    var metaTagDescription = document.querySelector('textarea[name="product_description[' + language_id + '][meta_description]"]');
    var hiddenDescription = document.querySelector('input[name="product_description[' + language_id + '][hidden_description]"]');
    var nameDescriptionElement = document.querySelector('textarea[name="product_description[' + language_id + '][description_supp]"]');
    var metaTagKeyword = document.querySelector('textarea[name="product_description[' + language_id + '][meta_keyword]"]');
    var tag = document.querySelector('input[name="product_description[' + language_id + '][tag]"]');

    // Vérifier que tous les éléments nécessaires existent
    if (!nameInput || !metaTagTitle || !conditionnameElement || !metaTagDescription || !nameDescriptionElement || !metaTagKeyword || !tag) {
        console.warn('Some required elements are missing for language_id: ' + language_id, {
            nameInput: !!nameInput,
            metaTagTitle: !!metaTagTitle,
            conditionnameElement: !!conditionnameElement,
            metaTagDescription: !!metaTagDescription,
            nameDescriptionElement: !!nameDescriptionElement,
            metaTagKeyword: !!metaTagKeyword,
            tag: !!tag
        });
        return;
    }

    var name = nameInput.value;
    var conditionname = conditionnameElement.textContent.trim();
    var additionalDescriptionHtml = nameDescriptionElement.value.trim();
    var additionalDescriptionText = additionalDescriptionHtml
      .replace(/<\/?[^>]+(>|$)/g, "") // Supprime les balises HTML
      .replace(/&nbsp;/g, ' ') // Remplace les espaces insécables HTML par des espaces
      .replace(/\s+/g, ' '); // Remplace les espaces multiples par un seul espace

    metaTagTitle.value =  '(' + conditionname + ') ' + name + ' UPC: ' + upc;
    metaTagDescription.value = additionalDescriptionText;
    hiddenDescription.value = additionalDescriptionText;
    var tagkeywords = conditionname + ' ' + name + ' ' + upc;
    tagkeywords = tagkeywords.replace(/[.,;:'"\{\}\[\]\(\)@%$&\-]/g, '');
    var tagkeywords = tagkeywords.split(/\s+/).join(','); // Sépare les mots et les joint avec des virgules
    if (tagkeywords.endsWith(',')) {
      tagkeywords = tagkeywords.slice(0, -1);
    }

    metaTagKeyword.value = tagkeywords;
    tag.value = tagkeywords;


    var keywords = conditionname + ' ' + name;
    //console.log('SEO Keywords - Original:', keywords);
    
    // Remove accents and special characters for SEO URL
    keywords = keywords.normalize('NFD').replace(/[\u0300-\u036f]/g, ''); // Remove accents
    keywords = keywords.toLowerCase(); // Convert to lowercase
    keywords = keywords.replace(/[^a-z0-9\s\-]/g, ''); // Keep only a-z, 0-9, spaces, and hyphens
    keywords = keywords.split(/\s+/).join('-'); // Replace spaces with hyphens
    keywords = keywords.replace(/-+/g, '-'); // Replace multiple hyphens with single hyphen
    keywords = keywords.replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
    keywords = keywords.trim();
    
    // Truncate to 64 characters at last complete word if too long
    if (keywords.length > 64) {
        keywords = keywords.substring(0, 64);
        // Cut at last hyphen to keep complete words
        var lastHyphen = keywords.lastIndexOf('-');
        if (lastHyphen > 0) {
            keywords = keywords.substring(0, lastHyphen);
        }
        //console.log('SEO Keywords - Truncated to fit 64 chars');
    }
    
    //console.log('SEO Keywords - After cleaning:', keywords, 'Length:', keywords.length);

    // Populate new SEO URL fields for all stores only if keyword is valid
    if (keywords && keywords.length > 0 && keywords.length <= 64) {
        //console.log('SEO Keywords - Valid, populating fields');
        try {
            var storesJson = document.getElementById('stores-json');
            if (storesJson && storesJson.value) {
                var stores = JSON.parse(storesJson.value);
                //console.log('SEO Keywords - Stores found:', stores.length);
                stores.forEach(function(store) {
                    var seoInput = document.getElementById('input-keyword-' + store.store_id + '-' + language_id);
                    if (seoInput) {
                        seoInput.value = keywords;
                        //console.log('SEO Keywords - Set field input-keyword-' + store.store_id + '-' + language_id + ' to:', keywords);
                    } else {
                        //console.warn('SEO Keywords - Field not found: input-keyword-' + store.store_id + '-' + language_id);
                    }
                });
            } else {
                //console.warn('SEO Keywords - stores-json not found or empty');
            }
        } catch (error) {
            //console.error('Error populating SEO URL fields:', error);
        }
    } else {
        //console.warn('SEO Keywords - Invalid or too long:', keywords, 'Length:', keywords.length);
    }
}
function generateDescription(language_id) {
    // Vérifier que les éléments essentiels existent
    const productIdElem = document.getElementById('product-id');
    if (!productIdElem) {
        console.warn('product_id element not found');
        return;
    }
    

    var form = document.getElementById('form-product');
    if (!form) {
        console.warn('form-product element not found');
        return;
    }
    
    // Récupérer le nom du produit
    var nameElem = form.querySelector(`input[name="product_description[${language_id}][name]"]`);
    var name = nameElem ? nameElem.value.trim() : '';

    // Début de la description avec les styles
    var description = `<style>
        .secondary-list-item {
            list-style-type: none;
            padding-left: 3em;
            text-indent: -1em;
        }
    </style>`;

    // Titre H1
    description += `<h1>${htmlspecialchars(name)}</h1>`;

    // Récupérer le category_id pour vérifier les exclusions
    var categoryIdElem = document.getElementById('category-id');
    var category_id = categoryIdElem ? parseInt(categoryIdElem.value) : 0;
    
    // Catégories où la condition ne doit pas apparaître
    var excludedCategories = [73836, 20349, 178893, 182066, 123417, 112529, 58540, 33602, 146496, 48619, 20357, 80077, 123422, 96991, 35190, 48677, 182068, 42425];

    // Condition du produit (seulement si pas dans les catégories exclues)
    if (!excludedCategories.includes(category_id)) {
        var conditionElem = document.getElementById(`condition-name-${language_id}`);
        var conditionName = conditionElem ? conditionElem.textContent.trim() : '';
        
        if (conditionName) {
            description += `<h3 style="color: darkblue;"><b>Condition:</b> <b style="color: black;">${conditionName}</b></h3>`;
        }

        // Conditions supplémentaires
        var conditionSuppElem = form.querySelector(`textarea[name="product_description[${language_id}][condition_supp]"]`);
        if (conditionSuppElem) {
            var conditionSupp = conditionSuppElem.value.trim();
            if (conditionSupp && conditionSupp !== '<p><br></p>') {
                description += `<h4 style="color: red;"><b>Additional Conditions:</b></h4>`;
                description += conditionSupp;
            }
        }
    }

    // Accessoires inclus
    var includedAccessoriesElem = form.querySelector(`textarea[name="product_description[${language_id}][included_accessories]"]`);
    if (includedAccessoriesElem) {
        var includedAccessories = includedAccessoriesElem.value.trim();
        if (includedAccessories && includedAccessories !== '<p><br></p>') {
            description += `<h3 style="color: darkblue;"><b>Included Accessories:</b></h3>`;
            description += includedAccessories;
        }
    }

    // Description supplémentaire
    var descriptionSuppElem = form.querySelector(`textarea[name="product_description[${language_id}][description_supp]"]`);
    if (descriptionSuppElem) {
        var descriptionSupp = descriptionSuppElem.value.trim();
        if (descriptionSupp && descriptionSupp !== '<p><br></p>') {
            description += `<h3 style="color: darkblue;"><b>Description:</b></h3>`;
            description += descriptionSupp;
        }
    }

    // Caractéristiques spécifiques
    description += '<h3 style="color: darkblue;"><b>Specific Features:</b></h3><ul class="three-columns">';

    var specificsTable = document.getElementById(`specifics-${language_id}`);
    var hasSpecifics = false;

    if (specificsTable) {
        var specificsRows = specificsTable.querySelectorAll('tbody tr');

        specificsRows.forEach(function(row) {
            var tds = row.querySelectorAll('td');
            
            // Name - chercher dans TD[1]
            var nameElem = tds[1] ? tds[1].querySelector(`[id*="product-description-${language_id}-specifics-Name"]`) : null;
            var name = nameElem ? nameElem.textContent.trim() : '';
            
            // Value - toujours dans TD[3] (4ème colonne)
            var valueElem = tds[3] ? tds[3].querySelector('input, select') : null;
            var values = [];
            
            if (valueElem) {
                if (valueElem.tagName.toLowerCase() === 'select' && valueElem.multiple) {
                    // Select multiple - récupérer toutes les options sélectionnées
                    values = Array.from(valueElem.selectedOptions)
                        .map(option => option.text.trim())
                        .filter(value => value !== '');
                } else if (valueElem.tagName.toLowerCase() === 'select') {
                    // Select simple
                    if (valueElem.selectedOptions[0] && valueElem.selectedOptions[0].value !== '') {
                        values.push(valueElem.selectedOptions[0].text.trim());
                    }
                } else {
                    // Input text
                    var value = valueElem.value.trim();
                    if (value !== '') {
                        values.push(value);
                    }
                }
            }
            
            // Ajouter à la description seulement si name et values sont valides
            if (name !== '' && values.length > 0 && !(values.length === 1 && values[0] === '')) {
                var valueList = values.map(ucwords);
                description += `<li><b>${name}:</b> ${valueList.join(', ')}</li>`;
                hasSpecifics = true;
            }
        });
    }

    if (!hasSpecifics) {
        description += '<li>No specific features available.</li>';
    }

    description += '</ul>';

    // Définition des termes en fonction de la langue
    var labels = language_id == 1 ? {
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

    // Fonction pour récupérer les valeurs des inputs
    function getInputValue(name) {
        var elem = form.querySelector(`input[name="${name}"]`);
        if (elem) {
            var value = parseFloat(elem.value);
            return isNaN(value) ? 'N/A' : value;
        }
        return 'N/A';
    }

    function getStringValue(name) {
        var elem = form.querySelector(`input[name="${name}"]`);
        return elem ? elem.value : 'N/A';
    }

    // Modèle, dimensions et poids
    var modelValue = getStringValue("model");
    description += `<p><b>${labels.Model}</b> ${ucwords(htmlspecialchars(modelValue))}</p>`;
    description += `<p><b>${labels.Dimension}</b> ${getInputValue("length")}x${getInputValue("width")}x${getInputValue("height")}${labels.Inch}</p>`;
    description += `<p><b>${labels.Weight}</b> ${getInputValue("weight")}${labels.Lbs}</p>`;

    // Images du produit
description += '<h3 style="color: darkblue;"><b>Photos:</b></h3>';
description += '<table bgcolor="FFFFFF" style="width: 500px;" border="1" cellspacing="1" cellpadding="5" align="center"><tbody>';

// Image principale
var imageElem = document.getElementById('thumb-image');
if (imageElem) {
    // thumb-image EST l'img directement, pas un conteneur
    var imageUrl = imageElem.src
        .replace(/-\d+x\d+\./, '.') // Remplace -300x300. ou -100x100. par .
        .replace('/cache/', '/');    // Enlève /cache/
    description += `<tr><td style="text-align: center;" align="center" valign="middle"><img src="${imageUrl}" width="450"></td></tr>`;
}

// Images additionnelles
var imageElements = document.querySelectorAll('[id^="product-image"] img');
imageElements.forEach(function(img) {
    var imageUrl = img.src
        .replace(/-\d+x\d+\./, '.') // Remplace -300x300. ou -100x100. par .
        .replace('/cache/', '/');    // Enlève /cache/
    description += `<tr><td style="text-align: center;" align="center" valign="middle"><img src="${imageUrl}" width="450"></td></tr>`;
});

description += '</tbody></table>';

    // Mise à jour du champ description et affichage
    var descriptionTextarea = document.querySelector(`textarea[name="product_description[${language_id}][description]"]`);
    if (descriptionTextarea) {
        descriptionTextarea.value = description;
        var displayDivId = descriptionTextarea.id.replace('product-description-', 'display-product-description-');
        var displayDiv = document.getElementById(displayDivId);
        if (displayDiv) {
            displayDiv.innerHTML = description;
        }
    }
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
        var user_token = document.querySelector('input[name="user_token"]').value;

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
        xhr.open('POST', 'index.php?route=shopmanager/shipping.get_shipping&user_token=' + user_token, true);
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
// Déclenchée uniquement par le changement de PRICE
function updatePriceWithShipping() {
    var marketplaceItemInputs = document.querySelectorAll('[id^="marketplace-items-"]');

    var priceInput = document.getElementById('input-price');
    var shippingCostInput = document.getElementById('input-shipping-cost');
    var priceWithShippingInput = document.getElementById('input-price-with-shipping');
    var price = parseFloat(priceInput.value) || 0;
    var shippingCost = parseFloat(shippingCostInput.value) || 0;

    priceWithShippingInput.value = (price + shippingCost).toFixed(2);
    if (shippingCost === 0) {
        calculateShippingCost();
    }

    // Cacher le badge — price est la source de vérité, pas de suggestion nécessaire
    var priceDisplay = document.getElementById('price-computed-display');
    if (priceDisplay) { priceDisplay.style.display = 'none'; }

    marketplaceItemInputs.forEach(function(input) {
        var encodedJson = input.value.trim();
        var decodedJson = decodeHtmlEntities(encodedJson);
        var jsonObject = JSON.parse(decodedJson);
        var marketplace_item_id = jsonObject.marketplace_item_id;
        if (marketplace_item_id) {
            editEbayPrice(marketplace_item_id, price.toFixed(2));
        } else {
            console.warn('Valeur non valide pour marketplace_item_id:', marketplace_item_id);
        }
    });
}

// Déclenchée par le changement de SHIPPING COST
// Met à jour price_with_shipping ET affiche le prix suggéré (pour maintenir le même total)
function updateShippingCost() {
    var priceInput = document.getElementById('input-price');
    var shippingCostInput = document.getElementById('input-shipping-cost');
    var priceWithShippingInput = document.getElementById('input-price-with-shipping');

    var price = parseFloat(priceInput.value) || 0;
    var newShipping = parseFloat(shippingCostInput.value) || 0;
    // Capturer l'ancien total AVANT de le recalculer
    var oldPriceWithShipping = parseFloat(priceWithShippingInput.value) || 0;

    // Mettre à jour price_with_shipping = price + nouveau shipping
    priceWithShippingInput.value = (price + newShipping).toFixed(2);

    // Prix suggéré = ancien total - nouveau shipping
    // (= ce que price devrait être pour conserver le même total)
    var suggestedPrice = (oldPriceWithShipping - newShipping).toFixed(2);
    var priceDisplay = document.getElementById('price-computed-display');
    if (priceDisplay && suggestedPrice !== price.toFixed(2)) {
        priceDisplay.textContent = '→ $' + suggestedPrice;
        priceDisplay.style.display = 'inline-block';
    } else if (priceDisplay) {
        priceDisplay.style.display = 'none';
    }
}

// Simulateur de rabais — n'affecte AUCUN champ, pas d'AJAX.
// Appelée aussi quand price_with_shipping change → affiche le price implicite.
function updatePrice() {
    var price            = parseFloat((document.getElementById('input-price') || {}).value) || 0;
    var shippingCost     = parseFloat((document.getElementById('input-shipping-cost') || {}).value) || 0;
    var priceWithShip    = parseFloat((document.getElementById('input-price-with-shipping') || {}).value) || 0;
    var discPct          = parseFloat((document.getElementById('input-discount') || {}).value) || 0;

    // Badge discount : price × (1 - disc%) + shipping
    var badge = document.getElementById('discount-simulated-display');
    if (badge) {
        if (discPct > 0) {
            var simTotal = (price * (1 - discPct / 100) + shippingCost).toFixed(2);
            badge.textContent = '→ CA$' + simTotal;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }

    // Badge price-computed : price implicite depuis price_with_shipping - shipping
    var computedPrice = (priceWithShip - shippingCost).toFixed(2);
    var priceDisplay = document.getElementById('price-computed-display');
    if (priceDisplay) {
        if (computedPrice !== price.toFixed(2) && priceWithShip > 0) {
            priceDisplay.textContent = '→ $' + computedPrice;
            priceDisplay.style.display = 'inline-block';
        } else {
            priceDisplay.style.display = 'none';
        }
    }
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
    // Récupérer la valeur actuelle du champ caché
    var hiddenValue = $('#actual-value-1-' + specificsRow).val();

    // Sélectionner le champ de saisie cible
    var targetElement = $('#product-description-1-' + specificsRow);

    // Stocker la valeur initiale
    var originalValue;
    if (targetElement.is('select[multiple]')) {
        var selectedValues = targetElement.val();
        originalValue = selectedValues ? selectedValues.join(',') : ''; // Vérification si 'selectedValues' n'est pas null
    } else {
        originalValue = targetElement.val();
    }
    $('#hidden-original-value-1-' + specificsRow).val(originalValue);

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
    $('.btTrf-' + specificsRow).hide();
    $('.btUndo-' + specificsRow).show();
}

function undoValue(specificsRow) {
    // Récupérer la valeur originale stockée
    var originalValue = $('#hidden-original-value-1-' + specificsRow).val();

    // Sélectionner le champ de saisie cible
    var targetElement = $('#product-description-1-' + specificsRow);

    // Réinitialiser le champ selon son type avec la valeur originale
    if (targetElement.is('select[multiple]')) {
        // Pour les multi-selects, restaurer les valeurs multiples
        if (originalValue && originalValue.trim() !== '') {
            var values = originalValue.split(',').map(v => v.trim());
            targetElement.val(values).trigger('change');
        } else {
            targetElement.val(null).trigger('change');
        }
    } else if (targetElement.is('select')) {
        // Pour les selects simples, restaurer la valeur
        if (originalValue && originalValue.trim() !== '') {
            targetElement.val(originalValue).trigger('change');
        } else {
            targetElement.val(null).trigger('change');
        }
    } else if (targetElement.is('textarea') || targetElement.is('input')) {
        // Pour les textarea et inputs, restaurer la valeur originale
        targetElement.val(originalValue || '');
    }

    // Vider la colonne 3 (suggestion/valeur affichée)
    $('#original-value-1-' + specificsRow).text('');

    // Réinitialiser la couleur de fond et le texte de la ligne (supprimer orange)
    var specificRowElem = document.getElementById('specifics-1-' + specificsRow);
    if (specificRowElem) {
        specificRowElem.style.removeProperty('background-color');
        specificRowElem.style.removeProperty('color');
        // Réinitialiser aussi les <td> de la ligne
        var tdElements = specificRowElem.querySelectorAll('td');
        tdElements.forEach(function(td) {
            td.style.removeProperty('background-color');
            td.style.removeProperty('color');
        });
    }
    
    // Vérifier si la traduction est activée
    var toTranslate = 0;
    var fieldId = 'product-description-1-' + specificsRow;
    var toTranslateFieldId = 'to-translate-1-' + specificsRow;
    
    
    // Vérifier d'abord le champ spécifique au row
    if ($('#' + toTranslateFieldId).length && $('#' + toTranslateFieldId).val()) {
        var toTranslateVal = $('#' + toTranslateFieldId).val();
        if (toTranslateVal === '1' || toTranslateVal === 1 || toTranslateVal === true || toTranslateVal === 'true') {
            toTranslate = 1;
        }
    } 
    // Sinon vérifier le champ global to-translate
    else if ($('#to-translate').length && $('#to-translate').val()) {
        try {
            var toTranslateVal = $('#to-translate').val();
            
            // Vérifier si c'est un objet JSON
            if (typeof toTranslateVal === 'string' && (toTranslateVal.startsWith('{') || toTranslateVal.startsWith('['))) {
                var toTranslateObj = JSON.parse(toTranslateVal);
                if (toTranslateObj[fieldId] === true || toTranslateObj[fieldId] === 1 || toTranslateObj[fieldId] === '1') {
                    toTranslate = 1;
                }
            } else if (toTranslateVal === '1' || toTranslateVal === 1 || toTranslateVal === true || toTranslateVal === 'true') {
                toTranslate = 1;
            }
        } catch (e) {
        }
    } else {
    }
    
    
    // Vérifier si la valeur n'est pas vide
    var hasValue = false;
    if (targetElement.is('select[multiple]')) {
        var currentVal = targetElement.val();
        hasValue = currentVal && currentVal.length > 0 && currentVal.some(v => v && v.trim() !== '');
    } else {
        var currentVal = targetElement.val();
        hasValue = currentVal && currentVal.trim() !== '';
    }
    
    
    // Si to_translate est activé ET qu'il y a une valeur à traduire, traduire le contenu
    if (toTranslate && typeof translateContentForAllLanguages === 'function') {
        if (hasValue) {
            //translateContentForAllLanguages(fieldId, '', 'product');
        } else {
            // Vider les champs dans toutes les langues si la valeur originale est vide
            var languages = {};
            try {
                languages = JSON.parse($('#languages-json').val());
            } catch (e) {}
            
            Object.keys(languages).forEach(function(language_id) {
                if (language_id === '1') return; // Skip source language
                var langTarget = $('#product-description-' + language_id + '-' + specificsRow);
                if (langTarget.length) {
                    if (langTarget.is('select')) {
                        langTarget.val(null).trigger('change');
                    } else {
                        langTarget.val('');
                    }
                }
            });
        }
    } else {
        // Si to_translate n'est pas activé, copier la valeur dans toutes les langues
        var languages = {};
        try {
            languages = JSON.parse($('#languages-json').val());
        } catch (e) {}
        
        Object.keys(languages).forEach(function(language_id) {
            if (language_id === '1') return; // Skip source language
            var langTarget = $('#product-description-' + language_id + '-' + specificsRow);
            if (langTarget.length) {
                if (hasValue) {
                    // Copier la valeur
                    if (langTarget.is('select[multiple]')) {
                        var values = currentVal; // currentVal est déjà un array pour multi-select
                        var normalizedValues = [];
                        values.forEach(function(val) {
                            var existingOption = null;
                            langTarget.find('option').each(function() {
                                if ($(this).val().toLowerCase() === val.toLowerCase()) {
                                    existingOption = $(this);
                                    return false;
                                }
                            });
                            if (existingOption) {
                                normalizedValues.push(existingOption.val());
                            } else {
                                langTarget.prepend('<option value="' + val + '">' + val + '</option>');
                                normalizedValues.push(val);
                            }
                        });
                        langTarget.val(normalizedValues).trigger('change');
                    } else if (langTarget.is('select')) {
                        var existingOption = null;
                        langTarget.find('option').each(function() {
                            if ($(this).val().toLowerCase() === currentVal.toLowerCase()) {
                                existingOption = $(this);
                                return false;
                            }
                        });
                        if (existingOption) {
                            langTarget.val(existingOption.val()).trigger('change');
                        } else {
                            langTarget.prepend('<option value="' + currentVal + '">' + currentVal + '</option>');
                            langTarget.val(currentVal).trigger('change');
                        }
                    } else {
                        langTarget.val(currentVal);
                    }
                } else {
                    // Vider si pas de valeur
                    if (langTarget.is('select')) {
                        langTarget.val(null).trigger('change');
                    } else {
                        langTarget.val('');
                    }
                }
            }
        });
    }
    
    // Masquer le bouton Undo et afficher le bouton Transfer
    $('.btUndo-' + specificsRow).hide();
    $('.btTrf-' + specificsRow).show();
}


// Initialiser les écouteurs d'événements après le chargement complet de la page
window.addEventListener('load', function() {



    // COMMENTÉ: generateInfo() maintenant appelé APRÈS handleTranslationAndModal (ligne 2408)
    // pour s'assurer que les specifics sont chargés avant de générer la description
    /*
    // Vérifier si le champ n'est pas vide
    if (inputField && inputField.value.trim() !== '') {
        // Vous pouvez ajouter ici le code que vous souhaitez exécuter si le champ n'est pas vide
        if (typeof generateInfo === 'function') {
    generateInfo();
}

       
    }
    */
   

    var form = document.getElementById('form-product');
    var cancelLink = document.querySelector('a.btn-light'); // Ajustez le sélecteur ici
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
    elements.forEach(function(element) {
       
        if (element.tagName.toLowerCase() === 'textarea' && element.classList.contains('summernote')) {
            $(element).on('summernote.blur', function() {
                // Guard contre la ré-entrance (summernote('code','') peut re-déclencher blur)
                if ($(element).data('sn-cleaning')) return;
                isFormModified = true;
                // Nettoyer le contenu si vide (évite <br>, <div><br></div>, <p><br></p>, etc.)
                var html = $(element).summernote('code');
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = html || '';
                if ((tempDiv.textContent || tempDiv.innerText || '').trim() === '') {
                    $(element).data('sn-cleaning', true);
                    $(element).summernote('code', '');
                    $(element).data('sn-cleaning', false);
                }
                // Appeler generateInfo() pour le champ name (-0), condition_supp, included_accessories
                var elementId = element.id;
                var elementName = element.name || '';
                if (
                    (elementId && elementId.match(/product-description-\d+-0$/)) ||
                    elementName.match(/product_description\[\d+\]\[condition_supp\]/) ||
                    elementName.match(/product_description\[\d+\]\[included_accessories\]/)
                ) {
                    if (typeof generateInfo === 'function') {
                        generateInfo();
                    }
                }
            });
        } else {
            element.addEventListener('change', function() {
                isFormModified = true;
                // Appeler generateInfo() UNIQUEMENT pour le champ name (ID se termine par -0)
                var elementId = element.id;
                if (elementId && elementId.match(/product-description-\d+-0$/)) {
                    if (typeof generateInfo === 'function') {
                        generateInfo();
                    }
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
        shippingCostInput.addEventListener('change', updateShippingCost);
    }

    var priceWithShippingInput = document.getElementById('input-price-with-shipping');
    if (priceWithShippingInput) {
        priceWithShippingInput.addEventListener('change', updatePrice);
    }
    
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


    initializeDragAndDrop();
    let locationInputElement = document.getElementById('input-location');
    const quantityInput = document.getElementById('input-quantity');
    const saveButton = document.getElementById('saveButton');
    const cancelButton = document.getElementById('cancelButton'); // Correction ici
    //const form = document.getElementById('form-product');

    // Création du message d'erreur
    const messageElement = document.createElement('span');
    messageElement.style.color = 'red';
    messageElement.textContent = 'Location cannot be empty when quantity is greater than 0';
    messageElement.style.display = 'none';
    locationInputElement.parentElement.appendChild(messageElement);

    function validateForm() {
        const isLocationEmpty = locationInputElement.value.trim() === '';
        const quantityValue = parseFloat(quantityInput.value) || 0;

        if (quantityValue > 0 && isLocationEmpty) {
            messageElement.style.display = 'inline';
            saveButton.disabled = true;
            saveButton.style.opacity = '0.5'; // Griser le bouton
        } else {
            messageElement.style.display = 'none';
            saveButton.disabled = false;
            saveButton.style.opacity = '1';
        }
    }

    function removeCancelButton() {
        if (cancelButton) {
            cancelButton.remove(); // Supprime le bouton du DOM
        }
    }

    // Désactiver le bouton save au chargement
    //saveButton.disabled = true;
    //saveButton.style.opacity = '0.5';

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

    var user_token = document.querySelector('input[name="user_token"]').value;
    const sku = document.getElementById('input-sku').value;
    const upc= document.querySelector('#input-upc').value;
    let quantityInputElement = document.getElementById('input-quantity');
    const unallocatedQuantityInput = document.getElementById('input-unallocated-quantity');

    let initialQuantity = parseInt(quantityInputElement.value, 10);
    let initialUnallocatedQuantity = parseInt(unallocatedQuantityInput.value, 10);





    function checkQuantityChange() {
        const currentQuantity = parseInt(quantityInputElement.value, 10);
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

    quantityInputElement.addEventListener('change', checkQuantityChange);
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
        // Activer ou désactiver le bouton de téléchargement en fonction de la sélection de fichiers
        $('#upload-images-btn').prop('disabled', !(secondaryImageFiles.length > 0));
        $('#upload-image-btn').prop('disabled', !(primaryImageFile));
        $('#sourcescode-btn').prop('disabled', !sourcescode || sourcescode.trim() === ''); // Désactiver si sourcescode est vide
    }

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


    // Récupérer les éléments
    var tokenElement = document.querySelector('input[name="user_token"]');
    var skuElement = document.querySelector('#input-sku');
    var upcElement = document.querySelector('#input-upc');
    var locationInputLabel = document.getElementById('input-location');
    var printLabelButton = document.getElementById('btn-print-label');
    var printQuantityLabelButton = document.getElementById('btn-print-quantity-label');

    // Vérification des éléments requis
    if (!tokenElement || !skuElement || !locationInputLabel || !printLabelButton || !printQuantityLabelButton) {
        console.error('Un ou plusieurs éléments nécessaires sont manquants.');
        return;
    }

    var user_token = tokenElement.value;

    // Fonction d'ouverture de la fenêtre d'impression
   

    // Impression à partir de la location
    printLabelButton.addEventListener('click', function () {
        var location = locationInputLabel.value.trim();
        if (location) {
            openPrintLabel(null,null,1,location, 'yes');
        } else {
            alert(TEXT_ALERT_ENTER_LOCATION_PRINT);
        }
    });

    // Impression à partir du SKU
    printQuantityLabelButton.addEventListener('click', function () {
        var sku = skuElement.value.trim();
        var upc = upcElement ? upcElement.value.trim() : null;

        if (sku) {
            openPrintLabel(sku, upc, 1, '', 'yes');
        } else {
            alert(TEXT_ALERT_NO_SKU);
        }
    });

    // Fonction pour ajouter un nouveau fabricant
    document.getElementById("btn-add-manufacturer").addEventListener("click", function () {
        var manufacturerName = document.querySelector('input[name="manufacturer"]').value;
        var user_token = document.querySelector('input[name="user_token"]').value;

        if (manufacturerName) {
            $.ajax({
                url: 'index.php?route=shopmanager/manufacturer.add&ajax=true&user_token=' + user_token,
                type: 'post',
                data: { name: manufacturerName },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        document.querySelector('input[name="manufacturer_id"]').value = response.manufacturer_id;
                    } else {
                        console.error(TEXT_ERROR_MANUFACTURER + ' : ' + response.error);
                    }
                },
                error: function (xhr, status, error) {
                    console.error(TEXT_ERROR_MANUFACTURER + ' : ' + error);
                }
            });
        } else {
            alert(TEXT_ALERT_ENTER_MANUFACTURER_NAME);
        }
    });

    // Fonction pour modifier un fabricant existant
    document.getElementById("btn-edit-manufacturer").addEventListener("click", function () {
        var manufacturerId = document.querySelector('input[name="manufacturer_id"]').value;
        var manufacturerName = document.querySelector('input[name="manufacturer"]').value;
        var user_token = document.querySelector('input[name="user_token"]').value;

        if (manufacturerId && manufacturerName) {
            $.ajax({
                url: 'index.php?route=shopmanager/manufacturer.edit&ajax=true&user_token=' + user_token + '&manufacturer_id=' + manufacturerId,
                type: 'post',
                data: { name: manufacturerName },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert(TEXT_SUCCESS_MANUFACTURER_UPDATED);
                    } else {
                        alert(TEXT_ERROR_MANUFACTURER + ' : ' + response.error);
                    }
                },
                error: function (xhr, status, error) {
                    alert(TEXT_ERROR_MANUFACTURER + ' : ' + error);
                }
            });
        } else {
            alert(TEXT_ALERT_SELECT_MANUFACTURER_NAME);
        }
    });

    // Fonction pour supprimer un fabricant existant
    document.getElementById("btn-delete-manufacturer").addEventListener("click", function () {
        var manufacturerId = document.querySelector('input[name="manufacturer_id"]').value;
        var user_token = document.querySelector('input[name="user_token"]').value;

        if (manufacturerId) {
            if (confirm(TEXT_CONFIRM_DELETE_MANUFACTURER)) {
                $.ajax({
                    url: 'index.php?route=shopmanager/manufacturer.delete&ajax=true&user_token=' + user_token + '&manufacturer_id=' + manufacturerId,
                    type: 'post',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert(TEXT_SUCCESS_MANUFACTURER_DELETED);
                            document.querySelector('input[name="manufacturer"]').value = '';
                            document.querySelector('input[name="manufacturer_id"]').value = '';
                        } else {
                            alert(TEXT_ERROR_MANUFACTURER + ' : ' + response.error);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert(TEXT_ERROR_MANUFACTURER + ' : ' + error);
                    }
                });
            }
        } else {
            alert(TEXT_ALERT_SELECT_MANUFACTURER_DELETE);
        }
    });

    

    const excludedKeywords = ['[description]', '[meta_title]', '[meta_description]', '[tag]', '[meta_keyword]', 'response_', 'display_','_specifics_Name'];
    
    // Process event: translate THIS field only, then call generateInfo
    const processTranslationEvent = (event) => {
        const element = event.target;
        let elementName = element.getAttribute('name');
        let elementID = element.id;

        // Skip if not a product-description field
        if (!elementID || !elementID.startsWith('product-description-1-')) {
            return;
        }

        // Skip excluded fields
        if (elementName && excludedKeywords.some(keyword => elementName.includes(keyword))) {
            return;
        }

        if (elementID && excludedKeywords.some(keyword => elementID.includes(keyword))) {
            return;
        }

        // Extract row number from ID (e.g., product-description-1-12 -> 12)
        const idParts = elementID.split('-');
        const rowId = idParts[idParts.length - 1];
        
        // Check if to-translate field exists and equals 0 (copy directly without translation)
        const toTranslateField = document.getElementById(`to-translate-1-${rowId}`);
        
        if (toTranslateField && toTranslateField.value === '0') {
            
            // Copy value directly to language 2
            const targetElement = document.getElementById(elementID.replace('-1-', '-2-'));
            if (targetElement) {
                if (element.tagName === 'SELECT') {
                    if (element.multiple) {
                        // For multi-select, copy all selected values
                        const selectedValues = Array.from(element.selectedOptions).map(opt => opt.value);
                        Array.from(targetElement.options).forEach(opt => {
                            opt.selected = selectedValues.includes(opt.value);
                        });
                    } else {
                        // For single select, copy selected value
                        targetElement.value = element.value;
                    }
                } else {
                    // For input/textarea, copy value directly
                    targetElement.value = element.value;
                }
            }
            
            // Still call generateInfo after copying
            if (typeof generateInfo === 'function') {
                generateInfo();
            }
            return;
        }

        
        // Create to-translate with ONLY this field
        let hiddenInput = document.querySelector("input#to-translate");
        if (!hiddenInput) {
            hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.id = "to-translate";
            hiddenInput.name = "to_translate";
            document.body.appendChild(hiddenInput);
        }
        
        // Set ONLY this field for translation
        let translationData = {};
        translationData[elementID] = true;
        hiddenInput.value = JSON.stringify(translationData);
        
        // Translate this field, then generate info
        /*handleTranslationAndModal('form-product', false).then(() => {
            if (typeof generateInfo === 'function') {
                generateInfo();
            }
        }).catch(error => {
            console.error('❌ Translation error:', error);
        });*/
    };
    
    function setupFieldTranslation() {
        var sourceLanguageId = '1';
        
        // Event delegation on form for ALL fields (including dynamically added ones)
        const form = document.getElementById('form-product');
        if (form) {
            // Use event delegation for blur events (captures all fields including dynamic ones)
            form.addEventListener('blur', processTranslationEvent, true);
            
            // Also handle change events for selects
            form.addEventListener('change', function(event) {
                const element = event.target;
                if (element.tagName === 'SELECT' && element.id.startsWith('product-description-1-')) {
                    processTranslationEvent(event);
                }
            }, true);
            
        }
        
        // Also handle Summernote fields
        $(document).on('summernote.blur', 'textarea[id^="product-description-1-"]', processTranslationEvent);
    }

    setupFieldTranslation();

    // Auto-sync all fields on page load
    function autoSyncAllFields() {
        if (document.getElementById('loadingModal')) {
            showModal('#loadingModal');
        }
        
        const sourceLanguageId = '1';
        const targetLanguageId = '2';
        let fieldsToProcess = [];
        
        // Find all source language fields
        const sourceFields = document.querySelectorAll(`input[id^='product-description-${sourceLanguageId}-'], textarea[id^='product-description-${sourceLanguageId}-'], select[id^='product-description-${sourceLanguageId}-']`);
        
        
        sourceFields.forEach(function(sourceField) {
            const sourceId = sourceField.id;
            const sourceName = sourceField.getAttribute('name');
            
            // Skip excluded fields
            if (sourceName && excludedKeywords.some(keyword => sourceName.includes(keyword))) {
                return;
            }
            
            if (sourceId && excludedKeywords.some(keyword => sourceId.includes(keyword))) {
                return;
            }
            
            // Get source value
            let sourceValue = '';
            if (sourceField.tagName === 'SELECT') {
                if (sourceField.multiple) {
                    sourceValue = Array.from(sourceField.selectedOptions).map(opt => opt.value).join(',');
                } else {
                    sourceValue = sourceField.value;
                }
            } else {
                sourceValue = sourceField.value;
            }
            
            // Skip empty source fields
            if (!sourceValue || sourceValue.trim() === '') {
                return;
            }
            
            // Find corresponding target field
            const targetId = sourceId.replace(`-${sourceLanguageId}-`, `-${targetLanguageId}-`);
            const targetField = document.getElementById(targetId);
            
            if (!targetField) {
                return;
            }
            
            // Get target value
            let targetValue = '';
            if (targetField.tagName === 'SELECT') {
                if (targetField.multiple) {
                    targetValue = Array.from(targetField.selectedOptions).map(opt => opt.value).join(',');
                } else {
                    targetValue = targetField.value;
                }
            } else {
                targetValue = targetField.value;
            }
            
            // Check if target is empty OR different from source
            let needsSync = false;
            
            if (!targetValue || targetValue.trim() === '') {
                // Target is completely empty
                needsSync = true;
            } else if (sourceField.tagName === 'SELECT' && sourceField.multiple && targetField.tagName === 'SELECT' && targetField.multiple) {
                // For multi-select, compare selected options (both must be SELECT)
                const sourceOptions = Array.from(sourceField.selectedOptions).map(opt => opt.value).sort();
                const targetOptions = Array.from(targetField.selectedOptions).map(opt => opt.value).sort();
                // Check if arrays are different
                if (sourceOptions.length !== targetOptions.length) {
                    needsSync = true;
                }
            } // else: for other fields, do not sync if target has any value
            
            if (needsSync) {
                // Extract row ID
                const idParts = sourceId.split('-');
                const rowId = idParts[idParts.length - 1];
                
                // Check to_translate field
                const toTranslateField = document.getElementById(`to-translate-${sourceLanguageId}-${rowId}`);
                
                fieldsToProcess.push({
                    sourceId: sourceId,
                    targetId: targetId,
                    sourceField: sourceField,
                    targetField: targetField,
                    sourceValue: sourceValue,
                    rowId: rowId,
                    needsTranslation: !toTranslateField || toTranslateField.value !== '0'
                });
            }
            
        });
        
        if (fieldsToProcess.length === 0) {
            if (document.getElementById('loadingModal')) {
                hideModal('#loadingModal');
            }
            return;
        }
        
        
        // Process fields sequentially
        let processedCount = 0;
        
        function processNextField() {
            if (processedCount >= fieldsToProcess.length) {
                if (document.getElementById('loadingModal')) {
                    hideModal('#loadingModal');
                }
                if (typeof generateInfo === 'function') {
                    generateInfo();
                }
                return;
            }
            
            const field = fieldsToProcess[processedCount];
            processedCount++;
            
            if (field.needsTranslation) {
                // Translate the field
                
                let hiddenInput = document.querySelector("input#to-translate");
                if (!hiddenInput) {
                    hiddenInput = document.createElement("input");
                    hiddenInput.type = "hidden";
                    hiddenInput.id = "to-translate";
                    hiddenInput.name = "to_translate";
                    document.body.appendChild(hiddenInput);
                }
                
                let translationData = {};
                translationData[field.sourceId] = true;
                hiddenInput.value = JSON.stringify(translationData);
                
               /* handleTranslationAndModal('form-product', false).then(() => {
                    setTimeout(processNextField, 200); // Small delay between translations
                }).catch(error => {
                    console.error(`❌ Translation error for ${field.sourceId}:`, error);
                    processNextField(); // Continue even if one fails
                }); */
                
                // Translation disabled: skip and continue loop
                processNextField();
                
            } else {
                // Copy directly (to_translate = 0)
                
                if (field.targetField.tagName === 'SELECT') {
                    if (field.targetField.multiple) {
                        const sourceValues = Array.from(field.sourceField.selectedOptions).map(opt => opt.value);
                        
                        // First, add missing options to target if they don't exist
                        sourceValues.forEach(value => {
                            const optionExists = Array.from(field.targetField.options).some(opt => opt.value === value);
                            if (!optionExists) {
                                const newOption = document.createElement('option');
                                newOption.value = value;
                                newOption.text = value;
                                field.targetField.add(newOption);
                            }
                        });
                        
                        // Then select the appropriate options
                        Array.from(field.targetField.options).forEach(opt => {
                            opt.selected = sourceValues.includes(opt.value);
                        });
                        // Debug log for product-description-2-14
                        if (field.sourceId === 'product-description-1-14') {
                            const selected = Array.from(field.targetField.selectedOptions).map(opt => opt.value);
                        }
                    } else {
                        field.targetField.value = field.sourceField.value;
                        if (field.sourceId === 'product-description-1-14') {
                            const selected = Array.from(field.targetField.selectedOptions).map(opt => opt.value);
                        }
                    }
                } else {
                    field.targetField.value = field.sourceField.value;
                }
                
                setTimeout(processNextField, 100); // Small delay
            }
        }
        
        // Start processing
        processNextField();
    }
    
    // Run auto-sync after a short delay to ensure DOM is fully loaded
    setTimeout(autoSyncAllFields, 500);
    //setTimeout(generateInfo, 500);
  



// Attendre un court instant pour s'assurer que le DOM est mis à jour avant de continuer


    // Existing code for form initialization
    const product_id = document.getElementById('product-id').value;
    const inputField = document.getElementById('product-description-1-0');

    // COMMENTÉ: generateInfo() déjà appelé dans window.addEventListener('load') ligne 1220
    /*
    if (inputField && inputField.value.trim() !== '') {
        if (typeof generateInfo === 'function') {
            generateInfo();
        }
    }
    */

    var form = document.getElementById('form-product');
    var cancelLink = document.querySelector('a.btn-light');
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

    // Remove data-oc-toggle to prevent common.js from handling submit
    // We handle it ourselves with proper contentType to avoid WAF 403
    form.removeAttribute('data-oc-toggle');

    form.addEventListener('submit', function(event) {
        // Prevent default form submission
        event.preventDefault();

        // Call the function to handle translation and modal display
        handleTranslationAndModal('form-product',false).then(() => {
            // Use AJAX with proper contentType and headers
            $.ajax({
                url: form.action,
                type: 'POST',
                data: $(form).serialize(),
                dataType: 'json',
                contentType: 'application/x-www-form-urlencoded', // Force URL-encoded to avoid WAF 403
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Tell OpenCart this is AJAX
                },
                success: function(json) {
                    $('.alert-dismissible').remove();
                    
                    if (json['redirect']) {
                        location = json['redirect'];
                    }
                    if (json['success']) {
                        var cancelBtn = document.getElementById('cancelButton');
                        var listUrl = cancelBtn ? cancelBtn.href : null;
                        $('#alert').prepend('<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check"></i> ' + json['success'] + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                        setTimeout(() => { if (listUrl) { location.href = listUrl; } else { location.reload(); } }, 1500);
                    }
                    if (json['error']) {
                        let errorMsg = json['error']['warning'] || 'Unknown error';
                        
                        // Show all error details in console for debugging
                        console.error('Form validation errors:', json['error']);
                        
                        // Build detailed error message
                        let errorDetails = '<ul>';
                        for (let key in json['error']) {
                            if (key !== 'warning') {
                                errorDetails += '<li><strong>' + key + ':</strong> ' + json['error'][key] + '</li>';
                            }
                        }
                        errorDetails += '</ul>';
                        
                        $('#alert').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i> ' + errorMsg + errorDetails + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Save error:', xhr.responseText);
                    $('#alert').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i> Error saving product: ' + error + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                }
            });
        }).catch((error) => {
            console.error('Error during translation:', error);
        });
    });

    form.addEventListener('submit', function() {
        // Réinitialise isFormModified à false lorsque le formulaire est soumis
        isFormModified = false;
    });
    
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



   
    // Fonction pour ajouter des boutons de suppression
    function addDeleteButtons() {
        const checkboxes = document.querySelectorAll('.save-data-checkbox');
        checkboxes.forEach(checkbox => {
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.setAttribute('data-bs-toggle', 'tooltip');
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
        const tableBody = document.getElementById('uploaded-images');
        
        if (!tableBody) {
            console.error('Table body element with ID "uploaded-images" not found');
            return; // Exit the function if the element is not found
        }
        
        tableBody.innerHTML = ''; // Now safe to set innerHTML
        rowsData.forEach(data => {
            tableBody.appendChild(data.row);
        });
    }

    // Fonction pour rafraîchir le tableau après suppression d'une image
    function refreshTable() {
        const tableBody = document.getElementById('uploaded-images');
        
        if (!tableBody) {
            console.error('Table body element with ID "uploaded-images" not found');
            return;
        }
        
        const remainingRows = document.querySelectorAll('.col-sm-2, .col-md-2');
        tableBody.innerHTML = '';
        
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
    //handleImages();

    // Use centralized functions from tools.js
    if (typeof initImageResolutionCheck === 'function') {
        initImageResolutionCheck();
    }
    if (typeof initImagePreview === 'function') {
        initImagePreview();
    }

});












function checkFormStatus() {
    var categoryChecked = document.querySelector('input[name^="product_category"]:checked');
    var conditionChecked = document.querySelector('input[name="condition_id"]:checked');
    var priceChecked = document.querySelector('input[name^="price_ebay"]:checked');
    var product_id = document.querySelector('input[name="product_id"]').value;

    
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
    showModal('#loadingModal');
    // Étape 1 : Extraire les prix existants et condition_marketplace_item_id pour chaque ligne
    rows.forEach(row => {
        const radioInput = row.querySelector('.save-data-radio');
        if (radioInput) {
            const conditionMarketplaceItemId = parseInt(radioInput.id.match(/\d+/)[0]);

            // Vérifier s'il y a un radio button correspondant (relatedRadio)
            const relatedRadio = document.querySelector(`#condition-${conditionMarketplaceItemId}`);
            if (relatedRadio) {
                // Vérifier s'il existe un prix
                const priceValue = parseFloat(radioInput.value) || null;  // Convertir en nombre ou laisser null

                prices.push({
                    conditionMarketplaceItemId: conditionMarketplaceId,
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
        hideModal('#loadingModal');
    });
}

function changeProduct(url) {
    // Définir l'URL de redirection avec le user_token et l'ID du produit
     
    // Redirection vers l'URL spécifiée
    window.location.href = url;
}
function initializeDragAndDrop() {

    // Gestion de la zone de dépôt pour l'image principale
    if (document.getElementById('drop-area-principal')) {

        document.getElementById('drop-area-principal').addEventListener('click', function() {
            document.getElementById('file-input-principal').click();
        });

        document.getElementById('drop-area-principal').addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '#f0f0f0';
        });

        document.getElementById('drop-area-principal').addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '';
        });

        document.getElementById('drop-area-principal').addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '';
            var files = e.dataTransfer.files;
            uploadFiles(files, 'principal');
        });
    }

    // Gestion de la zone de dépôt pour les images supplémentaires
    if (document.getElementById('drop-area-secondary')) {

        document.getElementById('drop-area-secondary').addEventListener('click', function() {
            document.getElementById('file-input-secondary').click();
        });

        document.getElementById('drop-area-secondary').addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '#f0f0f0';
        });

        document.getElementById('drop-area-secondary').addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '';
        });

        document.getElementById('drop-area-secondary').addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.backgroundColor = '';
            var files = e.dataTransfer.files;
            uploadFiles(files, 'secondary');
        });
    }

    // Gestion de l'input de fichier pour l'image principale
    if (document.getElementById('file-input-principal')) {
        document.getElementById('file-input-principal').addEventListener('change', function() {
            var files = this.files;
            uploadFiles(files, 'principal');
        });
    }

    // Gestion de l'input de fichier pour les images supplémentaires
    if (document.getElementById('file-input-secondary')) {
        document.getElementById('file-input-secondary').addEventListener('change', function() {
            var files = this.files;
            uploadFiles(files, 'secondary');
        });
    }
}

// Appel de la fonction d'initialisation après le chargement de la page

function uploadFiles(files, type) {
    var user_token = document.querySelector('input[name="user_token"]').value;
    var product_id = document.querySelector('input[name="product_id"]').value;

    var formData = new FormData();
    for (var i = 0; i < files.length; i++) {
        if (type === 'principal') {
            formData.append('imageprincipal', files[i]);
        } else {
            formData.append('imagesecondary[]', files[i]);
        }
    }

    var url = 'index.php?route=shopmanager/tools.uploadImagesFiles&product_id=' + product_id + '&user_token=' + user_token;

    // Déterminer l'URL d'upload selon le type d'image
    url  += (type === 'principal') ? '&type=pri'  : '&type=sec';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);

                if (response.success) {
                    if (response.product_images) {
                        updateImagesUI(response.product_images);
                    } else {
                        console.warn("⚠️ No product_images in response");
                    }
                    /*if (type === 'principal') {
                        displayUploadedImagePrincipal(response.image);
                    } else {
                        displayUploadedImages(response.images);
                    }*/
                    updateImagesUI(response.product_images);
                } else {
                    console.error("❌ Upload error:", response.error);
                    alert(TEXT_ERROR_IMAGE_UPLOAD + ': ' + (response.error || TEXT_ERROR_UNKNOWN));
                }
            } catch (e) {
                console.error("❌ JSON Parse error:", e);
                console.error("Response text:", xhr.responseText);
                alert(TEXT_ERROR_SERVER_RESPONSE);
            }
        } else {
            console.error("❌ XHR error - status:", xhr.status);
            alert(TEXT_ERROR_UPLOAD_STATUS + ': ' + xhr.status);
        }
    };
    xhr.onerror = function() {
        console.error("❌ XHR request failed");
        alert(TEXT_ERROR_NETWORK_UPLOAD);
    };
    xhr.send(formData);
}

function displayUploadedImagePrincipal(image) {
    var thumb = document.getElementById('thumb-image');
    thumb.src = image.thumb;
    document.getElementById('input-image').value = image.path;
}

function displayUploadedImages(images) {
    var tbody = document.getElementById('uploaded-images');
    images.forEach(function(image, index) {
        var row = document.createElement('tr');
        row.id = 'image-row' + index;
        row.innerHTML = `
            <td class="col-sm-1 text-left"><img src="${image.thumb}" alt="" class="img-thumbnail" /></td>
            <td class="col-sm-1 text-left">
                <button type="button" onclick="removeImage('${image.image}', 'sec', '#image-row${index}');" data-bs-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa-solid fa-minus-circle"></i></button>
            </td>
            <td class="col-sm-4 text-right"><input type="text" name="product_image[${index}][sort_order]" value="${image.sort_order}" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>
        `;
        tbody.appendChild(row);
    });
}

function cleanHTML(rawHTML) {
    // Créez un parser DOM virtuel
    const parser = new DOMParser();
    const doc = parser.parseFromString(rawHTML, 'text/html');

    // Convertir le document HTML en texte brut
    let htmlString = doc.documentElement.outerHTML;

    // Identifiez le segment clé où commencer à conserver le contenu
    const segmentStart = "const lang = document.documentElement.lang;";

    // Vérifiez si le segment clé existe
    const segmentIndex = htmlString.indexOf(segmentStart);
    if (segmentIndex !== -1) {
        // Conservez tout ce qui commence à partir du segment clé
        htmlString = htmlString.slice(segmentIndex);
    }

    // Analysez à nouveau le contenu nettoyé pour retourner un document valide
    const cleanedDoc = parser.parseFromString(htmlString, 'text/html');

    // Supprimez tous les éléments inutiles restants
  //  cleanedDoc.querySelectorAll('script, meta, link').forEach(el => el.remove());

    // Retournez le contenu nettoyé
    return cleanedDoc.documentElement.outerHTML;
}

function uploadImages() {
    // Récupérer les valeurs de `product_id` et `user_token`
    var product_id = document.querySelector('input[name="product_id"]').value;
    var user_token = document.querySelector('input[name="user_token"]').value;
    var sourcecode = document.querySelector('textarea[name="sourcecode"]').value; // Assuming `sourcecode` is in a textarea

    // Récupérer les fichiers image du formulaire
    var formData = new FormData();
    var primaryImageFile = $('#input-image-principal')[0] ? $('#input-image-principal')[0].files[0] : null;
    var secondaryImageFiles = $('#input-images-secondary')[0] ? $('#input-images-secondary')[0].files : [];
    sourcecode = cleanHTML(sourcecode);
    // Ajouter `product_id` et `sourcecode` aux données du formulaire
    formData.append('product_id', product_id);
    formData.append('sourcecode', sourcecode);
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
    }
 

    // Envoyer les données au contrôleur via AJAX
    $.ajax({
        url: 'index.php?route=shopmanager/tools.uploadImagesFiles&user_token=' + user_token,
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateImagesUI(response.product_images);
                $('#input-image-principal').val('');  // Efface l'image principale
                $('#input-image-secondary').val('');   // Efface les images secondaires
                $('#input-sourcecode').val('');             // Efface le code source
                $('#upload-images-btn').prop('disabled');
                $('#upload-image-btn').prop('disabled');
                $('#sourcescode-btn').prop('disabled'); // Désactiver si sourcescode est vide
            } else if (response.error) {
                alert(TEXT_ERROR_MANUFACTURER + ' : ' + response.error);
            }
        },
        error: function(xhr) {
            console.error("Erreur lors du téléchargement des images : " + xhr.responseText);
            alert(TEXT_ERROR_MANUFACTURER + ' : ' + xhr.responseText);
        }
    });
}


// Fonction pour mettre à jour l'interface avec les nouvelles images
function updateImagesUI(productImages) {
   
    // Mettre à jour l'image principale
    if (productImages.primary) {
        
        // L'élément img a directement l'ID thumb-image
        const thumbImg = document.getElementById('thumb-image');
        const inputImage = document.getElementById('input-image');
        const fullsizeImg = document.querySelector('.actual-image-preview');
        
        if (thumbImg && productImages.primary.image) {
            // Obtenir l'URL de base (tout ce qui est avant /image/)
            const currentSrc = thumbImg.src;
            const baseUrl = currentSrc.split('/image/')[0] + '/image/';
            
            // Construire la nouvelle URL avec le chemin relatif
            const newImageUrl = baseUrl + productImages.primary.image;
            
            thumbImg.src = newImageUrl;
            thumbImg.setAttribute('data-oc-placeholder', newImageUrl);
            
            // Mettre à jour aussi l'image fullsize pour le mouseover
            if (fullsizeImg) {
                fullsizeImg.src = newImageUrl;
                fullsizeImg.setAttribute('data-image-path', productImages.primary.image); // Chemin relatif seulement
            }
        } else {
            console.error('❌ Cannot update image:', {
                'thumbImg exists': !!thumbImg,
                'image path': productImages.primary.image
            });
        }
        
        if (inputImage && productImages.primary.image) {
            // Stocker SEULEMENT le chemin relatif dans l'input hidden
            inputImage.value = productImages.primary.image;
        } else {
            console.error('❌ Cannot save path:', {
                'inputImage exists': !!inputImage,
                'image value': productImages.primary.image
            });
        }
    }

    // Mettre à jour les images secondaires dans le tbody avec id="uploaded-images"
    var image_row = window.image_row || 0;
    
    if (productImages.secondary && productImages.secondary.length > 0) {
        productImages.secondary.forEach(function(imageData) {
            var html = '<tr id="product-image-row-' + image_row + '">';
            html += '  <td>';
            html += '    <div class="border rounded d-block actual-image-container" style="max-width: 300px; position: relative; display: inline-block; cursor: pointer; border-radius: 4px; padding: 2px;">';
            html += '      <img src="' + imageData.thumb + '" alt="" title="" id="product-image-' + image_row + '" data-oc-placeholder="' + imageData.thumb + '" class="img-fluid thumbnail-actual-image"/>';
            html += '      <input type="hidden" name="product_image[' + image_row + '][image]" value="' + imageData.image + '" id="input-product-image-' + image_row + '"/>';
            html += '      <div class="fullsize-actual-image-wrapper">';
            // Construire l'URL complète pour l'image preview
            var baseUrl = imageData.thumb.split('/image/')[0] + '/image/';
            var fullImageUrl = baseUrl + imageData.image;
            html += '        <img src="' + fullImageUrl + '" alt="" class="actual-image-preview" data-image-path="' + imageData.image + '" data-resolution="">';
            html += '        <div class="fullsize-resolution-overlay"></div>';
            html += '      </div>';
            html += '      <div class="d-grid">';
            html += '        <button type="button" data-oc-toggle="image" data-oc-target="#input-product-image-' + image_row + '" data-oc-thumb="#product-image-' + image_row + '" class="btn btn-primary rounded-0"><i class="fa-solid fa-pencil"></i> Edit</button>';
            html += '        <button type="button" data-oc-toggle="clear" data-oc-target="#input-product-image-' + image_row + '" data-oc-thumb="#product-image-' + image_row + '" class="btn btn-warning rounded-0"><i class="fa-regular fa-trash-can"></i> Clear</button>';
            html += '      </div>';
            html += '    </div>';
            html += '  </td>';
            html += '  <td><input type="text" name="product_image[' + image_row + '][sort_order]" value="' + (imageData.sort_order || 0) + '" placeholder="Sort Order" class="form-control"/></td>';
            html += '  <td class="text-end"><button type="button" onclick="$(\'#product-image-row-' + image_row + '\').remove();" data-bs-toggle="tooltip" title="Remove" class="btn btn-danger"><i class="fa-solid fa-minus-circle"></i></button></td>';
            html += '</tr>';

            $('#uploaded-images').append(html);
            
            // Use centralized function from tools.js for newly added image
            var container = document.getElementById('product-image-row-' + image_row);
            if (container) {
                var thumb = container.querySelector('.thumbnail-actual-image');
                if (thumb && typeof checkImageResolution === 'function') {
                    checkImageResolution(thumb);
                }
            }
            
            image_row++;
        });
        
        // Reinitialize image preview after adding new images
        if (typeof initImagePreview === 'function') {
            initImagePreview();
        }
        
        window.image_row = image_row;
    }
    
    if (typeof generateInfo === 'function') {
        generateInfo();
    }

}

function uploadEbayImages() {
    // Get product_id and user_token
    var product_id = document.querySelector('input[name="product_id"]').value;
    var user_token = document.querySelector('input[name="user_token"]').value;
    
    if (!product_id) {
        alert(TEXT_PRODUCT_ID_MISSING);
        return;
    }
    
    // Disable button during upload
    $('#ebay-images-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
    
    // Prepare data
    var formData = new FormData();
    formData.append('product_id', product_id);
    
    // Send request to controller
    $.ajax({
        url: 'index.php?route=shopmanager/maintenance/image.syncImagesForProductWitheBay&user_token=' + user_token,
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            // Re-enable button
            $('#ebay-images-btn').prop('disabled', false).html('<i class="fa-brands fa-ebay"></i> eBay Images');
            
            // DEBUG: Log response to console
            console.log('eBay Images Response:', response);
            console.log('product_images:', response.product_images);
            console.log('product_images.secondary:', response.product_images?.secondary);
            console.log('secondary length:', response.product_images?.secondary?.length);
            
            if (response.success) {
                // Update UI with new images (primary and secondary)
                if (response.product_images) {
                    // Import replaces DB images, so reset secondary rows before repainting
                    $('#uploaded-images').empty();
                    window.image_row = 0;

                    console.log('Calling updateImagesUI with imported images');
                    updateImagesUI(response.product_images);
                } else {
                    console.error('No product_images payload in response!');
                }
                alert(response.success);
            } else if (response.error) {
                alert(TEXT_ERROR_MANUFACTURER + ' : ' + response.error);
            }
        },
        error: function(xhr) {
            // Re-enable button
            $('#ebay-images-btn').prop('disabled', false).html('<i class="fa-brands fa-ebay"></i> eBay Images');
            console.error("Error importing eBay images: " + xhr.responseText);
            alert(TEXT_ERROR_IMPORTING_EBAY_IMAGES_PREFIX + xhr.responseText);
        }
    });
}

function removeProductImage(imagePath, productImageId, rowId) {
    
    var user_token = document.querySelector('input[name="user_token"]').value;
    
    $.ajax({
        url: 'index.php?route=shopmanager/tools.deleteProductImagePermanent&user_token=' + user_token,
        type: 'post',
        data: {
            image_path: imagePath,
            product_image_id: productImageId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#' + rowId).remove();
            } else if (response.error) {
                alert(TEXT_ERROR_MANUFACTURER + ' : ' + response.error);
            }
        },
        error: function(xhr) {
            console.error("Error deleting image: " + xhr.responseText);
            alert(TEXT_ERROR_DELETING_IMAGE);
        }
    });
}

function removeImage(image, type, elementSelector) {

    var product_id = document.querySelector('input[name="product_id"]').value;
    var user_token = document.querySelector('input[name="user_token"]').value;


    $.ajax({
        url: 'index.php?route=shopmanager/tools.deleteProductImage&user_token=' + user_token,
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
                alert(TEXT_ERROR_MANUFACTURER + ' : ' + response.error);
            }
        },
        error: function(xhr, status, error) {
            alert(TEXT_ERROR_MANUFACTURER + ' : ' + error);
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
    var user_token = document.querySelector('input[name="user_token"]').value;
    var countrySelect = document.getElementById('input-made-in-country-id');
    console.log('[COO][editMadeInCountry] start', { product_id: product_id, hasSelect: !!countrySelect });

        if (countrySelect) {
            var made_in_country_id = countrySelect.value;
            console.log('[COO][editMadeInCountry] selected', {
                made_in_country_id: made_in_country_id,
                selected_text: countrySelect.options[countrySelect.selectedIndex] ? countrySelect.options[countrySelect.selectedIndex].textContent.trim() : ''
            });
        } else {
            console.error("L'élément select avec id='input-made-in-country-id-" + product_id + "' n'existe pas.");
        }

    // var made_in_country_id = mySelect.val()
	//document.getElementByName("product[" + product_row +"][made_in_country_id]").value;
	//alert (item_id +"selected " + mySelect.val());
 	 $.ajax({
          url: 'index.php?route=shopmanager/catalog/product.editMadeInCountry&user_token=' + user_token,

		   method: "POST",
		  data: {product_id:product_id,
				made_in_country_id:made_in_country_id,
             //   marketplace_item_id:marketplace_item_id,
           //     quantity:quantity
           },
		dataType: 'json',
		crossDomain: true,
           success:function() {
             console.log('[COO][editMadeInCountry] ajax success', { product_id: product_id, made_in_country_id: made_in_country_id });

			 //alert(json['succes']);
             if(made_in_country_id > 0){
                document.getElementById('check-made-in-country-id').style.backgroundColor='green';
               }else{
                document.getElementById('check-made-in-country-id').style.backgroundColor='red';
               }

			 if (typeof syncCountryFields === 'function') {
                console.log('[COO][editMadeInCountry] calling syncCountryFields()');
				setTimeout(syncCountryFields, 100);
			}
            },
		error: function(xhr, ajaxOptions, thrownError) {
            console.error('[COO][editMadeInCountry] ajax error', {
                product_id: product_id,
                made_in_country_id: made_in_country_id,
                thrownError: thrownError,
                statusText: xhr.statusText,
                responseText: xhr.responseText
            });
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	  });
}


function endListing(product_id, marketplace_item_id, marketplace_account_id, marketplace_id) {
    if (!confirm('End this eBay listing? The item will be removed from eBay.')) return;
    var user_token = document.querySelector('input[name="user_token"]').value;
    var btn = $('#end-listing-btn-' + product_id + '-' + marketplace_account_id);
    btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
    $.ajax({
        url: 'index.php?route=shopmanager/ebay.endListing&user_token=' + user_token,
        type: 'POST',
        dataType: 'json',
        data: {
            product_id: product_id,
            marketplace_item_id: marketplace_item_id,
            marketplace_account_id: marketplace_account_id
        },
        success: function(json) {
            if (json.success) {
                // Replace the span with the "not listed" state
                var span = $('#marketplace-account-id-' + product_id + '-' + marketplace_account_id);
                var thumb = span.find('img').attr('src');
                var name  = span.find('img').attr('alt');
                span.html(
                    '<a href="javascript:void(0);" onclick="addToMarketplace(\'' + product_id + '\',\'' + marketplace_account_id + '\',\'' + marketplace_id + '\');">' +
                    '<img src="' + thumb + '" alt="' + name + '" style="width:25px;height:auto;filter:grayscale(100%);opacity:0.5;"></a>'
                );
            } else {
                btn.prop('disabled', false).html('<i class="fa-solid fa-ban"></i>');
                alert(json.message || 'Error ending listing');
            }
        },
        error: function(xhr) {
            // HTTP 200 but JSON parse failed = PHP had a warning/notice before the JSON
            // but the controller still cleared the DB item_id — treat as success
            if (xhr.status === 200) {
                var span = $('#marketplace-account-id-' + product_id + '-' + marketplace_account_id);
                var thumb = span.find('img').attr('src');
                var name  = span.find('img').attr('alt');
                span.html(
                    '<a href="javascript:void(0);" onclick="addToMarketplace(\'' + product_id + '\',\'' + marketplace_account_id + '\',\'' + marketplace_id + '\');">' +
                    '<img src="' + thumb + '" alt="' + name + '" style="width:25px;height:auto;filter:grayscale(100%);opacity:0.5;"></a>'
                );
            } else {
                btn.prop('disabled', false).html('<i class="fa-solid fa-ban"></i>');
                alert('Connection error: ' + xhr.status);
            }
        }
    });
}

function addToMarketplace(product_id,marketplace_account_id,marketplace_id, is_products = true) {
    var user_token = document.querySelector('input[name="user_token"]').value;
  

  
  //  var quantity = document.querySelector('input[name="quantity"]').value;
  //  var unallocated_quantity = document.querySelector('input[name="unallocated_quantity"]').value;
        $.ajax({
            url: `index.php?route=shopmanager/marketplace.addToMarketplace&user_token=${user_token}`,
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
            error: function(xhr) {
                alert(TEXT_ERROR_OCCURRED + ': ' + xhr.responseText);
            }
        });
    
}
function handleMarketplaceAddUIUpdate(json, marketplace_account_id, product_id, is_products = false) {

    // Construire dynamiquement les IDs des éléments cachés et du span
    var spanElement = document.getElementById(`marketplace-account-id-${product_id}-${marketplace_account_id}`);
    var urlProductInput = document.querySelector(`input[name="url_product_${product_id}_${marketplace_account_id}"]`);
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
                <input type="hidden" name="url_product_${product_id}_${marketplace_account_id}" value="${baseUrl}">
                <a href="javascript:void(0);" onclick="addToMarketplace('${product_id}', '${marketplace_account_id}', '9');">
                    <img src="${newImageUrl}" alt="${marketplaceName}" style="width:25px; height:auto; filter: grayscale(100%);">
                </a>
            `;
        }
    } else {
        console.warn(`Élément(s) non trouvé(s) pour marketplace-account-id-${product_id}_${marketplace_account_id}`);
    }

}






function openPrintLabel(sku = '', upc = '', quantity = 1, location = '', force = 'no') {
    const user_token = document.querySelector('input[name="user_token"]').value;
    const condition_id = document.querySelector('input[name="condition_id"]').value;


    // Si le SKU est identique au UPC, on ignore le UPC
    if (sku === upc) {
       //upc = '';
    }

    const url = 'index.php?route=shopmanager/tools.create_label' +
        '&sku=' + encodeURIComponent(sku) +
        '&upc=' + encodeURIComponent(upc) +
        '&quantity=' + encodeURIComponent(quantity) +
        '&location=' + encodeURIComponent(location) +
        '&user_token=' + encodeURIComponent(user_token);


    if ((condition_id && condition_id !== '1000') || upc == '' || force == 'yes') {
        window.open(url, 'printWindow', 'width=288,height=96');
    } else {
    }
}

// Helper: vérifie si un field name correspond à un champ "pays d'origine" (noms variés selon catégorie eBay)
const COUNTRY_SPECIFIC_KEYWORDS = ['Country/Region of Manufacture', 'Country of Origin', 'Country of Origin/Region of Manufacture'];
function isCountrySpecificsField(fieldName) {
    return COUNTRY_SPECIFIC_KEYWORDS.some(function(kw) { return fieldName.includes(kw); });
}

// Synchroniser le select made_in_country_id avec le champ Country/Region of Manufacture
function syncCountryFields() {
    const countrySelect = document.getElementById('input-made-in-country-id');
    
    // Chercher le champ specifics (select OU input) de manière robuste
    // Couvre les noms eBay variés selon la catégorie: "Country/Region of Manufacture", "Country of Origin", etc.
    let countrySpecificsField = null;
    const allSpecificFields = document.querySelectorAll('select, input[type="text"], textarea');
    for (let field of allSpecificFields) {
        if (field.name && field.name.includes('product_description[1][specifics]') && isCountrySpecificsField(field.name)) {
            countrySpecificsField = field;
            break;
        }
    }

    console.log('[COO][syncCountryFields] fields', {
        hasCountrySelect: !!countrySelect,
        hasSpecificsField: !!countrySpecificsField,
        specificsFieldName: countrySpecificsField ? countrySpecificsField.name : null,
        specificsFieldTag: countrySpecificsField ? countrySpecificsField.tagName : null
    });
    
    if (!countrySelect || !countrySpecificsField) {
        console.warn('[COO][syncCountryFields] abort: missing countrySelect or countrySpecificsField');
        return;
    }
    
    // Pour made_in_country_id: utiliser la valeur courante du select
    const selectedCountryId = countrySelect.value;
    const selectedCountryName = countrySelect.options[countrySelect.selectedIndex]
        ? countrySelect.options[countrySelect.selectedIndex].textContent.trim()
        : '';
    
    // Pour specifics: prendre la valeur sélectionnée
    const specificsValue = (countrySpecificsField.value || '').trim();

    console.log('[COO][syncCountryFields] values', {
        selectedCountryId: selectedCountryId,
        selectedCountryName: selectedCountryName,
        specificsValue: specificsValue
    });
    
    // Cas 1: Select made_in a une valeur ET specifics est vide → copier dans specifics ET traduire
    if (selectedCountryId && selectedCountryId !== '0' && !specificsValue) {
        console.log('[COO][syncCountryFields] case1: made_in set, specifics empty -> copy made_in to specifics');
        countrySpecificsField.value = selectedCountryName;
        // Déclencher l'événement change
        countrySpecificsField.dispatchEvent(new Event('change', { bubbles: true }));
        
        // Traduire pour les autres langues
        // Chercher tous les selects Country/Region pour les autres langues
        const allCountryFields = document.querySelectorAll('select, input[type="text"], textarea');
        for (let field of allCountryFields) {
            if (field.name && isCountrySpecificsField(field.name) && field !== countrySpecificsField) {
                field.value = selectedCountryName;
                field.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    }
    // Cas 2: Specifics a une valeur MAIS made_in select est vide → sélectionner le bon pays
    else if (specificsValue && (!selectedCountryId || selectedCountryId === '0')) {
        console.log('[COO][syncCountryFields] case2: specifics set, made_in empty -> resolve made_in from specifics');
        // Chercher l'option qui correspond au specifics value dans made_in
        const options = countrySelect.querySelectorAll('option');
        let found = false;
        for (let option of options) {
            const optionText = option.textContent.trim();
            if (optionText === specificsValue) {
                option.selected = true;
                countrySelect.value = option.value;
                found = true;
                // Déclencher le onchange de made_in_country_id
                const onchangeAttr = countrySelect.getAttribute('onchange');
                if (onchangeAttr) {
                    // Extraire le product_id du onchange="editMadeInCountry(xxxxx)"
                    const match = onchangeAttr.match(/editMadeInCountry\((\d+)\)/);
                    if (match && typeof editMadeInCountry === 'function') {
                        console.log('[COO][syncCountryFields] case2 -> triggering editMadeInCountry', { matchedProductId: match[1] });
                        editMadeInCountry(match[1]);
                    }
                }
                break;
            }
        }
        if (!found) {
            console.warn('[COO][syncCountryFields] case2: no matching made_in option found for specificsValue', { specificsValue: specificsValue });
        }
    }
    // Cas 3: Les deux ont des valeurs différentes → dialogue modal avec radio buttons
    else if (specificsValue && selectedCountryName && selectedCountryId && selectedCountryId !== '0' && selectedCountryName !== specificsValue) {
        console.log('[COO][syncCountryFields] case3: conflict detected -> loading modal', {
            selectedCountryName: selectedCountryName,
            specificsValue: specificsValue
        });
        // Récupérer le user_token pour l'authentification
        const user_token = document.querySelector('input[name="user_token"]')?.value;
        if (!user_token) {
            console.error('user_token not found');
            return;
        }
        
        // Charger le modal via AJAX avec le contrôleur
        const modalUrl = 'index.php?route=shopmanager/country_conflict_popup&user_token=' + user_token + '&made_in_country=' + encodeURIComponent(selectedCountryName) + '&specifics_country=' + encodeURIComponent(specificsValue);
        console.log('[COO][syncCountryFields] fetch modal url', modalUrl);
        fetch(modalUrl)
            .then(response => {
                console.log('[COO][syncCountryFields] fetch response', { ok: response.ok, status: response.status });
                if (!response.ok) {
                    throw new Error('Failed to load modal: ' + response.status);
                }
                return response.text();
            })
            .then(modalHtml => {
                console.log('[COO][syncCountryFields] modal html received', { length: modalHtml ? modalHtml.length : 0, hasModalId: !!(modalHtml && modalHtml.includes('countryConflictModal')) });
                // Vérifier si le HTML contient le modal
                if (!modalHtml.includes('countryConflictModal')) {
                    console.error('Modal HTML does not contain countryConflictModal div');
                    return;
                }
                
                // Supprimer l'ancien modal s'il existe
                const existingModal = document.getElementById('countryConflictModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                // Ajouter le modal au DOM
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                console.log('[COO][syncCountryFields] modal appended to DOM');
                
                const confirmButton = document.getElementById('confirmCountryChoice');
                
                if (!confirmButton) {
                    console.error('confirmCountryChoice button not found in modal');
                    return;
                }
                console.log('[COO][syncCountryFields] modal ready, waiting for confirm click');
                
                // Gérer le clic sur Confirm
                confirmButton.addEventListener('click', function() {
                        const selectedChoice = document.querySelector('input[name="countryChoice"]:checked')?.value;
                    console.log('[COO][syncCountryFields] modal confirm clicked', { selectedChoice: selectedChoice });
            
            if (selectedChoice === '1') {
                // Utiliser Made in Country → copier dans specifics et traduire toutes les langues
                countrySpecificsField.value = selectedCountryName;
                countrySpecificsField.dispatchEvent(new Event('change', { bubbles: true }));
                
                // Traduire pour les autres langues
                const allCountryFields = document.querySelectorAll('select, input[type="text"], textarea');
                for (let field of allCountryFields) {
                    if (field.name && isCountrySpecificsField(field.name) && field !== countrySpecificsField) {
                        field.value = selectedCountryName;
                        field.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            } else if (selectedChoice === '2') {
                // Utiliser Country/Region of Manufacture → changer made_in_country et traduire toutes les langues
                const options = countrySelect.querySelectorAll('option');
                let foundCountryId = null;
                for (let option of options) {
                    const optionText = option.textContent.trim();
                    if (optionText === specificsValue) {
                        option.selected = true;
                        countrySelect.value = option.value;
                        foundCountryId = option.value;
                        break;
                    }
                }
                
                if (foundCountryId) {
                    // Déclencher le onchange de made_in_country_id
                    const onchangeAttr = countrySelect.getAttribute('onchange');
                    if (onchangeAttr) {
                        const match = onchangeAttr.match(/editMadeInCountry\((\d+)\)/);
                        if (match && typeof editMadeInCountry === 'function') {
                            editMadeInCountry(match[1]);
                        }
                    }
                    
                    // Traduire pour les autres langues avec la valeur de specifics
                    const allCountryFields = document.querySelectorAll('select, input[type="text"], textarea');
                    for (let field of allCountryFields) {
                        if (field.name && isCountrySpecificsField(field.name) && field !== countrySpecificsField) {
                            field.value = specificsValue;
                            field.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                }
            }
            
            // Fermer et supprimer le modal
            const modal = document.getElementById('countryConflictModal');
            if (modal) {
                modal.remove();
            }
        });
            })
            .catch(error => {
                console.error('Error loading country conflict popup:', error);
            });
    }
    // Cas 4: Aucun des deux n'a de valeur → forcer choix utilisateur et alimenter les deux champs
    else if ((!selectedCountryId || selectedCountryId === '0') && !specificsValue) {
        console.log('[COO][syncCountryFields] case4: both empty -> opening picker modal');
        openCountryBothEmptyPicker(countrySelect, countrySpecificsField);

    }
}

function openCountryBothEmptyPicker(countrySelect, countrySpecificsField) {
    if (!countrySelect || !countrySpecificsField) {
        return;
    }

    if (document.getElementById('countryBothEmptyModal')) {
        return;
    }

    const optionsHtml = Array.from(countrySelect.options)
        .filter(function(opt) { return opt.value && opt.value !== '0'; })
        .map(function(opt) {
            return '<option value="' + opt.value + '">' + opt.textContent.trim() + '</option>';
        }).join('');

    const modalHtml =
        '<div id="countryBothEmptyModal" class="modal" tabindex="-1" style="display:block;background:rgba(0,0,0,0.5);">' +
        '  <div class="modal-dialog">' +
        '    <div class="modal-content">' +
        '      <div class="modal-header bg-warning">' +
        '        <h5 class="modal-title">Country Required</h5>' +
        '      </div>' +
        '      <div class="modal-body">' +
        '        <p>Both fields are empty. Please choose one country to apply to both fields.</p>' +
        '        <select id="countryBothEmptySelect" class="form-select">' +
        '          <option value="0">-----------------------------------</option>' +
                     optionsHtml +
        '        </select>' +
        '      </div>' +
        '      <div class="modal-footer">' +
        '        <button type="button" class="btn btn-primary" id="countryBothEmptyConfirm">Confirm</button>' +
        '      </div>' +
        '    </div>' +
        '  </div>' +
        '</div>';

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const confirmBtn = document.getElementById('countryBothEmptyConfirm');
    const pickerSelect = document.getElementById('countryBothEmptySelect');

    if (!confirmBtn || !pickerSelect) {
        return;
    }

    confirmBtn.addEventListener('click', function() {
        const selectedValue = pickerSelect.value;
        if (!selectedValue || selectedValue === '0') {
            alert('Please select a country.');
            return;
        }

        const selectedOption = pickerSelect.options[pickerSelect.selectedIndex];
        const selectedText = selectedOption ? selectedOption.textContent.trim() : '';

        countrySelect.value = selectedValue;
        countrySpecificsField.value = selectedText;
        countrySpecificsField.dispatchEvent(new Event('change', { bubbles: true }));

        const allCountryFields = document.querySelectorAll('select, input[type="text"], textarea');
        for (let field of allCountryFields) {
            if (field.name && isCountrySpecificsField(field.name) && field !== countrySpecificsField) {
                field.value = selectedText;
                field.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        const onchangeAttr = countrySelect.getAttribute('onchange');
        if (onchangeAttr) {
            const match = onchangeAttr.match(/editMadeInCountry\((\d+)\)/);
            if (match && typeof editMadeInCountry === 'function') {
                editMadeInCountry(match[1]);
            }
        }

        const modal = document.getElementById('countryBothEmptyModal');
        if (modal) {
            modal.remove();
        }
    });
}

// syncCountryFields est appelé directement depuis updateSpecificsTable après rendu des champs

// Function to manually trigger translation for all languages
function translateAllContent() {
    // Check if translation function exists
    if (typeof translateContentForAllLanguages !== 'function') {
        alert(TEXT_TRANSLATION_FUNCTION_NOT_AVAILABLE);
        return;
    }
    
    // Show loading indicator
    const translateBtn = event.target.closest('button');
    const originalHTML = translateBtn.innerHTML;
    translateBtn.disabled = true;
    translateBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Translating...';
    
    let translationCount = 0;
    let totalTranslations = 0;
    
    // Find all fields that need translation
    // 1. Product name/description fields with language tabs
    const languageTabs = document.querySelectorAll('[id^="language-"]');
    
    // 2. Specifics fields
    const specificsFields = document.querySelectorAll('[id^="product_description_1_"][id*="_specifics_"]');
    
    totalTranslations = specificsFields.length;
    
    if (totalTranslations === 0) {
        alert(TEXT_NO_FIELDS_FOUND_TRANSLATE);
        translateBtn.disabled = false;
        translateBtn.innerHTML = originalHTML;
        return;
    }
    
    // Translate each specific field
    specificsFields.forEach(function(field) {
        const fieldId = field.id;
        console.log('Translating field:', fieldId);
        
        // Call translation function
        try {
            translateContentForAllLanguages(fieldId, '', 'product');
            translationCount++;
        } catch(e) {
            console.error('Translation error for ' + fieldId + ':', e);
        }
    });
    
    // Wait a bit for translations to complete, then restore button
    setTimeout(function() {
        translateBtn.disabled = false;
        translateBtn.innerHTML = originalHTML;
        alert(TEXT_TRANSLATION_COMPLETED_FIELDS.replace('%s', translationCount));
    }, 2000);
}
