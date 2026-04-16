// Original: warehouse/product/publish_list.js
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

// ============================================
// END DUPLICATED FUNCTIONS FROM TOOLS.JS
// ============================================

/*
document.addEventListener('DOMContentLoaded', function () {
    // var productId = document.querySelector('input[name="product_id"]').value;
     const form = document.getElementById('form-product');
     
     // Désactive le submit via Enter dans tout le formulaire
     form.addEventListener('keypress', function (e) {
         if (e.key === 'Enter') {
             e.preventDefault(); // Bloque l'action par défaut
             return false; // Empêche la propagation
         }
     });
 });*/
 // Ajouter un écouteur pour le champ `search` si nécessaire
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
     const user_token = document.querySelector('input[name="user_token"]').value;
     const selectedCategory = document.getElementById('categoryFilter');
     const selectedCategoryId = selectedCategory.value;
     const selectedOption = selectedCategory.options[selectedCategory.selectedIndex];
 
     // Vérifier si une catégorie est sélectionnée
     if (selectedCategoryId) {
         // Vérifier si la catégorie est rouge
         const isRed = selectedOption.style.color === 'red';
 
         if (isRed) {
             // Construire l'URL et ouvrir une nouvelle fenêtre
             const url = `index.php?route=warehouse/product/category.edit&user_token=${user_token}&category_id=${selectedCategoryId}`;
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
     const user_token = document.querySelector('input[name="user_token"]').value;
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
        alert(TEXT_ENTER_KEYWORD_SEARCH);
         return;
     }
 
     // Nettoyer les résultats précédents
     if (searchResults) {
         searchResults.innerHTML = ''; // Vide le contenu
     }
 
 
     // Envoyer la requête avec fetch
     fetch(`index.php?route=warehouse/marketplace/ebay/api.searchByName&user_token=${user_token}`, {
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
                    alert(TEXT_ERROR_PREFIX_GENERIC + data.error);
                 } else {
                    alert(TEXT_UNKNOWN_ERROR_OCCURRED);
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
     const tokenInput = document.querySelector('input[name="user_token"]');
     const searchResults = document.getElementById('search-results');
     const saveButtons = document.querySelectorAll('.save-action'); // Tous les boutons save
     const feedButtons = document.querySelectorAll('.feed-action'); // Tous les boutons feed
 
     const user_token = tokenInput.value;
 
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
 
     // Ajouter le user_token manuellement au cas où il n'est pas inclus dans le formulaire
     formObject.user_token = user_token;
 
     // Nettoyer les résultats précédents
     if (searchResults) {
         searchResults.innerHTML = ''; // Vide le contenu
     }
 
     // Envoyer les données avec fetch
     return fetch(`index.php?route=warehouse/product/research.product_source_info_feed&user_token=${user_token}`, {
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
         var url = 'index.php?route=warehouse/product/product_specific.editSpecificKey&user_token=' + user_token +
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
         var url = 'index.php?route=warehouse/product/product_specific.deleteSpecificKey&user_token=' + user_token +
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
         var url = 'index.php?route=warehouse/product/product_specific.addSpecificKey&user_token=' + user_token +
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
         var url = 'index.php?route=warehouse/product/product_specific.getSpecificKey&user_token=' + user_token +
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
                 deleteBtn.innerHTML = '<i class="fa-solid fa-trash-can" aria-hidden="true"></i>';
     
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
             const table = document.getElementById("Specific-table");
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
     
 
 //transferer 
 
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
 
 
   // Fonction pour ajouter un champ texte et calculer le prix manquant
 function removeUnnecessaryConditions() {
     const rows = document.querySelectorAll('#conditionsTable tbody tr');
     let prices = [];
    const loadingModalElement = document.getElementById('loadingModal');
    if (loadingModalElement) {
        bootstrap.Modal.getOrCreateInstance(loadingModalElement).show();
    }
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
     });

    if (loadingModalElement) {
        bootstrap.Modal.getOrCreateInstance(loadingModalElement).hide();
    }
 }
 
 // Exécuter la fonction lors du chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const loadingModalElement = document.getElementById('loadingModal');
    if (loadingModalElement) {
        bootstrap.Modal.getOrCreateInstance(loadingModalElement);
    }
});
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
 });*/function updatePriceCondition(input) {
     const key = input.dataset.key;
     const expression = input.value.trim();
 
     // Vérifie si une expression mathématique est présente
     const isMathExpression = /[+\-*/]/.test(expression);
 
     let price;
 
     if (isMathExpression) {
         try {
             // Évalue l'expression mathématique
             price = eval(expression);
         } catch (error) {
             alert(TEXT_INVALID_MATH_EXPRESSION);
             input.value = ''; // Réinitialise la valeur en cas d'erreur
             return;
         }
     } else {
         // Si ce n'est pas une expression mathématique, essaie de convertir directement en nombre
         price = parseFloat(expression);
     }
 
     // Vérifie si la valeur finale est valide (>0)
     if (!isNaN(price) && price > 0) {
         // Remplacer le champ texte par un span
         const td = input.closest('td');
         td.innerHTML = `<span id="price${key}" 
                               class="pedit-price" 
                               rel="${price.toFixed(2)}" 
                               rel1="${price.toFixed(2)}">
                               $${price.toFixed(2)}
                         </span>`;
 
         // Mettre à jour la valeur dans l'input radio correspondant
         const radio = document.getElementById(`condition_marketplace_item_id${key}`);
         if (radio) {
             radio.value = price.toFixed(2);
         }
     } else {
         alert(TEXT_INVALID_PRICE_EXPRESSION);
         input.value = ''; // Réinitialise la valeur en cas d'entrée invalide
     }
     checkFormStatus();
 }
 
 // Gérer les clics sur les spans pour les transformer en champs de texte
 $(document).on('click', '.pedit-price', function (event) {
     event.preventDefault();
 
     const rel = $(this).attr('rel'); // Valeur actuelle
     const key = $(this).attr('id').replace('price', ''); // Extraire la clé depuis l'ID
 
     // Remplacer le span par un input texte
     const inputHTML = `<input type="text" id="price_input_${key}" 
                                class="price-input" 
                                value="${rel}" 
                                data-key="${key}" 
                                onchange="updatePriceCondition(this)">`;
 
     $(this).closest('td').html(inputHTML);
 
     // Focus sur le champ texte
     const inputElement = document.getElementById(`price_input_${key}`);
     if (inputElement) {
         inputElement.focus();
         inputElement.select();
     }
 });
 
 // Empêcher la soumission par défaut lors d'un appui sur "Enter" dans un input
 $('form').on('submit', function (e) {
     e.preventDefault();
 });
 
 // Mettre à jour l'attribut 'rel1' en temps réel lors de la modification
 $(document).on('input', '.price-input', function () {
     const key = $(this).data('key');
     $(`#price${key}`).attr('rel1', $(this).val());
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
         containerDiv.className = 'input-group-custom'; // Ajout de classe Bootstrap pour aligner le champ et le bouton
 
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
 });
 
 
  
 /*
 document.addEventListener("DOMContentLoaded", function () {
     // Fonction pour ajouter un nouveau fabricant
     document.getElementById("btn-add-manufacturer").addEventListener("click", function () {
         var manufacturerName = document.querySelector('input[name="manufacturer"]').value;
         var user_token = document.querySelector('input[name="user_token"]').value;
 
         if (manufacturerName) {
             $.ajax({
                 url: 'index.php?route=warehouse/product/manufacturer.add&ajax=true&user_token=' + user_token,
                 type: 'post',
                 data: { name: manufacturerName },
                 dataType: 'json',
                 success: function (response) {
                     if (response.success) {
                        alert(TEXT_MANUFACTURER_ADDED_SUCCESS);
                         document.querySelector('input[name="manufacturer_id"]').value = response.manufacturer_id;
                     } else {
                        alert(TEXT_ERROR_PREFIX_GENERIC + response.error);
                     }
                 },
                 error: function (xhr, status, error) {
                    alert(TEXT_ERROR_ADD_MANUFACTURER_PREFIX + error);
                 }
             });
         } else {
            alert(TEXT_ENTER_MANUFACTURER_NAME);
         }
     });
 
     // Fonction pour modifier un fabricant existant
     document.getElementById("btn-edit-manufacturer").addEventListener("click", function () {
         var manufacturerId = document.querySelector('input[name="manufacturer_id"]').value;
         var manufacturerName = document.querySelector('input[name="manufacturer"]').value;
         var user_token = document.querySelector('input[name="user_token"]').value;
 
         if (manufacturerId && manufacturerName) {
             $.ajax({
                 url: 'index.php?route=warehouse/product/manufacturer.edit&ajax=true&user_token=' + user_token + '&manufacturer_id=' + manufacturerId,
                 type: 'post',
                 data: { name: manufacturerName },
                 dataType: 'json',
                 success: function (response) {
                     if (response.success) {
                        alert(TEXT_MANUFACTURER_UPDATED_SUCCESS);
                     } else {
                        alert(TEXT_ERROR_PREFIX_GENERIC + response.error);
                     }
                 },
                 error: function (xhr, status, error) {
                    alert(TEXT_ERROR_EDIT_MANUFACTURER_PREFIX + error);
                 }
             });
         } else {
            alert(TEXT_SELECT_MANUFACTURER_AND_NAME);
         }
     });
 
     // Fonction pour supprimer un fabricant existant
     document.getElementById("btn-delete-manufacturer").addEventListener("click", function () {
         var manufacturerId = document.querySelector('input[name="manufacturer_id"]').value;
         var user_token = document.querySelector('input[name="user_token"]').value;
 
         if (manufacturerId) {
            if (confirm(TEXT_CONFIRM_DELETE_MANUFACTURER)) {
                 $.ajax({
                     url: 'index.php?route=warehouse/product/manufacturer.delete&ajax=true&user_token=' + user_token + '&manufacturer_id=' + manufacturerId,
                     type: 'post',
                     dataType: 'json',
                     success: function (response) {
                         if (response.success) {
                            alert(TEXT_MANUFACTURER_DELETED_SUCCESS);
                             document.querySelector('input[name="manufacturer"]').value = '';
                             document.querySelector('input[name="manufacturer_id"]').value = '';
                         } else {
                            alert(TEXT_ERROR_PREFIX_GENERIC + response.error);
                         }
                     },
                     error: function (xhr, status, error) {
                        alert(TEXT_ERROR_DELETE_MANUFACTURER_PREFIX + error);
                     }
                 });
             }
         } else {
            alert(TEXT_SELECT_MANUFACTURER_DELETE);
         }
     });
 });
 */
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
 
 function highlightMaxLengths() {
     // Sélectionner tous les éléments contenant les longueurs
     const lengths = document.querySelectorAll(".item-length");
 
     let maxLength = 0;
 
     // Trouver la valeur maximale
     lengths.forEach(element => {
         const lengthValue = parseInt(element.dataset.length, 10);
         if (lengthValue > maxLength) {
             maxLength = lengthValue;
         }
     });
     // Appliquer un style rouge avec texte blanc à tous les éléments ayant la valeur maximale
     lengths.forEach(element => {
         const lengthValue = parseInt(element.dataset.length, 10);
         if (lengthValue === maxLength) {
             element.style.backgroundColor = "red";
             element.style.color = "white";
             element.style.fontWeight = "bold";
             element.style.padding = "2px 4px";
             element.style.borderRadius = "3px";
         }
     });
 }
 
 function pasteTitle(button) {
     const title = button.getAttribute("data-title"); // Récupère la valeur du bouton
     const inputField = document.getElementById("title-search"); // Champ de saisie cible
 
     if (inputField) {
         inputField.value = title; // Colle la valeur dans le champ
         // Optionnel : Met à jour le compteur de caractères
         if (typeof updateCharacterCount === "function") {
             updateCharacterCount(inputField, 'char-count-search');
         }
     }
 }
 
 function compareAndUpdateTitle() {
     // Récupère les champs d'entrée
     const titleInput = document.getElementById("title");
     const titleSearchInput = document.getElementById("title-search");
 
     // Vérifie si les champs existent
     if (titleInput && titleSearchInput) {
         // Compare les longueurs des valeurs
         const titleLength = titleInput.value.length;
         const titleSearchLength = titleSearchInput.value.length;
 
         if (titleSearchLength < titleLength) {
             // Met à jour la valeur de #title avec celle de #title-search
             titleInput.value = titleSearchInput.value;
 
             // Met à jour le compteur de caractères si une fonction existe
             if (typeof updateCharacterCount === "function") {
                 updateCharacterCount(titleInput, 'char-count');
             }
         }
     }
 }
 
 
async function fastProductSearch(product_id, upc) {
    var user_token = document.querySelector('input[name="user_token"]').value;

    const url = 'index.php?route=warehouse/product/research.product_source_info_fast_feed&view=fast_list&user_token=' + user_token;
    const productRow = document.getElementById(`product-row-${product_id}`);

    // Vérifie si la ligne de détails est déjà affichée
    if (productRow.style.display === "table-row") {
        productRow.style.display = "none"; // Masquer si déjà affiché
        return;
    }

    // Ajoute un indicateur de chargement temporaire
    productRow.innerHTML = `<td colspan="99" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td>`;
    productRow.style.display = "table-row";

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: product_id,
                upc: upc
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const jsonResponse = await response.json(); // Convertir la réponse en JSON
        if (jsonResponse.html == 'category') {
            // Construire l'URL et ouvrir une nouvelle fenêtre
            const categoryId = document.querySelector('input[name="category_id"]').value;
            const url = `index.php?route=warehouse/product/category.edit&user_token=${user_token}&category_id=${categoryId}`;
            window.open(url, 'category'); // Ouvrir dans une nouvelle fenêtre ou onglet
        }
        const htmlContent = jsonResponse.html || '<td colspan="99" class="text-center text-danger">No data received.</td>'; // Extraire la clé HTML

        // Remplace la ligne cachée par le HTML reçu et l'affiche
        productRow.innerHTML = `<td colspan="99">${htmlContent}</td>`;
        productRow.style.display = "table-row";
        if (typeof reloadSpecificHandlers === 'function') {
            reloadSpecificHandlers();
        }

        checkFormStatus();
        compareAndUpdateTitle();
    } catch (error) {
        console.error('Error loading product details:', error);
        productRow.innerHTML = `<td colspan="99" class="text-center text-danger">Failed to load data. Please try again.</td>`;
    }
}



function handleDelete(productId) {
    checkProduct(productId);
    if (confirm(confirmMessage)) {
        $('#form-product' + productId).submit();
    }
}

function handleEnable(productId) {
    if(productId){
    checkProduct(productId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-product' + productId);
        form.action = enableUrl;
        form.submit();
    }
}

function handleDisable(productId) {
    if(productId){
    checkProduct(productId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-product' + productId);
        form.action = disableUrl;
        form.submit();
    }
}

function handleCopy(productId) {
    if(productId){
    checkProduct(productId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-product' + productId);
        form.action = copyUrl;
        form.submit();
    }
}

function checkProduct(productId) {
    // Sélectionner la case à cocher directement par son sélecteur d'attribut
    var checkbox = $('input[name="selected[' + productId + ']"]');
    checkbox.prop('checked', !checkbox.prop('checked'));
}




document.addEventListener("DOMContentLoaded", function () {

        // Ajouter un écouteur d'événement sur les champs de recherche
        $('#search-form input, #search-form select').on('change', function() {
            var currentInput = $(this);
            
            // Réinitialiser tous les champs sauf celui qui vient de changer et ceux à ne pas modifier
            $('#search-form input, #search-form select').each(function() {
                if ($(this).attr('name') !== currentInput.attr('name') 
                    && $(this).attr('id') !== 'input-status' 
                    && $(this).attr('id') !== 'input-image' 
                    && $(this).attr('id') !== 'input-limit') {
                    $(this).val('');
                }
            });
            
            // Cliquer sur le bouton de filtre
            $('#button-filter').click();
        });


    });
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.querySelector('#input-limit');
            if (!select) {
                return;
            }
            select.addEventListener('change', function () {
                const selectedValue = select.value;
                const newUrl = limitLink.replace('{page}', '1').replace('&limit=', '&limit=' + selectedValue);
                // Rediriger vers l'URL avec les paramètres appropriés
                window.location.href = newUrl;
            });
        });         
    
document.addEventListener("DOMContentLoaded", function () {
    // État initial des éléments supplémentaires (cachés par défaut)
    let showMore = false;

    const toggleButtonElement = document.getElementById('toggle-button');
    const additionalFields = document.getElementById('additional-fields');

    if (!toggleButtonElement || !additionalFields) {
        return;
    }

    // Gestion de l'affichage des champs supplémentaires au clic sur le bouton
    toggleButtonElement.addEventListener('click', function () {
        const button = this;
        
        // Basculer l'état d'affichage
        showMore = !showMore;

        // Afficher ou masquer les champs en fonction de l'état
        if (showMore) {
            additionalFields.classList.add('show');
            button.innerHTML = '<i class="fa fa-chevron-up"></i> Show Less';
        } else {
            additionalFields.classList.remove('show');
            button.innerHTML = '<i class="fa fa-chevron-down"></i> Show More';
        }
    });
});


document.addEventListener("DOMContentLoaded", function () {
    function initAutocomplete(inputName, filterType) {
        var user_token = document.querySelector('input[name="user_token"]').value;

        var $inputSku = $('#input-sku');

        // Vérifier que l'élément existe et qu'il a une valeur avant d'appliquer select()
        if ($inputSku.length && $inputSku.val()) {
            $inputSku.focus().select();
        }
   


        $('input[name=\'' + inputName + '\']').autocomplete({
        
            'source': function(request, response) {
                $.ajax({
                    url: 'index.php?route=warehouse/product/product.autocomplete&user_token=' + user_token + '&' + filterType + '=' + encodeURIComponent(request),
                    dataType: 'json',
                    success: function(json) {

                        response($.map(json, function(item) {
                            if (item.hasOwnProperty('name') && item.hasOwnProperty('product_id')) {
                                return {
                                    label: item['name'],
                                    value: item['product_id']
                                };
                            } else if (item.hasOwnProperty('model') && item.hasOwnProperty('product_id')) {
                                return {
                                    label: item['model'],
                                    value: item['product_id']
                                };
                            } else {
                                return null;
                            }
                        }).filter(Boolean)); // Remove null values
                    }
                });
            },
           'select': function(item) {
                if (item && item.label) {
                    $('input[name=\'' + inputName + '\']').val(item['label']);
                }
            }
        });
    }

    // Initialiser l'autocomplétion pour chaque champ
    initAutocomplete('filter_sku', 'filter_sku');
    initAutocomplete('filter_product_id', 'filter_product_id');
    initAutocomplete('filter_name', 'filter_name');
    initAutocomplete('filter_category_id', 'filter_category_id');
    initAutocomplete('filter_model', 'filter_model');
});
 
 