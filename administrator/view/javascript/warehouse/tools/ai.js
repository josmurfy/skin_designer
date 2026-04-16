// Original: warehouse/tools/ai.js
// ============================================
// FUNCTIONS DUPLICATED FROM TOOLS.JS (PRODUCTION SAFETY)
// ============================================

// ============================================
// API REQUEST QUEUE (3 CONCURRENT, RETRY ON 429)
// ============================================
const apiQueue = {
    queue: [],
    running: 0,
    maxConcurrent: 3,  // 3 requêtes en parallèle
    minDelay: 800,     // 800ms entre chaque démarrage
    lastRequestTime: 0,

    async add(requestFunc) {
        return new Promise((resolve, reject) => {
            this.queue.push({ requestFunc, resolve, reject });
            this.processNext();
        });
    },

    async processNext() {
        if (this.running >= this.maxConcurrent || this.queue.length === 0) return;

        const now = Date.now();
        const waitTime = Math.max(0, this.minDelay - (now - this.lastRequestTime));
        if (waitTime > 0) {
            setTimeout(() => this.processNext(), waitTime);
            return;
        }

        this.running++;
        this.lastRequestTime = Date.now();
        const { requestFunc, resolve, reject } = this.queue.shift();

        // Lance le prochain slot immédiatement (parallélisme)
        setTimeout(() => this.processNext(), this.minDelay);

        try {
            const result = await this._withRetry(requestFunc);
            resolve(result);
        } catch (error) {
            reject(error);
        } finally {
            this.running--;
            this.processNext();
        }
    },

    // Retry automatique sur 429 avec backoff exponentiel
    async _withRetry(requestFunc, maxRetries = 3) {
        let delay = 2000;
        for (let attempt = 0; attempt <= maxRetries; attempt++) {
            const result = await requestFunc();
            // Si c'est une Response HTTP, vérifier le statut 429
            if (result && typeof result.status === 'number' && result.status === 429) {
                if (attempt < maxRetries) {
                    appendLoadingMessage(`[ATTENTE] Limite OpenAI atteinte, retry dans ${delay/1000}s...`, 'warning');
                    await new Promise(r => setTimeout(r, delay));
                    delay *= 2;
                    continue;
                }
            }
            return result;
        }
    }
};

// ============================================
// CLEANUP INVALID SELECT OPTIONS
// ============================================
function cleanupInvalidSelectOptions() {
    // Remove [object Object] and other invalid options from all select elements
    document.querySelectorAll('select option').forEach(function(option) {
        const val = option.value;
        if (val === '[object Object]' || val === 'undefined' || val === 'null' || 
            val.includes('[object') || option.textContent === '[Object Object]') {
            option.remove();
        }
    });
}

// Run cleanup on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', cleanupInvalidSelectOptions);
} else {
    cleanupInvalidSelectOptions();
}

function stripHtmlTagsFromFormData(formData) {
    Object.keys(formData).forEach(function (key) {
        if (typeof formData[key] === 'string') {
            // Supprime les balises HTML en utilisant une expression régulière
            formData[key] = formData[key].replace(/<\/?[^>]+(>|$)/g, '').trim();
        }
    });
    return formData;
}

// ============================================
// LOADING POPUP UTILITY
// ============================================
function showLoadingPopup(title = "Chargement en cours...") {
    var loadingTitle = document.getElementById("loading-title");
    var loadingMessages = document.getElementById("loading-messages");
    var loadingPopup = document.getElementById("loading-popup");
    var closeLoadingBtn = document.getElementById("close-loading-btn");
    
    if (!loadingTitle || !loadingMessages || !loadingPopup) {
        console.warn('Loading popup elements not found in DOM');
        return;
    }
    
    loadingTitle.textContent = title;
    loadingMessages.innerHTML = '';
    loadingPopup.style.display = 'block';
    if (closeLoadingBtn) {
        closeLoadingBtn.style.display = 'none';
    }
}

function appendLoadingMessage(message, type = 'info') {
    const container = document.getElementById("loading-messages");
    
    if (!container) {
        console.warn('Loading messages container not found in DOM');
        return;
    }
    
    const color = {
        info: '#007bff',
        success: '#28a745',
        warning: '#ffc107',
        error: '#dc3545'
    }[type] || '#000';

    const icon = {
        info: 'ℹ️',
        success: '✅',
        warning: '⚠️',
        error: '❌'
    }[type] || '';

    const line = document.createElement('div');
    line.innerHTML = `<span style="color:${color}">${icon} ${message}</span>`;
    container.appendChild(line);
    container.scrollTop = container.scrollHeight;
}

function finishLoadingPopup(message = '✅ Terminé !') {
    appendLoadingMessage(message, 'info');
    const closeBtn = document.getElementById('close-loading-btn');
    if (closeBtn) {
        closeBtn.style.display = 'inline-block';
    }
}

function hideLoadingPopup() {
    const popup = document.getElementById("loading-popup");
    if (popup) {
        popup.style.display = 'none';
    }
}

// ============================================
// END DUPLICATED FUNCTIONS FROM TOOLS.JS
// ============================================

function getProductSpecific(aspectName, callback) {
  
    var product_id = document.querySelector('input[name="product_id"]').value;
    var user_token = document.querySelector('input[name="user_token"]').value;


    var data = {
        product_id: product_id,
        aspectName: aspectName
    };
//alert(data.product_id);
    
    // Use apiQueue to prevent 429 errors
    apiQueue.add(async () => {
        return await fetch('index.php?route=warehouse/tools/ai.getProductSpecific&user_token=' + user_token, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
    })
    .then(async response => {
        const text = await response.text();
        try {
            const json = JSON.parse(text);
            if (json && json.error) {
                callback(json.error, null);
            } else if (json && json.success) {
                callback(null, json.success);
            } else {
                callback(`Valeur non trouvée \nRéponse brute : ${text}`, null);
            }
        } catch (e) {
            callback(`Erreur de parsing JSON : ${e.message}\nRéponse brute : ${text}`, null);
        }
    })
  .catch(error => {
    const fullError = error && error.stack ? `${error.message}\n${error.stack}` : error.message;
    callback(`Erreur réseau : ${fullError}`, null);
});
  
}
function getProductSpecificMARDE(aspectName, callback) {
    const user_token = document.querySelector('input[name="user_token"]').value;

    apiQueue.add(async () => {
        return await fetch('index.php?route=warehouse/tools/ai.getProductSpecific&user_token=' + user_token + '&aspect_name=' + encodeURIComponent(aspectName));
    })
        .then(async response => {
            const text = await response.text();
            try {
                const json = JSON.parse(text);
                if (json && json.value) {
                    callback(null, json.value);
                } else {
                    callback('Valeur non trouvée dans la réponse.', null);
                }
            } catch (e) {
                callback(`Erreur de parsing JSON : ${e.message}\nRéponse brute : ${text}`, null);
            }
        })
        .catch(error => {
            const fullError = error && error.stack ? `${error.message}\n${error.stack}` : error.message;
            callback(`Erreur réseau : ${fullError}`, null);
        });
}

async function verifyAllSpecifics() {
    showLoadingPopup('Vérification des aspects avec l\'IA en cours...');

    const rows = Array.from(document.querySelectorAll('[id^="specifics-1-"]'));

    // Filtrer seulement les lignes non encore vérifiées
    const toVerify = rows.filter(row => {
        const rowId = row.id.replace('specifics-1-', '');
        const verifiedSourceElement = document.getElementById(`verified-source-1-${rowId}`);
        return verifiedSourceElement && verifiedSourceElement.value.toLowerCase() !== 'yes';
    }).map(row => row.id.replace('specifics-1-', ''));

    appendLoadingMessage(`[INFO] ${toVerify.length} aspect(s) à vérifier (3 en parallèle)`, 'info');

    // Traiter par batches de 3 en parallèle
    const batchSize = 3;
    for (let i = 0; i < toVerify.length; i += batchSize) {
        const batch = toVerify.slice(i, i + batchSize);
        await Promise.allSettled(batch.map(rowId => verifySpecific(rowId, 'false')));
    }

    finishLoadingPopup();
}
async function verifySpecific(row, finish = 'false') {
    // Ouvrir le popup quand appelé directement depuis un bouton individuel
    if (finish === 'true') {
        showLoadingPopup('Vérification de l\'aspect #' + row + ' avec l\'IA...');
    }

    const user_token = document.querySelector('input[name="user_token"]').value;
    const Actual_value = document.getElementById('hidden-original-value-1-' + row).value;
    const specificRowElem = document.getElementById('specifics-1-' + row);

    if (!specificRowElem) {
        appendLoadingMessage(`[ERREUR] Élément introuvable pour la ligne ${row}`, 'danger');
        if( finish === 'true') { finishLoadingPopup(); }
        return JSON.stringify({ success: false, error: 'Specific row element not found', row: row });
    }

    const language_id = 1;
    const specificNameElem = specificRowElem.querySelector('input[name*="[Name]"]');
    const specificValueElem = specificRowElem.querySelector('input[name*="[Value]"], select[name*="[Value][]"], select[name*="[Value]"]');

    if (!specificNameElem || !specificValueElem) {
        appendLoadingMessage(`[ERREUR] Input ou select manquant pour la ligne ${row}`, 'danger');
        if( finish === 'true') { finishLoadingPopup(); }
        return JSON.stringify({ success: false, error: 'Required elements not found', row: row });
    }

    const specificName = specificNameElem.value.toLowerCase();
    let specificValue = '';

    if (specificValueElem.tagName.toLowerCase() === 'select') {
        const selectedOptions = Array.from(specificValueElem.selectedOptions).map(option => option.value);
        specificValue = selectedOptions.join(', ');
    } else {
        specificValue = specificValueElem.value;
    }

    const excludedKeys = ["language", "rating", "year", "region code", "country", "mpn"];
    if (excludedKeys.some(keyword => specificName.includes(keyword)) && specificValue.trim() !== "") {
        appendLoadingMessage(`[IGNORÉ] ${specificName} avec une valeur déjà définie`, 'warning');
        if( finish === 'true') { finishLoadingPopup(); }
        return JSON.stringify({ success: false, message: `Clé ignorée: ${specificName}`, row: row });
    }

    if (specificName.includes("item")) {
        appendLoadingMessage(`[IGNORÉ] ${specificName} contient 'item'`, 'warning');
        if( finish === 'true') { finishLoadingPopup(); }
        return JSON.stringify({ success: false, message: `Clé ignorée: ${specificName}`, row: row });
    }

    // === BRAND: chercher la valeur depuis un autre specific "Brand" ou fallback fabricant ===
    // NB: exactement "brand" seulement — "Compatible Brand" doit passer normalement
    if (specificName.toLowerCase() === 'brand') {
        let brandValue = '';

        // 1) Chercher un autre specific row dont le Name == "Brand" (case-insensitive)
        const allSpecificRows = document.querySelectorAll('[id^="specifics-1-"]');
        for (const otherRow of allSpecificRows) {
            const otherRowId = otherRow.id.replace('specifics-1-', '');
            if (otherRowId === String(row)) continue; // skip soi-même
            const otherNameElem = otherRow.querySelector('input[name*="[Name]"]');
            const otherValueElem = otherRow.querySelector('input[name*="[Value]"], select[name*="[Value][]"], select[name*="[Value]"]');
            if (otherNameElem && otherNameElem.value.trim().toLowerCase() === 'brand' && otherValueElem) {
                const otherVal = otherValueElem.tagName.toLowerCase() === 'select'
                    ? Array.from(otherValueElem.selectedOptions).map(o => o.value).join(', ')
                    : otherValueElem.value.trim();
                if (otherVal) { brandValue = otherVal; break; }
            }
        }

        // 2) Fallback: champ fabricant
        if (!brandValue) {
            const manufacturerInput = document.getElementById('input-manufacturer');
            brandValue = manufacturerInput ? manufacturerInput.value.trim() : '';
        }

        if (brandValue) {
            if (specificValue.toLowerCase() === brandValue.toLowerCase()) {
                // Valeur déjà correcte → vert (pas d'injection, juste confirmation)
                updateTargetElement(row, brandValue, 'green', 'no');
                appendLoadingMessage(`[BRAND] '${specificName}' déjà correct: ${brandValue}`, 'success');
            } else {
                // Valeur différente → orange = injection réelle dans le champ
                document.getElementById('original-value-' + language_id + '-' + row).textContent = specificValue;
                document.getElementById('hidden-original-value-' + language_id + '-' + row).value = specificValue;
                updateTargetElement(row, brandValue, 'orange');
                $('#btUndo-' + language_id + '-' + row).show();
                appendLoadingMessage(`[BRAND] '${specificName}' mis à jour: ${brandValue}`, 'warning');
            }
            if (finish === 'true') { finishLoadingPopup(); }
            return JSON.stringify({ success: true, message: 'Brand auto-rempli.', row: row, value: brandValue });
        }
        // Si aucune source disponible, on continue vers l'IA normalement
    }

    const productName = document.querySelector('input[name="product_description[' + language_id + '][name]"]')?.value || '';
    const descriptionFull = document.querySelector('input[name="product_description[' + language_id + '][hidden_description]"]')?.value || '';
    const description = descriptionFull.substring(0, 500); // Limit to 500 chars to prevent 429 token errors
    let category = document.getElementById('input-category')?.value || '';
    category = category.replace(/\s*\([^)]*\)/, '').trim();

    const responseElemId = `response-product-description-${language_id}-` + row;
    const responseElem = document.getElementById(responseElemId);

    if (!responseElem) {
        appendLoadingMessage(`[ERREUR] Élément réponse introuvable pour ${responseElemId}`, 'danger');
        if( finish === 'true') { finishLoadingPopup(); }
        return JSON.stringify({ success: false, error: `Response element not found`, row: row });
    }

    // === COMPATIBLE* : lire uniquement Included Accessories ===
    // S'applique à: Compatible Model, Compatible With, Compatible Product, Compatible Product Line,
    //               Compatible Series, Compatible Vehicle Make, Compatible Vehicle Model
    const isCompatibleFreeText = /compatible (model|with|product(?: line)?|series|vehicle (?:make|model))/i.test(specificName);
    let compatibleContext = '';
    if (isCompatibleFreeText) {
        let accessoriesText = '';

        // Helper: strip HTML en respectant les séparateurs de blocs
        function stripHtmlWithSeparators(html) {
            // Remplacer les balises bloc/br par \n avant de stripper
            return html
                .replace(/<br\s*\/?>/gi, '\n')
                .replace(/<\/(p|div|li|tr|td|h[1-6])>/gi, '\n')
                .replace(/<[^>]+>/g, '')
                .replace(/&nbsp;/gi, ' ')
                .replace(/&amp;/gi, '&')
                .replace(/&lt;/gi, '<')
                .replace(/&gt;/gi, '>')
                .replace(/[ \t]+/g, ' ')       // espaces multiples → 1 espace
                .replace(/\n[ \t]+/g, '\n')    // trim chaque ligne
                .replace(/[ \t]+\n/g, '\n')
                .replace(/\n{3,}/g, '\n\n')    // max 2 sauts de ligne
                .trim();
        }

        // 1) Textarea (valeur sauvegardée)
        const taAccessories = document.querySelector('textarea[name="product_description[' + language_id + '][included_accessories]"]');
        if (taAccessories && taAccessories.value.trim()) {
            accessoriesText = stripHtmlWithSeparators(taAccessories.value);
        }

        // 2) Fallback: lire le .note-editable associé au textarea included_accessories
        if (!accessoriesText) {
            if (taAccessories) {
                const wrapper = taAccessories.closest('.col-sm-8, .col-sm-10, div');
                if (wrapper) {
                    const editable = wrapper.querySelector('.note-editable');
                    if (editable) {
                        accessoriesText = (editable.innerText || editable.textContent || '').replace(/[ \t]+/g, ' ').trim();
                    }
                }
            }
        }

        console.log(`[COMPATIBLE MODEL] included_accessories text:`, accessoriesText.substring(0, 300));
        if (accessoriesText) {
            compatibleContext = accessoriesText;
        }
    }

    // === CONDITION: extraire le nom de condition du produit ===
    let conditionContext = '';
    if (/condition/i.test(specificName)) {
        const conditionLabel = document.getElementById('condition-name-' + language_id);
        if (conditionLabel) {
            const conditionText = conditionLabel.textContent.trim();
            if (conditionText) {
                conditionContext = '\nProduct condition: ' + conditionText;
            }
        }
    }

    // Get available options if it's a select (eBay specifics have restricted values)
    let availableOptions = '';
    const isMultiSelect = specificValueElem.tagName.toLowerCase() === 'select' && specificValueElem.multiple;

    // isCompatibleModel = type "Compatible*"
    // isCompatibleExtract = Compatible* ET aucune valeur sélectionnée → on fait l'extraction
    // Si des valeurs sont déjà là → on fait la vérif normale TRUE/FALSE
    const isCompatibleModel = isCompatibleFreeText;
    const isCompatibleExtract = isCompatibleFreeText && !specificValue.trim();

    if (specificValueElem.tagName.toLowerCase() === 'select') {
        const maxOpts = isCompatibleModel ? 9999 : 10;
        const options = Array.from(specificValueElem.options)
            .map(opt => opt.value)
            .filter(val => val && val.trim() !== '')
            .slice(0, maxOpts);
        if (options.length > 0) {
            const moreCount = specificValueElem.options.length - 1 - options.length;
            const moreText = (!isCompatibleModel && moreCount > 0) ? ` (${moreCount} more available)` : '';
            availableOptions = isCompatibleModel
                ? `\nAvailable eBay options (choose ALL that apply, return exact values):\n${options.join('\n')}`
                : `\nYou MUST choose from available options: ${options.join(', ')}${moreText}`;
        }
    }

    let prompt;
    if (isCompatibleExtract) {
        // Champ vide → extraction complète depuis included_accessories
        const accessoriesInfo = compatibleContext
            ? `\n\nCompatible models text from product description:\n${compatibleContext}`
            : '';
        const listSection = availableOptions
            ? `\n\nExisting options (use these exact values if they match, but you can also return values NOT in this list if they appear in the compatible models text):\n${availableOptions.replace(/^.*?:\n/, '')}`
            : '';
        prompt = `For the eBay listing "${productName}" (category: ${category}), extract ALL compatible model numbers/names.${accessoriesInfo}${listSection}\n\nInstructions:\n- Extract every compatible model explicitly mentioned in the "Compatible models text" above.\n- Also include any matching options from the existing options list if they apply.\n- Return the values separated by semicolons.\n- Format: prefix each model with "For [Brand]" (e.g. "For Brother DCP-9040CN", "For HP OfficeJet 8600"). Detect the brand from context (product title, description, or model prefix). If multiple brands, each model gets its own "For [Brand]" prefix.\n- If an existing option already has the correct "For [Brand] Model" format, use its exact text.\n- If no compatible models are found anywhere, return an empty string.`;
    } else if (isCompatibleModel && specificValue.trim()) {
        // Champ déjà rempli → vérification normale des valeurs existantes
        prompt = `Based on the product title "${productName}"${category ? ' (category: ' + category + ')' : ''}, are the following values correct for the eBay aspect "${specificName}"?\nCurrent values: ${specificValue}\nDescription: ${description}${conditionContext}\nConsider the product title as primary evidence. Reply with ONLY 'TRUE' if all values are accurate, or 'FALSE' if any are incorrect. Do not include any explanation.`;
    } else {
        prompt = (!specificValue.trim())
            // OLD PROMPT (backup):
            // ? `What is the most accurate and confirmed value for the eBay aspect "${specificName}" for the product titled "${productName}" in the "${category}" category?${availableOptions}${compatibleContext}${conditionContext}\nUse only information from reliable sources.`
            // : `Is "${specificValue}" the correct and confirmed value for the "${specificName}" aspect of the product titled "${productName}" in the "${category}" category?\nDescription: ${description}${availableOptions}${compatibleContext}${conditionContext}\nReply with ONLY 'TRUE' if accurate or 'FALSE' if not. Do not include any explanation.`
            ? `What is the most accurate value for the eBay aspect "${specificName}" for the product "${productName}"${category ? ' in category "' + category + '"' : ''}?${availableOptions}${compatibleContext}${conditionContext}\nBase your answer on the product title and your knowledge. Use only information from reliable sources.`
            : `Based on the product title "${productName}"${category ? ' (category: ' + category + ')' : ''} and the description below, is "${specificValue}" a correct and accurate value for the eBay aspect "${specificName}"?\nDescription: ${description}${availableOptions}${compatibleContext}${conditionContext}\nConsider the product title as primary evidence. Reply with ONLY 'TRUE' if correct or 'FALSE' if not. Do not include any explanation.`;
    }

    let system_prompt = "ONLY return plain text. Do NOT wrap your answer in JSON or any object. Do not include any additional text or explanations.";
    if (isCompatibleExtract) {
        system_prompt += " Return compatible model values separated by semicolons. Each value MUST be prefixed with \"For [Brand]\" (e.g. \"For Brother DCP-9040CN\"). Use exact option list values when they match (they may already include the brand prefix); otherwise format as \"For [Brand] [Model]\" using the brand detected from context. If nothing found, return an empty string.";
    } else if (isMultiSelect) {
        system_prompt += " Separate multiple values with a semicolon. Return ONLY values from the provided list, matching exactly (case-sensitive).";
    } else {
        system_prompt += " Limit your response to a SHORT answer.";
    }
    if (availableOptions && !isCompatibleModel) {
        system_prompt += " You must ONLY return values from the provided list, matching exactly.";
    }

    const extra_system_prompt = "";

    const data = {
        prompt,
        system_prompt: system_prompt + extra_system_prompt,
        max_tokens: isCompatibleExtract ? 600 : 50,
        temperature: 0
    };

    console.log(`[AI INPUT] row=${row} specific="${specificName}"`, data);
    appendLoadingMessage(`[IA] Vérification de '${specificName}'...`, 'info');

    // Retry loop pour absorber les 429 rate-limit d'OpenAI
    let data_response = null;
    let retryDelay = 3000;
    const maxRetries = 3;
    for (let attempt = 0; attempt <= maxRetries; attempt++) {
        try {
            const response = await apiQueue.add(async () => {
                return await fetch('index.php?route=warehouse/tools/ai.prompt_ai&user_token=' + user_token, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
            });
            const text = await response.text();
            data_response = JSON.parse(text);
            console.log(`[AI OUTPUT] row=${row} specific="${specificName}"`, data_response);
        } catch (e) {
            appendLoadingMessage(`[ERREUR RÉSEAU] ${e.message}`, 'danger', true);
            if (finish === 'true') { finishLoadingPopup(); }
            return JSON.stringify({ success: false, error: e.message, row });
        }

        // Si erreur 429 / rate_limit → on attend puis on retry
        const errStr = (data_response.error || '').toLowerCase();
        if (errStr.includes('429') || errStr.includes('rate_limit') || errStr.includes('rate limit')) {
            if (attempt < maxRetries) {
                appendLoadingMessage(`[ATTENTE] Rate limit OpenAI, retry dans ${retryDelay/1000}s... (${attempt+1}/${maxRetries})`, 'warning');
                await new Promise(r => setTimeout(r, retryDelay));
                retryDelay *= 2;
                continue;
            }
        }
        break; // Pas de 429, on sort de la boucle
    }

    if (data_response.error) {
            appendLoadingMessage(`[ERREUR] ${data_response.error}`, 'danger', true);
            if( finish === 'true') { finishLoadingPopup(); }
            return JSON.stringify({ success: false, error: data_response.error, row });
        }
        
        // Ensure rawMessage is always a string
        let rawMessage = '';
        if (Array.isArray(data_response.success)) {
            rawMessage = data_response.success
                .map(item => typeof item === 'object' ? JSON.stringify(item) : String(item))
                .join(', ');
        } else if (typeof data_response.success === 'object' && data_response.success !== null) {
            // L'IA retourne parfois {"result":"TRUE"} ou {"answer":"TRUE"} ou {"value": false}
            const keys = Object.keys(data_response.success);
            if (keys.length === 1) {
                const val = data_response.success[keys[0]];
                // Ne pas utiliser `|| ''` car false (boolean) est falsy et serait converti en ''
                rawMessage = (val === null || val === undefined) ? '' : String(val);
            } else {
                rawMessage = JSON.stringify(data_response.success);
            }
        } else {
            rawMessage = String(data_response.success || '');
        }
        
        const messageLower = rawMessage.toLowerCase();
        let extractedValue = rawMessage.split(/is: |is |: /)[1]?.trim().replace(/\.$/, '') || rawMessage.trim().replace(/\.$/, '') || '';

        if (extractedValue.length > 70 && !extractedValue.includes(',') && !extractedValue.includes(';')) {
            extractedValue = extractedValue.substring(0, 70);
        }

        // === COMPATIBLE MODEL: handler dédié (seulement si extraction, pas vérif) ===
        if (isCompatibleExtract) {
            if (!rawMessage || rawMessage.toLowerCase() === 'none' || rawMessage.trim() === '') {
                appendLoadingMessage(`[COMPATIBLE MODEL] Aucun modèle trouvé par l'IA`, 'warning');
                if (finish === 'true') { finishLoadingPopup(); }
                return JSON.stringify({ success: false, message: 'No compatible models found', row });
            }

            // Séparer par ; ou , ou newline
            const returnedModels = rawMessage.split(/[;\n]+/).map(v => v.trim()).filter(v => v && v.toLowerCase() !== 'none');
            const selectElem = specificValueElem;
            const normalizedValues = [];

            for (const model of returnedModels) {
                // Chercher option existante (insensible à la casse)
                let existingOption = null;
                Array.from(selectElem.options).forEach(opt => {
                    if (opt.value.toLowerCase() === model.toLowerCase()) existingOption = opt;
                });
                if (existingOption) {
                    normalizedValues.push(existingOption.value);
                } else {
                    // Ajouter la nouvelle option dans le select
                    const safeModel = model.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    $(selectElem).append('<option value="' + safeModel + '">' + safeModel + '</option>');
                    normalizedValues.push(model);
                }
            }

            document.getElementById('original-value-' + language_id + '-' + row).textContent = '';
            document.getElementById('hidden-original-value-' + language_id + '-' + row).value = '';
            updateTargetElement(row, normalizedValues.join(','), 'orange');
            $('#btUndo-' + language_id + '-' + row).show();
            appendLoadingMessage(`[COMPATIBLE MODEL] ${normalizedValues.length} modèle(s): ${normalizedValues.join(', ')}`, 'success');
            console.log(`[COMPATIBLE MODEL] injected:`, normalizedValues);
            if (finish === 'true') { finishLoadingPopup(); }
            return JSON.stringify({ success: true, message: 'Compatible models injected.', row, value: normalizedValues.join(',') });
        }

        if ((messageLower === 'true' || messageLower.includes(specificValue.toLowerCase())) && specificValue.trim() !== "") {
            updateTargetElement(row, specificValue, 'green', 'no');
            appendLoadingMessage(`[OK] '${specificName}' vérifié comme valide.`, 'success');
            if( finish === 'true') { finishLoadingPopup(); }
            return JSON.stringify({ success: true, message: 'Validé.', row: row, value: specificValue });

        } else if ((!specificValue.trim() || extractedValue.trim()) && messageLower !== 'false') {
            document.getElementById('original-value-' + language_id + '-' + row).textContent = specificValueElem.value;
            document.getElementById('hidden-original-value-' + language_id + '-' + row).value = specificValueElem.value;

            // Préserver la casse du AI output — ne pas modifier extractedValue
            specificValue = extractedValue.replace(/;/g, ',');
            updateTargetElement(row, specificValue, 'orange');
            $('#btUndo' + row).show();
            appendLoadingMessage(`[INFO] '${specificName}' mis à jour par l'IA.`, 'warning');
            if( finish === 'true') { finishLoadingPopup(); }
            return JSON.stringify({ success: true, message: 'Mise à jour IA.', row: row, value: specificValue });

        } else if (messageLower === 'false') {
            appendLoadingMessage(`[IA] Valeur incorrecte pour '${specificName}', tentative de récupération...`, 'danger');
            
            try {
                const correctValue = await new Promise((resolve, reject) => {
                    getProductSpecific(specificName, function (error, value) {
                        if (error) reject(error);
                        else resolve(value);
                    });
                });
                
                if (correctValue.toLowerCase() === specificValue.toLowerCase()) {
                    updateTargetElement(row, correctValue, 'green', '');
                    appendLoadingMessage(`[OK] Valeur confirmée: '${correctValue}'`, 'success');
                    if( finish === 'true') { finishLoadingPopup(); }
                    return JSON.stringify({ success: true, message: 'Confirmé.', row, value: correctValue });
                } else {
                    updateTargetElement(row, correctValue, 'red', '');
                    appendLoadingMessage(`[CORRIGÉ] Valeur (${correctValue}) Reçue (${specificValue}) corrigée pour '${specificName}'`, 'danger');
                    if( finish === 'true') { finishLoadingPopup(); }
                    return JSON.stringify({ success: true, message: 'Corrigé.', row, value: correctValue });
                }
            } catch (error) {
                responseElem.textContent = 'Erreur de récupération';
                const fullError = error && error.stack ? `${error.message}\n${error.stack}` : typeof error === 'string' ? error : JSON.stringify(error);
                appendLoadingMessage(`[ERREUR] getProductSpecific: ${fullError}`, 'danger', true);
                if( finish === 'true') { finishLoadingPopup(); }
                return JSON.stringify({ success: false, error: fullError, row });
            }
        } else {
            responseElem.textContent = rawMessage;
            appendLoadingMessage(`[AVERTISSEMENT] Réponse inattendue: ${rawMessage}`, 'warning');
            if( finish === 'true') { finishLoadingPopup(); }
            return JSON.stringify({ success: false, message: rawMessage, row });
        }
}
function verifySpecificOLD(row, finish = 'false') {
    showLoadingPopup('Vérification des aspects avec l’IA en cours...');

    const user_token = document.querySelector('input[name="user_token"]').value;
    const Actual_value = document.getElementById('hidden-original-value-1-' + row).value;
    const specificRowElem = document.getElementById('specifics-1-' + row);

    if (!specificRowElem) {
        appendLoadingMessage(`[ERREUR] Élément introuvable pour la ligne ${row}`, 'danger');
        if( finish === 'true') { finishLoadingPopup(); }
        return JSON.stringify({ success: false, error: 'Specific row element not found', row: row });
    }

    const language_id = 1;
    const specificNameElem = specificRowElem.querySelector('input[name*="[Name]"]');
    const specificValueElem = specificRowElem.querySelector('input[name*="[Value]"], select[name*="[Value][]"], select[name*="[Value]"]');

    if (!specificNameElem || !specificValueElem) {
        appendLoadingMessage(`[ERREUR] Input ou select manquant pour la ligne ${row}`, 'danger');
        if( finish === 'true') { finishLoadingPopup(); }
        return JSON.stringify({ success: false, error: 'Required elements not found', row: row });
    }

    const specificName = specificNameElem.value.toLowerCase();
    let specificValue = '';

    if (specificValueElem.tagName.toLowerCase() === 'select') {
        const selectedOptions = Array.from(specificValueElem.selectedOptions).map(option => option.value);
        specificValue = selectedOptions.join(', ');
    } else {
        specificValue = specificValueElem.value;
    }

    const excludedKeys = ["language", "rating", "year", "region code", "country", "mpn"];
    if (excludedKeys.some(keyword => specificName.includes(keyword)) && specificValue.trim() !== "") {
        appendLoadingMessage(`[IGNORÉ] ${specificName} avec une valeur déjà définie`, 'warning');
        if( finish === 'true') { finishLoadingPopup(); }
        return JSON.stringify({ success: false, message: `Clé ignorée: ${specificName}`, row: row });
    }

    if (specificName.includes("item")) {
        appendLoadingMessage(`[IGNORÉ] ${specificName} contient 'item'`, 'warning');
        if( finish === 'true') { finishLoadingPopup(); }
        return JSON.stringify({ success: false, message: `Clé ignorée: ${specificName}`, row: row });
    }

    const productName = document.getElementById('product-description-1-0')?.value || '';
    const descriptionFull = document.getElementById('product-description-1-6')?.value || '';
    const description = descriptionFull.substring(0, 500); // Limit to 500 chars to prevent 429 token errors
    let category = document.getElementById('input-category')?.value || '';
    category = category.replace(/\s*\([^)]*\)/, '').trim();

    const responseElemId = `response-product-description-${language_id}-` + row;
    const responseElem = document.getElementById(responseElemId);

    if (!responseElem) {
        appendLoadingMessage(`[ERREUR] Élément réponse introuvable pour ${responseElemId}`, 'danger');
        if( finish === 'true') { finishLoadingPopup(); }
        return JSON.stringify({ success: false, error: `Response element not found`, row: row });
    }

    const prompt = (!specificValue.trim())
        ? `What is the most accurate and confirmed value for the eBay aspect \"${specificName}\" for the product titled \"${productName}\" in the \"${category}\" category?\nUse only information from reliable sources.`
        : `Is \"${specificValue}\" the correct and confirmed value for the \"${specificName}\" aspect of the product titled \"${productName}\" in the \"${category}\" category?\nDescription: ${description}\nReply with ONLY 'TRUE' if accurate or 'FALSE' if not. Do not include any explanation.`;

    let system_prompt = "ONLY return the answer without anything, Do not include any additional text or explanations.";
    if (specificValueElem.tagName.toLowerCase() === 'select' && specificValueElem.multiple) {
        system_prompt += " If the answer includes multiple details, separate them with a semicolon.";
    } else {
        system_prompt += " Limit your response to a SHORT answer.";
    }

    const extra_system_prompt = ((!specificValue.trim()) && specificName.includes("compatible")) 
        ? ` Must include before \"For \" the correct ${specificName.includes("model") ? "'BRAND NAME' 'MODEL NAME'" : "brand name"}.`
        : "";

    const data = {
        prompt,
        system_prompt: system_prompt + extra_system_prompt,
        max_tokens: 50,
        temperature: 0
    };

    appendLoadingMessage(`[IA] Vérification de '${specificName}'...`, 'info');

    return fetch('index.php?route=warehouse/tools/ai.prompt_ai&user_token=' + user_token, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(async response => {
        const text = await response.text();
        try {
            const data = JSON.parse(text);
            if (data.error) {
                appendLoadingMessage(`[ERREUR] ${data.error}`, 'danger', true);
                if( finish === 'true') { finishLoadingPopup(); }
                return JSON.stringify({ success: false, error: data.error, row });
            }
            const rawMessage = Array.isArray(data.success) ? data.success[0] : data.success;
            const messageLower = (rawMessage || '').toLowerCase();
            let extractedValue = rawMessage?.split(/is: |is |: /)[1]?.trim().replace(/\.$/, '') || rawMessage?.trim().replace(/\.$/, '') || '';

            if (extractedValue.length > 70 && !extractedValue.includes(',') && !extractedValue.includes(';')) {
                extractedValue = extractedValue.substring(0, 70);
            }

            if ((messageLower === 'true' || messageLower.includes(specificValue.toLowerCase())) && specificValue.trim() !== "") {
                updateTargetElement(row, specificValue, 'green', 'no');
                appendLoadingMessage(`[OK] '${specificName}' vérifié comme valide.`, 'success');
                if( finish === 'true') { finishLoadingPopup(); }
                return JSON.stringify({ success: true, message: 'Validé.', row: row, value: specificValue });

            } else if ((!specificValue.trim() || extractedValue.trim()) && messageLower !== 'false') {
                document.getElementById('original-value-' + language_id + '-' + row).textContent = specificValueElem.value;
                document.getElementById('hidden-original-value-' + language_id + '-' + row).value = specificValueElem.value;

                specificValue = extractedValue.replace(/;/g, ',');
                updateTargetElement(row, specificValue, 'orange');
                $('#btUndo' + row).show();
                appendLoadingMessage(`[INFO] '${specificName}' mis à jour par l'IA.`, 'warning');
                if( finish === 'true') { finishLoadingPopup(); }
                return JSON.stringify({ success: true, message: 'Mise à jour IA.', row: row, value: specificValue });

            } else if (messageLower === 'false') {
                appendLoadingMessage(`[IA] Valeur incorrecte pour '${specificName}', tentative de récupération...`, 'danger');
                getProductSpecific(specificName, function (error, correctValue) {
                    if (error) {
                        responseElem.textContent = 'Erreur de récupération';
                        const fullError = error && error.stack ? `${error.message}\n${error.stack}` : typeof error === 'string' ? error : JSON.stringify(error);
                        appendLoadingMessage(`[ERREUR] getProductSpecific: ${fullError}`, 'danger', true);
                        if( finish === 'true') { finishLoadingPopup(); }
                        return JSON.stringify({ success: false, error: fullError, row });
                    } else {
                        if (correctValue.toLowerCase() === specificValue.toLowerCase()) {
                            updateTargetElement(row, specificValue, 'green', '');
                            appendLoadingMessage(`[OK] Valeur confirmée: '${correctValue}'`, 'success');
                            if( finish === 'true') { finishLoadingPopup(); }
                            return JSON.stringify({ success: true, message: 'Confirmé.', row, value: correctValue });
                        } else {
                            updateTargetElement(row, specificValue, 'red', '');
                            appendLoadingMessage(`[CORRIGÉ] Valeur corrigée pour '${specificName}'`, 'danger');
                            if( finish === 'true') { finishLoadingPopup(); }
                            return JSON.stringify({ success: true, message: 'Corrigé.', row, value: correctValue });
                        }
                    }
                });
            } else {
                responseElem.textContent = rawMessage;
                appendLoadingMessage(`[AVERTISSEMENT] Réponse inattendue: ${rawMessage}`, 'warning');
                if( finish === 'true') { finishLoadingPopup(); }
                return JSON.stringify({ success: false, message: rawMessage, row });
            }
        } catch (e) {
            appendLoadingMessage(`[ERREUR] JSON invalide retourné : ${e.message}\nRéponse brute : ${text}`, 'danger', true);
            if( finish === 'true') { finishLoadingPopup(); }
            return JSON.stringify({ success: false, error: text, row });
        }
    })
    .catch(error => {
        const fullError = error && error.stack ? `${error.message}\n${error.stack}` : error.message || 'Unknown error';
        appendLoadingMessage(`[ERREUR] Fetch: ${fullError}`, 'danger', true);
        if( finish === 'true') { finishLoadingPopup(); }
        return JSON.stringify({ success: false, error: fullError, row });
    });
}


function verifySpecificOLDNEW(row) {
    var user_token = document.querySelector('input[name="user_token"]').value;
    var Actual_value = document.getElementById('hidden-original-value-1-' + row).value;
    var specificRowElem = document.getElementById('specifics-1-' + row);
    if (!specificRowElem) {
        return JSON.stringify({ success: false, error: 'Specific row element not found', row: row });
    }

    var language_id = 1;
    var specificNameElem = specificRowElem.querySelector('input[name*="[Name]"]');
    var specificValueElem = specificRowElem.querySelector('input[name*="[Value]"], select[name*="[Value][]"], select[name*="[Value]"]');

    if (!specificNameElem || !specificValueElem) {
        return JSON.stringify({ success: false, error: 'Required elements not found', row: row });
    }

    var specificName = specificNameElem.value.toLowerCase();
    var specificValue = '';

    if (specificValueElem.tagName.toLowerCase() === 'select') {
        var selectedOptions = Array.from(specificValueElem.selectedOptions).map(option => option.value);
        specificValue = selectedOptions.join(', ');
    } else {
        specificValue = specificValueElem.value;
    }

    // Exclure certaines clés si la valeur n'est pas vide
    const excludedKeys = ["language", "rating", "year", "region code", "country", "mpn"];
    if (excludedKeys.some(keyword => specificName.includes(keyword)) && specificValue.trim() !== "") {
        return JSON.stringify({ success: false, message: `Skipping verification for ${specificName} as it has a value.`, row: row });
    }

    // Exclure toute clé contenant "item", même si la valeur est vide
    if (specificName.includes("item")) {
        return JSON.stringify({ success: false, message: `Skipping verification for key containing "item": ${specificName}`, row: row });
    }

    var productNameElem = document.getElementById('product-description-1-0');
    var productName = productNameElem ? productNameElem.value : '';

    var descriptionElem = document.getElementById('product-description-1-6');
    var description = descriptionElem ? descriptionElem.value : '';

    var categoryElem = document.getElementById('input-category');
    var category = categoryElem ? categoryElem.value : '';

    if (category) {
        category = category.replace(/\s*\([^)]*\)/, '').trim();
    }

    var responseElemId = `response-product-description-${language_id}-` + row;
    var responseElem = document.getElementById(responseElemId);

    if (!responseElem) {
        return JSON.stringify({ success: false, error: `Response element not found for specificName: ${specificName}`, row: row });
    }

    var prompt = (!specificValue || specificValue.trim() === "")
        ? `Please provide the "${specificName}" most accurate and concise Aspect from the internet for the product: "${productName}" ${category}.`
        : `Verify if the "${specificName}" is (${specificValue}) value for the product: "${productName}" is accurate from the internet. Reply with **ONLY** 'TRUE' if it is accurate or 'FALSE' if it is not. Do not include any additional text or explanations.`;

    var system_prompt = "ONLY return the answer without anything, Do not include any additional text or explanations.";
    if (specificValueElem.tagName.toLowerCase() === 'select' && specificValueElem.multiple) {
        system_prompt += " If the answer includes multiple details, separate them with a semicolon.";
    } else {
        system_prompt += " Limit your response to a SHORT answer.";
    }

    var extra_system_prompt = ((!specificValue || specificValue.trim() === "") && specificName.includes("compatible")) 
        ? ` Must include before "For " the correct ${specificName.includes("model") ? "'BRAND NAME' 'MODEL NAME'" : "brand name"}.`
        : "";

    var data = {
        prompt: prompt,
        system_prompt: system_prompt + extra_system_prompt,
        max_tokens: 50,
        temperature: 0   
    };

    return fetch('index.php?route=warehouse/tools/ai.prompt_ai&user_token=' + user_token, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            return JSON.stringify({ success: false, error: data.error, row: row });
        }
        if (data.success) {
            var messageLower = data.success.toLowerCase();
            var extractedValue = '';

            if (messageLower.includes('is: ')) {
                extractedValue = data.success.split('is: ')[1].trim().replace(/\.$/, '');
            } else if (messageLower.includes('is ')) {
                extractedValue = data.success.split('is ')[1].trim().replace(/\.$/, '');
            } else if (messageLower.includes(': ')) {
                extractedValue = data.success.split(': ')[1].trim().replace(/\.$/, '');
            } else {
                extractedValue = data.success.trim().replace(/\.$/, '');
            }

            if (extractedValue.length > 70 && !extractedValue.includes(',') && !extractedValue.includes(';')) {
                extractedValue = extractedValue.substring(0, 70);
            }

            if ((messageLower.includes('true') || messageLower.includes(specificValue.toLowerCase())) && specificValue.trim() !== "") {
                updateTargetElement(row, specificValue, 'green', 'no');
                return JSON.stringify({ success: true, message: 'Value is verified as accurate.', row: row, value: specificValue });
            } else if ((specificValue.trim() === "" || extractedValue.trim() !== "") && extractedValue.toLowerCase() !== 'false') {
                var originalValue = specificValueElem.value;
                document.getElementById('original-value-' + language_id + '-' + row).textContent = originalValue;
                document.getElementById('hidden-original-value-' + language_id + '-' + row).value = originalValue;

                specificValue = extractedValue.replace(/;/g, ',');
                updateTargetElement(row, specificValue, 'orange');
                $('#btUndo' + row).show();
                return JSON.stringify({ success: true, message: 'Value updated.', row: row, value: specificValue });
            } else if (messageLower.includes('false')) {
                getProductSpecific(specificName, function (error, correctValue) {
                    if (error) {
                        responseElem.textContent = 'Error fetching correct value.';
                        return JSON.stringify({ success: false, error: 'Error fetching correct value.', row: row });
                    } else {
                        if (correctValue.toLowerCase() === specificValue.toLowerCase()) {
                            updateTargetElement(row, specificValue, 'green', 'no');
                            return JSON.stringify({ success: true, message: 'Correct value found.', row: row, value: specificValue });
                        } else {
                            updateTargetElement(row, correctValue, 'red', 'no');
                            return JSON.stringify({ success: true, message: 'Corrected value.', row: row, value: correctValue });
                        }
                    }
                });
            } else {
                responseElem.textContent = data.success;
                return JSON.stringify({ success: false, message: data.success, row: row });
            }
        } else {
            return JSON.stringify({ success: false, error: 'Unknown error', row: row });
        }
    })
    .catch(error => {
        return JSON.stringify({ success: false, error: error.message, row: row });
    });
}

function formatCompatibleModels(specificValue) {
    if (!specificValue) return "";

    // Remplace les séparateurs possibles ("/", ",", " and ") par une virgule
    specificValue = specificValue.replace(/\s*\/\s*|\s*,\s*|\s+and\s+/g, ',');

    // Divise la chaîne en une liste
    let models = specificValue.split(',');

    // Ajoute "For " devant chaque modèle et nettoie les espaces
    models = models.map(model => `For ${model.trim()}`);

    // Rejoint les modèles sous forme de chaîne séparée par des virgules
    return models.join(',');
}

function updateTargetElement(row, value, color, translate = '') {
    var specificRowElem = document.getElementById('specifics-1-' + row);
    if (!specificRowElem) {
        console.error('Row element not found:', row);
        return;
    }

    var targetElement = $('#product-description-1-' + row);
    var valueLowerCase = value.toLowerCase().trim(); // Convertir en minuscule pour vérifier "false"

    // Forcer la couleur rouge si la valeur est "false"
    if (valueLowerCase === "false") {
        color = "red";
    }

    // Mapper les couleurs vers des codes hex pour éviter les conflits Bootstrap
    var colorMap = {
        'green': '#28a745',
        'red': '#dc3545',
        'orange': '#fd7e14'
    };
    var bgColor = colorMap[color] || color;

    // Appliquer le style à la ligne <tr> avec !important
    specificRowElem.style.setProperty('background-color', bgColor, 'important');
    specificRowElem.style.setProperty('color', 'white', 'important');

    // Appliquer aussi aux cellules <td> pour que la couleur soit visible
    var tdElements = specificRowElem.querySelectorAll('td');
    tdElements.forEach(function(td) {
        td.style.setProperty('background-color', bgColor, 'important');
        td.style.setProperty('color', 'white', 'important');
    });

    // Gestion des champs SELECT - ensure value is always a string
    var suggestion = '';
    if (Array.isArray(value)) {
        suggestion = value
            .map(item => typeof item === 'object' ? JSON.stringify(item) : String(item))
            .join(', ');
    } else if (typeof value === 'object' && value !== null) {
        suggestion = JSON.stringify(value);
    } else {
        suggestion = String(value || '');
    }
    
    var languages = {};
    try {
        languages = JSON.parse($('#languages-json').val());
    } catch (e) {}

    if (color === 'orange') {
        // Propagation à toutes les langues
        var toTranslate = 0;
        var toTranslateFieldId = 'to-translate-1-' + row;
        
        
        // Vérifier d'abord le champ spécifique au row
        var specificField = $('#' + toTranslateFieldId);
        if (specificField.length && specificField.val()) {
            var toTranslateVal = specificField.val();
            if (toTranslateVal === '1' || toTranslateVal === 1 || toTranslateVal === true || toTranslateVal === 'true') {
                toTranslate = 1;
            }
        } 
        // Sinon vérifier le champ global to-translate (pour compatibilité)
        else if ($('#to-translate').length && $('#to-translate').val()) {
            try {
                var toTranslateVal = $('#to-translate').val();
                // Accept both boolean and string/number
                if (toTranslateVal === '1' || toTranslateVal === 1 || toTranslateVal === true || toTranslateVal === 'true') {
                    toTranslate = 1;
                }
            } catch (e) {}
        }
        
        
        Object.keys(languages).forEach(function(language_id) {
            var langTarget = $('#product-description-' + language_id + '-' + row);
            if (langTarget.length) {
                // Check if this is an eBay specifics select (restricted values)
                var isEbaySpecifics = langTarget.is('select') && 
                    langTarget.attr('name') && 
                    langTarget.attr('name').includes('[specifics]');
                
                // Always update language 1 (source) first
                if (language_id === '1') {
                    // Update the source field
                    if (langTarget.is('select')) {
                        var values = suggestion.split(',').map(val => val.trim());
                        var normalizedValues = [];
                        values.forEach(function (val) {
                            // Skip invalid values
                            if (!val || val === '[object Object]' || val === 'undefined' || val === 'null') {
                                return;
                            }
                            
                            var existingOption = null;
                            langTarget.find('option').each(function() {
                                if ($(this).val().toLowerCase() === val.toLowerCase()) {
                                    existingOption = $(this);
                                    return false;
                                }
                            });
                            if (existingOption) {
                                normalizedValues.push(existingOption.val());
                            } else if (!isEbaySpecifics) {
                                // Only add new options if NOT eBay specifics
                                var safeVal = String(val).trim();
                                if (safeVal && safeVal !== '[object Object]') {
                                    langTarget.prepend('<option value="' + safeVal + '">' + safeVal + '</option>');
                                    normalizedValues.push(safeVal);
                                }
                            } else {
                                // For eBay specifics, try fuzzy match
                                var fuzzyMatch = null;
                                langTarget.find('option').each(function() {
                                    var optVal = $(this).val().toLowerCase();
                                    var searchVal = val.toLowerCase();
                                    if (optVal.includes(searchVal) || searchVal.includes(optVal)) {
                                        fuzzyMatch = $(this);
                                        return false;
                                    }
                                });
                                if (fuzzyMatch) {
                                    normalizedValues.push(fuzzyMatch.val());
                                    appendLoadingMessage(`[INFO] Match approximatif: "${val}" → "${fuzzyMatch.val()}"`, 'warning');
                                } else {
                                    appendLoadingMessage(`[ERREUR] Valeur "${val}" non trouvée dans les options eBay disponibles`, 'danger');
                                }
                            }
                        });
                        langTarget.val(normalizedValues).trigger('change');
                    } else if (langTarget.is('input[type="text"]')) {
                        langTarget.val(suggestion);
                    }
                } else if (toTranslate && language_id !== '1') {
                    // Traduire depuis la langue principale (1) vers les langues cibles
                    // Utilise la fonction existante pour traduire le champ
                    translateContentForAllLanguages('product-description-1-' + row, '', 'product');
                    // On ne fait rien d'autre ici, la traduction s'occupera de remplir les champs
                } else {
                    // Copier la valeur dans tous les champs (si pas de traduction)
                    if (langTarget.is('select')) {
                        var values = suggestion.split(',').map(val => val.trim());
                        var normalizedValues = [];
                        values.forEach(function (val) {
                            // Skip invalid values
                            if (!val || val === '[object Object]' || val === 'undefined' || val === 'null') {
                                return;
                            }
                            
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
                                // Ensure val is a proper string before adding
                                var safeVal = String(val).trim();
                                if (safeVal && safeVal !== '[object Object]') {
                                    langTarget.prepend('<option value="' + safeVal + '">' + safeVal + '</option>');
                                    normalizedValues.push(safeVal);
                                }
                            }
                        });
                        langTarget.val(normalizedValues).trigger('change');
                    } else if (langTarget.is('input[type="text"]')) {
                        langTarget.val(suggestion);
                    }
                }
            }
        });
    } else if (color === 'red') {
        // Pour rouge, mettre à jour la colonne 3 avec un bouton de transfert
        var transferButtonHtml = '<button type="button" class="btn btn-sm btn-primary" onclick="transferAiSuggestion(' + row + ')" title="Transfer AI Suggestion" style="margin-left: 10px;">' +
            '<i class="fa fa-arrow-right"></i>' +
            '</button>';
        $('#original-value-1-' + row).html(suggestion + ' ' + transferButtonHtml);
        
        // Stocker la suggestion dans un attribut data pour pouvoir la récupérer
        $('#original-value-1-' + row).attr('data-suggestion', suggestion);
    } else if (color === 'green') {
        // Pour vert, vider la colonne 3
        $('#original-value-1-' + row).text('');
    }
}

// Fonction pour transférer la suggestion de l'IA vers le champ du formulaire
function transferAiSuggestion(row) {
    var suggestion = $('#original-value-1-' + row).attr('data-suggestion');
    var targetElement = $('#product-description-1-' + row);
    
    if (!suggestion || !targetElement.length) {
        console.error('Unable to transfer suggestion for row:', row);
        return;
    }
    
    // Stocker la valeur originale pour permettre un undo
    var originalValue;
    if (targetElement.is('select[multiple]')) {
        originalValue = targetElement.val() ? targetElement.val().join(',') : '';
    } else {
        originalValue = targetElement.val();
    }
    $('#hidden-original-value-1-' + row).val(originalValue);
    
    // Appliquer la nouvelle valeur dans le champ source (langue 1)
    if (targetElement.is('select[multiple]')) {
        // Détecter le séparateur: & ou ,
        var separator = suggestion.includes(' & ') ? ' & ' : ',';
        var values = suggestion.split(separator).map(val => val.trim());
        var normalizedValues = [];
        values.forEach(function(val) {
            var existingOption = null;
            targetElement.find('option').each(function() {
                if ($(this).val().toLowerCase() === val.toLowerCase()) {
                    existingOption = $(this);
                    return false;
                }
            });
            if (existingOption) {
                normalizedValues.push(existingOption.val());
            } else {
                targetElement.prepend('<option value="' + val + '">' + val + '</option>');
                normalizedValues.push(val);
            }
        });
        targetElement.val(normalizedValues).trigger('change');
    } else if (targetElement.is('select')) {
        var existingOption = null;
        targetElement.find('option').each(function() {
            if ($(this).val().toLowerCase() === suggestion.toLowerCase()) {
                existingOption = $(this);
                return false;
            }
        });
        if (existingOption) {
            targetElement.val(existingOption.val()).trigger('change');
        } else {
            targetElement.prepend('<option value="' + suggestion + '">' + suggestion + '</option>');
            targetElement.val(suggestion).trigger('change');
        }
    } else {
        targetElement.val(suggestion);
    }
    
    // Changer la couleur de la ligne en orange (comme le code existant)
    var specificRowElem = document.getElementById('specifics-1-' + row);
    if (specificRowElem) {
        specificRowElem.style.setProperty('background-color', '#fd7e14', 'important');
        specificRowElem.style.setProperty('color', 'white', 'important');
        var tdElements = specificRowElem.querySelectorAll('td');
        tdElements.forEach(function(td) {
            td.style.setProperty('background-color', '#fd7e14', 'important');
            td.style.setProperty('color', 'white', 'important');
        });
    }
    
    // Vider la colonne 3
    $('#original-value-1-' + row).text('');
    
    // Vérifier si la traduction est activée
    var toTranslateFieldId = 'to-translate-1-' + row;
    var specificField = $('#' + toTranslateFieldId);
    
    if (specificField.length && specificField.val() === '1') {
        // Utiliser la même logique que le code orange
       // translateContentForAllLanguages('product-description-1-' + row, '', 'product');
    } else {
        // Si pas de traduction, copier directement dans toutes les langues
        var languages = {};
        try {
            languages = JSON.parse($('#languages-json').val());
        } catch (e) {}
        
        Object.keys(languages).forEach(function(language_id) {
            if (language_id === '1') return; // Skip source language
            
            var langTarget = $('#product-description-' + language_id + '-' + row);
            if (langTarget.length) {
                if (langTarget.is('select')) {
                    // Détecter le séparateur: & ou ,
                    var separator = suggestion.includes(' & ') ? ' & ' : ',';
                    var values = suggestion.split(separator).map(val => val.trim());
                    var normalizedValues = [];
                    values.forEach(function (val) {
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
                } else if (langTarget.is('input[type="text"]')) {
                    langTarget.val(suggestion);
                }
            }
        });
    }
    
    // Afficher le bouton Undo et masquer Transfer
    $('#btTrf' + row).hide();
    $('#btUndo' + row).show();
}






async function correctSpellingAndFormat(fieldName  = 'product') {

    var product_id = document.querySelector('input[name="' + fieldName + '_id"]').value;
    try {
        var form = document.getElementById('form-' + fieldName);
        if (!form) {
            console.error('Form element not found');
            return;
        }
        var recognizedTextElement = document.getElementById('recognizedText');
        if (!recognizedTextElement) {
            console.error('recognizedText element not found');
            return;
        }

        // Préparer le texte avec les sauts de ligne supprimés
        var cleanedText = recognizedTextElement.value.replace(/[\r\n]+/g, ' ');
        var prompt = `Correct any spelling mistakes in the following text: "${cleanedText}"`;

        var data = buildAiData(prompt, `Only return correction result OR return "${cleanedText}"`, 100, 0.3);
        var aiResponse = await fetchAi(data); // Envoyer la requête à l'IA

        // Raccourcir le texte si la réponse dépasse 80 caractères
        
        // Sélectionner les éléments de résultat
        var aiResultElement = document.getElementById('recognizedText');
      
        
        // Afficher le résultat dans les éléments spécifiés
        if (aiResultElement) {
           // aiResultElement.value = aiResponse;
       
        } else {
            console.error('AI result element not found');
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Fonction suggestManufactuer
async function suggestManufactuer() {
    const search = document.getElementById('input-search');
    const manufacturerField = document.getElementById('input-manufacturer');
    const aiResultElement = document.getElementById('recognizedText');

    if (!search || !manufacturerField) {
        console.error('Required elements not found');
        return;
    }

   // Préparer le texte source
   let cleanedText;
   if (aiResultElement.value.trim() !== "") {
       // Convertir la valeur de aiResultElement en texte brut
       cleanedText = aiResultElement.value
           .replace(/[\r\n]+/g, ' ') // Supprimer les sauts de ligne
           .replace(/\s+/g, ' ') // Remplacer plusieurs espaces par un seul
           .trim(); // Supprimer les espaces en début et fin
   } else if (search.value.trim() !== "") {
       // Si aiResultElement est vide, utiliser le champ `search`
       cleanedText = search.value
           .replace(/[\r\n]+/g, ' ')
           .replace(/\s+/g, ' ')
           .trim();
   } else {
       console.error('No valid input found for AI suggestion');
       return;
   }

   if (cleanedText === "") {
       console.error('Input text is empty after cleaning');
       return;
   }

    // Construire le prompt pour l'IA
    const prompt = `Based on "${cleanedText}". Suggest a suitable manufacturer or brand name for this product.`;
    const systemPrompt = "Return ONLY manufacturer name without anything else";
    const data = buildAiData(prompt, systemPrompt, 100, 0.3);

    try {
        const aiResponse = await fetchAi(data);
    
        if (aiResponse) {
            let responseText = '';
    
            if (Array.isArray(aiResponse)) {
                // Si c'est un array, on prend le premier élément
                responseText = aiResponse.length > 0 ? aiResponse[0] : '';
            } else if (typeof aiResponse === 'string') {
                responseText = aiResponse.trim();
            }
    
            if (responseText !== '') {
                manufacturerField.value = responseText;
                ChangeManufacturer(responseText);
            } else {
                console.error('Empty response from AI');
            }
        } else {
            console.error('No response from AI');
        }
    } catch (error) {
        console.error('Error during AI fetch:', error);
    }
    
}

// Fonction suggestManufactuer
async function suggestManufactuerNAME(recognizedTextElement) {
    var fieldName = 'product';
    const manufacturerField = document.getElementById('input-manufacturer');

    var product_id = document.querySelector('input[name="' + fieldName + '_id"]').value;

    //alert(fieldName);
    var form = document.getElementById('form-' + fieldName);
    if (!form) {
        console.error('Form element not found');
        return;
    }
    if (!recognizedTextElement) {
       
    
   /*     var excludeSelectors = [ // '[name="' + fieldName + '_description[1][description_supp]"]'
            '#language2 input, #language2 select, #language2 textarea',
            '#specifics-language-2 input, #specifics-language-2 select, #specifics-language-2 textarea',
            '[name="' + fieldName + '_description[2][description]"]',  '[name="price"]','[name="shipping_cost"]', '[name="shipping_cost"]',
            '[name="' + fieldName + '_description[1][meta_description]"]', '[name="' + fieldName + '_description[1][meta_description]"]',
            '[name="weight"]', '[name="weight_oz"]','[name="lenght"]', '[name="width"]','[name="height"]','[name="upc"]','[name="sku"]',
            '[name="marketplace_item_id"]'
           
        ];*/
        var excludeSelectors = [
            // Langues et descriptions inutilisées
            '#language2 input, #language2 select, #language2 textarea',
            '#specifics-language-2 input, #specifics-language-2 select, #specifics-language-2 textarea',
            '[name="' + fieldName + '_description[1][name]"]',
            '[name="' + fieldName + '_description[1][description]"]',
            '[name="' + fieldName + '_description[1][description_supp]"]',
            '[name="' + fieldName + '_description[1][meta_title]"]',
            '[name="' + fieldName + '_description[1][meta_keyword]"]',
            '[name="' + fieldName + '_description[1][keyword]"]',
            '[name="' + fieldName + '_description[1][tag]"]',
           // '[name="' + fieldName + '_description[1][description_supp]"]',
            '[name="' + fieldName + '_description[1][included_accessories]"]',
        
            // Exclure tout ce qui commence par "' + fieldName + '_description[2]"
            '[name^="' + fieldName + '_description[2]"]',
        
            // Dimensions et poids
            '[name="weight"]',
            '[name="weight_oz"]',
            '[name="length"]',
            '[name="width"]',
            '[name="height"]',
        
            // Identifiants spécifiques
            '[name="upc"]',
            '[name="sku"]',
            '[name="marketplace_item_id"]',
        
            // Tous les "price_ebay"
            '[name^="price_ebay"]',
        
            // Localisation ou quantité non allouée
            '[name="quantity"]',
            '[name="location"]',
            '[name="unallocated_quantity"]',
        
            // Conditions
            '[name="conditions_json1"]',
        
            // Tous les champs contenant "json"
            '[name*="json"]',
        
            // Tous les "checkbox"
            '[name^="checkbox"]',
            '[id^="checkbox-none"]',
            '[name^="marketplace_items"]',
            'input[type="hidden"][id^="hidden-original-value-1-"]',

        
            // Coûts
            '[name="price"]',
            '[name="shipping_cost"]',
            '[name="price_with_shipping"]',
        
            // Autres
            '[name="files"]',
            '[name="shipping_carrier"]',
            '[name="marketplace_item_id"]',
            '[name="sku"]',
            '[name="' + fieldName + '_id"]',
            '[name="user_token"]',

            '[name="condition_id"]',
            '[name="length_class_id"]',
            '[name="weight_class_id"]',
            '[name="manufacturer_id"]',
            '[name="manufacturer"]',
            '[name="category_id"]',
            '[name="site_id"]',
            '[name="' + fieldName + '_category[]"]',

            '[name="checkbox-none"]',
            '[name="sourcecode"]',
            '[name="image"]',
            '[name^="' + fieldName + '_image"]',
            '[name*="[VerifiedSource]"]',
            '[name^="VerifiedSource"]',
            '[name^="hidden_"]',

            '[name="tax_class_id"]',
            '[name="minimum"]',
            '[name="subtract"]',
            '[name="stock_status_id"]',
            '[name="shipping"]',

            '[name="date_available"]',
            '[name="status"]',
            '[name="sort_order"]',
            '[name="filter"]',
            '[name="' + fieldName + '_store[]"]',

            '[name="download"]',
            '[name="related"]',
            '[name="option"]',
            '[name="points"]',
            '[name^="' + fieldName + '_reward"]',
            '[name^="' + fieldName + '_layout"]',
            '[name^="tag"]',
            '[name^="keyword"]',
            '[name^="keyword"]',
            '[name^="color"]',

        ];
        
    
        var elements = form.querySelectorAll('input, select, textarea');
        var formData = {};
    
        elements.forEach(function (element) {
            if (!element.matches(excludeSelectors.join(','))) {
                var name = element.name || element.id;
                if (name) {
                    formData[name] = element.value;
                }
            }
        });
    // Nettoyer les champs HTML avant conversion en JSON
            formData = stripHtmlTagsFromFormData(formData);

            // 3. Transformer formData en chaîne formatée
            var cleanedText = transformDataToFormattedString(formData);

            // 4. Enlever les doublons dans le texte
            cleanedText = removeDuplicateKeys(cleanedText);
            cleanedText = JSON.stringify(cleanedText);
      
    } else{
       // formData = stripHtmlTagsFromFormData(formData);Id('recognizedText');
        var cleanedText = recognizedTextElement.value.replace(/[\r\n]+/g, ' ');
      //  var cleanedText = recognizedTextElement.value;
    }             
    //e.error('recognizedText element not found');
   // var productName = form.querySelector('input[name="' + fieldName + '_description[1][name]"]').value;
   /* if (!productName) {pHtmlTagsFromFormData(formData);
        var cleanedText = recognizedTextElement.value.replace(/[\r\n]+/g, ' ');
      //  var cleanedText = recognizedTextElement.value;
    }             
   // var productName = form.querySelector('input[name="' + fieldName + '_description[1][name]"]').value;
   /* if (!productName) {
        console.error('Product name not found');
        return;
    }*/
    
       // var prompt = `Generate a concise and accurate title for a product with the following details: ${JSON.stringify(formData)}.`;
    //    var prompt = `Generate a product title with a length between 70 and 80 characters for the following details: ${JSON.stringify(formData)}. Ensure the title does not exceed 80 characters.`;
    
 // Construire le prompt pour l'IA
 const prompt = `Based on "${cleanedText}". Suggest a suitable manufacturer or brand name, or label record or distributor or producter for this product.`;
 const systemPrompt = "Return ONLY manufacturer name in json format {'manufacturer':} without anything else";
 const data = buildAiData(prompt, systemPrompt, 100, 0.3);

 try {
     // Appel à l'IA
     const aiResponse = await fetchAi(data);

     if (aiResponse && aiResponse.manufacturer !== "") {
         // Afficher le résultat dans le champ manufacturer
         manufacturerField.value =  '' + aiResponse.manufacturer;
         
     } else {
         console.error('No response from AI');
     }
 } catch (error) {
     console.error('Error during AI fetch:', error);
 } 
}
// Fonction suggestModel
async function suggestModel() {
    const search = document.getElementById('input-search');
 const manufacturerField = document.getElementById('input-model');
 const aiResultElement = document.getElementById('recognizedText');

 if (!search || !manufacturerField) {
     console.error('Required elements not found');
     return;
 }

// Préparer le texte source
let cleanedText;
if (aiResultElement.value.trim() !== "") {
    // Convertir la valeur de aiResultElement en texte brut
    cleanedText = aiResultElement.value
        .replace(/[\r\n]+/g, ' ') // Supprimer les sauts de ligne
        .replace(/\s+/g, ' ') // Remplacer plusieurs espaces par un seul
        .trim(); // Supprimer les espaces en début et fin
} else if (search.value.trim() !== "") { 
    // Si aiResultElement est vide, utiliser le champ `search`
    cleanedText = search.value
        .replace(/[\r\n]+/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
} else {
    console.error('No valid input found for AI suggestion');
    return;
}

if (cleanedText === "") {
    console.error('Input text is empty after cleaning');
    return;
}

 // Construire le prompt pour l'IA
 const prompt = `Based on "${cleanedText}". Suggest a suitable model number for this product.`;
 const systemPrompt = "Return ONLY model number without anything else";
 const data = buildAiData(prompt, systemPrompt, 100, 0.3);
 try {
    const aiResponse = await fetchAi(data);

    if (aiResponse) {
        let responseText = '';

        if (Array.isArray(aiResponse)) {
            // Si c'est un array, on prend le premier élément
            responseText = aiResponse.length > 0 ? aiResponse[0] : '';
        } else if (typeof aiResponse === 'string') {
            responseText = aiResponse.trim();
        }

        if (responseText !== '') {
            manufacturerField.value = responseText;
        } else {
            console.error('Empty response from AI');
        }
    } else {
        console.error('No response from AI');
    }
} catch (error) {
    console.error('Error during AI fetch:', error);
}

} 


async function suggestEntryName(fieldName = 'product', recognizedTextElement) {

    var product_id = document.querySelector('input[name="' + fieldName + '_id"]').value;

    //alert(fieldName);
    var form = document.getElementById('form-' + fieldName);
    if (!form) {
        console.error('Form element not found');
        return;
    }
    if (!recognizedTextElement) {
       
    
   /*     var excludeSelectors = [ // '[name="' + fieldName + '_description[1][description_supp]"]'
            '#language2 input, #language2 select, #language2 textarea',
            '#specifics-language-2 input, #specifics-language-2 select, #specifics-language-2 textarea',
            '[name="' + fieldName + '_description[2][description]"]',  '[name="price"]','[name="shipping_cost"]', '[name="shipping_cost"]',
            '[name="' + fieldName + '_description[1][meta_description]"]', '[name="' + fieldName + '_description[1][meta_description]"]',
            '[name="weight"]', '[name="weight_oz"]','[name="lenght"]', '[name="width"]','[name="height"]','[name="upc"]','[name="sku"]',
            '[name="marketplace_item_id"]'
           
        ];*/
        var excludeSelectors = [
            // Langues et descriptions inutilisées
            '#language2 input, #language2 select, #language2 textarea',
            '#specifics-language-2 input, #specifics-language-2 select, #specifics-language-2 textarea',
            '[name="' + fieldName + '_description[1][description]"]',
            '[name="' + fieldName + '_description[1][meta_title]"]',
            '[name="' + fieldName + '_description[1][meta_keyword]"]',
            '[name="' + fieldName + '_description[1][keyword]"]',
            '[name="' + fieldName + '_description[1][tag]"]',
           // '[name="' + fieldName + '_description[1][description_supp]"]',
            '[name="' + fieldName + '_description[1][included_accessories]"]',
        
            // Exclure tout ce qui commence par "' + fieldName + '_description[2]"
            '[name^="' + fieldName + '_description[2]"]',
        
            // Dimensions et poids
            '[name="weight"]',
            '[name="weight_oz"]',
            '[name="length"]',
            '[name="width"]',
            '[name="height"]',
        
            // Identifiants spécifiques
            '[name="upc"]',
            '[name="sku"]',
            '[name="marketplace_item_id"]',
        
            // Tous les "price_ebay"
            '[name^="price_ebay"]',
        
            // Localisation ou quantité non allouée
            '[name="quantity"]',
            '[name="location"]',
            '[name="unallocated_quantity"]',
        
            // Conditions
            '[name="conditions_json1"]',
        
            // Tous les champs contenant "json"
            '[name*="json"]',
        
            // Tous les "checkbox"
            '[name^="checkbox"]',
            '[id^="checkbox-none"]',
            '[name^="marketplace_items"]',
            'input[type="hidden"][id^="hidden-original-value-1-"]',

        
            // Coûts
            '[name="price"]',
            '[name="shipping_cost"]',
            '[name="price_with_shipping"]',
        
            // Autres
            '[name="files"]',
            '[name="shipping_carrier"]',
            '[name="marketplace_item_id"]',
            '[name="sku"]',
            '[name="' + fieldName + '_id"]',
            '[name="user_token"]',

            '[name="condition_id"]',
            '[name="length_class_id"]',
            '[name="weight_class_id"]',
            '[name="manufacturer_id"]',
            '[name="category_id"]',
            '[name="site_id"]',
            '[name="' + fieldName + '_category[]"]',

            '[name="checkbox-none"]',
            '[name="sourcecode"]',
            '[name="image"]',
            '[name^="' + fieldName + '_image"]',
            '[name*="[VerifiedSource]"]',
            '[name^="VerifiedSource"]',
            '[name^="hidden_"]',

            '[name="tax_class_id"]',
            '[name="minimum"]',
            '[name="subtract"]',
            '[name="stock_status_id"]',
            '[name="shipping"]',

            '[name="date_available"]',
            '[name="status"]',
            '[name="sort_order"]',
            '[name="filter"]',
            '[name="' + fieldName + '_store[]"]',

            '[name="download"]',
            '[name="related"]',
            '[name="option"]',
            '[name="points"]',
            '[name^="' + fieldName + '_reward"]',
            '[name^="' + fieldName + '_layout"]',
            '[name^="tag"]',
            '[name^="keyword"]',
            '[name^="keyword"]',
            '[name^="color"]',

            // Unit et informations supplémentaires (avec suffixes possibles)
            '[name*="unit_quantity"]',
            '[name*="unit_type"]',
            '[name*="master_id"]',
            '[name*="manufacturer"]',
            '[name*="model"]',
            '[name*="discount"]',
            '[name*="made_in_country"]',
            '[name*="url_product"]',
            '[name*="marketplace_name"]',

        ];
        
    
        var elements = form.querySelectorAll('input, select, textarea');
        var formData = {};

        elements.forEach(function (element) {
            if (!element.matches(excludeSelectors.join(','))) {
                var name = element.name || element.id;
                if (name) {
                    formData[name] = element.value;
                }
            }
        });

   // 2. Nettoyer les balises HTML du formData
        formData = stripHtmlTagsFromFormData(formData);

        // 3. Transformer formData en chaîne formatée
        var cleanedText = transformDataToFormattedString(formData);

        // 4. Enlever les doublons dans le texte
        cleanedText = removeDuplicateKeys(cleanedText);
        cleanedText = JSON.stringify(cleanedText);
      
    } else{
       // formData = stripHtmlTagsFromFormData(formData);Id('recognizedText');
        var cleanedText = recognizedTextElement.value.replace(/[\r\n]+/g, ' ');
      //  var cleanedText = recognizedTextElement.value;
    }             
    //e.error('recognizedText element not found');
    let category_id = $('#category_id').val();
    //let prompt = '';
    let keep = '';
    
    switch (category_id) {
        case '617': // Films/DVDs
            prompt = `Based on Titles: ${cleanedText}, create an optimized title in this format: {'title': 'Movie Title (DVD or Blu-ray, Year, Widescreen or Fullscreen), Other Info, Actors or Producer, Production Type, Disc Set'}`;
            keep = "keep the format {'title': 'Movie Title (DVD or Blu-ray, Year, Widescreen or Fullscreen)} when shortening the title ";
            break;
    
        case '261186': // Livres
            prompt = `Based on Titles: ${cleanedText}, create an optimized title in this format: {'title': 'Book Title (Author, Publisher, Year, Number of Pages), Other Info'}`;
            keep = "keep the format {'title': 'Book Title (Author, Publisher, Year, Number of Pages)} when shortening the title ";
            break;
    
        case '176984': // CD
            prompt = `Based on Titles: ${cleanedText}, create an optimized title in this format: {'title': 'Music CD Title (Author, Publisher, Year, Number of tracks), Other Info'}`;
            keep = "keep the format {'title': 'Music CD Title (Author, Publisher, Year, Number of tracks)} when shortening the title ";
            break;
    
        case '176985': // Vinyl
            prompt = `Based on Titles: ${cleanedText}, create an optimized title in this format: {'title': 'Music Vinyl Title (Author, Publisher, Year, Number of tracks), Other Info'}`;
            keep = "keep the format {'title': 'Music Vinyl Title (Author, Publisher, Year, Number of tracks)} when shortening the title ";
            break;
    
        case '139973': // Jeux vidéo
            prompt = `Based on Titles: ${cleanedText}, create an optimized eBay title in this format: {'title': 'Video Game Title (Platform like PS4, Xbox, PS3, Nintendo), Other Info'}`;
            keep = "keep the format {'title': 'Video Game Title (Platform like PS4, Xbox, PS3, Nintendo)} when shortening the title ";
            break;
    
        default:
            //let manufacturer = formData['manufacturer'] && typeof formData['manufacturer'] === 'string' ? ` Manufacturer: ${formData['manufacturer']}` : "";
            //let condition_name = formData['condition_name'] ? ` Condition: ${formData['condition_name']}` : "";
            //let model = formData['model'] ? ` Model: ${formData['model']}` : "";
            //let color = formData['color'] ? ` Color: ${formData['color']}` : "";
            let category_name = formData['category_name'] ? ` Category: ${formData['category_name']}` : "";
    
            prompt = `Based on Titles: ${cleanedText}, create an optimized product ${category_name} title in this format: {'title': Generated Title }`;
            keep = "";
            break;
    }
    
       /* var system_prompt = $('#category_id').val() == 617
            ? "The title should be a minLength=70 maxLength=80. Provide titles use this format: movie title (dvd or bluray, years of the movie, widescreen or full screen) actors or productor, production keep the number of disc set and if it's a Canadian version."
            : "The title should be a minLength=70 maxLength=80. Provide concise and accurate product titles. ";*/
            var system_prompt = $('#category_id').val() == 617
            ? "Return the value only in json {'title': your value} "
            : "Return the value only in json {'title': your value} ";
        
            var data = buildAiData(prompt, system_prompt, 100, 0.3);
    
        try {
            var aiResponse = await fetchAi(data);

            // Vérifier array en premier (avant object car array est aussi object en JS)
            if (Array.isArray(aiResponse)) {
                if (aiResponse.length > 0) {
                    aiResponse = aiResponse[0];
                } else {
                    aiResponse = '[UNKNOWN FORMAT]';
                }
            }
            
            // S'assurer qu'on récupère bien le title peu importe le format
            if (typeof aiResponse === 'object' && aiResponse !== null && !Array.isArray(aiResponse)) {
                if ('title' in aiResponse && typeof aiResponse.title === 'string') {
                    aiResponse = aiResponse.title;
                } else if ('message' in aiResponse && typeof aiResponse.message === 'object' && 'title' in aiResponse.message) {
                    aiResponse = aiResponse.message.title;
                } else {
                    console.warn('❌ Format d’objet inattendu:', JSON.stringify(aiResponse));
                    aiResponse = '[UNKNOWN FORMAT]';
                }
            } else if (typeof aiResponse === 'string') {
                try {
                    const parsed = JSON.parse(aiResponse);
                    if (parsed && parsed.title) {
                        aiResponse = parsed.title;
                    }
                } catch (e) {
                    console.warn('⚠️ String non parsable:', aiResponse);
                }
            }
            
            
            var aiResultElement = document.getElementById('ai-result-name');
            var aiResultElementCount = document.getElementById('ai-result-name-count');
          
            if (aiResponse.length > 80) {
                var system_prompt = $('#category_id').val() == 617
                ? "Return the value only in json {'title': your value} "
                : "Return the value only in json {'title': your value} ";
                var prompt = `Shorten this "${aiResponse}" to be between 70 and 80 characters: ${keep}`;
                data = buildAiData(prompt + keep, system_prompt, 100, 0.3);
                aiResponse = await fetchAi(data);

                // S'assurer qu'on récupère bien le title peu importe le format
                // Vérifier array en premier (avant object car array est aussi object en JS)
                if (Array.isArray(aiResponse)) {
                    if (aiResponse.length > 0) {
                        aiResponse = aiResponse[0];
                    } else {
                        aiResponse = '[UNKNOWN FORMAT]';
                    }
                }
                
                if (typeof aiResponse === 'object' && aiResponse !== null && !Array.isArray(aiResponse)) {
                    if ('title' in aiResponse && typeof aiResponse.title === 'string') {
                        aiResponse = aiResponse.title;
                    } else if ('message' in aiResponse && typeof aiResponse.message === 'object' && 'title' in aiResponse.message) {
                        aiResponse = aiResponse.message.title;
                    } else {
                        console.warn('❌ Format inattendu:', JSON.stringify(aiResponse));
                        aiResponse = '[UNKNOWN FORMAT]';
                    }
                } else if (typeof aiResponse === 'string') {
                    try {
                        const parsed = JSON.parse(aiResponse);
                        if (parsed && parsed.title) {
                            aiResponse = parsed.title;
                        }
                    } catch (e) {
                        // String non-JSON, on garde tel quel
                    }
                }
                // Vérifiez de nouveau si la longueur est correcte
                if (aiResponse.length > 80) {
                    aiResponse = aiResponse.substring(0, 80);
                }
            }
            
            // Ne pas afficher si format inconnu
            if (aiResponse === '[UNKNOWN FORMAT]' || aiResponse.includes('[UNKNOWN FORMAT]')) {
                console.error('AI returned unknown format, skipping display');
                return;
            }
            
            if (aiResultElement) {
                var textElement = document.getElementById('ai-result-name-text');
                if (textElement) {
                    textElement.textContent = aiResponse;
                } else {
                    aiResultElement.textContent = aiResponse;
                }
                aiResultElementCount.textContent = ' (' + aiResponse.length + ') ';
                aiResultElement.style.display = 'block';
                aiResultElementCount.style.display = 'inline';
                if (recognizedTextElement) {
                    switchEntryName('product','0');
                }else{
                    //aiSuggestDescriptionSupp(4,fieldName,recognizedTextElement);
                }
               
            } else {
                console.error('AI result element not found');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
async function suggestEntryNameFromProductSearch() {

    const search = document.getElementById('input-search');
  
    var aiResultElement = document.getElementById('title-search-name');
    var aiResultElementCount = document.getElementById('title-search-name-count');
    //const manufacturerField = document.getElementById('input-manufacturer');
    const aiResultElementrecognizedText = document.getElementById('recognizedText');

   /* if (!search || !manufacturerField) {
        console.error('Required elements not found');
        return;
    }*/

   // Préparer le texte source
   let cleanedText;
   if (aiResultElementrecognizedText.value.trim() !== "") {
       // Convertir la valeur de aiResultElementrecognizedText en texte brut
       cleanedText = aiResultElementrecognizedText.value
           .replace(/[\r\n]+/g, ' ') // Supprimer les sauts de ligne
           .replace(/\s+/g, ' ') // Remplacer plusieurs espaces par un seul
           .trim(); // Supprimer les espaces en début et fin
   } else if (search.value.trim() !== "") {
       // Si aiResultElementrecognizedText est vide, utiliser le champ `search`
       cleanedText = search.value
           .replace(/[\r\n]+/g, ' ')
           .replace(/\s+/g, ' ')
           .trim();
   } else {
       console.error('No valid input found for AI suggestion');
       return;
   }

   if (cleanedText === "") {
       console.error('Input text is empty after cleaning');
       return;
   }

   
    
   /* switch (category_id) {
        case '617': // Films/DVDs
            prompt = `Based on Titles: "${cleanedText}", create an optimized title in this format: {'title': 'Movie Title (DVD or Blu-ray, Year, Widescreen or Fullscreen), Other Info, Actors or Producer, Production Type, Disc Set'}`;
            keep = "keep the format {'title': 'Movie Title (DVD or Blu-ray, Year, Widescreen or Fullscreen)} when shortening the title ";
            break;
    
        case '261186': // Livres
            prompt = `Based on Titles: "${cleanedText}", create an optimized title in this format: {'title': 'Book Title (Author, Publisher, Year, Number of Pages), Other Info'}`;
            keep = "keep the format {'title': 'Book Title (Author, Publisher, Year, Number of Pages)} when shortening the title ";
            break;
    
        case '176984': // CD
            prompt = `Based on Titles: "${cleanedText}", create an optimized title in this format: {'title': 'Music CD Title (Author, Publisher, Year, Number of tracks), Other Info'}`;
            keep = "keep the format {'title': 'Music CD Title (Author, Publisher, Year, Number of tracks)} when shortening the title ";
            break;
    
        case '176985': // Vinyl
            prompt = `Based on Titles: "${cleanedText}", create an optimized title in this format: {'title': 'Music Vinyl Title (Author, Publisher, Year, Number of tracks), Other Info'}`;
            keep = "keep the format {'title': 'Music Vinyl Title (Author, Publisher, Year, Number of tracks)} when shortening the title ";
            break;
    
        case '139973': // Jeux vidéo
            prompt = `Based on Titles: "${cleanedText}", create an optimized eBay title in this format: {'title': 'Video Game Title (Platform like PS4, Xbox, PS3, Nintendo), Other Info'}`;
            keep = "keep the format {'title': 'Video Game Title (Platform like PS4, Xbox, PS3, Nintendo)} when shortening the title ";
            break;
    
        default:*/
            //let manufacturer = formData['manufacturer'] && typeof formData['manufacturer'] === 'string' ? ` Manufacturer: ${formData['manufacturer']}` : "";
            //let condition_name = formData['condition_name'] ? ` Condition: ${formData['condition_name']}` : "";
            //let model = formData['model'] ? ` Model: ${formData['model']}` : "";
            //let color = formData['color'] ? ` Color: ${formData['color']}` : "";
    
            prompt = `Based on: "${cleanedText}, create an optimized product title in this format: {'title': Generated Title }`;
            keep = "";
   //         break;
  //  }
    
       /* var system_prompt = $('#category_id').val() == 617
            ? "The title should be a minLength=70 maxLength=80. Provide titles use this format: movie title (dvd or bluray, years of the movie, widescreen or full screen) actors or productor, production keep the number of disc set and if it's a Canadian version."
            : "The title should be a minLength=70 maxLength=80. Provide concise and accurate product titles. ";*/
            var system_prompt = "Return the value only in json {'title': your value} ";
        
            var data = buildAiData(prompt, system_prompt, 100, 0.3);
    
        try {
            var aiResponse = await fetchAi(data);

            // S'assurer qu'on récupère bien le title peu importe le format
            if (typeof aiResponse === 'object' && aiResponse !== null) {
                if ('title' in aiResponse && typeof aiResponse.title === 'string') {
                    aiResponse = aiResponse.title;
                } else if ('message' in aiResponse && typeof aiResponse.message === 'object' && 'title' in aiResponse.message) {
                    aiResponse = aiResponse.message.title;
                } else {
                    console.warn('❌ Format d’objet inattendu:', aiResponse);
                    aiResponse = '[UNKNOWN FORMAT]';
                }
            } else if (typeof aiResponse === 'string') {
                try {
                    const parsed = JSON.parse(aiResponse);
                    if (parsed && parsed.title) {
                        aiResponse = parsed.title;
                    }
                } catch (e) {
                    console.warn('⚠️ String non parsable:', aiResponse);
                }
            }
            
            
            var aiResultElement = document.getElementById('ai-result-name');
            var aiResultElementCount = document.getElementById('ai-result-name-count');
          
            if (aiResponse.length > 80) {
                var system_prompt = "Return the value only in json {'title': your value} ";
                var prompt = `Shorten this "${aiResponse}" to be between 70 and 80 characters: ${keep}`;
                data = buildAiData(prompt + keep, system_prompt, 100, 0.3);
                aiResponse = await fetchAi(data);

                // S'assurer qu'on récupère bien le title peu importe le format
                if (typeof aiResponse === 'object' && aiResponse !== null) {
                    if ('title' in aiResponse && typeof aiResponse.title === 'string') {
                        aiResponse = aiResponse.title;
                    } else if ('message' in aiResponse && typeof aiResponse.message === 'object' && 'title' in aiResponse.message) {
                        aiResponse = aiResponse.message.title;
                    } else {
                        console.warn('❌ Format d’objet inattendu:', aiResponse);
                        aiResponse = '[UNKNOWN FORMAT]';
                    }
                } else if (typeof aiResponse === 'string') {
                    try {
                        const parsed = JSON.parse(aiResponse);
                        if (parsed && parsed.title) {
                            aiResponse = parsed.title;
                        }
                    } catch (e) {
                        console.warn('⚠️ String non parsable:', aiResponse);
                    }
                }
                // Vérifiez de nouveau si la longueur est correcte
                if (aiResponse.length > 80) {
                    aiResponse = aiResponse.substring(0, 80);
                }
            }
            if (aiResultElement) {
                aiResultElement.textContent = aiResponse;
                aiResultElementCount.textContent = ' (' + aiResponse.length + ') ';
                aiResultElement.style.display = 'inline';
                aiResultElementCount.style.display = 'inline';
             /*   if (recognizedTextElement) {
                    switchEntryName('product','0');
                }else{
                    aiSuggestDescriptionSupp(4,fieldName,recognizedTextElement);
                }*/
               
            } else {
                console.error('AI result element not found');
            }
        } catch (error) {
            console.error('Error:', error);
        }

         // Construire le prompt pour l'IA
  /*  const prompt = `Based on "${cleanedText}". Suggest a suitable manufacturer or brand name for this product.`;
    const systemPrompt = "Return ONLY manufacturer name without anything else";
    const data = buildAiData(prompt, systemPrompt, 100, 0.3);

    try {
        // Appel à l'IA
        const aiResponse = await fetchAi(data);

        if (aiResponse && aiResponse.trim() !== "") {
            // Afficher le résultat dans le champ manufacturer
            manufacturerField.value =  '' + aiResponse.trim();
        } else {
            console.error('No response from AI');
        }
    } catch (error) {
        console.error('Error during AI fetch:', error);
    } */
    }
function getAiPromptForDescription(formdata, productName) {
    if ($('#category_id').val() == 617) {
        return `Provide a general synopsis for the movie "${productName}". Do not consider the product condition. The synopsis should help the customer understand what the movie is about. with the following details: "${formdata}"`;
    } else if ($('#category_id').val() === '139973') {
        return `Provide a general synopsis for the video game "${productName}". Do not consider the product condition. The synopsis should help the customer understand what the video games is about. with the following details: "${formdata}"`;
    } else {
        return `Find a general description for a product named "${productName}". Do not consider the product condition. The description should help the customer understand what it is. with the following details: "${formdata}"`;
    }
}

function getAiPromptForImage(categoryName) {
    // DALL-E 3 prompt optimized for pure white background (NO shadows)
    
    return `Professional product photography for e-commerce: ${categoryName} category. 

Style: Clean product photography, ISOLATED ON WHITE, no shadows
Composition: 1-3 representative products, centered, floating on pure white
Background: SOLID PURE WHITE (#FFFFFF), completely flat, NO shadows, NO gradients, NO drop shadows, NO background texture
Lighting: Flat even lighting with minimal shadows on products only, background must be shadowless
Quality: High-resolution, sharp, professional e-commerce catalog
View: Straight-on or slight angle, products clearly visible

CRITICAL: Background must be 100% pure white with ZERO shadows or shading. Products isolated like Amazon product photos.`;
}


async function aiSuggestDescriptionSupp(specificsRow = 4, fieldName = 'product', recognizedTextElement) {
    //var product_id = document.querySelector('input[name="' + fieldName + '_id"]').value;

    var Button = $('#ai-suggest-description-supp-btn1');
    Button.prop('disabled', true).text('Generating...');


    var form = document.getElementById('form-' + fieldName);

    if (!recognizedTextElement) {
            if (!form) {
                console.error('Form element not found');
                return;
            }

    
            var excludeSelectors = [
                // Langues et descriptions inutilisées
                '#language2 input, #language2 select, #language2 textarea',
                '#specifics-language-2 input, #specifics-language-2 select, #specifics-language-2 textarea',
                '[name="' + fieldName + '_description[1][description]"]',
                '[name="' + fieldName + '_description[1][meta_title]"]',
                '[name="' + fieldName + '_description[1][meta_keyword]"]',
                '[name="' + fieldName + '_description[1][keyword]"]',
                '[name="' + fieldName + '_description[1][tag]"]',
                '[name="' + fieldName + '_description[1][description_supp]"]',
                '[name="' + fieldName + '_description[1][included_accessories]"]',
            
                // Exclure tout ce qui commence par "' + fieldName + '_description[2]"
                '[name^="' + fieldName + '_description[2]"]',
            
                // Dimensions et poids
                '[name="weight"]',
                '[name="weight_oz"]',
                '[name="length"]',
                '[name="width"]',
                '[name="height"]',
            
                // Identifiants spécifiques
                '[name="upc"]',
                '[name="sku"]',
                '[name="marketplace_item_id"]',
            
                // Tous les "price_ebay"
                '[name^="price_ebay"]',
            
                // Localisation ou quantité non allouée
                '[name="quantity"]',
                '[name="location"]',
                '[name="unallocated_quantity"]',
            
                // Conditions
                '[name="conditions_json1"]',
            
                // Tous les champs contenant "json"]',
            
                // Tous les "checkbox"
                '[name^="checkbox"]',
                '[id^="checkbox-none"]',
                'input[type="hidden"][id^="hidden-original-value-1-"]',
    
            
                // Coûts
                '[name="price"]',
                '[name="shipping_cost"]',
                '[name="price_with_shipping"]',
            
                // Autres
                '[name="files"]',
                '[name="shipping_carrier"]',
                '[name="marketplace_item_id"]',
                '[name="sku"]',
                '[name="' + fieldName + '_id"]',
                '[name="user_token"]',
    
                '[name="condition_id"]',
                '[name="length_class_id"]',
                '[name="weight_class_id"]',
                '[name="manufacturer_id"]',
                '[name="category_id"]',
                '[name="site_id"]',
                '[name="' + fieldName + '_category[]"]',
    
                '[name="checkbox-none"]',
                '[name="sourcecode"]',
                '[name="image"]',
                '[name^="' + fieldName + '_image"]',
                '[name*="[VerifiedSource]"]',
                '[name^="VerifiedSource"]',
                '[name^="hidden_"]',
    
                '[name="tax_class_id"]',
                '[name="minimum"]',
                '[name="subtract"]',
                '[name="stock_status_id"]',
                '[name="shipping"]',
    
                '[name="date_available"]',
                '[name="status"]',
                '[name="sort_order"]',
                '[name="filter"]',
                '[name="' + fieldName + '_store[]"]',
    
                '[name="download"]',
                '[name="related"]',
                '[name="option"]',
                '[name="points"]',
                '[name^="' + fieldName + '_reward"]',
                '[name^="' + fieldName + '_layout"]',
                '[name^="tag"]',
                '[name^="keyword"]',
                '[name^="keyword"]',
                '[name^="color"]',

                  // Unit et informations supplémentaires (avec suffixes possibles)
                '[name*="unit_quantity"]',
                '[name*="unit_type"]',
                '[name*="master_id"]',
                '[name*="manufacturer"]',
                '[name*="model"]',
                '[name*="discount"]',
                '[name*="made_in_country"]',
                '[name*="url_product"]',
                '[name*="marketplace_name"]',

    
            ];

            var elements = form.querySelectorAll('input, select, textarea');
            var formData = {};

            elements.forEach(function (element) {
                if (!element.matches(excludeSelectors.join(','))) {
                    var name = element.name || element.id;
                    if (name) {
                        formData[name] = element.value;
                    }
                }
            });
                // Nettoyer les champs HTML avant conversion en JSON
            formData = stripHtmlTagsFromFormData(formData);

            // 3. Transformer formData en chaîne formatée
            var cleanedText = transformDataToFormattedString(formData);

            // 4. Enlever les doublons dans le texte
            cleanedText = removeDuplicateKeys(cleanedText);
            cleanedText = JSON.stringify(cleanedText);
      

    }else{
        var recognizedTextElement = document.getElementById('recognizedText');
        var cleanedText = recognizedTextElement.value.replace(/[\r\n]+/g, ' ');

    }

    if (fieldName == 'catalog') {
        var productName = form.querySelector('input[name="path"]').value;

    } else{
    var productName = form.querySelector('input[name="' + fieldName + '_description[1][name]"]').value;

    }
    if (!productName) {
        console.error('Product name not found');
        return;
    }
    var textareaId = `${fieldName}-description-1-${specificsRow}`;
    var aiResultElement = document.getElementById(textareaId);
    if (aiResultElement) {
        var prompt = getAiPromptForDescription(cleanedText, productName);
        var system_prompt = $('#category_id').val() == 617
        ? "Return the value only in json {'description': } "
        : "Return the value only in json {'description': } ";
        var data = buildAiData(prompt, "Provide a general product description." + system_prompt, 1000, 0.7);
        try {

            
            var aiResponse = await fetchAi(data);
        
// S'assurer qu'on récupère bien le title peu importe le format
if (typeof aiResponse === 'object' && aiResponse !== null) {
    if ('description' in aiResponse && typeof aiResponse.description === 'string') {
        aiResponse = aiResponse.description;
    } else if ('message' in aiResponse && typeof aiResponse.message === 'object' && 'description' in aiResponse.message) {
        aiResponse = aiResponse.message.description;
    } else {
        console.warn('❌ Format d’objet inattendu:', aiResponse);
        aiResponse = '[UNKNOWN FORMAT]';
    }
} else if (typeof aiResponse === 'string') {
    try {
        const parsed = JSON.parse(aiResponse);
        if (parsed && parsed.description) {
            aiResponse = parsed.description;
        }
    } catch (e) {
        console.warn('⚠️ String non parsable:', aiResponse);
    }
}

        
            var formattedTextJson = await getFormattedText(aiResponse);
            try {
                // Vérification et parsing du JSON
                //const formattedTextObj = formattedTextJson;
                const formattedTextObj = formattedTextJson;//JSON.parse(formattedTextJson);
                if (formattedTextObj){//} && formattedTextObj.html) {
                    // Utilisation directe du HTML retourné
                    const formattedHtml = formattedTextObj.trim(); 
            
            
                    aiResultElement.value = formattedHtml;
            
                    if (aiResultElement.classList.contains('summernote')) {
                        $(`#${textareaId}`).summernote('code', formattedHtml);
                        //getTranslate(formattedHtml, 2, 'Fr', specificsRow, 'summernote', fieldName);
                    }
            
                    if (typeof generateInfo === 'function') {
                        generateInfo();
                    }
                } else {
                    console.error("Invalid response format: missing 'html' field");
                }
            } catch (error) {
                console.error("Error parsing formattedTextJson:", error);
            }
           

                Button.prop('disabled', false).text('Generated');
            
            
                Button.removeClass("btn btn-primary").addClass("btn btn-success");
            
                // Ajouter un délai de 3 secondes (3000 millisecondes)
                setTimeout(function() {
                    // Changer à nouveau le texte après 3 secondes
                    Button.removeClass("btn btn-success").addClass("btn btn-primary");
                    Button.prop('disabled', false).html('<i class="fa-solid fa-robot"></i> <i class="fa-solid fa-question"></i>');
                
            
                }, 3000); // 3000 millisecondes = 3 secondes 
           
        } catch (error) {
            console.error('Error:', error);
        }
    } 
}
async function aiSuggestCategoryDescription(specificsRow, form, fieldName = 'category') {
    var Button = $('#ai-suggest-description-category-btn');

    // Désactiver le bouton et afficher l'état de génération
    if (Button.length) {
        Button.prop('disabled', true).text('Generating...');
    }

    var formElement = document.getElementById(form);
    if (!formElement) {
        console.error('Error: Form element not found');
        return;
    }

    // Liste des champs à exclure (category-specific)
    var excludeSelectors = [
        '#language2 input, #language2 select, #language2 textarea',
        '#specifics-language-2 input, #specifics-language-2 select, #specifics-language-2 textarea',
        '[name^="category_description[2]"]',
        '[name="user_token"]',
        '[name^="checkbox"]', '[name^="hidden_"]',
        '[name="status"]', '[name="sort_order"]',
        '[name="filter"]', '[name="category_store[]"]',
        '[name^="category_layout"]'
    ];

    // Récupérer les valeurs du formulaire
    var elements = formElement.querySelectorAll('input, select, textarea');
    var formData = {};

    elements.forEach(function (element) {
        if (!element.matches(excludeSelectors.join(','))) {
            var name = element.name || element.id;
            if (name) {
                formData[name] = element.value.trim();
            }
        }
    });

    // Déterminer le nom de la catégorie + path
    var categoryInput = formElement.querySelector('input[name="category_description[1][name]"]');
    var categoryName = categoryInput ? categoryInput.value.trim() : '';

    var categoryPathInput = formElement.querySelector('input[name="path"]');
    var categoryPath = categoryPathInput ? categoryPathInput.value.replace('Search by category >', '').trim() : '';
    if (categoryPath) {
        categoryName = categoryName + ' From: ' + categoryPath;
    }

    if (!categoryName) {
        console.error('Error: Category name not found');
        return;
    }

    // Générer la requête AI
    var prompt = getAiPromptForDescription(JSON.stringify(formData), categoryName);
    var data = buildAiData(prompt, "Provide a category description. Return the value only in json {'description': } ", 500, 0.7);

    try {
        const aiResponse = await fetchAi(data);
        console.log("AI Category Response Type:", typeof aiResponse);
        console.log("AI Category Response:", aiResponse);
        
        // Extraire le texte selon le type de réponse
        let extractedText = '';
        
        if (!aiResponse) {
            console.error("Error: AI response is null or undefined");
            return;
        }
        
        if (typeof aiResponse === 'string') {
            extractedText = aiResponse.trim();
        } else if (Array.isArray(aiResponse)) {
            if (aiResponse.length > 0) {
                extractedText = aiResponse.join('\n').trim();
            } else {
                console.error("Error: AI response array is empty", aiResponse);
                return;
            }
        } else if (typeof aiResponse === 'object' && aiResponse !== null) {
            // Support bracket-notation keys (e.g. "category_description[1][description]")
            var bracketKey = 'category_description[' + specificsRow + '][description]';
            if (aiResponse[bracketKey]) {
                extractedText = aiResponse[bracketKey];
            } else if (aiResponse.category_description) {
                extractedText = aiResponse.category_description;
            } else if (aiResponse.description) {
                extractedText = aiResponse.description;
            } else if (aiResponse.html) {
                extractedText = aiResponse.html;
            } else if (aiResponse.text) {
                extractedText = aiResponse.text;
            } else if (aiResponse.content) {
                extractedText = aiResponse.content;
            } else if (aiResponse.success) {
                extractedText = aiResponse.success;
            } else {
                // Fallback: search all keys for one containing "[description]"
                for (var key in aiResponse) {
                    if (key.indexOf('[description]') !== -1) {
                        extractedText = aiResponse[key];
                        break;
                    }
                }
                if (!extractedText) {
                    console.error("Error: AI response object does not contain expected properties", aiResponse);
                    return;
                }
            }
        } else {
            console.error("Error: AI response is not a string, array, or object", typeof aiResponse, aiResponse);
            return;
        }
        
        if (!extractedText || extractedText === '') {
            console.error("Error: Extracted text is empty");
            return;
        }

        var textareaId = 'category-description-1-' + specificsRow;
        var aiResultElement = document.getElementById(textareaId);

        if (!aiResultElement) {
            console.error('Error: AI result element not found');
            return;
        }

        const formattedTextJson = await getFormattedText(extractedText);

           try {
               let formattedHtml = '';
               
               // Vérifier si c'est déjà du HTML (commence par <)
               if (typeof formattedTextJson === 'string' && formattedTextJson.trim().startsWith('<')) {
                   formattedHtml = formattedTextJson.trim();
               } else {
                   // Essayer de parser comme JSON
                   let formattedTextObj = JSON.parse(formattedTextJson);
                   
                   if (Array.isArray(formattedTextObj)) {
                       formattedHtml = formattedTextObj[0];
                   } else if (typeof formattedTextObj === 'string') {
                       formattedHtml = formattedTextObj;
                   } else if (formattedTextObj && formattedTextObj.html) {
                       formattedHtml = formattedTextObj.html.trim();
                   } else {
                       console.error("Invalid response format: missing 'html' field or unrecognized format");
                       return;
                   }
               }
               
               if (!formattedHtml || formattedHtml === '') {
                   console.error("Error: Formatted HTML is empty");
                   return;
               }
               
               // Nettoyer les backslashes d'échappement
               formattedHtml = formattedHtml.replace(/\\'/g, "'").replace(/\\"/g, '"').replace(/\\\\/g, '\\');
           
               aiResultElement.value = formattedHtml;
           
               // Set EN description in summernote + translate to FR
               if (aiResultElement.classList.contains('summernote')) {
                   $(`#${textareaId}`).summernote('code', formattedHtml);
                   getTranslate(formattedHtml, 2, 'Fr', specificsRow, 'summernote', 'category');
               }

               // Generate meta for EN immediately
               generateCategoryMeta(1, specificsRow);
               // Generate meta for FR after translation completes
               setTimeout(function() {
                   generateCategoryMeta(2, specificsRow);
               }, 5000);

           } catch (error) {
               console.error("Error parsing formattedTextJson:", error);
           }

        // Réactiver le bouton après génération
        if (Button.length) {
            Button.prop('disabled', false).text('Generated');
            Button.removeClass("btn-primary").addClass("btn-success");

            setTimeout(() => {
                Button.removeClass("btn-success").addClass("btn-primary");
                Button.prop('disabled', false).html('<i class="fa-solid fa-robot"></i> <i class="fa-solid fa-question"></i>');
            }, 3000);
        }

    } catch (error) {
        console.error('Error:', error);
    }
}

// Generate meta_title, meta_description, meta_keyword for a category language
// Uses the name and description fields of that language (same logic as product generateMetaTag)
function generateCategoryMeta(languageId, descriptionRow) {
    var nameEl = document.querySelector('input[name="category_description[' + languageId + '][name]"]');
    var descEl = document.getElementById('category-description-' + languageId + '-' + descriptionRow);
    
    if (!nameEl) return;
    
    var name = nameEl.value.trim();
    // Get description text (strip HTML from summernote)
    var descHtml = '';
    if (descEl && descEl.classList.contains('summernote')) {
        descHtml = $('#category-description-' + languageId + '-' + descriptionRow).summernote('code');
    } else if (descEl) {
        descHtml = descEl.value;
    }
    var descText = descHtml.replace(/<\/?[^>]+(>|$)/g, '').replace(/&nbsp;/g, ' ').replace(/\s+/g, ' ').trim();
    
    // meta_title = category name
    var metaTitleRow = descriptionRow + 1;
    var metaTitleEl = document.getElementById('category-description-' + languageId + '-' + metaTitleRow);
    if (metaTitleEl) {
        metaTitleEl.value = name;
    }
    
    // meta_description = description text (truncated to 160 chars)
    var metaDescRow = descriptionRow + 2;
    var metaDescEl = document.getElementById('category-description-' + languageId + '-' + metaDescRow);
    if (metaDescEl) {
        metaDescEl.value = descText.substring(0, 160);
    }
    
    // meta_keyword = words from name, comma-separated
    var metaKeywordRow = descriptionRow + 3;
    var metaKeywordEl = document.getElementById('category-description-' + languageId + '-' + metaKeywordRow);
    if (metaKeywordEl) {
        var keywords = name.replace(/[.,;:'"\{\}\[\]\(\)@%$&\-]/g, '');
        keywords = keywords.split(/\s+/).join(',');
        if (keywords.endsWith(',')) keywords = keywords.slice(0, -1);
        metaKeywordEl.value = keywords;
    }
}

// Fonction principale pour effectuer la traduction
function getTranslate(text, languageId, targetLanguage, rowId, summernote, fieldName = 'product') {
    var targetId = `${fieldName}-description-${languageId}-${rowId}`;
    var targetFieldElement = document.getElementById(targetId);

    if (targetFieldElement) {
        if (text === '' || text == '<p><br></p>') {
            targetFieldElement.value = '';
            if (summernote == 'summernote') {
                $(`#${targetId}`).summernote('code', '');
            }
        } else {
          
            var data = buildTranslationData(text, targetLanguage);
            fetchTranslationData(data, targetFieldElement, targetId, summernote);
        }
    } else {
        console.error('Element not found for ID: ' + targetId);
    }
}

// Fonction pour construire les données de la requête de traduction
function buildTranslationData(text, targetLanguage) {
    var containsHtml = /<\/?[a-z][\s\S]*>/i.test(text);
    var prompt = containsHtml ? text : text;

    return {
        text_field: prompt,
        targetLanguage: targetLanguage
    };
}

function fetchTranslationData(data, targetFieldElement, targetId, summernote) {
    var user_token = document.querySelector('input[name="user_token"]').value;

    // Créez un objet FormData pour envoyer les données sous forme POST
    var formData = new FormData();
    formData.append('text_field', data.text_field);
    formData.append('targetLanguage', data.targetLanguage);

    apiQueue.add(async () => {
        return await fetch('index.php?route=warehouse/tools/ai.translate&user_token=' + user_token, {
            method: 'POST',
            body: formData // FormData gère automatiquement les en-têtes
        });
    })
    .then(async response => {
        const text = await response.text();
        let json;
        try {
            json = JSON.parse(text);
        } catch (e) {
            console.error('Translation response is not valid JSON:', e, text);
            const status = response.status;
            const statusText = response.statusText || '';
            alert(`Translation service error (${status} ${statusText}).`);
            return;
        }

        if (!response.ok) {
            const errMsg = (json && json.error) ? json.error : `HTTP ${response.status}`;
            console.error('Translation HTTP error:', errMsg);
            alert(errMsg);
            return;
        }

        if (json.error) {
            console.error('Translation error:', json.error);
            alert(json.error);
            return;
        }

        // Gérer les différents formats de réponse
        var translatedText = '';
        var isArrayResponse = false;
        if (typeof json.success === 'string') {
            translatedText = json.success;
        } else if (Array.isArray(json.success) && json.success.length > 0) {
            // Si c'est un tableau et que tous les éléments sont des strings, garder le tableau complet
            if (json.success.every(item => typeof item === 'string')) {
                translatedText = json.success; // Garder comme array
                isArrayResponse = true;
            } else if (typeof json.success[0] === 'string') {
                translatedText = json.success[0];
            } else if (json.success[0] && json.success[0].translation) {
                translatedText = json.success[0].translation;
            }
        } else if (json.success && json.success.translation) {
            translatedText = json.success.translation;
        }
        
        if (!translatedText || (Array.isArray(translatedText) && translatedText.length === 0)) {
            console.error('Unable to extract translation from response:', json.success);
            alert('Format de réponse de traduction invalide');
            return;
        }
    
        // Si c'est déjà un array, pas besoin de décoder
        var decodedText;
        if (isArrayResponse) {
            decodedText = translatedText; // Garder l'array tel quel
        } else {
            // Décoder les entités HTML pour les strings
            decodedText = htmlDecode(translatedText);

            // Remplacer les guillemets français par des guillemets standards
            decodedText = decodedText.replace(/«|»/g, '"');

            // Corriger les accents en majuscule
            // Vérifier si le texte ressemble à un JSON mal formé
            if (decodedText.startsWith('[') && decodedText.endsWith(']') && decodedText.indexOf('"') === -1) {
                console.warn('Le JSON ne contient pas de guillemets, tentative de correction...');
                // Correction : Ajout de guillemets autour des valeurs
                decodedText = decodedText.replace(/\[|\]/g, '') // Supprime les crochets
                                         .split(', ') // Sépare les éléments
                                         .map(item => `"${item.trim()}"`) // Ajoute des guillemets autour
                                         .join(', '); // Recrée la liste
                decodedText = `[${decodedText}]`; // Réajoute les crochets
            }
        }

        if (targetFieldElement.tagName.toLowerCase() === 'select') {
            if (targetFieldElement.multiple) {
                try {
                    let options;
                    
                    // Si decodedText est déjà un array, l'utiliser directement
                    if (Array.isArray(decodedText)) {
                        options = decodedText;
                    } else if (typeof decodedText === 'string' && (decodedText.startsWith('[') || decodedText.startsWith('{'))) {
                        options = JSON.parse(decodedText); // Essayer de parser JSON
                    } else if (typeof decodedText === 'string') {
                        // Traiter comme une liste séparée par des virgules
                        options = decodedText.split(',').map(item => item.trim()).filter(item => item !== '');
                    } else {
                        options = [decodedText];
                    }

                    if (Array.isArray(options)) {
                        $(targetFieldElement).val([]); // Deselect all before adding

                        options.forEach(option => {
                            // Utiliser filter() au lieu d'un sélecteur CSS pour éviter les problèmes d'échappement
                            let existingOption = $(targetFieldElement).find('option').filter(function() {
                                return $(this).val() === option;
                            });
                            if (existingOption.length === 0) {
                                // Créer l'option manuellement pour préserver les caractères Unicode
                                let newOption = document.createElement('option');
                                newOption.value = option;
                                newOption.textContent = option;
                                $(targetFieldElement).append(newOption);
                            }
                            // Select the option using filter
                            $(targetFieldElement).find('option').filter(function() {
                                return $(this).val() === option;
                            }).prop('selected', true);
                        });

                        // Correction globale : forcer le texte affiché à partir de la value pour toutes les options
                        $(targetFieldElement).find('option').each(function() {
                            this.textContent = this.value;
                        });
                        $(targetFieldElement).trigger('change'); // Refresh the select (useful for Select2)
                    }
                } catch (error) {
                    console.error('Error decoding JSON for select:', error);
                    // En cas d'erreur, traiter comme du texte brut séparé par des virgules
                    let options = decodedText.split(',').map(item => item.trim()).filter(item => item !== '');
                    $(targetFieldElement).val([]); // Deselect all before adding
                    
                    options.forEach(option => {
                        // Utiliser filter() au lieu d'un sélecteur CSS pour éviter les problèmes d'échappement
                        let existingOption = $(targetFieldElement).find('option').filter(function() {
                            return $(this).val() === option;
                        });
                        if (existingOption.length === 0) {
                            // Créer l'option manuellement pour préserver les caractères Unicode
                            let newOption = document.createElement('option');
                            newOption.value = option;
                            newOption.textContent = option;
                            $(targetFieldElement).append(newOption);
                        } else {
                            // Mettre à jour le textContent de l'option existante
                            existingOption[0].textContent = option;
                        }
                        // Select the option using filter
                        $(targetFieldElement).find('option').filter(function() {
                            return $(this).val() === option;
                        }).prop('selected', true);
                    });
                    
                    $(targetFieldElement).trigger('change');
                }
            } else {
                // For single select, just set the value directly
                // Escape quotes in the selector to avoid jQuery syntax errors
                let escapedText = decodedText.replace(/"/g, '\\"');
                let existingOption = $(targetFieldElement).find(`option[value="${escapedText}"]`);
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
    })
    .catch(error => {
        console.error('An error occurred while translating:', error);
        alert('Translation failed. Please try again.');
    });
}



// Fonction pour traduire tout le contenu pour toutes les langues
function translateContentForAllLanguages(elementId, summernote = '', fieldName = 'product') {
    var languages = JSON.parse($('#languages-json').val());
    var ids = extractLanguageAndRowId(elementId);

    if (ids && ids.languageId) {
        delete languages[ids.languageId]; // Supprimer la langue source des langues à traduire

        for (var targetLanguageId in languages) {
            var targetLanguage = languages[targetLanguageId];
            var rowId = ids.rowId;
            if (summernote === 'summernote') {
                var value = $(`#${elementId}`).summernote('code');
                getTranslate(value, targetLanguageId, targetLanguage, rowId, 'summernote', fieldName);
            } else {
                var element = $(`#${elementId}`);
                if (element.is('input')) {
                    var value = element.val();
                    if (value && value.trim() !== '') {
                        getTranslate(value, targetLanguageId, targetLanguage, rowId, '', fieldName);
                    } else {
                        // Vider le champ cible si la source est vide
                        $(`#${fieldName}-description-${targetLanguageId}-${rowId}`).val('');
                    }
                } else if (element.is('select')) {
                    if (element.prop('multiple')) {
                        let selectedValues = [];
                    
                        // Récupérer toutes les valeurs sélectionnées
                        element.find('option:selected').each(function() {
                            selectedValues.push($(this).val().trim());
                        });
                    
                    
                        if (selectedValues.length > 0) {
                            // Envoyer les valeurs à la traduction
                            getTranslate(selectedValues, targetLanguageId, targetLanguage, rowId, '', fieldName);
                        } else {
                            // Vider le champ cible si la source est vide
                            $(`#${fieldName}-description-${targetLanguageId}-${rowId}`).val(null).trigger('change');
                        }
                    } else {
                        var value = element.find('option:selected').text();
                        if (value && value.trim() !== '') {
                            getTranslate(value, targetLanguageId, targetLanguage, rowId, '', fieldName);
                        } else {
                            // Vider le champ cible si la source est vide
                            $(`#${fieldName}-description-${targetLanguageId}-${rowId}`).val(null).trigger('change');
                        }
                    }
                } else if (element.is('textarea')) {
                    var value = element.val();
                    if (value && value.trim() !== '') {
                        getTranslate(value, targetLanguageId, targetLanguage, rowId, '', fieldName);
                    } else {
                        // Vider le champ cible si la source est vide
                        $(`#${fieldName}-description-${targetLanguageId}-${rowId}`).val('');
                    }
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
    // phoenixliquidation uses dash format: product-description-1-1
    var idMatch = elementId.match(/-description-(\d+)-(\d+)/);
    if (idMatch) {
        return {
            languageId: idMatch[1],
            rowId: idMatch[2]
        };
    }
    console.error('Could not extract IDs from elementId:', elementId);
    return null;
}

function translateAllFields(formName, allFields = true) {

    var languagesElement = $('#languages-json');
    
    var languages = JSON.parse(languagesElement.val());
    
    var sourceLanguageId = '1'; // Supposons que '1' est l'ID pour l'anglais

    // Retirer l'anglais des langues à traduire
    delete languages[sourceLanguageId];

    var toTranslateElement = $('#to-translate');
    
    var toTranslate = toTranslateElement.val();
    
    var translationFields = {};

    // Liste des champs à copier directement sans traduction
   /* const copyDirectlyFields = [
        "Brand", "Exclusive Event/Retailer", "Franchise", "TV/Streaming Show", 
        "Movie", "Professional Grader", "Model Grader", "Collection", "Product Line",
        "Series", "Animation Studio", "Autographed By", "Designer", 
        "Autograph Authentication"
    ];*/

    if (toTranslate && toTranslate.trim() !== "" && allFields === false) {
        try {
            translationFields = JSON.parse(toTranslate);
        } catch (error) {
            console.error('Error parsing to_translate JSON:', error);
            return;
        }
    } else if (allFields) {

        var selector = `#${formName} [id*="-description-${sourceLanguageId}"]`;
        
        var foundElements = $(`#${formName}`).find(`[id*="-description-${sourceLanguageId}"]`);

        foundElements.each(function() {
            let element = $(this);
            let elementName = element.attr('name');
            let elementID = element.attr('id');

            const excludedKeywords = ['[description]', '[meta_title]', '[meta_description]', '[tag]', '[meta_keyword]', 'response_', 'display_','_specifics_Name'];

            if (elementName && excludedKeywords.some(keyword => elementName.includes(keyword))) {
                return;
            }

            if (elementID && excludedKeywords.some(keyword => elementID.includes(keyword))) {
                return;
            }

            translationFields[element.attr('id')] = true;
        });
    }


    // Parcourir les champs à traduire
    Object.keys(translationFields).forEach(function(elementId) {
        var element = $(`#${elementId}`);
        if (element.length === 0) {
            return;
        }


        var ids = extractLanguageAndRowId(elementId);
        if (!ids || ids.languageId !== sourceLanguageId) {
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

        //let elementName = element.attr('name'); // Vérifie le `name`
       // let fieldLabel = element.closest('.form-group').find('label').text().trim(); // Vérifie le label
       /* let isCopyDirectly = copyDirectlyFields.some(keyword => 
            (elementName && elementName.includes(keyword)) || 
            (fieldLabel && fieldLabel.includes(keyword))
        );*/

        for (var targetLanguageId in languages) {
            var targetField = $(`#product-description-${targetLanguageId}-${ids.rowId}`);
            
            if (value === '' || value === '<p><br></p>') {
                if (targetField.hasClass('summernote')) {
                    targetField.summernote('code', '');
                } else {
                    targetField.val('');
                }
            } else if (isNumeric || isEmpty) {//|| isCopyDirectly
                targetField.val(value);
            } else {
                getTranslate(value, targetLanguageId, languages[targetLanguageId], ids.rowId);
            }
        }
    });
}



// Function to handle translation and modal display
function handleTranslationAndModal(formName, allFields = true) {
    // Make handleTranslationAndModal globally accessible
    window.handleTranslationAndModal = handleTranslationAndModal;
    // Make verifyAllSpecifics globally accessible
    window.verifyAllSpecifics = verifyAllSpecifics;
    return new Promise((resolve, reject) => {
    var form = document.getElementById(formName);

    if (!form) {
        console.error('❌ Form element not found:', formName);
        reject('Form not found');
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




async function fetchAiImage(data) {

    var user_token = document.querySelector('input[name="user_token"]').value;
    var Button = $('#ai-suggest-image-btn');
    var ButtonDownload = $('#ai-suggest-image-download-btn');

    Button.prop('disabled', true).text('Generating...');
    const response = await fetch('index.php?route=warehouse/tools/ai.prompt_ai_image&user_token=' + user_token, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    const json = await response.json();

   
    if (json.success) {
        return { url: json.success };
    } else {
        throw new Error(json.error || 'Unknown error');
    }
}

async function aiSuggestImage(form, fieldName = 'product') {
    var form = document.getElementById(form);
    var Button = $('#ai-suggest-image-btn');
    var ButtonDownload = $('#ai-suggest-image-download-btn');
    var ButtonOpenImage = $('#ai-suggest-image-open-btn');
    var inputElement = document.getElementById('image-url');
    
    if (!form) {
        console.error('Form element not found');
        return;
    }

    // Show loading state - ANIMATION START avec dégradé pulsant
    Button.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Generating...');
    Button.removeClass("btn btn-primary btn-success btn-danger").addClass("btn btn-warning");
    
    // Ajouter animation pulsante avec dégradé
    Button.css({
        'animation': 'pulse-glow 1.5s ease-in-out infinite',
        'background': 'linear-gradient(45deg, #ff9800, #ff5722, #ff9800)',
        'background-size': '200% 200%',
        'animation': 'gradient-shift 2s ease infinite'
    });
    
    // Ajouter les keyframes CSS dynamiquement si pas déjà présents
    if (!$('#ai-animation-styles').length) {
        $('head').append(`
            <style id="ai-animation-styles">
                @keyframes gradient-shift {
                    0% { background-position: 0% 50%; }
                    50% { background-position: 100% 50%; }
                    100% { background-position: 0% 50%; }
                }
                @keyframes pulse-glow {
                    0%, 100% { box-shadow: 0 0 10px rgba(255, 152, 0, 0.5); }
                    50% { box-shadow: 0 0 20px rgba(255, 87, 34, 0.8); }
                }
            </style>
        `);
    }

    // Reset buttons
    ButtonDownload.attr('onclick', '');
    ButtonOpenImage.attr('onclick', '');
    ButtonOpenImage.css('display', 'none');
    ButtonDownload.css('display', 'none');
    
    // Hide input if it exists
    if (inputElement) {
        inputElement.style.display = "none";
    }


   if (fieldName.trim() === 'category') {
  //  alert(fieldName);
    var category = form.querySelector('input[name="' + fieldName + '_description[1][name]"]').value;
    var categorypath = form.querySelector('input[name="path"]').value;
   
    var categorydescription = $(`textarea[id^="${fieldName}-description-1-1"]`).val();

   
    var tempElement = document.createElement('div');
    tempElement.innerHTML = categorydescription;
    var plainText = tempElement.textContent || tempElement.innerText || "";
    categorypath = categorypath.replace('Search by category >', '').trim();
    var categoryName = category + ' From: ' + categorypath + 'Based on :' + plainText;
//alert(categoryName);
} else {
    var categoryName = form.querySelector('input[name="' + fieldName + '_description[1][name]"]').value;
}

    if (!categoryName) {
        console.error('category name not found');
        Button.css({'animation': '', 'background': '', 'box-shadow': ''});
        Button.prop('disabled', false).html('<i class="fa-solid fa-robot"></i> <i class="fa-solid fa-photo"></i>');
        Button.removeClass("btn btn-warning").addClass("btn btn-primary");
        return;
    }

    var prompt = getAiPromptForImage(categoryName);
    var data = { prompt: prompt };

    try {
        // Récupérer l'image générée par l'IA
        const aiResponse = await fetchAiImage(data);

        if (aiResponse.url) {
            // Extraire l'URL de base (sans les paramètres après .png)
            const imageUrlBase = aiResponse.url;

            // Update image element - try both IDs (old: thumb-image-result, new: thumb-image)
            var aiResultElement = document.getElementById('thumb-image-result') || document.getElementById('thumb-image');
            
            // SUCCESS STATE - Remove animations, show success
            Button.css({'animation': '', 'background': '', 'box-shadow': ''});
            Button.removeClass("btn btn-warning").addClass("btn btn-success");
            Button.prop('disabled', false).html('<i class="fa-solid fa-check"></i> Generated!');
            
            ButtonDownload.prop('disabled', false).text('Download Image');
            ButtonDownload.removeClass("btn btn-primary").addClass("btn btn-success");
            ButtonDownload.attr('onclick', 'uploadFromLink("' + imageUrlBase + '");');
            ButtonOpenImage.attr('onclick', 'openAiImage("' + imageUrlBase + '");');
            ButtonOpenImage.css('display', 'block');
            ButtonDownload.css('display', 'block');

            if (inputElement) {
                inputElement.value = imageUrlBase;
                inputElement.style.display = "block";
            }

            if (aiResultElement) {
                aiResultElement.src = imageUrlBase;
                if (aiResultElement.id === 'thumb-image-result') {
                    // Old layout - set width/height
                    aiResultElement.style.width = '100px';
                    aiResultElement.style.height = '100px';
                }
                // New layout - img-fluid will handle sizing automatically
            } else {
                console.error('AI result image element not found');
            }
            
            // Reset button after 3 seconds
            setTimeout(function() {
                Button.removeClass("btn btn-success").addClass("btn btn-primary");
                Button.html('<i class="fa-solid fa-robot"></i> <i class="fa-solid fa-photo"></i>');
            }, 3000);
        } else {
            console.error('AI response did not contain an image URL');
            // ERROR STATE
            Button.css({'animation': '', 'background': '', 'box-shadow': ''});
            Button.prop('disabled', false).html('<i class="fa-solid fa-robot"></i> <i class="fa-solid fa-photo"></i>');
            Button.removeClass("btn btn-warning").addClass("btn btn-danger");
            setTimeout(function() {
                Button.removeClass("btn btn-danger").addClass("btn btn-primary");
            }, 3000);
        }
    } catch (error) {
        console.error('Error:', error);
        // ERROR STATE
        Button.css({'animation': '', 'background': '', 'box-shadow': ''});
        Button.prop('disabled', false).html('<i class="fa-solid fa-exclamation-triangle"></i> Error');
        Button.removeClass("btn btn-warning").addClass("btn btn-danger");
        setTimeout(function() {
            Button.removeClass("btn btn-danger").addClass("btn btn-primary");
            Button.html('<i class="fa-solid fa-robot"></i> <i class="fa-solid fa-photo"></i>');
        }, 3000);
    }
}


function formatAiResponse(description) {
    return htmlDecode(description.replace(/```html\n?/, '').replace(/```$/, ''));
}
function htmlDecode(input) {
    var e = document.createElement('textarea');
    e.innerHTML = input;
    // Handle cases where browser does not decode
    return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
} 

async function fetchAi(data) {
    // Use queue to prevent concurrent requests (429 errors)
    return apiQueue.add(async () => {
        var user_token = document.querySelector('input[name="user_token"]').value;
        $('#ai-suggest-entry-name-btn1').prop('disabled', true).text('Generating...');

        const response = await fetch('index.php?route=warehouse/tools/ai.prompt_ai&user_token=' + user_token, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const json = await response.json();

        $('#ai-suggest-entry-name-btn1').prop('disabled', false).html('<i class="fa-solid fa-robot"></i><i class="fa-solid fa-question"></i>');

        if (json.success) {
            return json.success;
        } else if (json.error) {
            console.error('AI API Error:', json.error);
            throw new Error(json.error);
        } else {
            console.error('AI API returned empty response:', json);
            throw new Error('AI API returned empty or invalid response');
        }
    });
}
async function getFormattedText(description) {
    var prompt = `Format the following text with HTML tags for bold, italics, and paragraphs where appropriate:"${description}"
   `;

    var data = buildAiData(prompt, ' Return a valid JSON object with  key "html" containing the formatted text as a properly escaped string. Example: {"html": "<p><strong>Formatted text</strong></p>"} as a properly escaped string.', 500, 0.7);

    let response = await fetchAi(data);

    try {
        if (!response) {
            throw new Error("Empty AI response");
        }

        console.log('getFormattedText - Raw response type:', typeof response, response);

        // Gérer les différents types de réponse
        let htmlContent = '';
        
        if (Array.isArray(response)) {
            // Si c'est un tableau, fusionner tous les éléments
            htmlContent = response.join('\n');
            console.log('getFormattedText - Response is array, joined:', htmlContent);
        } else if (typeof response === 'string') {
            // Si c'est déjà une chaîne
            htmlContent = response.trim();
            console.log('getFormattedText - Response is string:', htmlContent.substring(0, 50) + '...');
        } else if (typeof response === 'object' && response !== null && response.html) {
            // Si c'est un objet avec propriété html
            htmlContent = response.html;
            console.log('getFormattedText - Response is object with html property');
        } else {
            throw new Error("Invalid AI response format - not string, array, or object with html property");
        }

        // Nettoyer les backslashes d'échappement
        htmlContent = htmlContent.replace(/\\'/g, "'").replace(/\\"/g, '"').replace(/\\\\/g, '\\');
        console.log('getFormattedText - Cleaned backslashes');

        // Si le contenu HTML commence déjà par <, le retourner directement
        if (htmlContent.startsWith('<')) {
            console.log('getFormattedText - Content is already HTML, returning directly');
            return htmlContent;
        }

        // Sinon, retourner au format JSON
        console.log('getFormattedText - Returning as JSON object');
        return JSON.stringify({ html: htmlContent });
        
    } catch (error) {
        console.error("Error processing AI response:", error, response);
        return JSON.stringify({ error: "Invalid JSON response" });
    }
}

function buildAiData(prompt, systemPrompt, maxTokens, temperature) {
    return {
        prompt: prompt,
        system_prompt: systemPrompt,
        max_tokens: maxTokens,
        temperature: temperature
    };
}
function buildAiDataImage(prompt) {
    return {
        prompt: prompt,
        system_prompt: systemPrompt,
        max_tokens: maxTokens,
        temperature: temperature
    };
}

/**
 * Normalise la réponse de l'API AI (gère les tableaux d'objets, objets simples, strings)
 * @param {*} response - La réponse brute de l'API
 * @returns {string} - Le contenu HTML normalisé
 */
function normalizeAiResponse(response) {
    // Gérer différents types de réponse
    if (Array.isArray(response)) {
        // Si c'est un tableau d'objets avec category/details ou category/content
        if (response.length > 0 && typeof response[0] === 'object') {
            // Extraire details, content, condition_details, ou tout autre champ de contenu
            return response.map(item => 
                item.details || item.content || item.text || item.html || 
                item.condition_details || item.accessories || item.description || ''
            ).join('\n');
        } else {
            // Tableau de strings simples
            return response.join('');
        }
    } else if (typeof response === 'object' && response !== null) {
        // Si c'est un objet, essayer d'extraire le contenu
        return response.html || response.content || response.text || response.description || 
               response.details || response.condition_details || response.accessories || '';
    }
    
    // Convertir en string si nécessaire
    if (typeof response !== 'string') {
        response = String(response);
    }
    
    response = response.trim();
    
    // Nettoyer le HTML de Summernote si présent (extraire seulement le contenu éditable)
    if (response.includes('note-editable')) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = response;
        const editableContent = tempDiv.querySelector('.note-editable');
        if (editableContent) {
            response = editableContent.innerHTML.trim();
        }
    }
    
    return response;
}

function openAiImage(imageUrl) {
    // Ouvre l'image dans une nouvelle fenêtre ou un nouvel onglet
    window.open(imageUrl, '_blank').focus();
}

/**
 * AI Generate Additional Conditions (condition_supp)
 * Uses existing condition_name and current textarea content
 */
async function aiSuggestConditionSupp(languageId, specificsRow) {
    const button = $(`#ai-suggest-condition-supp-btn${languageId}`);
    button.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Generating...');

    try {
        // Récupérer le condition_name sélectionné
        const conditionRadio = document.querySelector('input[name="condition_id"]:checked');
        const conditionName = conditionRadio ? conditionRadio.closest('.form-check').querySelector('label').textContent.trim() : 'Used';
        
        // Récupérer le contenu actuel du textarea
        const textareaId = `product-description-${languageId}-${specificsRow}`;
        const summernoteCode = $(`#${textareaId}`).summernote('code');
        const currentContent = summernoteCode.replace(/<[^>]*>/g, '').trim();
        
        
        // Récupérer les informations du produit pour le contexte
        const productName = $(`input[name="product_description[${languageId}][name]"]`).val() || '';
        const brand = $('select[name="manufacturer_id"] option:selected').text() || '';
        const upc = $('input[name="upc"]').val() || '';
        
        // Construire le prompt
        let prompt = `Generate detailed additional condition information for this product:\n\n`;
        prompt += `Product: ${productName}\n`;
        if (brand && brand !== '---') prompt += `Brand: ${brand}\n`;
        if (upc) prompt += `UPC: ${upc}\n`;
        prompt += `Condition: ${conditionName}\n\n`;
        
        if (currentContent) {
            prompt += `EXISTING INFORMATION (MUST KEEP AND PRIORITIZE):\n${currentContent}\n\n`;
            prompt += `INSTRUCTIONS:\n`;
            prompt += `1. KEEP all the existing information above - it is accurate and must be preserved\n`;
            prompt += `2. Enhance and expand the existing details with additional relevant information\n`;
            prompt += `3. Add complementary details about:\n`;
            prompt += `   - Physical condition specifics (scratches, wear, dents, etc.)\n`;
            prompt += `   - Functionality status\n`;
            prompt += `   - Packaging condition\n`;
            prompt += `   - Any notable defects or issues\n`;
            prompt += `   - Cosmetic condition details\n`;
            prompt += `4. Organize all information (existing + new) in a clear HTML bullet list\n`;
            prompt += `5. Do NOT contradict or remove the existing information\n`;
            prompt += `6. If the existing info is complete, just format it better with HTML\n\n`;
        } else {
            prompt += `CRITICAL RULES - DO NOT INVENT:\n`;
            prompt += `1. NO physical access to item\n`;
            prompt += `2. Provide ONLY generic "${conditionName}" expectations\n`;
            prompt += `3. MAXIMUM 3 bullet points TOTAL\n`;
            prompt += `4. Each bullet: max 10-12 words\n`;
            prompt += `5. Use "may show", "typical for used"\n\n`;
        }
        
        prompt += `Return ONE simple <ul> with MAXIMUM 3 short <li> items. Be BRIEF.`;

        const systemPrompt = `You are brief. Maximum 3 bullet points TOTAL. Each bullet max 12 words. Be generic. Use "may show", "typical". NO specific defects.`;

        // Construire les données AI et appeler l'API
        const aiData = buildAiData(prompt, systemPrompt, 150, 0.7);
        
        let response = await fetchAi(aiData);
        
        console.log('aiSuggestConditionSupp - Raw API response:', response);
        
        // Utiliser la fonction de normalisation
        response = normalizeAiResponse(response);
        console.log('aiSuggestConditionSupp - Normalized response:', response.substring(0, 100) + '...');
        
        if (response && response !== '' && response !== '[object Object]') {
            console.log('aiSuggestConditionSupp - Final response to insert:', response.substring(0, 100) + '...');
            // Insérer le contenu généré dans le Summernote editor
            $(`#${textareaId}`).summernote('code', response);
            
            // Déclencher la traduction automatique comme dans aiSuggestDescriptionSupp
            getTranslate(response, 2, 'Fr', specificsRow, 'summernote', 'product');
            
            // Appeler generateInfo() comme dans aiSuggestDescriptionSupp
            if (typeof generateInfo === 'function') {
                generateInfo();
            }
            
            // Notification de succès
            showNotification('✅ Additional Conditions generated successfully!', 'success');
        } else {
            console.error('❌ [aiSuggestConditionSupp] Empty response after processing!');
            throw new Error('Empty response from AI');
        }
        
    } catch (error) {
        console.error('Error generating additional conditions:', error);
        showNotification('❌ Error generating additional conditions', 'error');
    } finally {
        button.prop('disabled', false).html('<i class="fa-solid fa-robot"></i><i class="fa-solid fa-question"></i>');
    }
}

/**
 * AI Generate Included Accessories
 * Searches internet with UPC to find real accessories included with the product
 */
async function aiSuggestIncludedAccessories(languageId, specificsRow) {
    const button = $(`#ai-suggest-included-accessories-btn${languageId}`);
    button.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Generating...');

    try {
        // Récupérer les informations du produit
        const productName = $(`input[name="product_description[${languageId}][name]"]`).val() || '';
        const brand = $('select[name="manufacturer_id"] option:selected').text() || '';
        const upc = $('input[name="upc"]').val() || '';
        const model = $('input[name="model"]').val() || '';
        
        // Récupérer le contenu actuel s'il existe
        const textareaId = `product-description-${languageId}-${specificsRow}`;
        const summernoteCode = $(`#${textareaId}`).summernote('code');
        const currentContent = summernoteCode.replace(/<[^>]*>/g, '').trim();
        
        
        if (!upc && !productName) {
            showNotification('⚠️ Please add product name or UPC first', 'warning');
            button.prop('disabled', false).html('<i class="fa-solid fa-robot"></i><i class="fa-solid fa-question"></i>');
            return;
        }
        
        // Construire le prompt avec recherche internet
        let prompt = `Based on internet research and product specifications, list the ACTUAL accessories included with this product:\n\n`;
        prompt += `Product: ${productName}\n`;
        if (brand && brand !== '---') prompt += `Brand: ${brand}\n`;
        if (upc) prompt += `UPC: ${upc}\n`;
        if (model) prompt += `Model: ${model}\n\n`;
        
        if (currentContent) {
            prompt += `EXISTING ACCESSORIES LIST (VERIFIED - MUST KEEP):\n${currentContent}\n\n`;
            prompt += `INSTRUCTIONS:\n`;
            prompt += `1. The accessories listed above are VERIFIED and accurate - KEEP them all\n`;
            prompt += `2. Research if there are any ADDITIONAL accessories not yet listed\n`;
            prompt += `3. Add ONLY accessories that are truly included in the retail package\n`;
            prompt += `4. Verify the accuracy of existing accessories and correct only if clearly wrong\n`;
            prompt += `5. Organize the complete list (existing + any new) in a clear HTML format\n`;
            prompt += `6. Do NOT remove any existing accessories unless they are clearly incorrect\n\n`;
        } else {
            prompt += `CRITICAL RULES - DO NOT GUESS:\n`;
            prompt += `1. If you do NOT know what accessories come with this product, say "Unable to verify included accessories"\n`;
            prompt += `2. DO NOT assume common accessories are included\n`;
            prompt += `3. DO NOT list "instruction manual" unless you KNOW it's included\n`;
            prompt += `4. DO NOT list cables, chargers, batteries unless VERIFIED for this specific model\n`;
            prompt += `5. For media (CD, DVD, books): typically NO accessories unless special edition\n`;
            prompt += `6. When uncertain, return ONLY: "No additional accessories information available"\n`;
            prompt += `7. Better to list NOTHING than to invent accessories\n\n`;
        }
        
        prompt += `Rules:\n`;
        prompt += `1. Only list accessories ACTUALLY included (cables, manuals, adapters, batteries)\n`;
        prompt += `2. Be specific (e.g., "USB-C cable" not just "cable")\n`;
        prompt += `3. Do NOT list the main product itself\n`;
        prompt += `4. Do NOT invent accessories - only real ones\n`;
        prompt += `5. Keep it SHORT - max 3-5 items\n`;
        prompt += `6. If you don't know, return: <p>Product only (no additional accessories)</p>\n`;
        if (currentContent) {
            prompt += `7. KEEP the existing accessories in the list\n`;
        }
        prompt += `\nFormat as simple HTML <ul> with <li> tags. KEEP IT SHORT - max 5 items.`;

        const systemPrompt = `You are a product research specialist. CRITICAL: DO NOT guess. If you don't know, say "Product only (no additional accessories)". Most products have NO or MINIMAL accessories. Keep lists SHORT (max 5 items). NEVER INVENT.`;

        // Construire les données AI et appeler l'API avec température plus basse pour plus de précision
        const aiData = buildAiData(prompt, systemPrompt, 200, 0.5);
        
        let response = await fetchAi(aiData);
        
        console.log('aiSuggestIncludedAccessories - Raw API response:', response);
        
        // Utiliser la fonction de normalisation
        response = normalizeAiResponse(response);
        
        console.log('aiSuggestIncludedAccessories - Normalized response:', response.substring(0, 150) + '...');
        response = normalizeAiResponse(response);
        
        if (response && response !== '' && response !== '[object Object]') {
            // Insérer le contenu généré dans le Summernote editor
            $(`#${textareaId}`).summernote('code', response);
            
            // Déclencher la traduction automatique comme dans aiSuggestDescriptionSupp
           // getTranslate(response, 2, 'Fr', specificsRow, 'summernote', 'product');
            
            // Appeler generateInfo() comme dans aiSuggestDescriptionSupp
            if (typeof generateInfo === 'function') {
                generateInfo();
            }
            
            // Notification de succès
            showNotification('✅ Included Accessories generated successfully!', 'success');
        } else {
            console.error('❌ [aiSuggestIncludedAccessories] Empty response after processing!');
            throw new Error('Empty response from AI');
        }
        
    } catch (error) {
        console.error('Error generating included accessories:', error);
        showNotification('❌ Error generating included accessories', 'error');
    } finally {
        button.prop('disabled', false).html('<i class="fa-solid fa-robot"></i><i class="fa-solid fa-question"></i>');
    }
}

function showNotification(message, type = 'info') {
    // Créer une notification temporaire
    const notification = $(`
        <div class="alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'} alert-dismissible fade show" 
             role="alert" 
             style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `);
    
    $('body').append(notification);
    
    // Auto-dismiss après 3 secondes
    setTimeout(() => {
        notification.fadeOut(() => notification.remove());
    }, 3000);
}


function transformDataToFormattedString(data) {
    const result = {};
    
    Object.keys(data).forEach(key => {
        const value = data[key];
        const formattedKey = formatKey(key);

        // Si la valeur est non vide
        if (value !== null && value !== undefined && value !== '') {
            // Si la clé existe déjà, éviter les doublons
            if (result[formattedKey]) {
                if (Array.isArray(value)) {
                    result[formattedKey] += `, ${value.join(', ')}`;
                } else {
                    result[formattedKey] += `, ${value}`;
                }
            } else {
                // Ajouter la clé et la valeur au résultat
                result[formattedKey] = Array.isArray(value) ? value.join(', ') : value;
            }
        }
    });

    // Construire le texte final
    return Object.entries(result)
        .map(([key, value]) => `${key}: ${value}`)
        .join('; ');
}

// Fonction pour formater les clés
function formatKey(key) {
    return key
        .replace(/product_description\[\d+\]\[specifics\]\[(.*?)\]\[.*?\]/g, '$1') // Simplifie les clés spécifiques
        .replace(/product_description\[\d+\]\[(.*?)\]/g, '$1') // Simplifie les clés générales
        .replace(/_/g, ' ') // Remplace les underscores par des espaces
        .replace(/\[\]$/, '') // Retire les crochets vides
        .trim(); // Supprime les espaces superflus
}

function removeDuplicateKeys(text) {
    // Remplacer les patterns "Key: Key, Value" par "Key: Value"
    // Utilise une regex avec backreference pour détecter la répétition
    return text.replace(/(\w+(?:\s+\w+)*?):\s*\1,\s*/g, '$1: ');
}
