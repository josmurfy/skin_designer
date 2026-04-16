// Original: shopmanager/catalog/category_specific_list.js
function handleDelete(categoryId) {
    checkCategory(categoryId);
    if (confirm(confirmMessage)) {
        $('#form-category').submit();
    }
}

function handleEnable(categoryId) {
    if(categoryId){
    checkCategory(categoryId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-category');
        form.action = enableUrl;
        form.submit();
    }
}

function handleDisable(categoryId) {
    if(categoryId){
    checkCategory(categoryId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-category');
        form.action = disableUrl;
        form.submit();
    }
}

function handleCopy(categoryId) {
    if(categoryId){
    checkCategory(categoryId);
    }

    if (confirm(confirmMessage)) {
        var form = document.getElementById('form-category');
        form.action = copyUrl;
        form.submit();
    }
}

function checkCategory(categoryId) {
    // Sélectionner la case à cocher directement par son sélecteur d'attribut
    var checkbox = $('input[name="selected[' + categoryId + ']"]');
    checkbox.prop('checked', !checkbox.prop('checked'));
}




$(document).ready(function() {

    $('#button-filter').on('click', function() {
        var user_token = document.querySelector('input[name="user_token"]').value;
      //  alert(user_token);

        var url = 'index.php?route=shopmanager/catalog/category_specific&user_token=' + user_token;
    
        var filter_specific_name = $('input[name=\'filter_specific_name\']').val();
    
        if (filter_specific_name) {
            url += '&filter_specific_name=' + encodeURIComponent(filter_specific_name);
        }
    

    //    alert(filter_status);
      var limit = $('select[name=\'limit\']').val();
    
      if (limit != '*') {
        url += '&limit=' + encodeURIComponent(limit);
      }
    
        location = url;
    });
        // Ajouter un écouteur d'événement sur les champs de recherche
        $('#search-form input, #search-form select').on('change', function() {
            var currentInput = $(this);
            
            // Réinitialiser tous les champs sauf celui qui vient de changer et ceux à ne pas modifier
            $('#search-form input, #search-form select').each(function() {
                if ($(this).attr('name') !== currentInput.attr('name') 
                    && $(this).attr('id') !== 'input-status' 
                    && $(this).attr('id') !== 'input-ebay-listable' 
                    && $(this).attr('id') !== 'input-specifics'
                    && $(this).attr('id') !== 'input-limit') {
                    $(this).val('');
                }
            });
            
            // Cliquer sur le bouton de filtre
            $('#button-filter').click();
        });

      
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.querySelector('#input-limit');
            select.addEventListener('change', function () {
                const selectedValue = select.value;
                const newUrl = limitLink.replace('{page}', '1').replace('&limit=', '&limit=' + selectedValue);
                // Rediriger vers l'URL avec les paramètres appropriés
                window.location.href = newUrl;
            });
        });         

});
