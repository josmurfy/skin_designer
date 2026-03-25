document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-product');
    form.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            return false;
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('input-search');
    if (searchInput) {
        searchInput.addEventListener('input', toggleButtons);
        searchInput.addEventListener('change', toggleButtons);
    }

    const modelInput = document.getElementById('input-model');
    if (modelInput) {
        modelInput.addEventListener('input', toggleButtons);
        modelInput.addEventListener('change', toggleButtons);
    }
});

function toggleButtons() {
    const searchInput = document.getElementById('input-search');
    const modelInput = document.getElementById('input-model');
    const ButtonSearchByUPC = document.getElementById('search-by-upc');
    const ButtonSearchByName = document.getElementById('search-by-name');
    const ButtonSearchByModel = document.getElementById('search-by-model');

    const searchValue = searchInput.value.trim();
    const modelValue = modelInput.value.trim();

    // Gérer les boutons pour le champ de recherche
    if (searchValue === '') {
        // Si le champ est vide, masquer les boutons de recherche par UPC et par nom
        ButtonSearchByUPC.style.display = 'none';
        ButtonSearchByName.style.display = 'none';
    } else if (!isNaN(searchValue)) {
        // Si la valeur est numérique, afficher les deux boutons (UPC et nom)
        ButtonSearchByUPC.style.display = 'inline-block';
        ButtonSearchByName.style.display = 'inline-block';
    } else {
        // Si la valeur est non numérique, afficher uniquement le bouton de recherche par nom
        ButtonSearchByUPC.style.display = 'none';
        ButtonSearchByName.style.display = 'inline-block';
    }

    // Gérer le bouton pour le champ modèle
    if (modelValue === '') {
        // Si le champ modèle est vide, masquer le bouton
        ButtonSearchByModel.style.display = 'none';
    } else {
        // Si le champ modèle contient une valeur, afficher le bouton
        ButtonSearchByModel.style.display = 'inline-block';
    }
}

function toggleButtonsOLD() {
    const searchInput = document.getElementById('input-search');
    const modelInput = document.getElementById('input-model');
    const ButtonSearchByUPC = document.getElementById('search-by-upc');
    const ButtonSearchByName = document.getElementById('search-by-name');
    const ButtonSearchByModel = document.getElementById('search-by-model');

    const searchValue = searchInput.value.trim();

    if (searchValue === '') {
        // Si le champ est vide, masquer les deux boutons
        ButtonSearchByUPC.style.display = 'none';
        ButtonSearchByName.style.display = 'none';
   
    } else if (!isNaN(searchValue)) {
        // Si la valeur est numérique, afficher le bouton submit et masquer l'autre
        ButtonSearchByUPC.style.display = 'inline-block';
        ButtonSearchByName.style.display = 'inline-block';
    } else {
        // Si la valeur est non numérique, afficher le bouton "Search by Name" et masquer le bouton submit
        ButtonSearchByUPC.style.display = 'none';
        ButtonSearchByName.style.display = 'inline-block';
      
    }

    if (modelInput === '') { 
        ButtonSearchByModel.style.display = 'none';
    } else if (!isNaN(modelInput)) {
        // Si la valeur est numérique, afficher le bouton submit et masquer l'autre
        ButtonSearchByModel.style.display = 'inline-block';      
    } else {    
        ButtonSearchByModel.style.display = 'inline-block';
    }
}

function filterByCategory() {
    const token = document.querySelector('input[name="token"]').value;
    const selectedCategory = document.getElementById('categoryFilter');
    const selectedCategoryId = selectedCategory.value;
    const selectedOption = selectedCategory.options[selectedCategory.selectedIndex];

    // Vérifier si une catégorie est sélectionnée
    if (selectedCategoryId) {
        // Vérifier si la catégorie est rouge
        const isRed = selectedOption.style.color === 'red';

        if (isRed) {
            // Construire l'URL et ouvrir une nouvelle fenêtre
            const url = `index.php?route=shopmanager/catalog/category.edit&token=${token}&category_id=${selectedCategoryId}`;
            window.open(url, 'category'); // Ouvrir dans une nouvelle fenêtre ou onglet
        }
    }

    // Filtrer les lignes du tableau
    const tableBody = document.getElementById('tableBody');
    const rows = tableBody.querySelectorAll('tr');

    rows.forEach(row => {
        const categoryId = row.getAttribute('data-category-id');
        const checkbox = row.querySelector('input[type="checkbox"]');

        if (selectedCategoryId === "" || categoryId === selectedCategoryId) {
            row.style.display = ""; // Afficher la ligne
            if (checkbox) {
                checkbox.disabled = false; // Activer la case
            }
        } else {
            row.style.display = "none"; // Masquer la ligne
            if (checkbox) {
                checkbox.checked = false; // Décocher la case
                checkbox.disabled = true; // Désactiver la case
            }
        }
    });
}

function SearchByName(model = null) {
    const token = document.querySelector('input[name="token"]').value;
    let keyword; // Déclare la variable keyword à l'échelle de la fonction

  
 //   const keyword = document.getElementById('input-search').value.trim();
    const searchResults = document.getElementById('search-results');
    const toggleButtonContainer = document.getElementById('toggle-button-container');
    const toggleButton = document.getElementById('toggle-button');
    const ocrDiv = document.getElementById('ocr');
    const icons = toggleButton.querySelectorAll('.fa');

    let json_items = null;
    const jsonItemsField = document.getElementById('jsonItems');
    if (jsonItemsField && jsonItemsField.value) {
        try {
            json_items = JSON.parse(jsonItemsField.value);
        } catch (error) {
            console.error('Invalid JSON in jsonItems field:', error);
        }
    }

    let selectedItems = null;

    if (model) {
        keyword = document.getElementById('input-model').value.trim();
    } else {
        keyword = document.getElementById('input-search').value.trim();
       
    }
    const selectedCheckboxes = document.querySelectorAll('input[name="selected_ebay_item[]"]:checked');
    if (selectedCheckboxes.length > 0) {
        selectedItems = Array.from(selectedCheckboxes).map(item => item.value);
    }
    if (!keyword) {
        alert('Please enter a keyword to search.');
        return;
    }

    // Nettoyer les résultats précédents
    if (searchResults) {
        searchResults.innerHTML = ''; // Vide le contenu
    }

 //   console.log('Search keyword:', keyword);

    // Envoyer la requête avec fetch
    fetch(`index.php?route=shopmanager/ebay.searchByName&token=${token}`, {
        method: 'POST',
       
        headers: { 'Content-Type': 'application/json' },
    
        body: JSON.stringify({ keyword: keyword, selected_ebay_item: selectedItems, json_items : json_items }), // Envoyer uniquement les valeurs

    })
        .then(response => {
            // Lire la réponse comme texte brut
            return response.text().then(text => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}\nResponse Body:\n${text}`);
                }
                return text;
            });
        })
        .then(text => {
            try {
                // Essayer de convertir la réponse en JSON
                const data = JSON.parse(text);

                if (data.success) {
                    // Afficher les résultats de la recherche
                    if (searchResults) {
                        searchResults.innerHTML = data.html || '<p>No results found.</p>';

                      

                        if (data.html && data.html.trim() !== '<p>No results found.</p>') {
                            // Afficher le bouton si des résultats sont trouvés
                            toggleButtonContainer.style.display = 'block';
                            ocrDiv.style.maxHeight = '0px';
                            ocrDiv.style.display = 'none';
                            ocrDiv.style.overflow = 'hidden';
                            handleimage();
                          
                        } else {
                            // Masquer le bouton si aucun résultat
                            toggleButtonContainer.style.display = 'none';
                            ocrDiv.style.maxHeight = `${ocrDiv.scrollHeight}px`;
                            ocrDiv.style.display = 'block';
                            ocrDiv.style.overflow = 'visible';
                            icons.forEach(icon => {
                                icon.classList.remove('fa-chevron-up');
                                icon.classList.add('fa-chevron-down');
                            });
                        }
                        toggleFeedButton();
                        highlightMaxLengths();
                    }
                } else if (data.error) {
                    alert('Error: ' + data.error);
                } else {
                    alert('An unknown error occurred.');
                }
            } catch (error) {
                console.error('JSON Parsing Error:', error);
                console.error('Response Text:', text); // Affiche la réponse brute
                if (searchResults) {
                    searchResults.innerHTML = `<pre class="text-danger">${text}</pre>`; // Affiche l'erreur brute
                }
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);

            // Optionnel : afficher un message d'erreur dans l'interface
            if (searchResults) {
                searchResults.innerHTML = `<p class="text-danger">${error.message}</p>`;
            }
        });

        



      
          
            // Fonction pour ajouter les événements liés à l'image
            function addImageEvents(thumbnail, fullsize) {
                thumbnail.addEventListener('mouseover', function () {
                    fullsize.style.display = 'block';
                });
        
                thumbnail.addEventListener('mousemove', function (event) {
                    const rect = thumbnail.getBoundingClientRect();
                    // Taille de l'image en taille réelle
                        const fullsizeWidth = fullsize.offsetWidth;
                        const fullsizeHeight = fullsize.offsetHeight;

                        // Centrer l'image au pointeur
                        const topPosition = event.clientY - rect.top - (fullsizeHeight / 2);
                        const leftPosition = event.clientX - rect.left - (fullsizeWidth / 2);

                        fullsize.style.top = `${topPosition}px`;
                        fullsize.style.left = `${leftPosition}px`;
                });
        
                thumbnail.addEventListener('mouseout', function () {
                    fullsize.style.display = 'none';
                });
            }
        
            // Parcourir chaque image miniature et ajouter les événements
        function handleimage() {
            const images = document.querySelectorAll('.thumbnail-image');
            images.forEach(imageElement => {
                const fullsize = imageElement.nextElementSibling;
                if (fullsize) {
                    addImageEvents(imageElement, fullsize);
                }
            });
        }

        


      

}

  

    // Fonction pour vérifier si au moins une case est cochée
    function toggleFeedButton() {
        const checkboxes = document.querySelectorAll('input[name="selected_ebay_item[]"]'); // Toutes les cases à cocher
        const feedButtons = document.querySelectorAll('.feed-action'); // Tous les boutons feed
    
        // Vérifie si au moins une case est cochée
        const isAnyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
    
        // Affiche ou cache tous les boutons feed-action en fonction de la sélection
        feedButtons.forEach(button => {
            button.style.display = isAnyChecked ? 'inline-block' : 'none';
        });
    }


    function toggleSelectAlleBayItem(masterCheckbox) {
        // Obtenir toutes les cases à cocher visibles dans le tableau
        const checkboxes = document.querySelectorAll('input[name="selected_ebay_item[]"]:not([disabled])');
    
        // Changer l'état de chaque case à cocher visible en fonction de la case maître
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = masterCheckbox.checked;
        });
    
        // Mettre à jour les autres comportements associés
        toggleFeedButton();
    }
    


async function FeedProductForm() {
    var productId = document.querySelector('input[name="product_id"]').value;
    const form = document.getElementById('form-product' + productId);
    const tokenInput = document.querySelector('input[name="token"]');
    const searchResults = document.getElementById('search-results');
    const saveButtons = document.querySelectorAll('.save-action'); // Tous les boutons save
    const feedButtons = document.querySelectorAll('.feed-action'); // Tous les boutons feed

    const token = tokenInput.value;

    // Extraire toutes les données du formulaire
    const formData = new FormData(form);


    // Supprimer le champ "search" du FormData
    formData.delete('input-search');

    // Convertir FormData en objet JSON
    const formObject = {};
    formData.forEach((value, key) => {
        // Normaliser la clé : retirer les crochets `[]` si présents
        const normalizedKey = key.replace(/\[\]$/, '');

        if (!formObject[normalizedKey]) {
            formObject[normalizedKey] = value;
        } else {
            // Si plusieurs valeurs pour une clé, les regrouper dans un tableau
            formObject[normalizedKey] = [].concat(formObject[normalizedKey], value);
        }
    });

    // Ajouter le token manuellement au cas où il n'est pas inclus dans le formulaire
    formObject.token = token;

    // Nettoyer les résultats précédents
    if (searchResults) {
        searchResults.innerHTML = ''; // Vide le contenu
    }

    // Envoyer les données avec fetch
    return fetch(`index.php?route=shopmanager/catalog/product_search.product_source_info_feed&token=${token}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formObject), // Envoyer toutes les données du formulaire
    })
        .then(response => {
            // Lire la réponse brute comme du texte
            return response.text().then(text => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}\nResponse Body:\n${text}`);
                }
                return text;
            });
        })
        .then(text => {
            try {
                // Essayer de convertir la réponse en JSON
                const data = JSON.parse(text);
                if (searchResults) {
                    searchResults.innerHTML = data.html || '<p>No results found.</p>';
                }

                if (typeof reloadSpecificHandlers === 'function') {
                    reloadSpecificHandlers();
                }

                // Afficher les boutons save et cacher les boutons feed
                saveButtons.forEach(button => {
                    if (button.style) button.style.display = 'inline-block';
                });
                feedButtons.forEach(button => {
                    if (button.style) button.style.display = 'none';
                });

                checkFormStatus();
                compareAndUpdateTitle();
            } catch (error) {
                console.error('JSON Parsing Error:', error);
                console.error('Response Text:', text); // Affiche la réponse brute
                if (searchResults) {
                    searchResults.innerHTML = `<pre class="text-danger">${text}</pre>`; // Affiche l'erreur brute
                }
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);

            if (searchResults) {
                searchResults.innerHTML = `<p class="text-danger">${error.message}</p>`;
            }
        });
}

var specifics_row = 0; // Initialisation du compteur

function addSpecifics() {
    // Récupérer le tableau avec l'ID "Specific_table" dans lequel ajouter la nouvelle ligne
    var tableBody = document.querySelector('#Specific_table tbody'); // Cibler le tbody du tableau par son ID

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
        var token = document.querySelector('input[name="token"]').value;
        var url = 'index.php?route=shopmanager/catalog/product_specific.editSpecificKey&token=' + token +
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
      //  .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
    }

    // Fonction pour appeler `deleteSpecificKey` via AJAX avec GET
    function deleteSpecificKey(specificKey, category_id) {
        var token = document.querySelector('input[name="token"]').value;
        var url = 'index.php?route=shopmanager/catalog/product_specific.deleteSpecificKey&token=' + token +
            '&specific_key=' + encodeURIComponent(specificKey) +
            '&category_id=' + encodeURIComponent(category_id);

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
      //  .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
    }

    // Fonction pour appeler `addSpecificKey` via AJAX avec GET
    function addSpecificKey(specificKey, category_id, replacementTerm) {
        var token = document.querySelector('input[name="token"]').value;
        var url = 'index.php?route=shopmanager/catalog/product_specific.addSpecificKey&token=' + token +
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
       // .then(data => console.log(data))
        .catch(error => console.error('Error:', error));
    }

    // Fonction pour vérifier si une clé spécifique existe déjà via AJAX avec GET
    function checkIfSpecificKeyExists(specificKey, category_id) {
        var token = document.querySelector('input[name="token"]').value;
        var url = 'index.php?route=shopmanager/catalog/product_specific.getSpecificKey&token=' + token +
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
    
    function initializeSpecificHandlers() {
        // Sélectionner tous les éléments <select> dans le tableau
        const categoryIdInput = document.querySelector('input[name="category_id"]');
        const categorySpecificNamesInput = document.querySelector('input[name="category_specific_names_json"]');
        
        // Vérifier si les éléments nécessaires existent
        if (!categoryIdInput || !categorySpecificNamesInput) {
            console.error('Category ID or Category Specific Names JSON input is missing.');
            return;
        }
    
        const category_id = categoryIdInput.value;
    
        // Vérifier si l'élément contient des données
        let categorySpecificNames;
        if (categorySpecificNamesInput.value) {
            try {
                categorySpecificNames = JSON.parse(categorySpecificNamesInput.value);
            } catch (error) {
                console.error('Invalid JSON in categorySpecificNamesInput:', error);
                return;
            }
        } else {
            console.error('categorySpecificNames is not defined. Ensure the hidden input contains the necessary data.');
            return; // Arrêter si les données sont manquantes
        }
    
        const selects = document.querySelectorAll("select.form-control");
    
        // Ajouter un écouteur d'événement sur chaque select
        selects.forEach(function (selectElement) {
            selectElement.addEventListener("change", function (event) {
                const selectedValue = event.target.value; // La nouvelle valeur sélectionnée
                const selectNameArray = event.target.name.split("specifics_select[")[1]; // Extraire le nom de la clé
                const selectName = selectNameArray.split("][]")[0]; // Extraire le nom de la clé
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
    
        updateRowStatus();
        updateCheckboxOnTextChange();

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

       async function updateSelectsAndRows() {
            const table = document.getElementById("Specific_table");
            if (!table) {
                console.error("Table not found");
                return;
            }
    
            const rows = table.querySelectorAll("tbody tr");
    
            rows.forEach(function (row) {
                const select = row.querySelector("select.form-control");
                const checkbox = row.querySelector("input.save-data-checkbox");
    
                if (!select || !checkbox) {
                    return;
                }
    
                // Vérifier si aucune option n'est sélectionnée
                if (!select.value) {
                    const selectNameMatch = select.name.match(/\[([^\]]+)\]\[\]/); // Extraire la clé comme "Type"
                    if (selectNameMatch) {
                        const specificKey = selectNameMatch[1]; // Obtenir la clé spécifique
    
                        // Vérifier si la clé existe dans les options
                        const matchingOption = Array.from(select.options).find(
                            option => option.value === specificKey
                        );
    
                        if (matchingOption) {
                            // Sélectionner l'option correspondante
                            select.value = specificKey;
    
                            // Marquer la case comme cochée
                            checkbox.checked = true;
    
                            // Ajouter la classe CSS pour succès
                            row.classList.remove("strong-warning", "strong-danger");
                            row.classList.add("strong-success");
    
                            // Appeler la fonction `SelectRow`
                            SelectRow(checkbox);
                        }
                    }
                }
            });
        }
    
        // Appeler la fonction au chargement initial
        updateSelectsAndRows();
    }
    
    // Appeler la fonction après le chargement initial de la page
    document.addEventListener("DOMContentLoaded", function () {
     //   initializeSpecificHandlers();
    });
    
    // Réexécuter la fonction après l'injection dynamique de HTML
    function reloadSpecificHandlers() {
        initializeSpecificHandlers();
    }

function checkFormStatus() {
    var categoryChecked = document.querySelector('input[name^="product_category"]:checked');
    var conditionChecked = document.querySelector('input[name="condition_id"]:checked');
    var priceChecked = document.querySelector('input[name^="price_ebay"]:checked');
    var saveButtons = document.querySelectorAll('.save-action'); // Tous les boutons save


    // Vérifie si priceChecked a une valeur non vide et supérieure à 0
    let isPriceValid = false;

    if (priceChecked) {
        // Récupérer l'input correspondant au prix de la ligne sélectionnée
        var priceInput = document.querySelector(`#price_input_${priceChecked.id.replace('condition_marketplace_item_id', '')}`);
        
        if (priceInput) {
            const priceValue = parseFloat(priceInput.value.trim());

            // Vérifie si le prix est valide
            isPriceValid = !isNaN(priceValue) && priceValue > 0;

            // Ajouter ou retirer la bordure rouge
            if (!isPriceValid) {
                priceInput.style.border = '2px solid darkred';
            } else {
                priceInput.style.border = '';
            }
        }
        var priceSpan = document.querySelector(`#price${priceChecked.id.replace('condition_marketplace_item_id', '')}`);
  
    
        if (priceSpan) {
            // Extraire la valeur du prix depuis le contenu texte du span
            const priceValue = parseFloat(priceSpan.textContent.replace('$', '').trim());
       
            // Vérifie si le prix est valide
            isPriceValid = !isNaN(priceValue) && priceValue > 0;
    
            // Ajouter ou retirer la bordure rouge (modification de la bordure du span)
            if (!isPriceValid) {
                priceSpan.style.border = '2px solid darkred';
            } else {
                priceSpan.style.border = '';
            }
        }
    }

    // Vérifie toutes les conditions
    const allConditionsMet = categoryChecked && conditionChecked && isPriceValid;
   // console.log(' allConditionsMet : ' + allConditionsMet);
   // console.log(' categoryChecked : ' + categoryChecked);
   // console.log(' conditionChecked : ' + conditionChecked);
   // console.log(' isPriceValid : ' + isPriceValid);

    // Activer ou désactiver tous les boutons save-action et feed-action
    saveButtons.forEach(button => {
        button.disabled = !allConditionsMet;
        button.style.display = 'inline-block';
    });
}

  
  
   // Fonction qui met à jour le bouton radio save-data-radio basé sur la sélection de condition
    function updateCondition(conditionMarketplaceItemId) {
        // Désélectionner tous les boutons radio save-data-radio
        const radios = document.querySelectorAll('.save-data-radio');
        const inputs = document.querySelectorAll('.price-input');
        radios.forEach(function(radio) {
            radio.checked = false; // Décocher tous les boutons
        });
 
        inputs.forEach(function(input) {
            input.style.border = '';
        });
        // Sélectionner le bouton radio correspondant à conditionMarketplaceItemId
        const targetRadio = document.querySelector(`#condition_marketplace_item_id${conditionMarketplaceItemId}`);
        
        if (targetRadio) {
          
            targetRadio.checked = true; // Cocher le bouton radio correspondant
        }
    }

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

