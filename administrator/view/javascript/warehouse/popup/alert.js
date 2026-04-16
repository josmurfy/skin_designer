// Original: warehouse/popup/alert.js
function showAlertPopup(message) {
    var popupSelector = null;

    if (document.getElementById('alert-popup')) {
        popupSelector = '#alert-popup';
    } else if (document.getElementById('alert-error')) {
        popupSelector = '#alert-error';
    }

    if (!popupSelector) {
        alert(message);
        return;
    }

    $(popupSelector + ' .modal-body').text(message);
    showModal(popupSelector);
}

$(document).on('click', '#alert-popup .btn-close, #alert-popup .btn-secondary, #alert-error .btn-close, #alert-error .btn-secondary', function() {
    hideModal('#alert-popup');
});