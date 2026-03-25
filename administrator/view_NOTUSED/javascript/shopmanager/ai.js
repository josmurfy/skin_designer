function getProductSpecific(aspectName, callback) {
  
    var product_id = document.querySelector('input[name="product_id"]').value;
    var token = document.querySelector('input[name="token"]').value;


    var data = {
        product_id: product_id,
        aspectName: aspectName
    };
//alert(data.product_id);
    fetch('index.php?route=shopmanager/ai/getProductSpecific&token=' + token, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(async response => {
        const text = await response.text();
        try {
            const json = JSON.parse(text);
            if (json && json.values) {
                callback(null, json.values);
            } else if (json && json.message) {
                callback(null, json.message);
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
    const token = document.querySelector('input[name="token"]').value;

    fetch('index.php?route=shopmanager/ai/getProductSpecific&token=' + token + '&aspect_name=' + encodeURIComponent(aspectName))
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

function verifyAllSpecifics() {
  
    var rows = document.querySelectorAll('[id^="specifics_1_"]');
    rows.forEach(row => {
        // Extraire l'ID de la ligne en supprimant le préfixe "specifics1-row"
        var rowId = row.id.replace('specifics_1_', '');

        // Sélectionner l'élément VerifiedSource correspondant
        var verifiedSourceElement = document.getElementById(`verified_source_1_${rowId}`);

        // Si l'élément existe et que sa valeur n'est pas "yes", appeler la fonction verifySpecific
        if (verifiedSourceElement && verifiedSourceElement.value.toLowerCase() !== 'yes') {
            verifySpecific(rowId);
        }
    });
    finishLoadingPopup();
}
function verifySpecific(row, finish = 'false') {
    showLoadingPopup('Vérification des aspects avec l’IA en cours...');

    const token = document.querySelector('input[name="token"]').value;
    const Actual_value = document.getElementById('hidden_original_value_1_' + row).value;
    const specificRowElem = document.getElementById('specifics_1_' + row);

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

    const productName = document.getElementById('product_description_1_0')?.value || '';
    const description = document.getElementById('product_description_1_6')?.value || '';
    let category = document.getElementById('input-category')?.value || '';
    category = category.replace(/\s*\([^)]*\)/, '').trim();

    const responseElemId = `response_product_description_${language_id}_` + row;
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

    return fetch('index.php?route=shopmanager/ai.prompt_ai&token=' + token, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(async response => {
        const text = await response.text();
        try {
            const data = JSON.parse(text);
            const rawMessage = Array.isArray(data.message) ? data.message.join(', ') : data.message;
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
                document.getElementById('original_value_' + language_id + '_' + row).textContent = specificValueElem.value;
                document.getElementById('hidden_original_value_' + language_id + '_' + row).value = specificValueElem.value;

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


function verifySpecificOLD(row, finish = 'false') {
    showLoadingPopup('Vérification des aspects avec l’IA en cours...');

    const token = document.querySelector('input[name="token"]').value;
    const Actual_value = document.getElementById('hidden_original_value_1_' + row).value;
    const specificRowElem = document.getElementById('specifics_1_' + row);

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

    const productName = document.getElementById('product_description_1_0')?.value || '';
    const description = document.getElementById('product_description_1_6')?.value || '';
    let category = document.getElementById('input-category')?.value || '';
    category = category.replace(/\s*\([^)]*\)/, '').trim();

    const responseElemId = `response_product_description_${language_id}_` + row;
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

    return fetch('index.php?route=shopmanager/ai.prompt_ai&token=' + token, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(async response => {
        const text = await response.text();
        try {
            const data = JSON.parse(text);
            const rawMessage = Array.isArray(data.message) ? data.message[0] : data.message;
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
                document.getElementById('original_value_' + language_id + '_' + row).textContent = specificValueElem.value;
                document.getElementById('hidden_original_value_' + language_id + '_' + row).value = specificValueElem.value;

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
    var token = document.querySelector('input[name="token"]').value;
    var Actual_value = document.getElementById('hidden_original_value_1_' + row).value;
    var specificRowElem = document.getElementById('specifics_1_' + row);
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

    var productNameElem = document.getElementById('product_description_1_0');
    var productName = productNameElem ? productNameElem.value : '';

    var descriptionElem = document.getElementById('product_description_1_6');
    var description = descriptionElem ? descriptionElem.value : '';

    var categoryElem = document.getElementById('input-category');
    var category = categoryElem ? categoryElem.value : '';

    if (category) {
        category = category.replace(/\s*\([^)]*\)/, '').trim();
    }

    var responseElemId = `response_product_description_${language_id}_` + row;
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

    return fetch('index.php?route=shopmanager/ai.prompt_ai&token=' + token, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            var messageLower = data.message.toLowerCase();
            var extractedValue = '';

            if (messageLower.includes('is: ')) {
                extractedValue = data.message.split('is: ')[1].trim().replace(/\.$/, '');
            } else if (messageLower.includes('is ')) {
                extractedValue = data.message.split('is ')[1].trim().replace(/\.$/, '');
            } else if (messageLower.includes(': ')) {
                extractedValue = data.message.split(': ')[1].trim().replace(/\.$/, '');
            } else {
                extractedValue = data.message.trim().replace(/\.$/, '');
            }

            if (extractedValue.length > 70 && !extractedValue.includes(',') && !extractedValue.includes(';')) {
                extractedValue = extractedValue.substring(0, 70);
            }

            if ((messageLower.includes('true') || messageLower.includes(specificValue.toLowerCase())) && specificValue.trim() !== "") {
                updateTargetElement(row, specificValue, 'green', 'no');
                return JSON.stringify({ success: true, message: 'Value is verified as accurate.', row: row, value: specificValue });
            } else if ((specificValue.trim() === "" || extractedValue.trim() !== "") && extractedValue.toLowerCase() !== 'false') {
                var originalValue = specificValueElem.value;
                document.getElementById('original_value_' + language_id + '_' + row).textContent = originalValue;
                document.getElementById('hidden_original_value_' + language_id + '_' + row).value = originalValue;

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
                responseElem.textContent = data.message;
                return JSON.stringify({ success: false, message: data.message, row: row });
            }
        } else {
            return JSON.stringify({ success: false, error: data.message, row: row });
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
    var specificRowElem = document.getElementById('specifics_1_' + row);
    if (!specificRowElem) {
        console.error('Row element not found:', row);
        return;
    }

    var targetElement = $('#product_description_1_' + row);
    var valueLowerCase = value.toLowerCase().trim(); // Convertir en minuscule pour vérifier "false"

    // Forcer la couleur rouge si la valeur est "false"
    if (valueLowerCase === "false") {
        color = "red";
    }

    // Appliquer le style à la ligne
    specificRowElem.style.backgroundColor = color;
    specificRowElem.style.color = "white";

    // Mettre à jour les champs cachés et les affichages
 

    // Gestion des champs SELECT
    if (targetElement.is('select')) {
        var values = value.split(',').map(val => val.trim()); // Nettoie les espaces

        values.forEach(function (val) {
            // Ajouter la valeur si elle n'existe pas
            if (targetElement.find('option[value="' + val + '"]').length === 0) {
                targetElement.prepend('<option value="' + val + '">' + val + '</option>');
            }
        });

        // Mettre à jour le SELECT seulement si la couleur est ORANGE et la valeur n'est pas "false"
        if (color === "orange" && translate === '' && valueLowerCase !== "false") {
            targetElement.val(values).trigger('change');
            $('#hidden_original_value_1_' + row).val(value);
            $('#original_value_1_' + row).text(Array.isArray(value) ? value.join(', ') : value);
        }
    }
    // Gestion des champs texte
    else if (targetElement.is('input[type="text"]')) {
        // Mettre à jour uniquement si la valeur n'est pas "false"
        if (valueLowerCase !== "false") {
            targetElement.val(value);
            $('#hidden_original_value_1_' + row).val(value);
            $('#original_value_1_' + row).text(Array.isArray(value) ? value.join(', ') : value);
        }
    }
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

    console.log('loadsuggestEntryName:' + fieldName);
    var product_id = document.querySelector('input[name="' + fieldName + '_id"]').value;
    console.log('product_id:' + product_id);

    //alert(fieldName);
    var form = document.getElementById('form-' + fieldName);
    if (!form) {
        console.error('Form element not found');
        return;
    }
    if (!recognizedTextElement) {
       
    
   /*     var excludeSelectors = [ // '[name="' + fieldName + '_description[1][description_supp]"]'
            '#language2 input, #language2 select, #language2 textarea',
            '#specifics_language2 input, #specifics_language2 select, #specifics_language2 textarea',
            '[name="' + fieldName + '_description[2][description]"]',  '[name="price"]','[name="shipping_cost"]', '[name="shipping_cost"]',
            '[name="' + fieldName + '_description[1][meta_description]"]', '[name="' + fieldName + '_description[1][meta_description]"]',
            '[name="weight"]', '[name="weight_oz"]','[name="lenght"]', '[name="width"]','[name="height"]','[name="upc"]','[name="sku"]',
            '[name="marketplace_item_id"]'
           
        ];*/
        var excludeSelectors = [
            // Langues et descriptions inutilisées
            '#language2 input, #language2 select, #language2 textarea',
            '#specifics_language2 input, #specifics_language2 select, #specifics_language2 textarea',
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
            'input[type="hidden"][id^="hidden_original_value_1_"]',

        
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
            '[name="token"]',

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
formData = transformDataToFormattedString(formData);
        var cleanedText = JSON.stringify(formData);
      
    } else{
       // formData = stripHtmlTagsFromFormData(formData);Id('recognizedText');
        var cleanedText = recognizedTextElement.value.replace(/[\r\n]+/g, ' ');
      //  var cleanedText = recognizedTextElement.value;
    }             
    //e.error('recognizedText element not found');
   // console.log('fieldName:' + fieldName);
   console.log('cleanedText:' + cleanedText);
   // var productName = form.querySelector('input[name="' + fieldName + '_description[1][name]"]').value;
   /* if (!productName) {pHtmlTagsFromFormData(formData);
        var cleanedText = recognizedTextElement.value.replace(/[\r\n]+/g, ' ');
      //  var cleanedText = recognizedTextElement.value;
    }             
   // console.log('fieldName:' + fieldName);
   console.log('cleanedText:' + cleanedText);
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

    console.log('loadsuggestEntryName:' + fieldName);
    var product_id = document.querySelector('input[name="' + fieldName + '_id"]').value;
    console.log('product_id:' + product_id);

    //alert(fieldName);
    var form = document.getElementById('form-' + fieldName);
    if (!form) {
        console.error('Form element not found');
        return;
    }
    if (!recognizedTextElement) {
       
    
   /*     var excludeSelectors = [ // '[name="' + fieldName + '_description[1][description_supp]"]'
            '#language2 input, #language2 select, #language2 textarea',
            '#specifics_language2 input, #specifics_language2 select, #specifics_language2 textarea',
            '[name="' + fieldName + '_description[2][description]"]',  '[name="price"]','[name="shipping_cost"]', '[name="shipping_cost"]',
            '[name="' + fieldName + '_description[1][meta_description]"]', '[name="' + fieldName + '_description[1][meta_description]"]',
            '[name="weight"]', '[name="weight_oz"]','[name="lenght"]', '[name="width"]','[name="height"]','[name="upc"]','[name="sku"]',
            '[name="marketplace_item_id"]'
           
        ];*/
        var excludeSelectors = [
            // Langues et descriptions inutilisées
            '#language2 input, #language2 select, #language2 textarea',
            '#specifics_language2 input, #specifics_language2 select, #specifics_language2 textarea',
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
            'input[type="hidden"][id^="hidden_original_value_1_"]',

        
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
            '[name="token"]',

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
formData = transformDataToFormattedString(formData);
        var cleanedText = JSON.stringify(formData);
      
    } else{
       // formData = stripHtmlTagsFromFormData(formData);Id('recognizedText');
        var cleanedText = recognizedTextElement.value.replace(/[\r\n]+/g, ' ');
      //  var cleanedText = recognizedTextElement.value;
    }             
    //e.error('recognizedText element not found');
   // console.log('fieldName:' + fieldName);
   console.log('cleanedText:' + cleanedText);
   // var productName = form.querySelector('input[name="' + fieldName + '_description[1][name]"]').value;
   /* if (!productName) {pHtmlTagsFromFormData(formData);
        var cleanedText = recognizedTextElement.value.replace(/[\r\n]+/g, ' ');
      //  var cleanedText = recognizedTextElement.value;
    }             
   // console.log('fieldName:' + fieldName);
   console.log('cleanedText:' + cleanedText);
   // var productName = form.querySelector('input[name="' + fieldName + '_description[1][name]"]').value;
   /* if (!productName) {
        console.error('Product name not found');
        return;
    }*/
    
       // var prompt = `Generate a concise and accurate title for a product with the following details: ${JSON.stringify(formData)}.`;
    //    var prompt = `Generate a product title with a length between 70 and 80 characters for the following details: ${JSON.stringify(formData)}. Ensure the title does not exceed 80 characters.`;
    let category_id = $('#category_id').val();
    //let prompt = '';
    let keep = '';
    
    switch (category_id) {
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
    
        default:
            let manufacturer = formData['manufacturer'] && typeof formData['manufacturer'] === 'string' ? ` Manufacturer: ${formData['manufacturer']}` : "";
            let condition_name = formData['condition_name'] ? ` Condition: ${formData['condition_name']}` : "";
            let model = formData['model'] ? ` Model: ${formData['model']}` : "";
            let color = formData['color'] ? ` Color: ${formData['color']}` : "";
    
            prompt = `Based on Titles: "${cleanedText}"${condition_name}${color}${manufacturer}${model}, create an optimized product title in this format: {'title': Generated Title }`;
            keep = "";
            break;
    }
    
        console.log('prompt:' + prompt);
       /* var system_prompt = $('#category_id').val() == 617
            ? "The title should be a minLength=70 maxLength=80. Provide titles use this format: movie title (dvd or bluray, years of the movie, widescreen or full screen) actors or productor, production keep the number of disc set and if it's a Canadian version."
            : "The title should be a minLength=70 maxLength=80. Provide concise and accurate product titles. ";*/
            var system_prompt = $('#category_id').val() == 617
            ? "Return the value only in json {'title': your value} "
            : "Return the value only in json {'title': your value} ";
        
            var data = buildAiData(prompt, system_prompt, 100, 0.3);
            console.log('system_prompt:' + system_prompt);
    
        try {
            var aiResponse = await fetchAi(data);
            console.log('AI brut:', aiResponse);

            // S'assurer qu'on récupère bien le title peu importe le format
            if (typeof aiResponse === 'object' && aiResponse !== null) {
                if ('title' in aiResponse && typeof aiResponse.title === 'string') {
                    aiResponse = aiResponse.title;
                    console.log('✅ Titre (direct):', aiResponse);
                } else if ('message' in aiResponse && typeof aiResponse.message === 'object' && 'title' in aiResponse.message) {
                    aiResponse = aiResponse.message.title;
                    console.log('✅ Titre (dans message):', aiResponse);
                } else {
                    console.warn('❌ Format d’objet inattendu:', aiResponse);
                    aiResponse = '[UNKNOWN FORMAT]';
                }
            } else if (typeof aiResponse === 'string') {
                try {
                    const parsed = JSON.parse(aiResponse);
                    if (parsed && parsed.title) {
                        aiResponse = parsed.title;
                        console.log('✅ Titre (après parse):', aiResponse);
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
                console.log('AI brut:', aiResponse);

                // S'assurer qu'on récupère bien le title peu importe le format
                if (typeof aiResponse === 'object' && aiResponse !== null) {
                    if ('title' in aiResponse && typeof aiResponse.title === 'string') {
                        aiResponse = aiResponse.title;
                        console.log('✅ Titre (direct):', aiResponse);
                    } else if ('message' in aiResponse && typeof aiResponse.message === 'object' && 'title' in aiResponse.message) {
                        aiResponse = aiResponse.message.title;
                        console.log('✅ Titre (dans message):', aiResponse);
                    } else {
                        console.warn('❌ Format d’objet inattendu:', aiResponse);
                        aiResponse = '[UNKNOWN FORMAT]';
                    }
                } else if (typeof aiResponse === 'string') {
                    try {
                        const parsed = JSON.parse(aiResponse);
                        if (parsed && parsed.title) {
                            aiResponse = parsed.title;
                            console.log('✅ Titre (après parse):', aiResponse);
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
                if (recognizedTextElement) {
                   // console.log('recognizedTextElement:' + recognizedTextElement);
                    switchEntryName('product','0');
                }else{
                    aiSuggestDescriptionSupp(4,fieldName,recognizedTextElement);
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
    
        console.log('prompt:' + prompt);
       /* var system_prompt = $('#category_id').val() == 617
            ? "The title should be a minLength=70 maxLength=80. Provide titles use this format: movie title (dvd or bluray, years of the movie, widescreen or full screen) actors or productor, production keep the number of disc set and if it's a Canadian version."
            : "The title should be a minLength=70 maxLength=80. Provide concise and accurate product titles. ";*/
            var system_prompt = "Return the value only in json {'title': your value} ";
        
            var data = buildAiData(prompt, system_prompt, 100, 0.3);
            console.log('system_prompt:' + system_prompt);
    
        try {
            var aiResponse = await fetchAi(data);
            console.log('AI brut:', aiResponse);

            // S'assurer qu'on récupère bien le title peu importe le format
            if (typeof aiResponse === 'object' && aiResponse !== null) {
                if ('title' in aiResponse && typeof aiResponse.title === 'string') {
                    aiResponse = aiResponse.title;
                    console.log('✅ Titre (direct):', aiResponse);
                } else if ('message' in aiResponse && typeof aiResponse.message === 'object' && 'title' in aiResponse.message) {
                    aiResponse = aiResponse.message.title;
                    console.log('✅ Titre (dans message):', aiResponse);
                } else {
                    console.warn('❌ Format d’objet inattendu:', aiResponse);
                    aiResponse = '[UNKNOWN FORMAT]';
                }
            } else if (typeof aiResponse === 'string') {
                try {
                    const parsed = JSON.parse(aiResponse);
                    if (parsed && parsed.title) {
                        aiResponse = parsed.title;
                        console.log('✅ Titre (après parse):', aiResponse);
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
                console.log('AI brut:', aiResponse);

                // S'assurer qu'on récupère bien le title peu importe le format
                if (typeof aiResponse === 'object' && aiResponse !== null) {
                    if ('title' in aiResponse && typeof aiResponse.title === 'string') {
                        aiResponse = aiResponse.title;
                        console.log('✅ Titre (direct):', aiResponse);
                    } else if ('message' in aiResponse && typeof aiResponse.message === 'object' && 'title' in aiResponse.message) {
                        aiResponse = aiResponse.message.title;
                        console.log('✅ Titre (dans message):', aiResponse);
                    } else {
                        console.warn('❌ Format d’objet inattendu:', aiResponse);
                        aiResponse = '[UNKNOWN FORMAT]';
                    }
                } else if (typeof aiResponse === 'string') {
                    try {
                        const parsed = JSON.parse(aiResponse);
                        if (parsed && parsed.title) {
                            aiResponse = parsed.title;
                            console.log('✅ Titre (après parse):', aiResponse);
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
                   // console.log('recognizedTextElement:' + recognizedTextElement);
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
  //  console.log('categoryName extracted:',categoryName);  // Ajout de logs pour vérifier le comportement
  //  console.log('Return:',`A highly detailed, photo-realistic image product category with a WHITE background for "${categoryName}" . Ensure the product image is centered `);  // Ajout de logs pour vérifier le comportement
  //  return `A highly detailed, photo-realistic image 1 to 3 products category with a WHITE background for "${categoryName}" . Ensure the product image is centered `;
 //   return `A highly detailed, photo-realistic image featuring some products Ensure image white border and centered on a pure WHITE background Clean. from the category:"${categoryName}" . `;
   // return `A highly detailed, photo-realistic image featuring no more than 1 to 3 products from the "${categoryName}" category. Ensure the image has a clean white border and that the products are centered on a pure WHITE background.`;
    return `A photo-realistic category image with exactly 1 to 3 products from the "${categoryName}". Ensure that the products are isolated, clearly visible, and centered. The background must be completely WHITE, and the image should have a clean, thin white border surrounding it. No additional products or elements should be in the image.`;
   // return `A photo-realistic image with exactly 1 to 3 products Ensure that the products are isolated, clearly visible, and centered. The background must be completely WHITE, and the image should have a clean, thin white border surrounding it. No additional products or elements should be in the image. from the "${categoryName}" category. `;

}

async function aiSuggestDescriptionSupp(specificsRow = 4, fieldName = 'product', recognizedTextElement) {
    var product_id = document.querySelector('input[name="' + fieldName + '_id"]').value;

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
                '#specifics_language2 input, #specifics_language2 select, #specifics_language2 textarea',
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
                'input[type="hidden"][id^="hidden_original_value_1_"]',
    
            
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
                '[name="token"]',
    
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
            formData = transformDataToFormattedString(formData);
       
            var cleanedText =  JSON.stringify(formData);
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
    var textareaId = `${fieldName}_description_1_${specificsRow}`;
    var aiResultElement = document.getElementById(textareaId);
    if (aiResultElement) {
        var prompt = getAiPromptForDescription(cleanedText, productName);
        var system_prompt = $('#category_id').val() == 617
        ? "Return the value only in json {'description': } "
        : "Return the value only in json {'description': } ";
        var data = buildAiData(prompt, "Provide a general product description." + system_prompt, 1000, 0.7);
   //     console.log('prompt:' + prompt);
        try {

            
            var aiResponse = await fetchAi(data);
        
// S'assurer qu'on récupère bien le title peu importe le format
if (typeof aiResponse === 'object' && aiResponse !== null) {
    if ('description' in aiResponse && typeof aiResponse.description === 'string') {
        aiResponse = aiResponse.description;
        console.log('✅ Titre (direct):', aiResponse);
    } else if ('message' in aiResponse && typeof aiResponse.message === 'object' && 'description' in aiResponse.message) {
        aiResponse = aiResponse.message.description;
        console.log('✅ Titre (dans message):', aiResponse);
    } else {
        console.warn('❌ Format d’objet inattendu:', aiResponse);
        aiResponse = '[UNKNOWN FORMAT]';
    }
} else if (typeof aiResponse === 'string') {
    try {
        const parsed = JSON.parse(aiResponse);
        if (parsed && parsed.description) {
            aiResponse = parsed.description;
            console.log('✅ Titre (après parse):', aiResponse);
        }
    } catch (e) {
        console.warn('⚠️ String non parsable:', aiResponse);
    }
}

        
            var formattedTextJson = await getFormattedText(aiResponse);
            console.log('✅ Titre (direct):', formattedTextJson);
            try {
                // Vérification et parsing du JSON
                //const formattedTextObj = formattedTextJson;
                const formattedTextObj = formattedTextJson;//JSON.parse(formattedTextJson);
                console.log('JSON:', JSON.stringify(formattedTextJson, null, 2));
                console.log('formattedTextObj:', formattedTextObj);
                if (formattedTextObj){//} && formattedTextObj.html) {
                    // Utilisation directe du HTML retourné
                    const formattedHtml = formattedTextObj.trim(); 
            
                    console.log('textareaId:', textareaId); 
                    console.log('formattedHtml:', formattedHtml);
            
                    aiResultElement.value = formattedHtml;
            
                    if (aiResultElement.classList.contains('summernote')) {
                        $(`#${textareaId}`).summernote('code', formattedHtml);
                        getTranslate(formattedHtml, 2, 'Fr', specificsRow, 'summernote', fieldName);
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
async function aiSuggestDescription(specificsRow, form, fieldName = 'product') {
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

    // Liste des champs à exclure
    var excludeSelectors = [
        '#language2 input, #language2 select, #language2 textarea',
        '#specifics_language2 input, #specifics_language2 select, #specifics_language2 textarea',
        '[name^="' + fieldName + '_description[2]"]',
        '[name="weight"]', '[name="weight_oz"]', '[name="length"]', '[name="width"]', '[name="height"]',
        '[name="upc"]', '[name="sku"]', '[name="marketplace_item_id"]', '[name^="price_ebay"]',
        '[name="quantity"]', '[name="location"]', '[name="unallocated_quantity"]',
        '[name="conditions_json1"]', '[name="price"]', '[name="shipping_cost"]', '[name="price_with_shipping"]',
        '[name="files"]', '[name="shipping_carrier"]', '[name="token"]',
        '[name^="checkbox"]', '[name^="hidden_"]', '[name="tax_class_id"]',
        '[name="minimum"]', '[name="subtract"]', '[name="stock_status_id"]', '[name="shipping"]',
        '[name="date_available"]', '[name="status"]', '[name="sort_order"]',
        '[name="filter"]', '[name="' + fieldName + '_store[]"]',
        '[name="download"]', '[name="related"]', '[name="option"]', '[name="points"]',
        '[name^="' + fieldName + '_reward"]', '[name^="' + fieldName + '_layout"]'
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

    console.log('fieldName extracted:', fieldName);

    // Déterminer le nom de la catégorie
    var categoryInput = formElement.querySelector('input[name="' + fieldName + '_description[1][name]"]');
    var categoryName = categoryInput ? categoryInput.value.trim() : '';

    if (fieldName.trim() === 'category') {
        var categoryPathInput = formElement.querySelector('input[name="path"]');
        var categoryPath = categoryPathInput ? categoryPathInput.value.replace('Search by category >', '').trim() : '';
        categoryName = categoryName + ' From: ' + categoryPath;
    }

    if (!categoryName) {
        console.error('Error: Category name not found');
        return;
    }

    // Générer la requête AI
    var prompt = getAiPromptForDescription(JSON.stringify(formData), categoryName);
    var data = buildAiData(prompt, "Provide a " + fieldName + " description for product to sell.", 500, 0.7);

    try {
        const aiResponse = await fetchAi(data);

        if (!aiResponse || typeof aiResponse !== 'string') {
            console.error("Error: AI response is empty or invalid.");
            return;
        }

        var textareaId = `${fieldName}_description_1_${specificsRow}`;
        var aiResultElement = document.getElementById(textareaId);

        if (!aiResultElement) {
            console.error('Error: AI result element not found');
            return;
        }

        const formattedTextJson = await getFormattedText(aiResponse);

           try {
               // Vérification et parsing du JSON
               //const formattedTextObj = formattedTextJson;
               const formattedTextObj = JSON.parse(formattedTextJson);
               console.log('JSON:', JSON.stringify(formattedTextJson, null, 2));
               console.log('formattedTextObj:', formattedTextObj);
                 if (formattedTextObj && formattedTextObj.html) {
                    // Utilisation directe du HTML retourné
                    const formattedHtml = formattedTextObj.html.trim(); 
           
                   console.log('textareaId:', textareaId);
                   console.log('formattedHtml:', formattedHtml);
           
                   aiResultElement.value = formattedHtml;
           
                   if (aiResultElement.classList.contains('summernote')) {
                       $(`#${textareaId}`).summernote('code', formattedHtml);
                       getTranslate(formattedHtml, 2, 'Fr', specificsRow, 'summernote', fieldName);
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


async function fetchAiImage(data) {

    var token = document.querySelector('input[name="token"]').value;
    var Button = $('#ai-suggest-image-btn');
    var ButtonDownload = $('#ai-suggest-image-download-btn');

    Button.prop('disabled', true).text('Generating...');
    const response = await fetch('index.php?route=shopmanager/ai.prompt_ai_image&token=' + token, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    const json = await response.json();

   
    if (json.success) {
        return { url: json.message };
    } else {
        throw new Error(json.message || 'Unknown error');
    }
}

async function aiSuggestImage(form, fieldName = 'product') {
    var form = document.getElementById(form);
    var Button = $('#ai-suggest-image-btn');
    var ButtonDownload = $('#ai-suggest-image-download-btn');
    var ButtonOpenImage = $('#ai-suggest-image-open-btn');
    var inputElement = document.getElementById('image-url');
    ButtonDownload.attr('onclick', '');
    ButtonOpenImage.attr('onclick', '');
    ButtonOpenImage.css('display', 'none');  // Utilisation de jQuery pour modifier le style
    ButtonDownload.css('display', 'none');  // Utilisation de jQuery pour modifier le style
// Afficher l'URL dans le champ input
   
   
    inputElement.style.display = "none";

    if (!form) {
        console.error('Form element not found');
        return;
    }

   // console.log('fieldName extracted:',fieldName);  // Ajout de logs pour vérifier le comportement

   if (fieldName.trim() === 'category') {
  //  alert(fieldName);
    var category = form.querySelector('input[name="' + fieldName + '_description[1][name]"]').value;
    var categorypath = form.querySelector('input[name="path"]').value;
   
    var categorydescription = $(`textarea[id^="${fieldName}_description_1_1"]`).val();

   
    var tempElement = document.createElement('div');
 //   console.log('categorydescription extracted:',categorydescription); 
    tempElement.innerHTML = categorydescription;
 //   console.log('tempElement.textContent extracted:',tempElement.textContent); 
    var plainText = tempElement.textContent || tempElement.innerText || "";
 //   console.log('plainText extracted:',plainText); 
    categorypath = categorypath.replace('Search by category >', '').trim();
    var categoryName = category + ' From: ' + categorypath + 'Based on :' + plainText;
//alert(categoryName);
} else {
    var categoryName = form.querySelector('input[name="' + fieldName + '_description[1][name]"]').value;
}

    if (!categoryName) {
        console.error('category name not found');
        return;
    }

    var prompt = getAiPromptForImage(categoryName);
    var data = { prompt: prompt };

    try {
        // Récupérer l'image générée par l'IA
        const aiResponse = await fetchAiImage(data);

        if (aiResponse.url) {
            // Extraire l'URL de base (sans les paramètres après .png)
            const imageUrlBase = aiResponse.url;; // aiResponse.url.split('?')[0];

       

            // Mettre à jour le bouton de téléchargement
         
            var aiResultElement = document.getElementById('thumb-image-result');
            ButtonDownload.prop('disabled', false).text('Download Image');
            Button.prop('disabled', false).text('Regenerate');
            ButtonDownload.removeClass("btn btn-primary").addClass("btn btn-warning");
          //  Button.setAttribute('onclick', 'uploadFromLink(' + imageUrlBase + ' );');
            ButtonDownload.attr('onclick', 'uploadFromLink("' + imageUrlBase + '");');
            ButtonOpenImage.attr('onclick', 'openAiImage("' + imageUrlBase + '");');
            ButtonOpenImage.css('display', 'block');  // Utilisation de jQuery pour modifier le style
            ButtonDownload.css('display', 'block');  // Utilisation de jQuery pour modifier le style
     // Afficher l'URL dans le champ input
           
            inputElement.value = imageUrlBase;
            inputElement.style.display = "block";
           

            if (aiResultElement) {
              
                aiResultElement.src = imageUrlBase;
                aiResultElement.style.width = '100px';
                aiResultElement.style.height = '100px';
            
            } else {
                console.error('AI result image element not found');
            }
        } else {
            console.error('AI response did not contain an image URL');
        }
    } catch (error) {
        console.error('Error:', error);
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

    var token = document.querySelector('input[name="token"]').value;
    $('#ai-suggest-entry-name-btn1').prop('disabled', true).text('Generating...');

  
    const response = await fetch('index.php?route=shopmanager/ai.prompt_ai&token=' + token, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    const json = await response.json();

    $('#ai-suggest-entry-name-btn1').prop('disabled', false).text('Done');

    console.log('json.success:',json.success);
    console.log('json.message:',json.message);

    if (json.success) {
        return json.message;
    } else {
        throw new Error(json.message || 'Unknown error');
    }
}
async function getFormattedText(description) {
    var prompt = `Format the following text with HTML tags for bold, italics, and paragraphs where appropriate:"${description}"
   `;

    var data = buildAiData(prompt, ' Return a valid JSON object with  key "html" containing the formatted text as a properly escaped string. Example: {"html": "<p><strong>Formatted text</strong></p>"} as a properly escaped string.', 500, 0.7);

    let response = await fetchAi(data);

    if (typeof response === 'object' && response !== null && response.html) {
        response = response.html;
    }

    try {
        if (!response) {
            throw new Error("Empty AI response");
        }

        // Forcer le format JSON si nécessaire
        if (typeof response === "object" && response.html) {
            return JSON.stringify({ html: String(response.html) }); // Force en JSON valide
        }

        if (typeof response === "string") {
            response = response.trim();

            // Vérifie si la réponse contient `html:` sans guillemets et corrige le JSON
            if (!response.includes('"html"') && response.includes("html:")) {
                console.warn("Fixing invalid JSON structure:", response);
                
                // Remplace `html:` par `"html":`
                response = response.replace(/html:/, '');
                response = response.replace(/{/, '');
                response = response.replace(/}/, '');
               // response = '{"' + response.trim() + '"}';
                //console.log("Fixed JSON:", response);
                // Vérifie si la valeur est vide et la remplace par [""] si nécessaire
                /*const match = response.match(/"html":\s*(.+)/);
                if (match && match[1].trim() === "") {
                    response = response.replace(/"html":\s*$/, '"html": [""]');
                }*/
            }

            console.log("Fixed JSON:", response);

            return response; // Retourne le JSON corrigé
        }
        throw new Error("Invalid AI response format");
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


function openAiImage(imageUrl) {
    // Ouvre l'image dans une nouvelle fenêtre ou un nouvel onglet
    window.open(imageUrl, '_blank').focus();
}
