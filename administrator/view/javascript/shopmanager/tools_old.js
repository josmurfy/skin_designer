// ============================================
// ⚠️ TOOLS.JS - DEPRECATED / NOT USED ANYMORE
// ============================================
// Date: 2026-01-06
// Reason: PRODUCTION SAFETY - Décentralisation des fonctions
// 
// TOUTES les fonctions de ce fichier ont été dupliquées
// dans les fichiers .js qui en ont besoin:
// - product_form.js
// - product_list.js  
// - product_search_info.js
//
// Ce fichier est conservé pour référence mais N'EST PLUS CHARGÉ
// par aucun template .twig
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

 // Helper function to decode HTML entities
  function decodeHTMLEntities(text) {
      var textArea = document.createElement('textarea');
      textArea.innerHTML = text;
      return textArea.value;
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

function htmlspecialchars_decode(str) {
    if (str === null || str === undefined) {
        return '';
    }
    str = String(str);
    
    return str.replace(/&amp;/g, '&')
              .replace(/&lt;/g, '<')
              .replace(/&gt;/g, '>')
              .replace(/&quot;/g, '"')
              .replace(/&#039;/g, "'");
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
    function addslashes(str) {
        if (str === null || str === undefined) {
            return '';
        }
        str = String(str);
        
        return str.replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
    }
  function addslashesNOT_USED(str) {
      return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
  }

  function doubleval(val) {
      return parseFloat(val) || 0;
  }

// ============================================
// AUDIO FEEDBACK FUNCTIONS
// ============================================
function playErrorSound() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const oscillator2 = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    oscillator2.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 800;
    oscillator2.frequency.value = 400;
    oscillator.type = 'square';
    oscillator2.type = 'square';
    
    gainNode.gain.setValueAtTime(0.5, audioContext.currentTime);
    gainNode.gain.setValueAtTime(0, audioContext.currentTime + 0.1);
    gainNode.gain.setValueAtTime(0.5, audioContext.currentTime + 0.15);
    gainNode.gain.setValueAtTime(0, audioContext.currentTime + 0.25);
    gainNode.gain.setValueAtTime(0.5, audioContext.currentTime + 0.3);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.5);
    oscillator2.start(audioContext.currentTime);
    oscillator2.stop(audioContext.currentTime + 0.5);
}

function playWarningSound() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 700;
    oscillator.type = 'triangle';
    
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
    gainNode.gain.setValueAtTime(0, audioContext.currentTime + 0.15);
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime + 0.3);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.7);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.7);
}

function playSuccessSound() {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const oscillator2 = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    oscillator2.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 600;
    oscillator2.frequency.value = 800;
    oscillator.type = 'sine';
    oscillator2.type = 'sine';
    
    gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.5);
    oscillator2.start(audioContext.currentTime + 0.1);
    oscillator2.stop(audioContext.currentTime + 0.5);
}

// ============================================
// RELATIVE TIME FORMATTING
// ============================================
function formatRelativeTime(timestamp) {
    if (!timestamp) return '-';
    
    const now = new Date();
    const date = new Date(timestamp);
    const diffMs = now - date;
    const diffSec = Math.floor(diffMs / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffHour = Math.floor(diffMin / 60);
    const diffDay = Math.floor(diffHour / 24);
    
    if (diffSec < 60) {
        return 'Just now';
    } else if (diffMin < 60) {
        return diffMin + ' min' + (diffMin > 1 ? 's' : '') + ' ago';
    } else if (diffHour < 24) {
        return diffHour + ' hour' + (diffHour > 1 ? 's' : '') + ' ago';
    } else if (diffDay < 7) {
        return diffDay + ' day' + (diffDay > 1 ? 's' : '') + ' ago';
    } else if (diffDay < 30) {
        const weeks = Math.floor(diffDay / 7);
        return weeks + ' week' + (weeks > 1 ? 's' : '') + ' ago';
    } else if (diffDay < 365) {
        const months = Math.floor(diffDay / 30);
        return months + ' month' + (months > 1 ? 's' : '') + ' ago';
    } else {
        const years = Math.floor(diffDay / 365);
        return years + ' year' + (years > 1 ? 's' : '') + ' ago';
    }
}

function updateRelativeTimes() {
    document.querySelectorAll('.relative-time').forEach(function(elem) {
        const timestamp = elem.dataset.timestamp;
        if (timestamp) {
            elem.textContent = formatRelativeTime(timestamp);
        }
    });
}

// ============================================
// AI COUNTRY DETECTION - AUTO ACCEPT SESSION
// ============================================
var autoAcceptAICountry = false;

function decodeHtmlEntities(text) {
    var textarea = document.createElement('textarea');
    textarea.innerHTML = text;
    return textarea.value;
}
function ucwordsNOT_USED(str) {
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
    var loadingTitle = document.getElementById("loading-title");
    var loadingMessages = document.getElementById("loading-messages");
    var loadingPopup = document.getElementById("loading-popup");
    var closeLoadingBtn = document.getElementById("close-loading-btn");
    
    // Vérifier que tous les éléments existent
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
    
    // Check if container exists before trying to use it
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

function removeDuplicateKeys(text) {
    // Remplacer les patterns "Key: Key, Value" par "Key: Value"
    // Utilise une regex avec backreference pour détecter la répétition
    return text.replace(/(\w+(?:\s+\w+)*?):\s*\1,\s*/g, '$1: ');
}


function htmlDecode(input) {
    let doc = new DOMParser().parseFromString(input, "text/html");
    return doc.documentElement.textContent;
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
    
    // Set data-resolution attribute for tools.js overlay display
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
    document.querySelectorAll('.actual-image-container').forEach(function(container) {
        const thumbnail = container.querySelector('.img-thumbnail, .thumbnail-actual-image');
        if (thumbnail) {
            checkImageResolution(thumbnail);
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

/**
 * Centralized Image Drag & Drop Upload Functionality
 * Handles drag & drop file upload for .actual-image-container elements
 * Used in product_list, product_search, maintenance_image_list, etc.
 * 
 * @param {string} uploadUrl - Base URL for upload (without user_token)
 * @param {function} onSuccess - Callback function on successful upload (receives response)
 * @param {function} onError - Callback function on error (receives xhr)
 */
function initImageDragAndDrop(uploadUrl, onSuccess, onError) {
    document.querySelectorAll('.actual-image-container').forEach(function (container) {
        // Skip if already initialized
        if (container.dataset.dragDropInitialized === 'true') {
            return;
        }
        container.dataset.dragDropInitialized = 'true';

        const fileInput = container.querySelector('input[type="file"]');
        const previewImage = container.querySelector('img.img-thumbnail, .thumbnail-actual-image');
        const fullImage = container.querySelector('.fullsize-actual-image, .actual-image-preview');
        const productId = container.id.replace('drop-', '');
        
        // Get user_token from page
        const tokenInput = document.querySelector('input[name="user_token"]');
        if (!tokenInput) {
            console.error('[initImageDragAndDrop] user_token not found');
            return;
        }
        const user_token = tokenInput.value;

        // Dragover event
        container.addEventListener('dragover', function (event) {
            event.preventDefault();
            event.stopPropagation();
            container.style.borderColor = '#007bff';
        });

        // Dragleave event
        container.addEventListener('dragleave', function () {
            container.style.borderColor = '#ccc';
        });

        // Drop event
        container.addEventListener('drop', function (event) {
            event.preventDefault();
            event.stopPropagation();
            container.style.borderColor = '#ccc';

            if (event.dataTransfer.files.length > 0) {
                const file = event.dataTransfer.files[0];

                // Update file input
                if (fileInput) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;
                }

                // Update preview image
                const reader = new FileReader();
                reader.onload = function (e) {
                    if (previewImage) previewImage.src = e.target.result;
                    if (fullImage) fullImage.src = e.target.result;
                };
                reader.readAsDataURL(file);

                // Upload via AJAX
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
                            alert((window.lang && lang.error_occurred ? lang.error_occurred : 'Error') + ' : ' + response.error);
                        }
                    },
                    error: function (xhr) {
                        console.error('[AJAX Error]', xhr.responseText);
                        if (onError) {
                            onError(xhr, container, productId);
                        } else {
                            alert((window.lang && lang.error_upload ? lang.error_upload : 'Upload error') + ' : ' + xhr.responseText);
                        }
                    }
                });
            }
        });

        // Click to open file picker (but don't interfere with image hover)
        if (fileInput) {
            container.addEventListener('click', function(e) {
                // Only open file picker if clicking directly on container or icon, not on image
                if (e.target === container || e.target.tagName === 'I' || e.target.classList.contains('fa-camera')) {
                    fileInput.click();
                }
            });

            // Manual file selection
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

    // Prevent browser from opening dropped images
    window.addEventListener('dragover', function(e) {
        e.preventDefault();
    }, false);
    
    window.addEventListener('drop', function(e) {
        if (!e.target.closest('.actual-image-container')) {
            e.preventDefault();
        }
    }, false);
}

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        initImagePreview();
        initImageResolutionCheck();
    });
} else {
    initImagePreview();
    initImageResolutionCheck();
}