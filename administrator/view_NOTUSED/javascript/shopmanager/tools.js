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
function fixAccents(text) {
    const accentsMap = {
        'À': 'À', 'Â': 'Â', 'Ä': 'Ä', 'Æ': 'Æ', 'Ç': 'Ç', 'È': 'È', 'É': 'É', 'Ê': 'Ê', 'Ë': 'Ë',
        'Î': 'Î', 'Ï': 'Ï', 'Ô': 'Ô', 'Œ': 'Œ', 'Ù': 'Ù', 'Û': 'Û', 'Ü': 'Ü', 'Ÿ': 'Ÿ',
        'á': 'a', 'à': 'a', 'â': 'a', 'ä': 'a', 'ã': 'a', 'å': 'a',
        'é': 'e', 'è': 'e', 'ê': 'e', 'ë': 'e',
        'í': 'i', 'ì': 'i', 'î': 'i', 'ï': 'i',
        'ó': 'o', 'ò': 'o', 'ô': 'o', 'ö': 'o', 'õ': 'o',
        'ú': 'u', 'ù': 'u', 'û': 'u', 'ü': 'u',
        'ý': 'y', 'ÿ': 'y',
        'ñ': 'n'
    };

    return text.replace(/[ÀÂÄÆÇÈÉÊËÎÏÔŒÙÛÜŸáàâäãåéèêëíìîïóòôöõúùûüýÿñ]/g, match => accentsMap[match] || match);
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

function decodeHtmlEntities(text) {
    var textarea = document.createElement('textarea');
    textarea.innerHTML = text;
    return textarea.value;
}
function ucwords(str) {
    return str.replace(/\b\w/g, char => char.toUpperCase());
}
function transformDataToFormattedStringOLD(data) {
    let result = '';
    
    Object.keys(data).forEach(key => {
        const value = data[key];

        // Si la valeur est un tableau, la convertir en chaîne
        if (Array.isArray(value)) {
            result += `${formatKey(key)}: ${value.join(', ')}\n`;
        } 
        // Si la valeur est un objet, parcourir récursivement
        else if (typeof value === 'object' && value !== null) {
            result += transformDataToFormattedString(value);
        } 
        // Si c'est une chaîne ou un nombre
        else if (value !== null && value !== undefined && value !== '') {
            result += `${formatKey(key)}: ${value}\n`;
        }
    });

    return result.trim().replace(/\n/g, ', '); // Remplace les retours à la ligne par des virgules
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

function cleanHTMLOLD(rawHTML) {
    // Créez un parser DOM virtuel
    const parser = new DOMParser();
    const doc = parser.parseFromString(rawHTML, 'text/html');

    // Supprimez les balises <script>, <meta>, et <link>
    doc.querySelectorAll('script, meta, link').forEach(el => el.remove());

    // Supprimez tout autre contenu inutile selon vos besoins
    const marker = doc.querySelector('#ttp-marker'); // Exemple : Supprimer un élément par ID
    if (marker) {
        marker.remove();
    }

    // Retournez le contenu nettoyé
    return doc.documentElement.outerHTML;
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

function showLoadingPopup(title = "Chargement en cours...") {
    document.getElementById("loading-title").textContent = title;
    document.getElementById("loading-messages").innerHTML = '';
    document.getElementById("loading-popup").style.display = 'block';
    document.getElementById("close-loading-btn").style.display = 'none';
}

function appendLoadingMessage(message, type = 'info') {
    const container = document.getElementById("loading-messages");
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
    document.getElementById('close-loading-btn').style.display = 'inline-block';
}

function hideLoadingPopup() {
    document.getElementById("loading-popup").style.display = 'none';
}

