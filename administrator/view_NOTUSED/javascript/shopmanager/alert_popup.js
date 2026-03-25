function showAlertPopup(message) {
    $('#alert-popup .modal-body').text(message);
    $('#alert-popup').modal('show');
}