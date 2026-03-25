# 🔧 PATTERNS DE PROGRAMMATION - PhoenixLiquidation
**Manuel de solutions réutilisables**

**Dernière mise à jour:** 15 janvier 2026

---

## 📖 COMMENT UTILISER CE MANUEL

**BUT:** Documenter des solutions de code pour les réutiliser sans refaire la roue

**UTILISATION:**
```
TOI: "Fait le pattern #1 pour allocation.js"
IA: → Lit le pattern #1
    → Applique la solution dans allocation.js
    → Adapte au contexte du fichier
```

---

## 🎯 PATTERN #1 - INTERNATIONALISATION (i18n) FR/EN/ES

**PROBLÈME:** Hardcoded strings en français/anglais dans JS

**SOLUTION:**

### 1️⃣ Language files PHP
```php
// administrator/language/en-gb/path/to/module.php
$_['text_confirm_delete'] = 'Are you sure you want to delete?';
$_['text_success_message'] = 'Operation completed successfully!';
$_['text_error_message'] = 'An error occurred';

// fr-fr
$_['text_confirm_delete'] = 'Êtes-vous sûr de vouloir supprimer?';
$_['text_success_message'] = 'Opération complétée avec succès!';
$_['text_error_message'] = 'Une erreur est survenue';

// es-es
$_['text_confirm_delete'] = '¿Está seguro de que desea eliminar?';
$_['text_success_message'] = '¡Operación completada con éxito!';
$_['text_error_message'] = 'Ocurrió un error';
```

### 2️⃣ Twig template
```twig
<script type="text/javascript">
var TEXT_CONFIRM_DELETE = '{{ text_confirm_delete|escape('js') }}';
var TEXT_SUCCESS_MESSAGE = '{{ text_success_message|escape('js') }}';
var TEXT_ERROR_MESSAGE = '{{ text_error_message|escape('js') }}';
</script>
<script src="view/javascript/path/to/module.js"></script>
```

### 3️⃣ JavaScript usage
```javascript
// Avant (hardcoded):
if (confirm('Êtes-vous sûr?')) { ... }

// Après (i18n):
if (confirm(TEXT_CONFIRM_DELETE)) { ... }
```

**RÈGLES:**
- ✅ Toujours 3 langues: EN/FR/ES
- ✅ Variables en UPPERCASE: `TEXT_XXX`
- ✅ Escape dans Twig: `|escape('js')`
- ✅ Charger JS APRÈS les variables

---

## 🎯 PATTERN #2 - DÉPLACER JS DE TWIG VERS FICHIER .JS

**PROBLÈME:** Code JavaScript mélangé dans les fichiers Twig alors qu'il devrait être dans le fichier .js du module

**SOLUTION:**

### 1️⃣ Identifier le JS inline dans Twig
```twig
<!-- ❌ MAUVAIS: JS inline dans Twig -->
<script type="text/javascript">
$('#inventory').on('click', 'thead a, .pagination a', function(e) {
    e.preventDefault();
    $('#inventory').load(this.href);
});

document.addEventListener('DOMContentLoaded', function() {
    updateRelativeTimes();
    setInterval(updateRelativeTimes, 60000);
});

$('#inventory').on('DOMSubtreeModified', function() {
    setTimeout(updateRelativeTimes, 100);
});
</script>
```

### 2️⃣ Déplacer dans le fichier .js du module
```javascript
// ✅ BON: Dans allocation.js
document.addEventListener('DOMContentLoaded', function() {
    // Pagination AJAX
    $('#inventory').on('click', 'thead a, .pagination a', function(e) {
        e.preventDefault();
        $('#inventory').load(this.href);
    });
    
    // Initialize relative time updates
    updateRelativeTimes();
    setInterval(updateRelativeTimes, 60000); // Update every minute
    
    // Reinit relative times after list loads
    $('#inventory').on('DOMSubtreeModified', function() {
        setTimeout(updateRelativeTimes, 100);
    });
});
```

### 3️⃣ Nettoyer le Twig (garder seulement variables et chargement)
```twig
<!-- ✅ BON: Twig propre avec seulement variables et scripts -->
<script type="text/javascript">
var TEXT_CONFIRM = '{{ text_confirm|escape('js') }}';
var TEXT_SKU_NOT_FOUND = '{{ text_sku_not_found|escape('js') }}';
var USER_TOKEN = '{{ user_token }}';
</script>
<script type="text/javascript" src="view/javascript/shopmanager/sound.js"></script>
<script type="text/javascript" src="view/javascript/shopmanager/inventory/allocation.js"></script>
{{ footer }}
```

**RÈGLES:**
- ✅ **GARDER dans Twig:** Variables PHP → JS (textes i18n, tokens, IDs)
- ✅ **GARDER dans Twig:** Chargement des scripts `<script src="...">`
- ❌ **DÉPLACER vers .js:** Event listeners (click, change, submit, etc.)
- ❌ **DÉPLACER vers .js:** Initialisations au DOMContentLoaded
- ❌ **DÉPLACER vers .js:** Fonctions JavaScript
- ❌ **DÉPLACER vers .js:** Logique métier et interactions

**AVANTAGES:**
- ✅ Séparation claire PHP/JS
- ✅ Fichier .js réutilisable et testable
- ✅ Twig plus lisible et maintenable
- ✅ Meilleur cache navigateur des fichiers .js
- ✅ Respect des bonnes pratiques MVC

**EXEMPLE COMPLET:**

**AVANT (Twig):**
```twig
<script>
var TOKEN = '{{ user_token }}';
$('#button-save').on('click', function() {
    var data = $('#form-data').serialize();
    $.ajax({
        url: 'index.php?route=module/save&user_token=' + TOKEN,
        data: data,
        success: function(json) {
            alert('Saved!');
        }
    });
});
</script>
<script src="view/javascript/module.js"></script>
```

**APRÈS:**

**Twig (seulement variables):**
```twig
<script>
var USER_TOKEN = '{{ user_token }}';
var TEXT_SAVED = '{{ text_saved|escape('js') }}';
</script>
<script src="view/javascript/module.js"></script>
```

**module.js (toute la logique):**
```javascript
document.addEventListener('DOMContentLoaded', function() {
    const saveButton = document.getElementById('button-save');
    
    if (saveButton) {
        saveButton.addEventListener('click', function() {
            const data = $('#form-data').serialize();
            
            $.ajax({
                url: 'index.php?route=module/save&user_token=' + USER_TOKEN,
                data: data,
                dataType: 'json',
                success: function(json) {
                    alert(TEXT_SAVED);
                }
            });
        });
    }
});
```

---

## 🎯 PATTERN #3 - PROTECTION NAVIGATION (beforeunload)

**PROBLÈME:** Utilisateur quitte page avec données scannées non sauvegardées

**SOLUTION:**

### 1️⃣ Flag global
```javascript
var hasScannedProducts = false;
```

### 2️⃣ Event listener
```javascript
window.addEventListener('beforeunload', function(e) {
    if (hasScannedProducts) {
        e.preventDefault();
        e.returnValue = TEXT_UNSAVED_CHANGES;
        return TEXT_UNSAVED_CHANGES;
    }
});
```

### 3️⃣ Activer flag lors du scan
```javascript
function processScan(sku) {
    // ... scan logic
    hasScannedProducts = true; // ✅ Activer protection
}
```

### 4️⃣ Désactiver après submit
```javascript
function submitForm() {
    hasScannedProducts = false; // ✅ Désactiver avant redirect
    // ... submit logic
}
```

### 5️⃣ Traduction
```php
$_['text_unsaved_changes'] = 'You have scanned products that are not yet saved. Are you sure you want to leave?';
```

---

## 🎯 PATTERN #4 - AUTO-SCAN CHECKBOX AVEC VALIDATION PAYS

**PROBLÈME:** Auto-scan sur checkbox + validation pays obligatoire avec popups AI séquentiels + sauvegarde valeurs originales

**⚠️ VARIABLES À ADAPTER:**
- `.product-checkbox` → Classe des checkboxes (adapter au contexte)
- `input-made-in-country-id-` → ID du select pays (adapter nom champ)
- `quantity` / `unallocated_quantity` → IDs des badges (adapter noms)
- `hasScannedProducts` → Flag global (doit exister)
- `showCountryPopupForScan()` → Fonction popup pays (doit exister)
- `autoScanProduct()` → Helper auto-scan (créer si absent)
- `toggleSubmitButton()` → Active/désactive submit (adapter)
- `updateQuantity()` → Fonction mise à jour quantités (adapter)

**SOLUTION:**

### 1️⃣ Individual checkbox avec popup AI country et sauvegarde originale
### 1️⃣ Individual checkbox avec popup AI country et sauvegarde originale
```javascript
document.addEventListener('change', function(e) {
    if (e.target.matches('input[type="checkbox"][name^="product_id"]')) {
        const checkbox = e.target;
        const isChecked = checkbox.checked;
        const row = checkbox.closest('tr');
        const productId = checkbox.value;
        const unallocatedQuantity = parseInt(document.getElementById('unallocated_quantity' + productId).textContent) || 0;
        
        if (isChecked) {
            // ✅ VALIDATION CHAMP REQUIS (ici: pays)
            const requiredField = document.getElementById('input-made-in-country-id-' + productId);
            const requiredValue = requiredField ? requiredField.value : '0';
            
            if (!requiredValue || requiredValue === '0') {
                checkbox.checked = false;
                
                // Affiche popup validation (avec AI si applicable)
                showCountryPopupForScan(productId, row, function() {
                    checkbox.checked = true;
                    autoScanProduct(row, productId, unallocatedQuantity);
                });
                return;
            }
            
            autoScanProduct(row, productId, unallocatedQuantity);
            playSuccessSound(); // UN son par checkbox
        } else {
            // Reset on uncheck - RESTORE ORIGINAL VALUES
            const originalQuantity = parseInt(row.dataset.originalQuantity);
            const originalUnallocated = parseInt(row.dataset.originalUnallocated);
            
            if (!isNaN(originalQuantity) && !isNaN(originalUnallocated)) {
                // Restore to original state
                updateQuantity('quantity', productId, originalQuantity);
                updateQuantity('unallocated_quantity', productId, originalUnallocated);
            }
            
            updateTotalQuantity(productId);
            
            // Clear stored values
            delete row.dataset.addedQuantity;
            delete row.dataset.originalQuantity;
            delete row.dataset.originalUnallocated;
            
            // Reset row style
            row.style.backgroundColor = '';
            row.style.color = '';
            row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
                td.style.backgroundColor = '';
                td.style.color = '';
            });
            
            const anyScanned = Array.from(document.querySelectorAll('tr[data-product-id]')).some(function(r) {
                const pid = r.dataset.productId;
                const qty = parseInt(document.getElementById('quantity' + pid).textContent) || 0;
                return qty > 0;
            });
            hasScannedProducts = anyScanned;
        }
        
        toggleSubmitButton();
        updateSelectAllState(); // Si select-all existe
    }
});

// Fonction helper pour auto-scan avec SAUVEGARDE ORIGINALE
function autoScanProduct(row, productId, unallocatedQuantity) {
    // Get current quantity
    const currentQuantity = parseInt(document.getElementById('quantity' + productId).textContent) || 0;
    
    // ⚠️ CRITIQUE: Sauvegarder valeurs ORIGINALES avant modification
    if (!row.dataset.originalQuantity) {
        row.dataset.originalQuantity = currentQuantity;
        row.dataset.originalUnallocated = unallocatedQuantity;
    }
    
    // ADD unallocated to existing quantity (pas remplacer!)
    const newQuantity = currentQuantity + unallocatedQuantity;
    updateQuantity('quantity', productId, newQuantity);
    updateQuantity('unallocated_quantity', productId, 0);
    updateTotalQuantity(productId);
    
    // Store added quantity for debugging (optionnel)
    row.dataset.addedQuantity = unallocatedQuantity;
    
    // Visual feedback - green (complete)
    row.style.backgroundColor = '#d4edda';
    row.style.color = '#155724';
    row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
        td.style.backgroundColor = '#d4edda';
        td.style.color = '#155724';
    });
    
    hasScannedProducts = true;
    toggleSubmitButton();
    // ⚠️ PAS de playSuccessSound() ici (éviter 100+ sons lors select-all)
}
```

### 2️⃣ Select-all checkbox avec popups séquentiels
```javascript
// ⚠️ ADAPTER: selectAllCheckbox = votre checkbox select-all
const selectAllCheckbox = document.getElementById('select-all-checkbox');

if (selectAllCheckbox) {
    selectAllCheckbox.onclick = function() {
        const isChecked = this.checked;
        
        if (isChecked) {
            // ✅ VALIDATION GLOBALE
            const itemsWithoutRequiredField = [];
            document.querySelectorAll('input[type="checkbox"][name^="product_id"]').forEach(function(checkbox) {
                const productId = checkbox.value;
                const requiredField = document.getElementById('input-made-in-country-id-' + productId);
                const requiredValue = requiredField ? requiredField.value : '0';
                
                if (!requiredValue || requiredValue === '0') {
                    itemsWithoutRequiredField.push({
                        checkbox: checkbox,
                        productId: productId,
                        row: checkbox.closest('tr')
                    });
                }
            });
            
            // Si items sans champ requis: popups séquentiels
            if (itemsWithoutRequiredField.length > 0) {
                selectAllCheckbox.checked = false;
                
                let currentIndex = 0;
                
                function processNextItem() {
                    if (currentIndex >= itemsWithoutRequiredField.length) {
                        // Tous validés: re-clic select-all après délai
                        setTimeout(function() {
                            selectAllCheckbox.click();
                        }, 500); // ⚠️ Délai pour fermeture modal Bootstrap
                        return;
                    }
                    
                    const item = itemsWithoutRequiredField[currentIndex];
                    currentIndex++;
                    
                    // Affiche popup validation
                    showCountryPopupForScan(item.productId, item.row, function() {
                        // Attendre avant prochain popup
                        setTimeout(function() {
                            processNextItem();
                        }, 300); // ⚠️ Délai entre popups
                    });
                }
                
                processNextItem();
                return;
            }
        }
        
        // Select-all normal (tous champs validés)
        document.querySelectorAll('input[type="checkbox"][name^="product_id"]').forEach(function(checkbox) {
            // ⚠️ CRITIQUE: Définir TOUTES les variables au début pour accessibilité dans if/else
            const productId = checkbox.value;
            const row = checkbox.closest('tr');
            const unallocatedQuantity = parseInt(document.getElementById('unallocated_quantity' + productId).textContent) || 0;
            
            checkbox.checked = isChecked;
            
            if (isChecked) {
                autoScanProduct(row, productId, unallocatedQuantity);
            } else {
                // Reset - RESTORE ORIGINAL VALUES
                const originalQuantity = parseInt(row.dataset.originalQuantity);
                const originalUnallocated = parseInt(row.dataset.originalUnallocated);
                
                if (!isNaN(originalQuantity) && !isNaN(originalUnallocated)) {
                    updateQuantity('quantity', productId, originalQuantity);
                    updateQuantity('unallocated_quantity', productId, originalUnallocated);
                }
                
                updateTotalQuantity(productId);
                
                delete row.dataset.addedQuantity;
                delete row.dataset.originalQuantity;
                delete row.dataset.originalUnallocated;
                
                row.style.backgroundColor = '';
                row.style.color = '';
                row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
                    td.style.backgroundColor = '';
                    td.style.color = '';
                });
            }
        });
        
        if (!isChecked) {
            hasScannedProducts = false;
        }
        
        // ⚠️ UN SEUL son après tout le traitement (pas 100+ sons!)
        if (isChecked) {
            playSuccessSound();
        }
        
        toggleSubmitButton();
    };
}

// Fonction pour mettre à jour l'état du select-all (indeterminate state)
function updateSelectAllState() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    if (selectAllCheckbox) {
        const allCheckboxes = document.querySelectorAll('input[type="checkbox"][name^="product_id"]');
        const checkedCheckboxes = document.querySelectorAll('input[type="checkbox"][name^="product_id"]:checked');
        
        if (allCheckboxes.length > 0) {
            selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
        }
    }
}
```

### 3️⃣ Handler bouton "Apply" modal (OBLIGATOIRE pour callback)
```javascript
// ⚠️ ADAPTER IDs selon votre modal:
// - scan-apply-country → bouton apply
// - scan-country-select → select du champ
// - scanCountryModal → modal ID
// - input-made-in-country-id- → select dans row

document.addEventListener('DOMContentLoaded', function() {
    const applyButton = document.getElementById('scan-apply-country');
    if (applyButton) {
        applyButton.addEventListener('click', function() {
            const selectedValue = document.getElementById('scan-country-select').value;
            const modal = document.getElementById('scanCountryModal');
            const productId = modal.dataset.productId;
            
            if (!selectedValue || selectedValue == '0') {
                alert('Please select a value');
                return;
            }
            
            // Auto-accept future (optionnel)
            const autoAcceptCheckbox = document.getElementById('auto-accept-ai-country');
            if (autoAcceptCheckbox && autoAcceptCheckbox.checked) {
                window.autoAcceptAICountry = true;
            }
            
            // Update field in row
            const fieldInRow = document.getElementById('input-made-in-country-id-' + productId);
            if (fieldInRow) {
                fieldInRow.value = selectedValue;
                
                // Save via AJAX (⚠️ ADAPTER route et params)
                const user_token = document.querySelector('input[name="user_token"]').value;
                
                fetch('index.php?route=shopmanager/product.editMadeInCountry&user_token=' + user_token, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'product_id=' + productId + '&made_in_country_id=' + selectedValue
                })
                .then(response => response.json())
                .then(json => {
                    if (json.success) {
                        // Visual feedback (⚠️ ADAPTER ID cellule)
                        const cell = document.getElementById('check-made-in-country-id-' + productId);
                        if (cell) {
                            cell.style.setProperty('background-color', 'green', 'important');
                        }
                        
                        // ⚠️ CRITIQUE: Blur AVANT fermeture (évite aria-hidden warning)
                        applyButton.blur();
                        
                        // Close modal Bootstrap
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        bsModal.hide();
                        
                        // ⚠️ CRITIQUE: Call callback pour continuer process
                        if (window.scanCallback) {
                            window.scanCallback();
                            window.scanCallback = null;
                        }
                        
                        playSuccessSound();
                    } else {
                        alert('Error: ' + (json.error || 'Unknown error'));
                        playErrorSound();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error saving country');
                    playErrorSound();
                });
            }
        });
    }
});
```

### 4️⃣ Fonction showCountryPopupForScan (déjà existante)
```javascript
// Cette fonction existe déjà dans le module - elle:
// - Affiche popup Bootstrap avec liste pays
// - Propose auto-select via AI (bouton "Let AI select" avec autoAcceptAICountry)
// - Permet sélection manuelle
// - Callback après sélection pour continuer le processus
});
```

**⚠️ ADAPTATIONS NÉCESSAIRES:**
1. **IDs/Classes:** `input[name^="product_id"]`, `#scan-apply-country`, `#scanCountryModal`, etc.
2. **Badges quantités:** `quantity` + `productId`, `unallocated_quantity` + `productId`
3. **Fonctions:** `showCountryPopupForScan()`, `autoScanProduct()`, `toggleSubmitButton()`, `updateQuantity()`, `updateTotalQuantity()`
4. **Route AJAX:** `shopmanager/product.editMadeInCountry` (adapter)
5. **Variables globales:** `hasScannedProducts`, `window.scanCallback`, `autoAcceptAICountry`

**POINTS CRITIQUES:**
- 🎵 **UN son au total** pour select-all (pas un par produit = éviter 100+ AudioContext)
- 💾 **row.dataset.originalQuantity/originalUnallocated** OBLIGATOIRE pour uncheck correct
- ⏱️ **setTimeout 300ms** entre popups (fermeture modal Bootstrap)
- ⏱️ **setTimeout 500ms** avant re-clic select-all final
- 🎯 **applyButton.blur()** AVANT fermeture modal (évite aria-hidden warning)
- 📞 **window.scanCallback()** OBLIGATOIRE pour continuer le process
- 🔄 **Handler Apply OBLIGATOIRE** dans chaque module utilisant popups
- ✅ **AJOUTER unallocated à quantity existante** (pas remplacer!)
- ⏪ **RESTAURER valeurs originales** au uncheck (pas faire calculs inverses)

---

## 📝 NOTES

**CONVENTIONS:**
- Numéroter patterns: #1, #2, #3...
- Indiquer fichiers types concernés
- Donner exemples de code complets
- Mentionner patterns liés/combinables

**AJOUTER UN PATTERN:**
```
"Ajoute pattern: [description]"
```

**UTILISER UN PATTERN:**
```
"Fait le pattern #X pour [fichier]"
```

---

## 🎯 PATTERN #5 - INTÉGRATION AUDIO (sound.js)

**PROBLÈME:** Ajouter des feedbacks sonores (succès, erreur, warning) dans un module

**SOLUTION:**

### 1️⃣ S'assurer que sound.js existe
```javascript
// administrator/view/javascript/shopmanager/sound.js (déjà créé)
function playSuccessSound() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 800;
    oscillator.type = 'sine';
    
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.3);
}

function playErrorSound() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 200;
    oscillator.type = 'sawtooth';
    
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.5);
}

function playWarningSound() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 400;
    oscillator.type = 'square';
    
    gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.4);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.4);
}
```

### 2️⃣ Charger sound.js dans votre Twig (AVANT module.js)
```twig
<script src="view/javascript/shopmanager/sound.js"></script>
<script src="view/javascript/shopmanager/inventory/monmodule.js"></script>
```

### 3️⃣ Utiliser dans votre module JS
```javascript
// Dans votre fichier JS

// Succès (scan OK, save OK, etc.)
function handleSuccess() {
    // ... votre logique
    playSuccessSound(); // ✅ Son de succès
}

// Erreur (validation failed, AJAX error, etc.)
function handleError() {
    // ... votre logique
    playErrorSound(); // ❌ Son d'erreur
}

// Avertissement (champ requis, confirmation, etc.)
function handleWarning() {
    // ... votre logique
    playWarningSound(); // ⚠️ Son d'avertissement
}
```

### 4️⃣ Éviter les multiples sons simultanés
```javascript
// ⚠️ IMPORTANT: Pour select-all ou boucles, UN SEUL son après tout le traitement
function selectAllProducts() {
    products.forEach(function(product) {
        processProduct(product); // Traitement sans son
    });
    
    // Son APRÈS tout le traitement
    playSuccessSound(); // ✅ UN son au total (pas 100+ AudioContext!)
}
```

**QUAND UTILISER:**
- ✅ Succès: Scan produit, sauvegarde réussie, validation OK
- ❌ Erreur: SKU introuvable, erreur AJAX, validation failed
- ⚠️ Warning: Champ requis, confirmation popup, attention utilisateur

**NOTES:**
- Son centralisé dans `/view/javascript/shopmanager/sound.js`
- Pas besoin de dupliquer les fonctions
- Éviter 100+ sons simultanés (AudioContext errors)

---

## 🎯 PATTERN #6 - RÉCONCILIATION NOMS FONCTIONS OPENCART 2→4

**PROBLÈME:** Lors de la migration OpenCart 2 → 4, certains noms de fonctions ont été simplifiés (retrait de "Product" par exemple). Les appels depuis JS/Controller/Twig peuvent pointer vers des noms obsolètes et causer des erreurs.

**CONTEXTE:**
- Migration OpenCart 2.x → OpenCart 4.x
- Simplification des noms: `editProduct()` → `edit()`, `deleteProduct()` → `delete()`
- Fichiers .tpl (OpenCart 2) → .twig (OpenCart 4)
- Nouvelle nomenclature routes, paramètres SQL, structure MVC-L
- Fonctions existantes mais non appelées correctement

**SOLUTION:**

### 1️⃣ MÉTHODOLOGIE DE RÉCONCILIATION

#### A. Identifier les appels cassés
```bash
# Chercher les appels de fonctions dans JS
grep -r "\.editProduct\|\.deleteProduct\|\.addProduct" administrator/view/javascript/

# Chercher dans les Twig
grep -r "editProduct\|deleteProduct\|addProduct" administrator/view/template/

# Comparer avec les vieux .tpl (référence OpenCart 2)
grep -r "function.*Product" administrator/view/template/**/*.tpl
```

#### B. Vérifier l'existence de la fonction dans le Controller
```php
// AVANT de créer une nouvelle fonction, vérifier si elle existe déjà!
// administrator/controller/shopmanager/product.php

class Product extends Controller {
    // Ancien nom (OpenCart 2)
    public function editProduct() { ... }  // ❌ Obsolète
    
    // Nouveau nom (OpenCart 4 - simplifié)
    public function edit() { ... }  // ✅ Nom actuel
}
```

#### C. Mapper les anciens noms → nouveaux noms
```markdown
| Ancien nom (OC2)      | Nouveau nom (OC4) | Fichier                          |
|-----------------------|-------------------|----------------------------------|
| editProduct()         | edit()            | controller/product.php           |
| deleteProduct()       | delete()          | controller/product.php           |
| getProductInfo()      | getInfo()         | model/product.php                |
| updateProductStatus() | updateStatus()    | controller/product.php           |
```

### 2️⃣ CORRECTION DES APPELS

#### Dans JavaScript (allocation.js, product.js, etc.)
```javascript
// ❌ ANCIEN (OpenCart 2)
function saveProduct(productId) {
    $.ajax({
        url: 'index.php?route=shopmanager/product/editProduct&user_token=' + USER_TOKEN,
        // ...
    });
}

// ✅ NOUVEAU (OpenCart 4)
function saveProduct(productId) {
    $.ajax({
        url: 'index.php?route=shopmanager/product.edit&user_token=' + USER_TOKEN,
        // ... (noter le point au lieu du slash dans la route)
    });
}
```

#### Dans Twig (product.twig, allocation.twig, etc.)
```twig
{# ❌ ANCIEN (OpenCart 2) #}
<button onclick="editProduct({{ product_id }})">Edit</button>

{# ✅ NOUVEAU (OpenCart 4) #}
<button onclick="edit({{ product_id }})">Edit</button>
```

#### Dans Controller (product.php)
```php
// ❌ ANCIEN (OpenCart 2 - nom long)
public function editProduct(): void {
    $product_id = $this->request->get['product_id'];
    // ...
}

// ✅ NOUVEAU (OpenCart 4 - nom simplifié)
public function edit(): void {
    $product_id = $this->request->get['product_id'];
    // ... (logique identique, seul le nom change)
}
```

### 3️⃣ DIFFÉRENCES OPENCART 2 vs 4

#### Routes
```php
// OpenCart 2: slash (/)
'index.php?route=catalog/product/edit'

// OpenCart 4: point (.)
'index.php?route=catalog/product.edit'
```

#### Templates
```php
// OpenCart 2: .tpl
view/template/catalog/product_form.tpl

// OpenCart 4: .twig
view/template/catalog/product_form.twig
```

#### Namespaces
```php
// OpenCart 2: Pas de namespace
class ControllerCatalogProduct extends Controller { }

// OpenCart 4: Namespace PSR-4
namespace Opencart\Admin\Controller\Catalog;
class Product extends \Opencart\System\Engine\Controller { }
```

### 4️⃣ CRÉER UN FICHIER TASK POUR TRACKER

**Créer:** `docs/DEV_TASKS.md` ou section dans `DEV_GUIDELINES.md`

```markdown
# TÂCHES MIGRATION OPENCART 2→4

## 🔍 MODULE: PRODUCT

### Controller: administrator/controller/shopmanager/product.php
- [ ] Vérifier tous les noms de méthodes publiques
- [ ] Mapper anciens noms → nouveaux noms
- [ ] Documenter les changements de paramètres SQL

### JavaScript: administrator/view/javascript/shopmanager/product.js
- [ ] Grep tous les appels AJAX ($.ajax, $.post)
- [ ] Vérifier les routes (slash → point)
- [ ] Vérifier les noms de fonctions appelées

### Twig: administrator/view/template/shopmanager/product*.twig
- [ ] Comparer avec .tpl originaux
- [ ] Vérifier onclick, onchange inline
- [ ] Vérifier les noms de fonctions JS appelées

### Résultats:
```
| Fichier           | Ligne | Ancien nom       | Nouveau nom | Status |
|-------------------|-------|------------------|-------------|--------|
| product.js        | 145   | editProduct()    | edit()      | ✅ Fix |
| allocation.twig   | 84    | deleteProduct()  | delete()    | ✅ Fix |
```

## 🔍 MODULE: ALLOCATION
(même structure)
```

### 5️⃣ WORKFLOW DE VÉRIFICATION

```bash
# 1. Trouver tous les .tpl (référence OpenCart 2)
find administrator/ -name "*.tpl" -type f

# 2. Pour chaque .tpl, comparer avec .twig correspondant
diff administrator/view/template/old/product.tpl \
     administrator/view/template/product.twig

# 3. Extraire les noms de fonctions JS des .tpl
grep -oP "function \w+\(" administrator/view/template/**/*.tpl | sort -u

# 4. Chercher ces fonctions dans les .js actuels
grep -r "function oldFunctionName" administrator/view/javascript/

# 5. Si fonction manquante, chercher dans controller PHP
grep -r "public function oldFunctionName" administrator/controller/
```

### 6️⃣ RÈGLES D'OR

1. **NE PAS CRÉER** de nouvelle fonction sans avoir fouillé les fichiers existants
2. **TOUJOURS COMPARER** avec les vieux .tpl (référence OpenCart 2)
3. **VÉRIFIER** les routes: `/` (OC2) → `.` (OC4)
4. **DOCUMENTER** chaque mapping dans le fichier TASK
5. **TESTER** après chaque correction (erreurs console JS, erreurs PHP)
6. **ATTENTION** aux mécanismes différents (SQL, paramètres, validation)

### 7️⃣ EXEMPLE COMPLET: RÉCONCILIATION edit()

#### Étape 1: Identifier le problème
```javascript
// Dans allocation.js - ligne 234
function saveCountry(productId, countryId) {
    $.ajax({
        url: 'index.php?route=shopmanager/product/editMadeInCountry', // ❌ 404 Error
        // ...
    });
}
```

#### Étape 2: Chercher dans le controller
```bash
grep -n "function.*MadeInCountry" administrator/controller/shopmanager/product.php
# Résultat: 456:    public function editMadeInCountry(): void {
```

#### Étape 3: Vérifier la route
```php
// Dans product.php - ligne 456
public function editMadeInCountry(): void {  // ✅ Fonction existe!
    $product_id = $this->request->post['product_id'];
    // ...
}
```

#### Étape 4: Corriger l'appel (route OC4)
```javascript
// Dans allocation.js - ligne 234 (CORRIGÉ)
function saveCountry(productId, countryId) {
    $.ajax({
        url: 'index.php?route=shopmanager/product.editMadeInCountry&user_token=' + USER_TOKEN,
        // ✅ Utilise le point au lieu du slash pour OC4
        // ...
    });
}
```

**QUAND UTILISER:**
- ✅ Appels JS cassés (404, fonction not found)
- ✅ Migration OpenCart 2 → 4 en cours
- ✅ Erreurs "undefined function" dans console
- ✅ Routes qui retournent 404
- ✅ Avant de créer une nouvelle fonction

**OUTILS UTILES:**
```bash
# Trouver tous les appels AJAX
grep -rn "\.ajax\|\.post\|\.get" administrator/view/javascript/

# Trouver toutes les routes
grep -rn "route=" administrator/view/

# Comparer structures OC2 vs OC4
diff -r old_opencart2/ administrator/ | grep "function"
```

---

## 🎯 PATTERN #7 - DEBUGGING JS/API AVEC LOGS

**PROBLÈME:** Un module (product_list, allocation, etc.) ne fonctionne plus. Erreurs API ou JS invisibles. Besoin de tracer l'exécution pour identifier le problème.

**CONTEXTE:**
- Code fonctionnait avant, maintenant cassé
- Erreurs silencieuses (pas de message dans console)
- Appels AJAX qui échouent sans indication
- Besoin de logs structurés pour debugger

**SOLUTION:**

### 1️⃣ AJOUTER UN SYSTÈME DE DEBUG LOGGING

#### Au début du fichier JS (exemple: product_list.js)
```javascript
// ============================================
// DEBUG LOGGING UTILITY (Pattern #7)
// ============================================
const DEBUG = true; // Set to false in production

function debugLog(message, data = null, type = 'info') {
    if (!DEBUG) return;
    
    const timestamp = new Date().toISOString().split('T')[1].slice(0, -1);
    const prefix = `[MODULE_NAME ${timestamp}]`;
    
    switch(type) {
        case 'error':
            console.error(`${prefix} ❌ ${message}`, data || '');
            break;
        case 'warn':
            console.warn(`${prefix} ⚠️  ${message}`, data || '');
            break;
        case 'success':
            console.log(`${prefix} ✅ ${message}`, data || '');
            break;
        default:
            console.log(`${prefix} ℹ️  ${message}`, data || '');
    }
}

function debugAjax(action, url, data) {
    debugLog(`AJAX ${action} START`, { url, data });
}

function debugAjaxSuccess(action, response) {
    debugLog(`AJAX ${action} SUCCESS`, response, 'success');
}

function debugAjaxError(action, error) {
    debugLog(`AJAX ${action} ERROR`, error, 'error');
}

// Log initialization
debugLog('MODULE loaded successfully');
```

### 2️⃣ INSTRUMENTER LES FONCTIONS CRITIQUES

#### Fonctions principales
```javascript
function myFunction(param1, param2) {
    debugLog('myFunction called', { param1, param2 });
    
    // Vérifier les variables critiques
    var user_token = document.querySelector('input[name="user_token"]').value;
    debugLog('user_token retrieved', { 
        user_token: user_token ? 'FOUND' : 'NOT_FOUND' 
    });
    
    if (!user_token) {
        debugLog('user_token is missing!', null, 'error');
        return;
    }
    
    // Suite de la fonction...
}
```

#### Appels AJAX
```javascript
// AVANT le $.ajax
debugAjax('updateProduct', 'index.php?route=shopmanager/product.update', {
    product_id: productId,
    quantity: newQuantity
});

$.ajax({
    url: 'index.php?route=shopmanager/product.update&user_token=' + user_token,
    type: 'post',
    data: {
        product_id: productId,
        quantity: newQuantity
    },
    dataType: 'json',
    success: function(response) {
        debugAjaxSuccess('updateProduct', response);
        
        if (response.success) {
            debugLog('Product updated successfully', response, 'success');
            // Traitement...
        } else {
            debugLog('API returned error', response.error, 'error');
        }
    },
    error: function(xhr, status, error) {
        debugAjaxError('updateProduct', {
            status: xhr.status,
            statusText: xhr.statusText,
            responseText: xhr.responseText,
            error: error
        });
        alert('Erreur API: ' + error);
    }
});
```

#### Event Listeners
```javascript
$(document).on('click', '.my-button', function(event) {
    debugLog('Button clicked', { 
        target: event.target,
        currentTarget: event.currentTarget 
    });
    
    var productId = $(this).attr('data-product-id');
    debugLog('Product ID extracted', { productId });
    
    if (!productId) {
        debugLog('Product ID is missing!', null, 'error');
        return;
    }
    
    // Suite...
});
```

### 3️⃣ VÉRIFIER LES ÉLÉMENTS DOM

```javascript
function checkDomElements(productId) {
    debugLog('Checking DOM elements for product', { productId });
    
    var elements = {
        quantity: $(`#quantity-${productId}`),
        location: $(`#location-${productId}`),
        country: $(`#input-made-in-country-id-${productId}`)
    };
    
    Object.keys(elements).forEach(key => {
        if (elements[key].length === 0) {
            debugLog(`Element ${key} NOT FOUND`, null, 'error');
        } else {
            debugLog(`Element ${key} FOUND`, { 
                value: elements[key].val() || elements[key].text() 
            });
        }
    });
    
    return elements;
}
```

### 4️⃣ LOGGER LES ERREURS RÉSEAU

```javascript
// Intercepter toutes les erreurs AJAX globalement
$(document).ajaxError(function(event, jqXHR, settings, thrownError) {
    debugLog('Global AJAX Error', {
        url: settings.url,
        type: settings.type,
        status: jqXHR.status,
        statusText: jqXHR.statusText,
        responseText: jqXHR.responseText,
        error: thrownError
    }, 'error');
});

// Intercepter toutes les erreurs JS globalement
window.addEventListener('error', function(event) {
    debugLog('JavaScript Error', {
        message: event.message,
        filename: event.filename,
        lineno: event.lineno,
        colno: event.colno,
        error: event.error
    }, 'error');
});
```

### 5️⃣ VÉRIFIER LES ROUTES ET TOKENS

```javascript
function validateRequest() {
    debugLog('Validating request prerequisites');
    
    // 1. Vérifier user_token
    var tokenInput = document.querySelector('input[name="user_token"]');
    if (!tokenInput) {
        debugLog('user_token INPUT NOT FOUND in DOM', null, 'error');
        return false;
    }
    
    var user_token = tokenInput.value;
    if (!user_token || user_token.trim() === '') {
        debugLog('user_token is EMPTY', null, 'error');
        return false;
    }
    debugLog('user_token validated', { token_length: user_token.length });
    
    // 2. Vérifier route format (OpenCart 4 uses . not /)
    var testRoute = 'index.php?route=shopmanager/product.update';
    if (testRoute.includes('/product/')) {
        debugLog('WRONG route format! Use . not /', { testRoute }, 'warn');
    }
    
    return true;
}
```

### 6️⃣ DEBUGGING PHP CONTROLLER (côté serveur)

#### Dans le controller PHP
```php
// Au début de la méthode
error_log("[PRODUCT_CONTROLLER] editMadeInCountry called with product_id: " . $this->request->post['product_id']);

try {
    $product_id = $this->request->post['product_id'];
    $made_in_country_id = $this->request->post['made_in_country_id'];
    
    error_log("[PRODUCT_CONTROLLER] Data received: " . json_encode([
        'product_id' => $product_id,
        'made_in_country_id' => $made_in_country_id
    ]));
    
    // Traitement...
    
    $this->model_shopmanager_product->updateCountry($product_id, $made_in_country_id);
    
    error_log("[PRODUCT_CONTROLLER] Update successful");
    
    $this->response->setOutput(json_encode([
        'success' => true,
        'message' => 'Country updated'
    ]));
    
} catch (Exception $e) {
    error_log("[PRODUCT_CONTROLLER] ERROR: " . $e->getMessage());
    
    $this->response->setOutput(json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]));
}
```

#### Vérifier les logs PHP
```bash
# Voir les logs en temps réel
tail -f /var/log/apache2/error.log
# ou
tail -f /var/log/php_errors.log

# Filtrer par module
grep "PRODUCT_CONTROLLER" /var/log/apache2/error.log
```

### 7️⃣ CHECKLIST DE DEBUGGING

```markdown
# CHECKLIST DEBUGGING MODULE

## Frontend (JavaScript)
- [ ] `const DEBUG = true` activé en haut du fichier JS
- [ ] `debugLog('module loaded')` s'affiche dans console
- [ ] Fonctions critiques ont `debugLog()` au début
- [ ] Tous les AJAX ont `debugAjax()` avant l'appel
- [ ] `success` callback a `debugAjaxSuccess()`
- [ ] `error` callback a `debugAjaxError()`
- [ ] Vérifier `user_token` existe dans DOM
- [ ] Vérifier routes utilisent `.` pas `/` (OpenCart 4)

## Backend (PHP)
- [ ] `error_log()` au début de chaque méthode publique
- [ ] Logger les données reçues (`$this->request->post`)
- [ ] Logger avant appels model
- [ ] Logger après appels model
- [ ] Try/catch autour du code critique
- [ ] Response JSON avec `success` ou `error`

## Network (Browser DevTools)
- [ ] Ouvrir DevTools > Network
- [ ] Filter par XHR/Fetch
- [ ] Vérifier statut HTTP (200, 404, 500, etc.)
- [ ] Vérifier Request Payload
- [ ] Vérifier Response (JSON valide?)
- [ ] Vérifier Headers (Content-Type, etc.)

## Console Errors
- [ ] Ouvrir DevTools > Console
- [ ] Aucune erreur rouge ?
- [ ] Warnings jaunes expliqués ?
- [ ] Logs de debug visibles ?
- [ ] Erreurs de syntaxe JS ?
```

### 8️⃣ PROBLÈMES COURANTS ET SOLUTIONS

| Problème | Symptôme | Solution |
|----------|----------|----------|
| Route 404 | AJAX retourne 404 | Vérifier route utilise `.` pas `/` (OC4) |
| Token manquant | Alert "token not found" | Vérifier `<input name="user_token">` existe |
| Element not found | `$(...).length === 0` | Vérifier ID/class exact dans HTML |
| JSON parse error | `JSON.parse()` fail | Vérifier `dataType: 'json'` dans AJAX |
| Silent failure | Aucune erreur visible | Ajouter `debugLog()` partout |
| CORS error | Network error | Vérifier même domaine/protocole |
| 500 Internal Error | PHP fatal error | Checker error.log PHP |
| Infinite loop | Browser freeze | Ajouter compteur + `debugLog()` |

### 9️⃣ EXEMPLE COMPLET: DEBUGGING editMadeInCountry()

#### JavaScript (product_list.js)
```javascript
function editMadeInCountry(product_id) {
    debugLog('editMadeInCountry called', { product_id });
    
    // 1. Vérifier token
    var tokenInput = document.querySelector('input[name="user_token"]');
    if (!tokenInput) {
        debugLog('user_token INPUT not found!', null, 'error');
        alert('Token input not found');
        return;
    }
    
    var user_token = tokenInput.value;
    debugLog('user_token retrieved', { 
        exists: !!user_token,
        length: user_token ? user_token.length : 0 
    });
    
    // 2. Vérifier select country
    var countrySelect = document.getElementById('input-made-in-country-id-' + product_id);
    if (!countrySelect) {
        debugLog('Country select not found!', { product_id }, 'error');
        alert('Select element not found');
        return;
    }
    
    var made_in_country_id = countrySelect.value;
    debugLog('Country selected', { made_in_country_id });
    
    // 3. AJAX call
    var ajaxUrl = 'index.php?route=shopmanager/product.editMadeInCountry&user_token=' + user_token;
    debugAjax('editMadeInCountry', ajaxUrl, { product_id, made_in_country_id });
    
    $.ajax({
        url: ajaxUrl,
        method: 'POST',
        data: {
            product_id: product_id,
            made_in_country_id: made_in_country_id
        },
        dataType: 'json',
        success: function(json) {
            debugAjaxSuccess('editMadeInCountry', json);
            
            if (json.error) {
                debugLog('API returned error', json.error, 'error');
                alert('Error: ' + json.error);
                return;
            }
            
            if (json.success) {
                debugLog('Country updated successfully', null, 'success');
                // Update UI...
            }
        },
        error: function(xhr, status, error) {
            debugAjaxError('editMadeInCountry', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            alert('AJAX Error: ' + error);
        }
    });
}
```

#### PHP (controller/shopmanager/product.php)
```php
public function editMadeInCountry(): void {
    error_log("[PRODUCT] editMadeInCountry START");
    
    try {
        if (!isset($this->request->post['product_id'])) {
            error_log("[PRODUCT] ERROR: product_id missing");
            $this->response->setOutput(json_encode([
                'success' => false,
                'error' => 'product_id is required'
            ]));
            return;
        }
        
        $product_id = (int)$this->request->post['product_id'];
        $made_in_country_id = (int)$this->request->post['made_in_country_id'];
        
        error_log("[PRODUCT] Data: " . json_encode([
            'product_id' => $product_id,
            'made_in_country_id' => $made_in_country_id
        ]));
        
        $this->load->model('shopmanager/product');
        $this->model_shopmanager_product->updateMadeInCountry($product_id, $made_in_country_id);
        
        error_log("[PRODUCT] Update successful");
        
        $this->response->setOutput(json_encode([
            'success' => true,
            'message' => 'Country updated successfully'
        ]));
        
    } catch (Exception $e) {
        error_log("[PRODUCT] EXCEPTION: " . $e->getMessage());
        
        $this->response->setOutput(json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]));
    }
}
```

**QUAND UTILISER:**
- ✅ Module ne fonctionne plus (marchait avant)
- ✅ Erreurs silencieuses (pas de message)
- ✅ AJAX échoue sans indication
- ✅ Besoin de tracer l'exécution
- ✅ Debugging après migration OpenCart
- ✅ Avant de demander de l'aide (avoir des logs!)

**OUTILS:**
- Browser DevTools (Console, Network, Sources)
- PHP error_log + tail -f
- console.log/error/warn
- try/catch blocks
- JSON.stringify() pour objects

**DÉSACTIVER EN PRODUCTION:**
```javascript
const DEBUG = false; // Désactive tous les debugLog()
```

---

## 🎯 PATTERN #8 - CORRECTION NAMESPACE OPENCART 4 (Classes PHP Globales)

**PROBLÈME:** Erreur "Class not found" lors de l'utilisation de classes PHP globales dans un namespace OpenCart 4

**EXEMPLE D'ERREUR:**
```
Error: Class "Opencart\Admin\Controller\Shopmanager\RecursiveIteratorIterator" not found
```

**CAUSE:**
OpenCart 4 utilise des namespaces PHP. Quand vous utilisez une classe globale PHP (comme `DateTime`, `Exception`, `RecursiveIteratorIterator`, etc.) sans préfixe, PHP la cherche dans le namespace actuel au lieu du namespace global.

**SOLUTION:**

### ❌ AVANT (OpenCart 2 - Ne fonctionne pas en OC4):
```php
<?php
namespace Opencart\Admin\Controller\Shopmanager;

class Product extends \Opencart\System\Engine\Controller {
    public function updateQuantity() {
        // ❌ ERREUR: PHP cherche Opencart\Admin\Controller\Shopmanager\RecursiveIteratorIterator
        $errors = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
        
        // ❌ ERREUR: PHP cherche Opencart\Admin\Controller\Shopmanager\DateTime
        $date = new DateTime();
        
        // ❌ ERREUR: PHP cherche Opencart\Admin\Controller\Shopmanager\Exception
        throw new Exception("Error message");
    }
}
```

### ✅ APRÈS (OpenCart 4 - Correct):
```php
<?php
namespace Opencart\Admin\Controller\Shopmanager;

class Product extends \Opencart\System\Engine\Controller {
    public function updateQuantity() {
        // ✅ CORRECT: Backslash \ indique le namespace global
        $errors = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($data));
        
        // ✅ CORRECT: DateTime du namespace global
        $date = new \DateTime();
        
        // ✅ CORRECT: Exception du namespace global
        throw new \Exception("Error message");
    }
}
```

**CLASSES PHP GLOBALES COURANTES À PRÉFIXER:**

```php
// Itérateurs
new \RecursiveIteratorIterator()
new \RecursiveArrayIterator()
new \ArrayIterator()
new \DirectoryIterator()

// Date/Heure
new \DateTime()
new \DateTimeZone()
new \DateInterval()

// Exceptions
new \Exception()
new \RuntimeException()
new \InvalidArgumentException()

// XML/DOM
new \DOMDocument()
new \SimpleXMLElement()
new \XMLReader()

// Autres classes courantes
new \stdClass()
new \PDO()
new \SplFileObject()
new \ReflectionClass()
```

**DÉTECTION RAPIDE:**

```bash
# Chercher classes globales potentiellement sans backslash dans un fichier
grep -n "new DateTime\|new Exception\|new RecursiveIteratorIterator\|new stdClass" fichier.php | grep -v "new \\\\"

# Chercher dans tout un dossier
find controller/ -name "*.php" -exec grep -l "new DateTime\|new Exception" {} \;
```

**RÈGLE SIMPLE:**
- ✅ Classe OpenCart (avec namespace) → Pas de backslash: `new ModelCatalogProduct()`
- ✅ Classe PHP globale → Backslash obligatoire: `new \DateTime()`

**MIGRATION OC2 → OC4:**
Chercher et remplacer systématiquement:
- `new RecursiveIteratorIterator(` → `new \RecursiveIteratorIterator(`
- `new DateTime(` → `new \DateTime(`
- `new Exception(` → `new \Exception(`
- etc.

**⚠️ ATTENTION:**
Ne pas confondre avec les classes OpenCart qui elles utilisent le namespace complet:
```php
// ❌ FAUX
new \Opencart\System\Engine\Controller();

// ✅ CORRECT (namespace relatif ou complet sans \ au début)
new \Opencart\System\Engine\Controller(); // OK si besoin de spécifier depuis root
// OU utiliser 'use' statement
```

---

## 🎯 PATTERN #9 - EVENT DELEGATION POUR AJAX RELOAD (403 Forbidden Fix)

**PROBLÈME:**
- Formulaire avec `data-oc-toggle="ajax"` envoie TOUTES les données de TOUS les produits
- Serveur bloque avec 403 Forbidden (payload trop large)
- Event listeners perdus après reload AJAX de la table

**SOLUTION:**

### ⚠️ VARIABLES À ADAPTER:
```javascript
'form-product'           // ⚠️ ADAPTER: ID du formulaire
'product.enable'         // ⚠️ ADAPTER: Route à intercepter
'product.disable'        // ⚠️ ADAPTER: Route à intercepter  
'product_id'             // ⚠️ ADAPTER: Paramètre à extraire
'made_in_country_id_'    // ⚠️ ADAPTER: Champs additionnels du produit
```

### 1️⃣ JavaScript - Event Delegation (product_list.js)

```javascript
// ============================================
// FIX 403 FORBIDDEN - Filter form data for enable/disable actions
// ============================================
// EVENT DELEGATION: Écouter sur document (ne se reload jamais)
// au lieu d'attacher sur le form (qui se recharge)
(function() {
    // Flag pour éviter d'initialiser plusieurs fois
    if (window._productFormFilterInitialized) {
        return;
    }
    window._productFormFilterInitialized = true;
    
    console.log('🎯 Initializing product form filter with event delegation');
    
    // Écouter sur document avec event delegation
    document.addEventListener('submit', function(e) {
        // ⚠️ ADAPTER: Vérifier si c'est le bon form
        if (e.target.id !== 'form-product') {
            return;
        }
        
        const form = e.target;
        const submitter = e.submitter;
        if (!submitter) return;
        
        const action = submitter.getAttribute('formaction');
        if (!action) return;
        
        // ⚠️ ADAPTER: Vérifier si c'est l'action à intercepter
        if (action.includes('product.enable') || action.includes('product.disable')) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            console.log('🎯 Intercepted enable/disable submit');
            
            // ⚠️ ADAPTER: Extraire les paramètres de l'URL
            const urlParams = new URLSearchParams(action.split('?')[1]);
            const productId = urlParams.get('product_id');
            const userToken = urlParams.get('user_token');
            
            if (!productId) {
                console.error('❌ No product_id found in action URL');
                return;
            }
            
            console.log('✅ Product ID:', productId);
            
            // Construire les données MINIMALES (seulement ce produit)
            const formData = new FormData();
            formData.append('product_id', productId);
            
            // ⚠️ ADAPTER: Ajouter champs spécifiques au produit si nécessaires
            const madeInField = form.querySelector('[name="made_in_country_id_' + productId + '"]');
            if (madeInField) {
                formData.append('made_in_country_id_' + productId, madeInField.value);
            }
            
            // ⚠️ ADAPTER: Ajouter autres champs dynamiques (URLs, etc.)
            form.querySelectorAll('[name^="url_product_' + productId + '_"]').forEach(function(field) {
                formData.append(field.name, field.value);
            });
            
            form.querySelectorAll('[name^="marketplace_name_' + productId + '_"]').forEach(function(field) {
                formData.append(field.name, field.value);
            });
            
            console.log('📤 Sending filtered data for product:', productId);
            
            // Envoyer la requête AJAX
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('✅ Response:', data);
                if (data.success || data.redirect) {
                    // Recharger la page
                    location.reload();
                } else if (data.error) {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('❌ AJAX Error:', error);
                alert('Error: ' + error.message);
            });
        }
    }, true); // useCapture=true pour intercepter AVANT common.js
    
    console.log('✅ Product form filter initialized with event delegation');
})();
```

### 2️⃣ Template Twig - Callbacks après AJAX load

```twig
<script type="text/javascript">
    // Dynamic content loading for table headers and pagination
    $('#product').on('click', 'thead a, .pagination a', function(e) {
        e.preventDefault();
        
        var url = this.href;
        
        $('#product').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><br/>Loading...</div>');
        
        // ⚠️ ADAPTER: Après reload, réinitialiser les fonctions nécessaires
        $('#product').load(url, function() {
            $('html, body').animate({ scrollTop: $('#product').offset().top - 100 }, 300);
            
            // Réinitialiser image preview
            if (typeof window.reinitImagePreview === 'function') {
                window.reinitImagePreview();
            }
            
            // Réinitialiser image resolution check
            if (typeof window.initImageResolutionCheck === 'function') {
                window.initImageResolutionCheck();
            }
            
            // ⚠️ NOTE: PAS besoin de réinitialiser le form filter!
            // Event delegation sur document = listener permanent!
        });
    });
    
    // Filter button
    $('#button-filter').on('click', function() {
        var url = ''; // ... build URL
        
        window.history.pushState({}, null, 'index.php?route=shopmanager/product&user_token={{ user_token }}' + url);
        
        $('#product').load('index.php?route=shopmanager/product.list&user_token={{ user_token }}' + url, function() {
            // Même callbacks que ci-dessus
            if (typeof window.reinitImagePreview === 'function') {
                window.reinitImagePreview();
            }
            if (typeof window.initImageResolutionCheck === 'function') {
                window.initImageResolutionCheck();
            }
        });
    });
</script>
```

### 3️⃣ Template - Boutons avec formaction

```twig
{# ⚠️ ADAPTER: Bouton avec formaction contenant product_id #}
<button type="submit" 
        form="form-product" 
        formaction="{% if product.status == 0 %}{{ product.enabled }}{% else %}{{ product.disabled }}{% endif %}" 
        class="btn btn-sm">
    {% if product.status == 0 %}<i class="fa-solid fa-ban"></i>{% else %}<i class="fa-solid fa-check"></i>{% endif %}
</button>
```

### 📌 POINTS CRITIQUES

**1. Event Delegation:**
- ✅ Listener attaché sur `document` (permanent)
- ✅ Vérifie `e.target.id` dynamiquement
- ✅ Form peut se recharger 1000 fois → Listener reste actif!
- ❌ Ne PAS attacher sur le form directement (perdu au reload)

**2. IIFE (Immediately Invoked Function Expression):**
```javascript
(function() {
    // Code s'exécute immédiatement 1 seule fois
    if (window._flagName) return; // Évite doublons
    window._flagName = true;
    // ... code
})();
```

**3. Éviter double chargement JS:**
- ❌ Controller: `$this->document->addScript('file.js')`
- ❌ Template: `<script src="file.js?v={{ random() }}">`
- ✅ Garder SEULEMENT dans controller OU template (pas les 2!)

**4. useCapture = true:**
```javascript
document.addEventListener('submit', handler, true); // Capture phase
// Intercepte AVANT que common.js (OpenCart) traite le submit
```

**5. Filtrage des données:**
- Envoyer SEULEMENT les champs du produit concerné
- Extraire `product_id` de l'URL `formaction`
- Construire FormData minimal (évite 403 Forbidden)

### 🎯 QUAND UTILISER CE PATTERN

✅ **Utilisez-le quand:**
- Formulaire AJAX avec `data-oc-toggle="ajax"`
- Table qui reload dynamiquement (pagination, tri, filtre)
- Boutons avec `formaction` spécifique à chaque ligne
- Erreur 403 Forbidden (payload trop large)
- Event listeners perdus après reload AJAX

❌ **N'utilisez PAS quand:**
- Page sans reload AJAX (event listeners normaux OK)
- Un seul élément à traiter (pas de liste)
- Form simple sans données massives

### 🔗 PATTERNS LIÉS

- Pattern #7 (Debugging) → Ajouter logs pour tracer les calls
- Pattern #2 (Déplacer JS) → Organiser le code proprement

---

## 🎯 PATTERN #10 - RÉUTILISATION MODELS OC4 (DRY Principe)

**DATE:** 8 janvier 2026  
**CONTEXTE:** Migration OpenCart 2→4, élimination code dupliqué  
**FICHIERS:** administrator/model/shopmanager/product_search.php, product.php

### 🔥 PROBLÈME

**Duplication de code entre models:**
```php
// ❌ MAUVAIS: Wrapper inutile dans product_search.php model
public function editProduct($product_id, $data) {
    $this->load->model('shopmanager/product');
    
    // Requête SQL qui duplique celle de product.php
    $this->db->query("UPDATE " . DB_PREFIX . "product SET 
        model = '" . $this->db->escape($data['model']) . "',
        price = '" . (float)$data['price'] . "',
        // ... 15 autres champs ...
        WHERE product_id = '" . (int)$product_id . "'");
    
    // Puis appelle le model product
    $this->model_shopmanager_product->editProduct($product_id, $data);
    
    return $data;
}
```

**Problèmes:**
- Code dupliqué (même SQL dans 2 fichiers)
- Maintenance cauchemar (corriger 2 fois)
- Wrapper sans valeur ajoutée
- Viole principe DRY (Don't Repeat Yourself)

### ✅ SOLUTION: Appel Direct au Model Principal

**1️⃣ Supprimer les wrappers inutiles**

```php
// ✅ BON: Supprimer complètement du product_search.php model
// AVANT: 70 lignes de wrappers
// APRÈS: 0 lignes - commentaire explicatif uniquement

// Pattern OC4: Utiliser directement les fonctions de product.php model
// editProduct() et editDescription() supprimés - appeler model_shopmanager_product directement
```

**2️⃣ Appeler directement dans le controller**

```php
// ✅ BON: Dans product_search.php controller
public function getSearchData() {
    $this->load->model('shopmanager/product');
    
    // Préparer les données
    $data = [...];
    
    // Appel DIRECT au model principal
    $this->model_shopmanager_product->editProduct($product_id, $data);
    
    // Plus besoin de wrapper intermédiaire!
}
```

**3️⃣ Simplifier la récupération de données**

```php
// ❌ MAUVAIS: Assignation champ par champ (130+ lignes)
$data['model'] = $product_info['model'];
$data['sku'] = $product_info['sku'];
$data['upc'] = $product_info['upc'];
$data['ean'] = $product_info['ean'];
// ... 25 autres champs ...

// ✅ BON: Pattern OC4 avec null coalescing (88 lignes)
$data['model'] = $product_info['model'] ?? 'n/a';
$data['sku'] = $product_info['sku'] ?? '';
$data['upc'] = $product_info['upc'] ?? '';
$data['ean'] = $product_info['ean'] ?? '';
// Valeurs par défaut intégrées
```

### 📋 CHECKLIST PATTERN #10

**Avant de créer un wrapper de model:**

- [ ] La fonction existe déjà dans le model principal?
- [ ] Le wrapper ajoute vraiment de la valeur?
- [ ] Impossible d'appeler directement le model principal?
- [ ] Le wrapper fait plus que juste rediriger?

**Si NON à 2+ questions → APPELER DIRECTEMENT le model principal**

### 🔄 MIGRATION OC4: getSpecials() → getDiscounts()

**OpenCart 4 a changé le système de prix spéciaux:**

```php
// ❌ OBSOLÈTE OC2/OC3: Table product_special séparée
$specials = $this->model_shopmanager_product->getSpecials($product_id);

// ✅ OC4: Table product_discount avec champ special (bool)
$discounts = $this->model_shopmanager_product->getDiscounts($product_id);

foreach ($discounts as $discount) {
    // Champ special=1 indique un prix spécial
    if ($discount['special'] == 1) {
        $special_price = $discount['price'];
    }
}
```

**Structure OC4:**
```sql
-- OC4: Une seule table uniforme
CREATE TABLE product_discount (
  product_id INT,
  customer_group_id INT,
  quantity INT,
  priority INT,
  price DECIMAL,
  type VARCHAR(50),
  special TINYINT(1),  -- ← Différencie special vs discount
  date_start DATE,
  date_end DATE
)
```

### 🎯 RÉSULTATS APPLIQUÉS

**Fichiers modifiés:**
- `administrator/model/shopmanager/product_search.php`
- `administrator/controller/shopmanager/product_search.php`
- `administrator/model/shopmanager/product.php`

**Métriques:**
- ❌ **Supprimé:** 112 lignes code dupliqué
- ✅ **Simplifié:** getSearchData() 130→88 lignes (-32%)
- ✅ **Unifié:** 1 seule fonction editProduct() dans tout le système
- ✅ **Modernisé:** getSpecials() → getDiscounts() (OC4)

### ✅ UTILISEZ CE PATTERN QUAND:

- Migration OpenCart 2→4
- Code dupliqué entre models détecté
- Wrapper qui ne fait que rediriger
- Besoin de simplifier récupération données

❌ **N'utilisez PAS quand:**
- Wrapper ajoute vraie valeur (transformation, validation)
- Logique métier spécifique au contexte
- Besoin d'isolation pour tests

### 🔗 PATTERNS LIÉS

- Pattern #6 (Réconciliation OC2→4) → Identifier noms fonctions changés
- Pattern #8 (Namespace OC4) → Classes et structure PHP
- OPENCART_GUIDELINES.md → Principe DRY et architecture MVC-L

---

## 🎯 PATTERN #11 - NETTOYAGE CODE (Commentaires + Indentation)

**PROBLÈME:** Fichiers PHP avec commentaires debug non pertinents et indentation incohérente (tabs/espaces mixtes)

**SOLUTION:**

### ⚠️ VARIABLES À ADAPTER:
```bash
# ⚠️ ADAPTER: Chemin vers le fichier PHP à nettoyer
FILE_PATH="/path/to/your/file.php"

# ⚠️ ADAPTER: Patterns de debug à supprimer selon votre projet
# Exemples: print_r, var_dump, echo debug, console.log (dans PHP inline), etc.
```

### 📋 PROCÉDURE COMPLÈTE

#### 1️⃣ **TOUJOURS CRÉER BACKUP AVANT**
```bash
# CRITIQUE: Ne jamais modifier sans backup!
cp "$FILE_PATH" "$FILE_PATH.backup"

# Vérifier que backup existe
ls -lh "$FILE_PATH.backup"
```

#### 2️⃣ **SUPPRIMER COMMENTAIRES DEBUG (via outils)**

Utiliser `replace_string_in_file` ou `multi_replace_string_in_file` pour:

```php
// ❌ SUPPRIMER ce type de commentaires:
//print("<pre>".print_r($data, true)."</pre>");
//var_dump($result);
// print_r($array, true);
// echo "DEBUG: " . $variable;

// ✅ GARDER les commentaires utiles:
/**
 * Description de la fonction
 * @param int $id
 * @return array
 */
// Section importante
// TODO: À implémenter plus tard
```

**Patterns courants à retirer:**
- `//print(` ou `// print(`
- `//var_dump(` ou `// var_dump(`
- `//echo "DEBUG` ou `// echo "DEBUG`
- Commentaires sections répétitifs: `// Image`, `// Category`, `// Product`
- Commentaires expliquant l'évident: `// Ajouter product_id à selected`

**Patterns à GARDER:**
- PHPDoc: `/** ... */`
- TODOs: `// TODO:`, `// FIXME:`
- Explications complexes de logique métier
- Références importantes: `// See: https://...`

#### 3️⃣ **NETTOYER INDENTATION (sed automatique)**

```bash
cd /path/to/directory

# ⚠️ ADAPTER: Chemin fichier
FILE="administrator/controller/module/file.php"

# 1. Corriger }else { → } else {
sed -i 's/}else {/} else {/g' "$FILE"

# 2. Corriger if( → if (
sed -i 's/if(/if (/g' "$FILE"
sed -i 's/}elseif(/} elseif (/g' "$FILE"

# 3. Remplacer 8 espaces par tab (début ligne)
sed -i 's/^        /\t\t/g' "$FILE"

# 4. Remplacer 4 espaces par tab (début ligne)
sed -i 's/^    /\t/g' "$FILE"

# 5. Nettoyer tabs + espaces en fin de ligne
sed -i 's/\t\s*$//' "$FILE"

# 6. Supprimer espaces multiples en fin de ligne
sed -i 's/\s\+$//' "$FILE"

# 7. Nettoyer lignes vides multiples (garder max 2)
sed -i '/^$/N;/^\n$/D' "$FILE"

echo "✅ Nettoyage indentation terminé"
```

#### 4️⃣ **VALIDATION CRITIQUE**

```bash
# TOUJOURS valider syntaxe PHP après modifications!
php -l "$FILE"

# Si erreur: restaurer backup immédiatement
# cp "$FILE.backup" "$FILE"

# Comparer nombre de lignes avant/après
wc -l "$FILE" "$FILE.backup"

# Vérifier changements avec diff
diff "$FILE.backup" "$FILE" | head -100
```

#### 5️⃣ **VÉRIFICATION FONCTIONNELLE**

```bash
# Tester dans navigateur si c'est un controller
# Vérifier logs d'erreur PHP
tail -f /var/log/php_errors.log

# Vérifier aucune régression
```

### 🎯 EXEMPLE COMPLET

```bash
#!/bin/bash
# Script de nettoyage de fichier PHP

FILE="/home/user/project/admin/controller/shop/product.php"

# 1. Backup
cp "$FILE" "$FILE.backup"

# 2. Nettoyage indentation
sed -i 's/}else {/} else {/g' "$FILE"
sed -i 's/if(/if (/g' "$FILE"
sed -i 's/^        /\t\t/g' "$FILE"
sed -i 's/^    /\t/g' "$FILE"
sed -i 's/\t\s*$//' "$FILE"
sed -i 's/\s\+$//' "$FILE"
sed -i '/^$/N;/^\n$/D' "$FILE"

# 3. Validation
if php -l "$FILE" > /dev/null 2>&1; then
    echo "✅ Syntaxe PHP valide"
    
    # Compter lignes nettoyées
    BEFORE=$(wc -l < "$FILE.backup")
    AFTER=$(wc -l < "$FILE")
    DIFF=$((BEFORE - AFTER))
    
    echo "📊 Lignes avant: $BEFORE"
    echo "📊 Lignes après: $AFTER"
    echo "🧹 Lignes nettoyées: $DIFF"
else
    echo "❌ ERREUR SYNTAXE! Restauration..."
    cp "$FILE.backup" "$FILE"
    exit 1
fi
```

### 📝 MÉTRIQUES TYPIQUES

**Exemple réel: product.php (2984 lignes)**
- ❌ **Supprimé:** 76 lignes commentaires debug
- ✅ **Nettoyé:** Indentation tabs/espaces mixtes
- ✅ **Harmonisé:** `}else` → `} else` (25 occurrences)
- ✅ **Résultat:** 2908 lignes finales (-2.5%)
- ⏱️ **Temps:** ~3 minutes (avec validation)

### ⚠️ POINTS CRITIQUES

1. **TOUJOURS créer backup** avant toute modification
2. **TOUJOURS valider syntaxe PHP** après (`php -l`)
3. **NE PAS supprimer** commentaires PHPDoc `/** */`
4. **NE PAS supprimer** TODOs/FIXMEs importants
5. **TESTER fonctionnellement** après nettoyage
6. **Garder backup** jusqu'à validation complète en production

### ✅ UTILISEZ CE PATTERN QUAND:

- Code hérité avec debug/commentaires inutiles
- Indentation mixte (tabs + espaces)
- Préparation code review / audit qualité
- Migration entre développeurs
- Refactoring majeur d'un module

### ❌ N'UTILISEZ PAS QUAND:

- Code en cours de debug actif
- Fichiers générés automatiquement
- Code tiers/vendor (ne pas modifier!)
- Pas de backup disponible
- Pas de tests unitaires pour valider

### 🔗 PATTERNS LIÉS

- Pattern #2 (Déplacer JS) → Nettoyage code frontend
- Pattern #7 (Debugging) → Gestion logs production
- DEV_GUIDELINES.md → Backup obligatoire avant modification

### 💡 BONNES PRATIQUES

**Ordre recommandé nettoyage:**
1. Backup
2. Supprimer commentaires debug (outils IA)
3. Nettoyer indentation (sed)
4. Validation syntaxe
5. Test fonctionnel
6. Commit Git

**Standards indentation PHP:**
- Utiliser **TABS** pour indentation (pas espaces)
- 1 tab = 1 niveau d'indentation
- Espaces après `if`, `else`, `foreach`, etc.
- `} else {` avec espaces (PSR-2)

**Configuration IDE recommandée:**
```json
// .editorconfig
[*.php]
indent_style = tab
indent_size = 4
trim_trailing_whitespace = true
insert_final_newline = true
```

---
## 🎯 PATTERN #12 - RÉINITIALISATION EVENT LISTENERS APRÈS AJAX

**PROBLÈME:** Event listeners (mouseenter, click, change, etc.) attachés au DOMContentLoaded ne fonctionnent plus après rechargement AJAX du contenu (pagination, tri, filtres)

**SYMPTÔMES:**
- ✅ Mouse hover fonctionne au premier chargement
- ❌ Mouse hover cassé après clic pagination
- ❌ Drag-and-drop ne fonctionne plus après tri
- ❌ Event listeners perdus après rechargement AJAX

**POURQUOI?**
```javascript
// Event listener attaché au chargement initial
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.item').forEach(function(item) {
        item.addEventListener('mouseenter', function() {
            // Fonctionne au chargement ✅
        });
    });
});

// AJAX reload (pagination, tri, filtres)
$('#content').load(url, function() {
    // Nouveau HTML chargé, MAIS event listeners PAS réappliqués ❌
});
```

**SOLUTION:**

### 1️⃣ Créer fonction globale de réinitialisation

Dans votre fichier JS (ex: `product_list.js`):

```javascript
// ============================================
// GLOBAL REINIT FUNCTION FOR AJAX RELOADS
// ============================================
/**
 * Réinitialise tous les event listeners après rechargement AJAX
 * Appelée par le Twig après $('#content').load()
 */
window.reinitEventListeners = function() {
    console.log('🔄 Réinitialisation event listeners après AJAX');
    
    // Reset les flags d'initialisation pour permettre la réinit
    document.querySelectorAll('.item-container').forEach(function(container) {
        container.dataset.initialized = 'false';
    });
    
    // Réappliquer tous les event listeners
    initMouseEvents();
    initDragAndDrop();
    initFormEvents();
    
    console.log('✅ Réinitialisation complète');
};

// Exposer autres fonctions si nécessaire
window.initMouseEvents = initMouseEvents;
window.initDragAndDrop = initDragAndDrop;
```

### 2️⃣ Créer fonctions d'initialisation avec protection double-init

```javascript
function initMouseEvents() {
    document.querySelectorAll('.item-container').forEach(function(container) {
        // Protection contre double initialisation
        if (container.dataset.mouseInitialized === 'true') return;
        container.dataset.mouseInitialized = 'true';

        const item = container.querySelector('.item');
        
        item.addEventListener('mouseenter', function() {
            // Votre code mouseenter
        });

        item.addEventListener('mouseleave', function() {
            // Votre code mouseleave
        });
    });
}

function initDragAndDrop() {
    document.querySelectorAll('.item-container').forEach(function(container) {
        // Protection contre double initialisation
        if (container.dataset.dragInitialized === 'true') return;
        container.dataset.dragInitialized = 'true';

        container.addEventListener('drop', function(e) {
            e.preventDefault();
            // Votre code drop
        });
    });
}
```

### 3️⃣ Appeler au DOMContentLoaded (chargement initial)

```javascript
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOMContentLoaded - Initialisation');
    
    // Initialiser tous les event listeners
    initMouseEvents();
    initDragAndDrop();
    initFormEvents();
    
    console.log('✅ Initialisation complète');
});
```

### 4️⃣ Appeler après chaque AJAX reload dans Twig

Dans votre template Twig (ex: `product.twig`):

```twig
<script type="text/javascript">
    // Pagination AJAX
    $('#content').on('click', 'thead a, .pagination a', function(e) {
        e.preventDefault();
        var url = this.href;
        
        $('#content').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i></div>');
        
        $('#content').load(url, function() {
            // Réinitialiser event listeners APRÈS chargement
            if (typeof window.reinitEventListeners === 'function') {
                window.reinitEventListeners();
            }
        });
    });

    // Filtres AJAX
    $('#button-filter').on('click', function() {
        var url = buildFilterUrl();
        
        $('#content').load(url, function() {
            // Réinitialiser event listeners APRÈS chargement
            if (typeof window.reinitEventListeners === 'function') {
                window.reinitEventListeners();
            }
        });
    });
</script>
```

**POINTS CRITIQUES:**

- 🛡️ **Protection double-init:** `dataset.initialized` empêche d'attacher 2x les mêmes listeners
- 🔄 **Reset flags AVANT réinit:** Permet de réappliquer les listeners sur nouveau DOM
- 🌍 **Exposition globale:** `window.reinitEventListeners` accessible depuis Twig
- ⏱️ **Callback load():** Appeler DANS le callback pour garantir DOM chargé
- ✅ **typeof check:** Vérifier que fonction existe avant appel

**RÈGLES:**

- ✅ Toujours créer fonction `window.reinit*` globale
- ✅ Protéger contre double initialisation avec `dataset`
- ✅ Appeler dans TOUS les callbacks AJAX (pagination, tri, filtres)
- ✅ Tester après chaque type de rechargement
- ❌ Ne PAS attacher listeners directement dans Twig (garder dans .js)

**EXEMPLE COMPLET (product_list.js):**

```javascript
// Fonctions d'initialisation
function initImagePreview() {
    document.querySelectorAll('.image-container').forEach(function(container) {
        if (container.dataset.previewInit === 'true') return;
        container.dataset.previewInit = 'true';

        const img = container.querySelector('img');
        img.addEventListener('mouseenter', function() {
            showFullPreview(container);
        });
    });
}

function initImageDragDrop(uploadUrl, successCallback, errorCallback) {
    document.querySelectorAll('.image-container').forEach(function(container) {
        if (container.dataset.dragInit === 'true') return;
        container.dataset.dragInit = 'true';

        container.addEventListener('drop', function(e) {
            e.preventDefault();
            handleImageUpload(e.dataTransfer.files[0], uploadUrl, successCallback);
        });
    });
}

// Fonction globale de réinit
window.reinitImagePreview = function() {
    // Reset flags
    document.querySelectorAll('.image-container').forEach(function(container) {
        container.dataset.previewInit = 'false';
        container.dataset.dragInit = 'false';
    });
    
    // Réappliquer
    initImagePreview();
    initImageDragDrop('index.php?route=upload', onSuccess, onError);
};

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    initImagePreview();
    initImageDragDrop('index.php?route=upload', onSuccess, onError);
});
```

**DANS TWIG (product.twig):**

```twig
$('#product').load(url, function() {
    if (typeof window.reinitImagePreview === 'function') {
        window.reinitImagePreview();
    }
});
```

**AVANTAGES:**

- ✅ Event listeners fonctionnent toujours après AJAX
- ✅ Code réutilisable (même pattern pour tous modules)
- ✅ Pas de fuite mémoire (pas de listeners doublés)
- ✅ Facile à tester et débugger
- ✅ Séparation claire JS/Twig

**CAS D'USAGE:**

- Pagination avec rechargement AJAX
- Tri de colonnes avec AJAX
- Filtres avec rechargement dynamique
- Infinite scroll
- Tabs avec contenu chargé dynamiquement
- Modals avec contenu AJAX

---

---

## 🎯 PATTERN #13 - FILTRAGE SQL vs PHP (Performance + Total Correct)

**PROBLÈME:** Filtre implémenté en PHP après la requête SQL → Performance médiocre + Total produits incorrect

**SYMPTÔMES:**
- ❌ Filtre fonctionne mais lent (traitement PHP de tous les résultats)
- ❌ `getTotalProducts()` retourne le total SANS filtre
- ❌ Pagination incorrecte (total ne correspond pas aux résultats filtrés)
- ❌ Performance dégradée avec beaucoup de produits

**POURQUOI C'EST UN PROBLÈME:**
```
Filtre "Not Listed" avec 50000 produits:
1. SQL retourne 50000 produits (TOUS) → 2.5s
2. PHP boucle et filtre → 3.8s
3. getTotalProducts() dit "50000" au lieu de "300"
4. Pagination cassée (affiche page 1 sur 2500 au lieu de 1 sur 15)
```

**SOLUTION:**

### ❌ AVANT (Filtre PHP post-traitement)

```php
public function getProducts($data) {
    // Requête SQL SANS filtre marketplace
    $sql = "SELECT * FROM product WHERE 1=1";
    // ... autres filtres (sku, quantity, etc.)
    
    $query = $this->db->query($sql);
    
    // ❌ Filtrage APRÈS en PHP (LENT!)
    foreach ($query->rows as $key => $result) {
        $product_data[$key] = $result;
        
        if (isset($data['filter_marketplace']) && $data['filter_marketplace'] == '0') {
            // Charger données marketplace
            $marketplace = $this->getMarketplace($result['product_id']);
            
            if (!empty($marketplace['marketplace_item_id'])) {
                unset($product_data[$key]); // ❌ Produit listé → retirer
            }
        }
    }
    
    return $product_data;
}

public function getTotalProducts($data) {
    // ❌ Requête SANS le filtre marketplace
    $sql = "SELECT COUNT(*) FROM product WHERE 1=1";
    // ... autres filtres SAUF marketplace
    
    return $query->row['total']; // ❌ TOTAL FAUX
}
```

### ✅ APRÈS (Filtre SQL dans WHERE)

```php
public function getProducts($data) {
    $sql = "SELECT * FROM product p WHERE 1=1";
    
    // ... autres filtres (sku, quantity, etc.)
    
    // ✅ AJOUTER FILTRE MARKETPLACE DANS SQL
    if (isset($data['filter_marketplace']) && $data['filter_marketplace'] !== '') {
        if ($data['filter_marketplace'] == '0') {
            // NOT LISTED: Pas dans marketplace ET quantity > 0
            $sql .= " AND NOT EXISTS(
                SELECT 1 FROM " . DB_PREFIX . "product_marketplace pm 
                WHERE pm.product_id = p.product_id 
                AND pm.marketplace_item_id IS NOT NULL 
                AND pm.marketplace_item_id != ''
            )";
            $sql .= " AND (p.quantity + p.unallocated_quantity) > 0";
            
        } elseif ($data['filter_marketplace'] == '1') {
            // LISTED: Dans marketplace
            $sql .= " AND EXISTS(
                SELECT 1 FROM " . DB_PREFIX . "product_marketplace pm 
                WHERE pm.product_id = p.product_id 
                AND pm.marketplace_item_id IS NOT NULL
            )";
            
        } elseif ($data['filter_marketplace'] == '2') {
            // ERROR: Avec erreur de listing
            $sql .= " AND EXISTS(
                SELECT 1 FROM " . DB_PREFIX . "product_marketplace pm 
                WHERE pm.product_id = p.product_id 
                AND pm.error IS NOT NULL AND pm.error != ''
            )";
        }
    }
    
    $query = $this->db->query($sql);
    
    // ✅ Plus besoin de filtrage PHP - déjà fait par SQL
    return $query->rows;
}

public function getTotalProducts($data) {
    $sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM product p WHERE 1=1";
    
    // ... autres filtres
    
    // ✅ COPIER EXACTEMENT LE MÊME FILTRE
    if (isset($data['filter_marketplace']) && $data['filter_marketplace'] !== '') {
        if ($data['filter_marketplace'] == '0') {
            $sql .= " AND NOT EXISTS(...) AND (p.quantity + p.unallocated_quantity) > 0";
        } elseif ($data['filter_marketplace'] == '1') {
            $sql .= " AND EXISTS(...)";
        } elseif ($data['filter_marketplace'] == '2') {
            $sql .= " AND EXISTS(...)";
        }
    }
    
    $query = $this->db->query($sql);
    return (int)$query->row['total']; // ✅ TOTAL CORRECT
}
```

**RÈGLES CRITIQUES:**

1. **Filtrage SQL obligatoire pour:**
   - ✅ Filtres sur relations (EXISTS, JOIN)
   - ✅ Filtres sur calculs (quantity + unallocated)
   - ✅ Tout filtre affectant le total/pagination

2. **getTotalProducts() DOIT avoir:**
   - ✅ EXACTEMENT les mêmes WHERE que getProducts()
   - ✅ Même logique conditionnelle
   - ✅ Tester que total = nombre résultats

3. **EXISTS vs JOIN:**
   - ✅ `EXISTS()` pour vérifier présence (plus performant)
   - ✅ `NOT EXISTS()` pour négation
   - ✅ `JOIN` seulement si besoin des données de la table liée

4. **Quand garder filtrage PHP:**
   - ✅ Formatage post-requête (JSON decode, etc.)
   - ✅ Enrichissement avec APIs externes
   - ❌ PAS pour filtrer les résultats

**PERFORMANCES COMPARÉES:**

```
Test: 50000 produits, filtre "Not Listed" avec quantity > 0
Résultat attendu: 300 produits

❌ FILTRAGE PHP POST-TRAITEMENT:
- Requête SQL: 2.5s (retourne 50000 rows)
- Traitement PHP: 3.8s (boucle sur 50000 items)
- Total: 6.3s
- Mémoire: 180 MB
- getTotalProducts(): 50000 (FAUX)

✅ FILTRAGE SQL WHERE:
- Requête SQL: 0.3s (retourne 300 rows filtrés)
- Traitement PHP: 0.05s (boucle sur 300 items)
- Total: 0.35s
- Mémoire: 8 MB
- getTotalProducts(): 300 (CORRECT)

AMÉLIORATION: 18x plus rapide, 22x moins de mémoire
```

**CHECKLIST IMPLÉMENTATION:**

- [ ] Identifier filtre actuellement en PHP
- [ ] Écrire condition SQL équivalente (EXISTS/JOIN)
- [ ] Ajouter dans getProducts() après autres WHERE
- [ ] COPIER EXACTEMENT dans getTotalProducts()
- [ ] Supprimer ancien filtrage PHP
- [ ] Test: Vérifier total affiché = nombre résultats
- [ ] Test: Pagination correcte
- [ ] Test: Performance < 1s

**CAS D'USAGE:**

- ✅ Filtre marketplace (listed/not listed/error)
- ✅ Filtre sur relations (produits avec/sans spécifiques)
- ✅ Filtre sur calculs (quantity totale)
- ✅ Tout filtre affectant la pagination

**PATTERNS LIÉS:**

- Pattern #10 (DRY Models) → Éviter duplication filtres
- OPENCART_GUIDELINES.md → Standards SQL OpenCart

---

## 🎯 PATTERN #14 - VARIABLES TWIG RACCOURCIES vs STANDARD OPENCART

**PROBLÈME:** Utilisation de variables raccourcies (`pd.xxx`) vs syntaxe OpenCart standard longue

**CONTEXTE:** OpenCart 4 utilise systématiquement la syntaxe complète dans ses templates officiels

**DÉCISION PROJET:** Suivre 100% les standards OpenCart (pas de raccourcis)

---

### ❌ APPROCHE RACCOURCIE (À ÉVITER)

```twig
{% for language in languages %}
  {% set pd = product_description[language.language_id] ?? {} %}
  
  <input value="{{ pd.name ?? '' }}" />
  <textarea>{{ pd.description ?? '' }}</textarea>
  <input value="{{ pd.meta_title ?? '' }}" />
{% endfor %}
```

**PROBLÈMES:**
- ❌ Différent des templates OpenCart officiels
- ❌ Incompatible avec mises à jour OpenCart
- ❌ Confusion lors de comparaisons avec fichiers originaux
- ❌ Incohérent si utilisé partiellement (mélange des deux styles)

---

### ✅ APPROCHE STANDARD OPENCART (RECOMMANDÉE)

```twig
{% for language in languages %}
  <input value="{{ product_description[language.language_id] ? product_description[language.language_id].name : '' }}" />
  <textarea>{{ product_description[language.language_id] ? product_description[language.language_id].description : '' }}</textarea>
  <input value="{{ product_description[language.language_id] ? product_description[language.language_id].meta_title : '' }}" />
{% endfor %}
```

**AVANTAGES:**
- ✅ 100% conforme OpenCart 4 standard
- ✅ Compatible avec futures mises à jour
- ✅ Facilite comparaisons avec templates originaux
- ✅ Code cohérent dans tout le projet
- ✅ Aucune ambiguïté sur l'origine de la variable

---

### 🔄 REFACTORING: Raccourci → Standard

**RECHERCHER ET REMPLACER:**

| Ancien (Raccourci) | Nouveau (Standard) |
|--------------------|-------------------|
| `{% set pd = product_description[...] %}` | ❌ Supprimer ligne |
| `{{ pd.name }}` | `{{ product_description[language.language_id] ? product_description[language.language_id].name : '' }}` |
| `{{ pd.description }}` | `{{ product_description[language.language_id] ? product_description[language.language_id].description : '' }}` |
| `{{ pd.xxx\|default('') }}` | `{{ product_description[language.language_id] ? product_description[language.language_id].xxx : '' }}` |
| `{% if pd.xxx is defined %}` | `{% if product_description[language.language_id] and product_description[language.language_id].xxx %}` |

**COMMANDE GREP POUR TROUVER:**
```bash
grep -n "pd\." file.twig
grep -n "{% set pd = " file.twig
```

---

### 📋 CHECKLIST REFACTORING

**AVANT:**
- [ ] Créer backup: `cp file.twig file.twig.backup`
- [ ] Lister toutes occurrences: `grep -n "pd\." file.twig`

**PENDANT:**
- [ ] Supprimer déclaration `{% set pd = ... %}`
- [ ] Remplacer chaque `pd.xxx` par syntaxe complète
- [ ] Vérifier conditions: `pd.xxx is defined` → `product_description[...] and ...xxx`
- [ ] Vérifier defaults: `pd.xxx|default('')` → `product_description[...] ? ...xxx : ''`

**APRÈS:**
- [ ] Vérifier aucune occurrence reste: `grep "pd\." file.twig`
- [ ] Tester formulaire dans navigateur
- [ ] Vérifier tous les champs s'affichent correctement

---

### ⚠️ POINTS CRITIQUES

**1. Syntaxe ternaire Twig:**
```twig
❌ INCORRECT: {{ pd.name ?? '' }}
✅ CORRECT:   {{ product_description[language.language_id] ? product_description[language.language_id].name : '' }}
```

**2. Conditions d'existence:**
```twig
❌ INCORRECT: {% if pd.name is defined %}
✅ CORRECT:   {% if product_description[language.language_id] and product_description[language.language_id].name %}
```

**3. Filters/Escaping:**
```twig
❌ INCORRECT: {{ pd.description|default('')|e }}
✅ CORRECT:   {{ product_description[language.language_id] ? product_description[language.language_id].description : '' }}
```

**4. Dans conditions multiples:**
```twig
❌ INCORRECT: style="display:{{ pd.condition is defined and pd.condition != '' ? 'block' : 'none' }}"
✅ CORRECT:   style="display:{{ product_description[language.language_id] and product_description[language.language_id].condition != '' ? 'block' : 'none' }}"
```

---

### 🎯 RÈGLE D'OR

**"Quand on modifie un template OpenCart, on suit EXACTEMENT leur style, même si c'est plus verbeux."**

**RAISON:** Maintenabilité > Concision

---

### 📦 CAS D'USAGE

**✅ APPLIQUÉ DANS:**
- `administrator/view/template/shopmanager/product_form.twig` (Jan 2026)
  - 17 occurrences de `pd.xxx` remplacées
  - Backup créé: `product_form.twig.backup`

**✅ PEUT S'APPLIQUER À:**
- Tout template Twig avec variables multi-dimensionnelles
- Templates avec boucles sur `languages` ou `stores`
- Formulaires avec champs traduisibles

---

### 🔗 PATTERNS LIÉS

- **OPENCART_GUIDELINES.md** → Standards templates Twig
- **Pattern #2** → Séparation Twig/JS propre

---

### 📚 RÉFÉRENCES

- OpenCart 4 Templates: `catalog/view/template/catalog/product_form.twig` (original)
- Twig Documentation: Ternary operator (`? :`)

---
