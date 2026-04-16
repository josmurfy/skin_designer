// Original: warehouse/card/card_list.js
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

function debugAjaxSuccess(action, response) {
}

function debugAjaxError(action, error) {
}

// ============================================
// CARD LIST FUNCTIONALITY
// ============================================

/**
 * Transfer Location for Card
 * Fonction pour modifier l'emplacement d'une carte
 */
function transferLocation(cardId) {
    var user_token = document.querySelector('input[name="user_token"]').value;

    // Récupérer les valeurs actuelles
    var location = $('#location-' + cardId).text().trim();

    // Créer un champ texte pour modifier l'emplacement
    var html = '<input type="text" id="location_input' + cardId + '" class="form-control location_input" value="' + location + '" placeholder="Entrez l\'emplacement" />';
    $('#location-' + cardId).html(html);

    // Donner le focus au champ texte et sélectionner tout le texte
    $('#location_input' + cardId).focus().select();

    // Gérer l'événement 'keydown' sur le champ texte
    $('#location_input' + cardId).on('keydown', function(event) {
        if (event.key === 'Enter' || event.keyCode === 13 || event.which === 13) {
            updateCardLocation(cardId, $(this).val());
        } else if (event.key === 'Escape' || event.keyCode === 27 || event.which === 27) {
            // Annuler la modification
            $('#location-' + cardId).html(location);
        }
    });

    // Gérer la perte de focus
    $('#location_input' + cardId).on('blur', function() {
        updateCardLocation(cardId, $(this).val());
    });
}

/**
 * Update Card Location
 * Met à jour l'emplacement d'une carte via AJAX
 */
function updateCardLocation(cardId, newLocation) {
    var user_token = document.querySelector('input[name="user_token"]').value;

    $.ajax({
        url: 'index.php?route=warehouse/card/card.updateLocation&user_token=' + user_token,
        type: 'POST',
        data: {
            card_id: cardId,
            location: newLocation
        },
        dataType: 'json',
        success: function(json) {
            if (json.success) {
                $('#location-' + cardId).html(newLocation || '-');
                showSuccess('Emplacement mis à jour avec succès');
            } else {
                $('#location-' + cardId).html($('#location-' + cardId).data('original') || '-');
                showError(json.error || 'Erreur lors de la mise à jour');
            }
        },
        error: function(xhr, status, error) {
            $('#location-' + cardId).html($('#location-' + cardId).data('original') || '-');
            showError('Erreur AJAX: ' + error);
        }
    });
}

/**
 * Show Success Message
 */
function showSuccess(message) {
    // Simple notification - you can enhance this
    alert(message);
}

/**
 * Show Error Message
 */
function showError(message) {
    // Simple notification - you can enhance this
    alert(TEXT_ERROR_PREFIX + message);
}

// ============================================
// DOCUMENT READY
// ============================================
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Handle location clicks for inline editing
    $(document).on('click', '.location-editable', function() {
        var cardId = $(this).data('card-id');
        var currentLocation = $(this).text().trim();
        $(this).data('original', currentLocation);
        transferLocation(cardId);
    });

    // Handle form submission for filters
    $('#form-filter').on('submit', function(e) {
        e.preventDefault();
        $('#button-filter').trigger('click');
    });

    // ── Merge selected cards ──────────────────────────────────────────────────
    $('#button-merge-cards').on('click', function() {
        var selected = [];
        $('input[name="selected[]"]:checked').each(function() {
            selected.push($(this).val());
        });

        if (selected.length < 2) {
            alert(typeof TEXT_MERGE_MIN !== 'undefined' ? TEXT_MERGE_MIN : 'Sélectionnez au moins 2 cartes.');
            return;
        }

        var msg = (typeof TEXT_CONFIRM_MERGE !== 'undefined' ? TEXT_CONFIRM_MERGE : 'Fusionner les cartes sélectionnées?')
                + '\n\n(' + selected.length + ' cartes sélectionnées)';

        if (!confirm(msg)) {
            return;
        }

        showLoadingPopup('Fusion en cours...');

        var url = typeof URL_MERGE_CARDS !== 'undefined' ? URL_MERGE_CARDS
                : 'index.php?route=warehouse/card/card.mergeCards&user_token=' + (typeof USER_TOKEN !== 'undefined' ? USER_TOKEN : '');

        $.ajax({
            url: url,
            type: 'POST',
            data: { selected: selected },
            dataType: 'json',
            success: function(json) {
                if (json.error) {
                    appendLoadingMessage(json.error, 'error');
                    finishLoadingPopup('❌ Erreur');
                } else {
                    appendLoadingMessage(json.success, 'success');
                    finishLoadingPopup('✅ Fusion terminée');
                    setTimeout(function() {
                        hideLoadingPopup();
                        location.reload();
                    }, 1800);
                }
            },
            error: function(xhr) {
                appendLoadingMessage('Erreur AJAX (' + xhr.status + ')', 'error');
                finishLoadingPopup('❌ Échec');
            }
        });
    });
});