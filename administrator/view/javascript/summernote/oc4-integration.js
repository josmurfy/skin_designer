// Configuration optimisée Summernote v0.8.20 pour OpenCart 4 - Bootstrap 5
// À intégrer dans vos propres fichiers JavaScript si nécessaire

var SummernoteConfig = {
    // Configuration de base
    lang: 'fr-FR',                    // Langue (fr-FR, de-DE, es-ES, ja-JP, pt-BR, zh-CN, etc.)
    height: 300,
    minHeight: 200,
    maxHeight: 400,
    focus: false,
    emptyPara: '',
    
    // Options de formatage
    disableDragAndDrop: true,         // Désactiver le drag-drop des images
    dialogsInBody: true,              // Important pour Bootstrap 5
    
    // Configuration des popovers (menus contextuels)
    popover: {
        image: [
            ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
            ['float', ['floatLeft', 'floatRight', 'floatNone']],
            ['remove', ['removeMedia']]
        ],
        link: [
            ['link', ['linkDialogShow', 'unlink']]
        ],
        air: [
            ['color', ['color']],
            ['font', ['bold', 'underline', 'clear']],
            ['para', ['ul', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']]
        ]
    },
    
    // Configuration de la barre d'outils
    toolbar: [
        ['style', ['style', 'strikethrough', 'clear']],
        ['font', ['bold', 'underline', 'clear']],
        ['fontname', ['fontname']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table', 'tableDelTable']],
        ['insert', ['link', 'picture', 'video', 'hr']],
        ['view', ['fullscreen', 'codeview', 'help']],
        ['history', ['undo', 'redo']]
    ]
};

// Fonction d'initialisation réutilisable
function initializeSummernote(selector) {
    $(selector).summernote(SummernoteConfig);
}

// Exemple d'utilisation dans un formulaire en onglets
function initializeAllSummernotes() {
    $('.summernote').each(function() {
        $(this).summernote(SummernoteConfig);
    });
}

// Fonction pour récupérer le contenu
function getSummernoteContent(selector) {
    return $(selector).summernote('code');
}

// Fonction pour définir le contenu
function setSummernoteContent(selector, content) {
    $(selector).summernote('code', content);
}

// Fonction pour détruire un éditeur
function destroySummernote(selector) {
    if ($(selector).hasClass('note-editor')) {
        $(selector).summernote('destroy');
    }
}

// Exemple d'utilisation:
/*
$(document).ready(function() {
    // Initialiser tous les éditeurs Summernote
    initializeAllSummernotes();
    
    // Ou initialiser un éditeur spécifique
    // initializeSummernote('#my-textarea');
    
    // Récupérer le contenu lors de la soumission du formulaire
    $('#myForm').on('submit', function(e) {
        var content = getSummernoteContent('#my-textarea');
        console.log('Contenu de l\'éditeur:', content);
    });
});
*/
