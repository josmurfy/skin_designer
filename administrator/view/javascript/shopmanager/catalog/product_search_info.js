// ============================================
// FUNCTIONS DUPLICATED FROM TOOLS.JS (PRODUCTION SAFETY)
// ============================================

function initImageDragAndDrop(uploadUrl, onSuccess, onError) {
    document.querySelectorAll('.actual-image-container').forEach(function (container) {
        if (container.dataset.dragDropInitialized === 'true') return;
        container.dataset.dragDropInitialized = 'true';

        const fileInput = container.querySelector('input[type="file"]');
        const previewImage = container.querySelector('img.img-thumbnail, .thumbnail-actual-image');
        const fullImage = container.querySelector('.fullsize-actual-image, .actual-image-preview');
        const productId = container.id.replace('drop-', '');
        
        const tokenInput = document.querySelector('input[name="user_token"]');
        if (!tokenInput) {
            console.error('[initImageDragAndDrop] user_token not found');
            return;
        }
        const user_token = tokenInput.value;

        container.addEventListener('dragover', function (event) {
            event.preventDefault();
            event.stopPropagation();
            container.style.borderColor = '#007bff';
        });

        container.addEventListener('dragleave', function () {
            container.style.borderColor = '#ccc';
        });

        container.addEventListener('drop', function (event) {
            event.preventDefault();
            event.stopPropagation();
            container.style.borderColor = '#ccc';

            if (event.dataTransfer.files.length > 0) {
                const file = event.dataTransfer.files[0];

                if (fileInput) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    if (previewImage) previewImage.src = e.target.result;
                    if (fullImage) fullImage.src = e.target.result;
                };
                reader.readAsDataURL(file);

                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('sourcecode', '');
                formData.append('imageprincipal', file);

                $.ajax({
                    url: uploadUrl + '&user_token=' + user_token,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success && onSuccess) {
                            onSuccess(response, container, productId);
                        } else if (response.error) {
                            alert((window.lang && lang.error_occurred ? lang.error_occurred : (typeof TEXT_ERROR !== 'undefined' ? TEXT_ERROR : 'Error')) + ' : ' + response.error);
                        }
                    },
                    error: function (xhr) {
                        console.error('[AJAX Error]', xhr.responseText);
                        if (onError) {
                            onError(xhr, container, productId);
                        } else {
                            alert((window.lang && lang.error_upload ? lang.error_upload : (typeof TEXT_UPLOAD_ERROR !== 'undefined' ? TEXT_UPLOAD_ERROR : 'Upload error')) + ' : ' + xhr.responseText);
                        }
                    }
                });
            }
        });

        if (fileInput) {
            container.addEventListener('click', function(e) {
                if (e.target === container || e.target.tagName === 'I' || e.target.classList.contains('fa-camera')) {
                    fileInput.click();
                }
            });

            fileInput.addEventListener('change', function () {
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    container.dispatchEvent(new DragEvent('drop', {
                        dataTransfer: new DataTransfer()
                    }));
                }
            });
        }
    });

    window.addEventListener('dragover', function(e) {
        e.preventDefault();
    }, false);
    
    window.addEventListener('drop', function(e) {
        if (!e.target.closest('.actual-image-container')) {
            e.preventDefault();
        }
    }, false);
}

// ============================================
// END DUPLICATED FUNCTIONS FROM TOOLS.JS
// ============================================

var specifics_row = 0; // Initialisation du compteur

function addSpecifics() {
    // Récupérer le tableau avec l'ID "Specific_table" dans lequel ajouter la nouvelle ligne
    var tableBody = document.querySelector('#Specific-table tbody'); // Cibler le tbody du tableau par son ID

    // Créer la nouvelle ligne <tr>
    var newRow = document.createElement('tr');
    newRow.className = 'strong-warning'; // Ajouter la classe "strong-warning"

    // Créer le contenu de la nouvelle ligne
    var html = `
        <td class="text-left">
            <input type="checkbox" checked class="save-data-checkbox" onclick="SelectRow(this)" name="specifics_checkbox[NEW][]" value="">
        </td>
        <td><input type="text" id="NEW" name="NEW" placeholder="" class="form-control" onchange="updateSpecifics('NEW')" /></td>
        <td>
            <select class="form-control" name="specifics_select[NEW][]">
                <!-- Options will be added by updateSelectOptions() -->
            </select>
        </td>
        <td>
            <input type="text" name="specifics_text[NEW][]" value="" placeholder="Specific Value" class="form-control">
        </td>
    `;

    // Injecter le HTML dans la nouvelle ligne
    newRow.innerHTML = html;

    // Ajouter la nouvelle ligne au tableau
    tableBody.appendChild(newRow);

    // Incrémenter le compteur de lignes
    specifics_row++;

    // Appeler la fonction pour mettre à jour les options du <select>
    updateSelectOptions();
}



function toggleSelectRows(checkbox) {
    // Vérifier si la case globale est cochée
    const isChecked = checkbox.checked;

    // Sélectionner uniquement les lignes avec 'strong-success' ou 'strong-warning'
    const rows = document.querySelectorAll('tr.strong-success, tr.strong-warning');

    // Parcourir ces lignes et cocher/décocher les cases à cocher dans ces lignes
    rows.forEach(function (row) {
        const rowCheckbox = row.querySelector('input[type="checkbox"].save-data-checkbox');
        if (rowCheckbox) {
            rowCheckbox.checked = isChecked;
            
        }
   /*     if(isChecked) {
            row.classList.remove('strong-warning', 'strong-danger');
            row.classList.add('strong-success');
        }else{
            row.classList.remove('strong-success');
            row.classList.add('strong-danger');
        }*/
    });
}

function formatTextToUppercase(text) {
    return text.split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ');
}
function SelectRow(checkbox) {
    const currentRow = checkbox.closest('tr');  // Trouver la ligne parente
    const selectElement = currentRow.querySelector('select.form-control');  // Sélectionner l'élément <select> dans la ligne
    const selectedValue = selectElement ? selectElement.value : '';  // La valeur sélectionnée du <select>
    const category_id = document.querySelector('input[name="category_id"]').value;  // Récupérer la valeur du category_id
    var specific_key =currentRow.querySelector('td:nth-child(2)').textContent.trim();

    // Si la case à cocher est cochée et que le select est vide
    if (checkbox.checked && selectedValue === '') {
        // Créer un nouvel input text pour remplacer le select
        const selectName = selectElement.name;
        const newInput = document.createElement('input');
        newInput.type = 'text';
        newInput.name = selectName;
        newInput.placeholder = formatTextToUppercase(specific_key);
        newInput.value = formatTextToUppercase(specific_key);
            editSpecificKey(specific_key, category_id, formatTextToUppercase(specific_key));
        newInput.className = 'form-control';

        // Remplacer le select par le nouvel input text
        selectElement.replaceWith(newInput);

        // Appliquer la classe "success" pour mettre en vert
        currentRow.classList.remove('strong-warning', 'strong-danger');
        currentRow.classList.add('strong-success');

        // Ajouter un écouteur d'événement pour le champ texte afin d'appeler `editSpecificKey` lorsqu'il est modifié
        newInput.addEventListener('input', function () {
            const newValue = newInput.value.trim();
            // Appeler `editSpecificKey` avec la nouvelle valeur
            editSpecificKey(specific_key, category_id, newValue);
        });

        newInput.addEventListener('change', function () {
            const newValue = newInput.value.trim();
            checkbox.name = "specifics_checkbox[" + newValue + "][]";
            checkbox.checked = true;
            select.name = "specifics_select[" + newValue + "][]";
            // Appeler `editSpecificKey` avec la nouvelle valeur
            editSpecificKey(specific_key, category_id, newValue);
        });
    } else if (!checkbox.checked) {
        // Si la case à cocher est décochée, réinitialiser l'état de la ligne
        currentRow.classList.remove('strong-success');
        currentRow.classList.add('strong-danger');
    }
}
    // Fonction pour appeler `editSpecificKey` via AJAX avec GET
    function editSpecificKey(specificKey, category_id, replacementTerm) {
        var user_token = document.querySelector('input[name="user_token"]').value;
        var url = 'index.php?route=shopmanager/catalog/product_specific.editSpecificKey&user_token=' + user_token +
            '&specific_key=' + encodeURIComponent(specificKey) +
            '&category_id=' + encodeURIComponent(category_id) +
            '&replacement_term=' + encodeURIComponent(replacementTerm);

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .catch(error => console.error('Error:', error));
    }

    // Fonction pour appeler `deleteSpecificKey` via AJAX avec GET
    function deleteSpecificKey(specificKey, category_id) {
        var user_token = document.querySelector('input[name="user_token"]').value;
        var url = 'index.php?route=shopmanager/catalog/product_specific.deleteSpecificKey&user_token=' + user_token +
            '&specific_key=' + encodeURIComponent(specificKey) +
            '&category_id=' + encodeURIComponent(category_id);

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .catch(error => console.error('Error:', error));
    }

    // Fonction pour appeler `addSpecificKey` via AJAX avec GET
    function addSpecificKey(specificKey, category_id, replacementTerm) {
        var user_token = document.querySelector('input[name="user_token"]').value;
        var url = 'index.php?route=shopmanager/catalog/product_specific.addSpecificKey&user_token=' + user_token +
            '&specific_key=' + encodeURIComponent(specificKey) +
            '&category_id=' + encodeURIComponent(category_id) +
            '&replacement_term=' + encodeURIComponent(replacementTerm);

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .catch(error => console.error('Error:', error));
    }

    // Fonction pour vérifier si une clé spécifique existe déjà via AJAX avec GET
    function checkIfSpecificKeyExists(specificKey, category_id) {
        var user_token = document.querySelector('input[name="user_token"]').value;
        var url = 'index.php?route=shopmanager/catalog/product_specific.getSpecificKey&user_token=' + user_token +
            '&specific_key=' + encodeURIComponent(specificKey) +
            '&category_id=' + encodeURIComponent(category_id);

        return fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => data.exists)
        .catch(error => {
            console.error('Error:', error);
            return false;
        });
    }
document.addEventListener("DOMContentLoaded", function () {
    // Sélectionner tous les éléments <select> dans le tableau
    var category_id = document.querySelector('input[name="category_id"]').value;
    var selects = document.querySelectorAll("select.form-control");
    const categorySpecificNamesInput = document.querySelector('input[name="category_specific_names_json"]');

    // Vérifier si l'élément existe et contient des données
    if (categorySpecificNamesInput && categorySpecificNamesInput.value) {
        var categorySpecificNames = JSON.parse(categorySpecificNamesInput.value);
    } else {
        console.error('categorySpecificNames is not defined. Ensure the hidden input contains the necessary data.');
        return; // Arrêter si les données sont manquantes
    }

    // Ajouter un écouteur d'événement sur chaque select
    selects.forEach(function (selectElement) {
        selectElement.addEventListener("change", function (event) {
            const selectedValue = event.target.value; // La nouvelle valeur sélectionnée
            let selectNameArray = event.target.name.split("specifics_select[")[1]; // Extraire le nom de la clé
            let selectName = selectNameArray.split("][]")[0]; // Extraire le nom de la clé
            const currentRow = event.target.closest('tr');
            
            // Obtenir la valeur dans la même ligne à partir de la 2ème cellule <td>
            const currentRowvalue = currentRow.querySelector('td:nth-child(2)');

            // Si la valeur est vide, on supprime la clé spécifique
            if (selectedValue === '') {
                editSpecificKey(currentRowvalue.textContent.trim(), category_id, '');
            } else {
                // Vérifier si la clé existe déjà via une requête ou effectuer une modification
                checkIfSpecificKeyExists(currentRowvalue.textContent.trim(), category_id, selectedValue)
                    .then((specificExists) => {
                        if (specificExists) {
                            editSpecificKey(currentRowvalue.textContent.trim(), category_id, selectedValue);
                        } else {
                            addSpecificKey(currentRowvalue.textContent.trim(), category_id, selectedValue);
                        }
                    });
            }

            // Mise à jour des noms des cases à cocher et des selects
            const rows = document.querySelectorAll(`tr:has(select[name="specifics_select[${selectName}][]"])`);

            rows.forEach(function (row) {
                const oldvalue = row.querySelector('td:nth-child(2)');
                if (currentRowvalue.textContent.trim() === oldvalue.textContent.trim()) {
                    const select = row.querySelector(`select[name^="specifics_select[${selectName}][]"]`);
                    const checkbox = row.querySelector(`input[name^="specifics_checkbox[${selectName}]"]`);

                    if (checkbox && select) {
                        if (selectedValue === '') {
                            checkbox.name = "specifics_checkbox[" + oldvalue.textContent.trim() + "][]";
                            select.name = "specifics_select[" + oldvalue.textContent.trim() + "][]";
                        } else {
                            checkbox.name = "specifics_checkbox[" + selectedValue + "][]";
                            checkbox.checked = true;
                            select.name = "specifics_select[" + selectedValue + "][]";
                        }

                        // Appliquer les classes CSS en fonction de la sélection
                        if (selectedValue) {
                            select.value = selectedValue;
                            row.classList.remove('strong-warning', 'strong-danger');
                            row.classList.add('strong-success');
                            checkbox.checked = true;
                        } else {
                            select.value = '';
                            row.classList.remove('strong-warning', 'strong-success');
                            row.classList.add('strong-danger');
                            checkbox.checked = false;
                        }
                    }
                }
            });
        });
    });



    // Mise à jour des lignes et des statuts
    function updateRowStatus() {
        const rows = document.querySelectorAll('tr');

        rows.forEach(function (row) {
            const checkbox = row.querySelector('input.save-data-checkbox');
            const textInput = row.querySelector('input.form-control');

            if (checkbox && textInput) {
                if (checkbox.checked && textInput.value.trim() !== '') {
                    row.classList.remove('strong-warning', 'strong-danger');
                    row.classList.add('strong-success');
                    checkbox.checked = true;
                }
            }
        });
    }

    // Fonction pour mettre à jour les cases à cocher selon le champ texte
    function updateCheckboxOnTextChange() {
        const rows = document.querySelectorAll('tr');

        rows.forEach(function (row) {
            const checkbox = row.querySelector('input.save-data-checkbox');
            const textInput = row.querySelector('input.form-control');

            if (checkbox && textInput) {
                textInput.addEventListener('input', function () {
                    const newValue = textInput.value.trim();

                    if (newValue !== '') {
                        checkbox.value = newValue;
                        checkbox.checked = true;

                        row.classList.remove('strong-warning', 'strong-danger');
                        row.classList.add('strong-success');
                    } else {
                        row.classList.remove('strong-success', 'strong-danger');
                        row.classList.add('strong-warning');
                    }
                });
            }
        });
    }
   
    // Mise à jour des options des selects au changement
    function updateSelectOptions() {
        const selectedValues = [];

        document.querySelectorAll('select.form-control').forEach(function (select) {
            if (select.value !== '') {
                selectedValues.push(select.value);
            }
        });

        const uniqueSelectedValues = [...new Set(selectedValues)];

        document.querySelectorAll('select.form-control').forEach(function (selectElement) {
            const currentValue = selectElement.value;
            const availableOptions = categorySpecificNames.filter(function (option) {
                return !uniqueSelectedValues.includes(option) || option === currentValue;
            });

            selectElement.innerHTML = '';

            const emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.textContent = '';
            selectElement.appendChild(emptyOption);

            availableOptions.forEach(function (option) {
                const optionElement = document.createElement('option');
                optionElement.value = option;
                optionElement.textContent = option;

                if (option === currentValue) {
                    optionElement.selected = true;
                }
                selectElement.appendChild(optionElement);
            });
        });
    }

    // Appel de la mise à jour initiale
    updateRowStatus();
    updateCheckboxOnTextChange();
 //   updateSelectOptions();

    // Gestion du changement des selects
    document.querySelectorAll('select.form-control').forEach(function (selectElement) {
        selectElement.addEventListener('change', function () {
       //     updateSelectOptions();
        });
    });
});


//transferer 

function checkFormStatus() {
    var categoryChecked = document.querySelector('input[name^="product_category"]:checked');
    var conditionChecked = document.querySelector('input[name="condition_id"]:checked');
    var priceChecked = document.querySelector('input[name^="price_ebay"]:checked');
    var product_id = document.querySelector('input[name="product_id"]').value;

    var saveButton = document.getElementById('saveButton');
    
    if ((categoryChecked && conditionChecked && priceChecked )|| product_id) {
      saveButton.disabled = false;
    } else {
      saveButton.disabled = true;
    }
  }
   // Fonction qui met à jour le bouton radio save-data-radio basé sur la sélection de condition
    function updateCondition(conditionMarketplaceItemId) {
        // Désélectionner tous les boutons radio save-data-radio
        const radios = document.querySelectorAll('.save-data-radio');
        
        radios.forEach(function(radio) {
            radio.checked = false; // Décocher tous les boutons
        });
    
        // Sélectionner le bouton radio correspondant à conditionMarketplaceItemId
        const targetRadio = document.querySelector(`#condition_marketplace_item_id${conditionMarketplaceItemId}`);
        
        if (targetRadio) {
          
            targetRadio.checked = true; // Cocher le bouton radio correspondant
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

// Exécuter la fonction lors du chargement de la page
document.addEventListener('DOMContentLoaded', $('#loadingModal').modal('show'));
/*
document.addEventListener('DOMContentLoaded', function() {
    checkFormStatus();

    const thumbnails = document.querySelectorAll('.thumbnail-image');
    const table = document.getElementById('table-image');
    const tableBody = table.querySelector('tbody');
    const rowsData = [];

    function removeUnnecessaryTitles() {
        thumbnails.forEach(thumbnail => {
            const imageCell = thumbnail.closest('.image-cell');
            const checkbox = imageCell ? imageCell.closest('tr').querySelector('.save-data-checkbox') : null;
            const titleCheckbox = imageCell.closest('tr').querySelector('input[name="titles[]"]');
            const name = titleCheckbox ? titleCheckbox.value.toLowerCase() : '';

            if (!checkbox || !imageCell || !titleCheckbox) {
                console.warn('Les éléments imageCell, checkbox ou titleCheckbox sont introuvables pour cette image.');
                return;
            }

            if (name.includes('only') || name.includes('scratch')) {
                titleCheckbox.remove();
                checkbox.checked = false;  // Retirer l'attribut 'checked' de la checkbox
            }
        });
    }

    // Appel initial pour vérifier les titres
    removeUnnecessaryTitles();
});*/
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('table-image');
    const tableBody = document.getElementById('tbody-image');  // Cibler l'élément avec l'ID tbody_images
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

/*
document.addEventListener("DOMContentLoaded", function () {
    // Sélectionner tous les champs de texte dans le formulaire
    var textFields = document.querySelectorAll('form input[type="text"]');

    textFields.forEach(function (field) {
        // Exclure les champs avec des valeurs numériques et les champs ayant "category" dans leur nom
        if (!isNaN(field.value) || field.name.toLowerCase().includes('category') || field.name.toLowerCase().includes('location')) {
            return; // Passer au prochain champ s'il est numérique ou contient "category"
        }

        // Créer un conteneur div pour le champ et le bouton
        var containerDiv = document.createElement('div');
   //     containerDiv.className = 'input-group-custom'; // Ajout de classe Bootstrap pour aligner le champ et le bouton

        // Insérer le conteneur juste avant le champ actuel
        field.parentNode.insertBefore(containerDiv, field);

        // Déplacer le champ dans le conteneur
        containerDiv.appendChild(field);

        // Créer un bouton de mise en majuscule
        var capitalizeButton = document.createElement('button');
        capitalizeButton.type = 'button';
        capitalizeButton.className = 'btn btn-warning btn-sm'; // Ajout de classes Bootstrap pour le style
        capitalizeButton.title = 'UpperCase';
        capitalizeButton.innerHTML = '<i class="fa fa-text-height"></i>'; // Utilisation de FontAwesome

        // Ajouter l'événement pour mettre en majuscule la première lettre de chaque mot
        capitalizeButton.addEventListener('click', function () {
            // Récupérer la valeur du champ de texte
            var text = field.value;
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

            // Mettre à jour le champ de texte avec le texte formaté
            field.value = capitalizedText;
        });

        // Insérer le bouton dans le conteneur à gauche du champ de texte
        containerDiv.insertBefore(capitalizeButton, field);
    });
});*/


  
// Gardez lautocomplétion existante
$('input[name="category"]').autocomplete({
   

    'source': function(request, response) {
        var user_token = document.querySelector('input[name="user_token"]').value;
        $.ajax({
            url: 'index.php?route=shopmanager/catalog/category/autocomplete&user_token=' + user_token + '&filter_name=' +  encodeURIComponent(request),
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


// Initialize drag & drop for image upload
document.addEventListener('DOMContentLoaded', function() {
    if (typeof initImageDragAndDrop === 'function') {
        initImageDragAndDrop(
            'index.php?route=shopmanager/tools.uploadImagesFiles&type=pri',
            function(response, container, productId) {
                // Success callback - reload page or update UI
                if (response.success) {
                    // Optionally reload images or update UI
                    location.reload();
                }
            },
            function(xhr, container, productId) {
                // Error callback
                console.error('[Drag&Drop] Upload error for product', productId, xhr.responseText);
            }
        );
    }
});
