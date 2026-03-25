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

        var url = 'index.php?route=shopmanager/catalog/category&user_token=' + user_token;
    
        var filter_category_id = $('input[name=\'filter_category_id\']').val();
    
        if (filter_category_id) {
            url += '&filter_category_id=' + encodeURIComponent(filter_category_id);
        }
        var filter_image = $('select[name=\'filter_image\']').val();
    
        if (filter_image != '*') {
            url += '&filter_image=' + encodeURIComponent(filter_image);
        }
          
        var filter_name = $('input[name=\'filter_name\']').val();
    
        if (filter_name) {
            url += '&filter_name=' + encodeURIComponent(filter_name);
        }
    
        var filter_leaf = $('select[name=\'filter_leaf\']').val();
    //alert(filter_leaf);
        if (filter_leaf != '*') {
            url += '&filter_leaf=' + encodeURIComponent(filter_leaf);
        }
        var filter_specifics = $('select[name=\'filter_specifics\']').val();
        //alert(filter_leaf);
            if (filter_specifics) {
                url += '&filter_specifics=' + encodeURIComponent(filter_specifics);
            }
      
    
        var filter_status = $('select[name=\'filter_status\']').val();
    
        if (filter_status != '*') {
            url += '&filter_status=' + encodeURIComponent(filter_status);
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
                    && $(this).attr('id') !== 'input-image' 
                    && $(this).attr('id') !== 'input-specifics'
                    && $(this).attr('id') !== 'input-leaf') {
                    $(this).val('');
                }
            });
            
            // Cliquer sur le bouton de filtre
            $('#button-filter').click();
        });

      
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.querySelector('#input-leaf');
            select.addEventListener('change', function () {
                const selectedValue = select.value;
                const newUrl = limitLink.replace('{page}', '1').replace('&limit=', '&limit=' + selectedValue);
                // Rediriger vers l'URL avec les paramètres appropriés
                window.location.href = newUrl;
            });
        });         

});
