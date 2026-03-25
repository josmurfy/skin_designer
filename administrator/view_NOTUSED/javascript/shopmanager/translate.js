// Fonction principale pour effectuer la traduction
function getTranslate(text, languageId, targetLanguage, rowId, summernote, fieldName = 'product') {
    //console.log(`getTranslate called with: text=${text}, languageId=${languageId}, targetLanguage=${targetLanguage}, rowId=${rowId}, summernote=${summernote}, fieldName=${fieldName}`);
    var targetId = `${fieldName}_description_${languageId}_${rowId}`;
    var targetFieldElement = document.getElementById(targetId);
    //console.log(`Target ID: ${targetId}`);

    if (targetFieldElement) {
        if (text === '' || text == '<p><br></p>') {
            //console.log('Empty text detected, clearing target field...');
            targetFieldElement.value = '';
            if (summernote == 'summernote') {
                $(`#${targetId}`).summernote('code', '');
            }
        } else {
          
            var data = buildTranslationData(text, targetLanguage);
            console.log('getTranslate value:', data);
            //console.log('Translation data:', data);
            fetchTranslationData(data, targetFieldElement, targetId, summernote);
        }
    } else {
        console.error('Element not found for ID: ' + targetId);
    }
}

// Fonction pour construire les données de la requête de traduction
function buildTranslationData(text, targetLanguage) {
    //console.log(`buildTranslationData called with: text=${text}, targetLanguage=${targetLanguage}`);
    var containsHtml = /<\/?[a-z][\s\S]*>/i.test(text);
    var prompt = containsHtml ? text : text;
    //console.log(`Contains HTML: ${containsHtml}, Prompt: ${prompt}`);

    return {
        text_field: prompt,
        targetLanguage: targetLanguage
    };
}

function fetchTranslationData(data, targetFieldElement, targetId, summernote) {
    var token = document.querySelector('input[name="token"]').value;

    // Créez un objet FormData pour envoyer les données sous forme POST
    var formData = new FormData();
    formData.append('text_field', data.text_field);
    formData.append('targetLanguage', data.targetLanguage);

    fetch('index.php?route=shopmanager/translate&token=' + token, {
        method: 'POST',
        body: formData // FormData gère automatiquement les en-têtes
    })
    .then(response => response.json()) // Conversion en JSON
    .then(json => {
        if (json.error) {
            console.error('Translation error:', json.error);
            alert(json.error);
        } else {
            var translatedText = json.success;
            console.log('translatedText:', translatedText);
        
            // Décoder les entités HTML
            var decodedText = htmlDecode(translatedText);
            console.log('decodedText before fix:', decodedText);

            // Remplacer les guillemets français par des guillemets standards
            decodedText = decodedText.replace(/«|»/g, '"');

            // Corriger les accents en majuscule
           // Vérifier si le texte ressemble à un JSON mal formé
           if (decodedText.startsWith("[") && decodedText.endsWith("]") && decodedText.indexOf('"') === -1) {
                console.warn('Le JSON ne contient pas de guillemets, tentative de correction...');
                
                // Correction : Ajout de guillemets autour des valeurs
                decodedText = decodedText.replace(/\[|\]/g, '') // Supprime les crochets
                                        .split(', ') // Sépare les éléments
                                        .map(item => `"${item.trim()}"`) // Ajoute des guillemets autour
                                        .join(', '); // Recrée la liste
                decodedText = `[${decodedText}]`; // Réajoute les crochets
            }
            console.log('decodedText after fix:', decodedText);

            if (targetFieldElement.tagName.toLowerCase() === 'select') {
                if (targetFieldElement.multiple) {
                    try {
                        let options = JSON.parse(decodedText); // Decode JSON containing multiple values

                        if (Array.isArray(options)) {
                            $(targetFieldElement).val([]); // Deselect all before adding

                            options.forEach(option => {
                                let existingOption = $(targetFieldElement).find(`option[value="${option}"]`);
                            
                                if (existingOption.length === 0) {
                                    let newOption = new Option(option, option);
                                    newOption.textContent = option; // **Forcer l'affichage correct**
                                    $(targetFieldElement).append(newOption);
                                }
                            
                                // Select the option
                                $(targetFieldElement).find(`option[value="${option}"]`).prop('selected', true);
                            });

                            $(targetFieldElement).trigger('change'); // Refresh the select (useful for Select2)
                        }
                    } catch (error) {
                        console.error('Error decoding JSON for select:', error);
                    }
                } else {
                    // For single select, just set the value directly
                    let existingOption = $(targetFieldElement).find(`option[value="${decodedText}"]`);
                    
                    if (existingOption.length === 0) {
                        let newOption = new Option(decodedText, decodedText);
                        newOption.textContent = decodedText; // **Forcer l'affichage correct**
                        $(targetFieldElement).append(newOption);
                    }
                    
                    // Select the option
                    $(targetFieldElement).val(decodedText).trigger('change');
                }
            } else {
                targetFieldElement.value = decodedText;
                if (summernote === 'summernote') {
                    $(`#${targetId}`).summernote('code', decodedText);
                }
            }
        }
    })
    .catch(error => {
        console.error('An error occurred:', error);
        alert('An error occurred: ' + error);
    });
}



// Fonction pour traduire tout le contenu pour toutes les langues
function translateContentForAllLanguagesBUG(elementId, summernote = '', fieldName = 'product') {
    //console.log(`translateContentForAllLanguages called with elementId=${elementId}, summernote=${summernote}, fieldName=${fieldName}`);
    var languages = JSON.parse($('#languages_json').val());
    var ids = extractLanguageAndRowId(elementId);
    //console.log('Languages:', languages);
    //console.log('Extracted IDs:', ids);

    if (ids && ids.languageId) {
        delete languages[ids.languageId]; // Supprimer la langue source des langues à traduire
        //console.log('Remaining languages after source removal:', languages);

        for (var targetLanguageId in languages) {
            var targetLanguage = languages[targetLanguageId];
            var rowId = ids.rowId;
            //console.log(`Translating for targetLanguageId=${targetLanguageId}, targetLanguage=${targetLanguage}, rowId=${rowId}`);
            if (summernote === 'summernote') {
                var value = $(`#${elementId}`).summernote('code');
                //console.log('Summernote value:', value);
                getTranslate(value, targetLanguageId, targetLanguage, rowId, 'summernote', fieldName);
            } else {
                var element = $(`#${elementId}`);
                //console.log('Element:', element);
                if (element.is('input')) {
                    var value = element.val();
                    //console.log('Input value:', value);
                    getTranslate(value, targetLanguageId, targetLanguage, rowId, '', fieldName);
                } else if (element.is('select')) {
                    if (element.prop('multiple')) {
                        let selectedValues = [];
                    
                        // Récupérer toutes les valeurs sélectionnées
                        element.find('option:selected').each(function() {
                            selectedValues.push($(this).text().trim());
                        });
                    
                        if (selectedValues.length > 0) {
                            console.log('Multiple select values:', selectedValues);
                    
                            // Convertir en JSON et envoyer à getTranslate()
                            getTranslate(JSON.stringify(selectedValues), targetLanguageId, targetLanguage, rowId, '', fieldName)
                                                 
                        }
                    } else {
                        var value = element.find('option:selected').text();
                        //console.log('Select value:', value);
                        getTranslate(value, targetLanguageId, targetLanguage, rowId, '', fieldName);
                    }
                } else if (element.is('textarea')) {
                    var value = element.val();
                    //console.log('Textarea value:', value);
                    getTranslate(value, targetLanguageId, targetLanguage, rowId, '', fieldName);
                } else {
                    console.error('Unsupported element type for translation.');
                }
            }
        }
    } else {
        console.error('Could not determine source language ID from elementId:', elementId);
    }
}

// Fonction pour extraire les IDs de langue et de ligne
function extractLanguageAndRowId(elementId) {
   
    var idMatch = elementId.match(/_description_(\d+)_(\d+)/);
    if (idMatch) {
        
        return {
            languageId: idMatch[1],
            rowId: idMatch[2]
        };
    }
    console.log('Extracted IDs:', idMatch);
    console.log(`extractLanguageAndRowId called with elementId=${elementId}`);
    console.error('Could not extract IDs from elementId:', elementId);
    return null;
}

function translateAllFields(formName, allFields = true) {
    console.log(`translateAllFields called with formName=${formName}`);

    var languages = JSON.parse($('#languages_json').val());
    var sourceLanguageId = '1'; // Supposons que '1' est l'ID pour l'anglais

    // Retirer l'anglais des langues à traduire
    delete languages[sourceLanguageId];

    var toTranslate = $('#to_translate').val();
    var translationFields = {};

    // Liste des champs à copier directement sans traduction
    const copyDirectlyFields = [
        "Brand", "Exclusive Event/Retailer", "Franchise", "TV/Streaming Show", 
        "Movie", "Professional Grader", "Model Grader", "Collection", "Product Line",
        "Series", "Animation Studio", "Autographed By", "Designer", 
        "Autograph Authentication"
    ];

    if (toTranslate && toTranslate.trim() !== "" && allFields === false) {
        try {
            translationFields = JSON.parse(toTranslate);
        } catch (error) {
            console.error('Error parsing to_translate JSON:', error);
            return;
        }
    } else if (allFields) {
        console.log('Scanning all fields with _description_ for source language.');

        $(`#${formName}`).find(`[id*="_description_${sourceLanguageId}"]`).each(function() {
            let element = $(this);
            let elementName = element.attr('name');
            let elementID = element.attr('id');

            const excludedKeywords = ['[description]', '[meta_title]', '[meta_description]', '[tag]', '[meta_keyword]', 'response_', 'display_','_specifics_Name'];

            if (elementName && excludedKeywords.some(keyword => elementName.includes(keyword))) {
                console.log(`Skipping element: ${elementID || 'no ID'} (excluded due to name filter)`);
                return;
            }

            if (elementID && excludedKeywords.some(keyword => elementID.includes(keyword))) {
                console.log(`Skipping element: ${elementID} (excluded due to ID filter)`);
                return;
            }

            console.log(`Adding element: ${element.attr('id')}`);
            translationFields[element.attr('id')] = true;
        });
    }

    console.log("Translation Fields:", translationFields);
    console.log("Translation Fields Keys:", Object.keys(translationFields));

    // Parcourir les champs à traduire
    Object.keys(translationFields).forEach(function(elementId) {
        var element = $(`#${elementId}`);
        if (element.length === 0) {
            console.log(`Element not found: ${elementId}`);
            return;
        }

        console.log(`Processing elementId=${elementId}`);

        var ids = extractLanguageAndRowId(elementId);
        if (!ids || ids.languageId !== sourceLanguageId) {
            console.log(`Skipping element with non-numeric language ID or unsupported element type: ${elementId}`);
            return;
        }

        let value = "";
        let elementType = "";

        if (element.hasClass('summernote')) {
            value = element.summernote('code').trim();
            elementType = "summernote";
        } else if (element.is('input') || element.is('textarea')) {
            value = element.val().trim();
            elementType = "text";
        } else if (element.is('select')) {
            if (element.prop('multiple')) {
                let selectedValues = [];
                element.find('option:selected').each(function() {
                    selectedValues.push($(this).text().trim());
                });
                value = selectedValues.length > 0 ? JSON.stringify(selectedValues) : "";
                elementType = "multiple_select";
            } else {
                value = element.find('option:selected').text().trim();
                elementType = "select";
            }
        }

        // Sécurisation de `value` pour éviter les erreurs
        value = (typeof value === "string") ? value.trim() : '';

        // Vérifier si la valeur est **numérique** ou si le champ fait partie de la liste à copier
        let isEmpty = typeof value === "string" && value === '' && value !== null;
        let isNumeric = typeof value === "string" && !isNaN(value) && value.trim() !== '';

        let elementName = element.attr('name'); // Vérifie le `name`
        let fieldLabel = element.closest('.form-group').find('label').text().trim(); // Vérifie le label
        let isCopyDirectly = copyDirectlyFields.some(keyword => 
            (elementName && elementName.includes(keyword)) || 
            (fieldLabel && fieldLabel.includes(keyword))
        );

        for (var targetLanguageId in languages) {
            var targetField = $(`#product_description_${targetLanguageId}_${ids.rowId}`);
            
            if (value === '' || value === '<p><br></p>') {
                console.log(`Setting empty value for targetLanguageId=${targetLanguageId}, rowId=${ids.rowId}`);
                if (targetField.hasClass('summernote')) {
                    targetField.summernote('code', '');
                } else {
                    targetField.val('');
                }
            } else if (isNumeric || isCopyDirectly || isEmpty) {
                console.log(`Copying value "${value}" for targetLanguageId=${targetLanguageId}, rowId=${ids.rowId}`);
                targetField.val(value);
            } else {
                console.log(`Translating value for targetLanguageId=${targetLanguageId}, rowId=${ids.rowId}`);
                getTranslate(value, targetLanguageId, languages[targetLanguageId], ids.rowId);
            }
        }
    });
}



// Function to handle translation and modal display
function handleTranslationAndModal(formName, allFields = true) {
    return new Promise((resolve, reject) => {
    console.log(`handleTranslationAndModal called with formName=${formName}`);
    var form = document.getElementById(formName);

    if (!form) {
        console.error('Form element not found');
        return;
    }

    // Call the translation function
    translateAllFields(formName, allFields);

    // Display the custom wait popup modal
    $('#loadingModal').modal('show');

    // Hide the modal after translations are done
    setTimeout(function() {
        $('#loadingModal').modal('hide');
    }, 100); // Adjust the timeout as needed
   
        // Your existing handleTranslationAndModal code here

        // Simulate translation process with a timeout (replace this with your actual translation logic)
        setTimeout(() => {
            // Call resolve() when the function completes
            resolve();
        }, 100); // Adjust the timeout duration as needed
    });
}

// Function to decode HTML entities
function htmlDecodeOLD(input) {
    var e = document.createElement('div');
    e.innerHTML = input;
    return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
}

function htmlDecode(input) {
    let doc = new DOMParser().parseFromString(input, "text/html");
    return doc.documentElement.textContent;
}
