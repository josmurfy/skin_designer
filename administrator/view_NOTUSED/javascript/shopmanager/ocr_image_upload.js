document.addEventListener('DOMContentLoaded', function() {
    const dropArea = document.getElementById('drop');
    const fileElem = document.getElementById('fileElem');
    const token = document.querySelector('input[name="token"]').value;

    // Gestion du clic sur la zone de dépôt
    if (dropArea) {
        dropArea.addEventListener('click', function() {
            fileElem.click();
        });

        // Gestion du dragover pour empêcher le comportement par défaut
        dropArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropArea.style.backgroundColor = '#f0f0f0';
        });

        // Réinitialiser le style quand on quitte la zone de dépôt
        dropArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropArea.style.backgroundColor = '';
        });

        // Gestion du dépôt du fichier
        dropArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropArea.style.backgroundColor = '';
            var files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files, token);
            }
        });
    }

    // Gestion de la sélection de fichier via l'input
    if (fileElem) {
        fileElem.addEventListener('change', function(e) {
            var files = this.files;
            if (files.length > 0) {
                handleFile(files, token);
            }
        });
    }
});



function updateCharacterCountOLD() {
    var maxLength = 80;
    var form = document.getElementById('form-product' + productId);
    var counterElement = document.getElementById('char-count');

    var inputElement = form.querySelector('input[name="product_name"]');
  //  console.log('inputElement:', inputElement);
 //   console.log('Nom de l\'élément:', inputElement.name); // Affiche le nom de l'élément dans la console

    // Récupérer la valeur de l'input
    var inputValue = inputElement.value;

    // Convertir les caractères spéciaux en entités HTML
   // var encodedValue = htmlspecialchars(inputValue);
    var encodedValue = inputValue;

    // Calculer la longueur de la chaîne encodée
    var currentLength = encodedValue.length;

    // Obtenir l'élément du compteur
   // var counterElement = document.getElementById(counterId);

    // Mettre à jour le texte du compteur
    counterElement.textContent = currentLength + '/' + maxLength;

    // Changer la couleur en fonction de la longueur actuelle
    if (currentLength > maxLength) {
        counterElement.style.color = 'red';
    } else {
        counterElement.style.color = 'green';
    }
}

// Fonction pour gérer le fichier et envoyer la requête avec le token
async function handleFile(files, token) {
    const formData = new FormData();
    formData.append('image', files[0]);

    // Soumettre la requête si un fichier est présent
    if (files.length > 0) {
        fetch('index.php?route=shopmanager/ocr.upload&token=' + token, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(async data => {
            if (data.success) {
                document.getElementById('recognizedText').value = data.text;
                await correctSpellingAndFormat('ocrForm');
                await suggestEntryName('ocrForm');
               
            } else {
                alert('Erreur : ' + data.error);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }
}
async function correctSpellingAndFormat(fieldName  = 'product') {
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

        var data = buildAiData(prompt, "Only return correction result", 100, 0.3);
        var aiResponse = await fetchAi(data); // Envoyer la requête à l'IA

        // Raccourcir le texte si la réponse dépasse 80 caractères
        
        // Sélectionner les éléments de résultat
        var aiResultElement = document.getElementById('recognizedText');
      
        
        // Afficher le résultat dans les éléments spécifiés
        if (aiResultElement) {
            aiResultElement.value = aiResponse;
       
        } else {
            console.error('AI result element not found');
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function suggestEntryNameOCR(fieldName = 'product', recognizedTextElement) {
   
    //alert(fieldName);
        var form = document.getElementById('form-' + fieldName);
        if (!form) {
            console.error('Form element not found');
            return;
        }
    
       

       // var prompt = `Generate a concise and accurate title for a product with the following details: ${recognizedTextElement.value}.`;
    //    var prompt = `Generate a product title with a length between 70 and 80 characters for the following details: ${recognizedTextElement.value}. Ensure the title does not exceed 80 characters.`;
        if ($('#category_id').val() === '617') {
            prompt = `Based on "${cleanedText}". Create an title using this format: movie title (DVD or Blu-ray, years of the movie, widescreen or fullscreen), other good info, actors or producer, production type, disc set.`;
        } else if ($('#category_id').val() === '139973') {
            prompt = `Based on "${cleanedText}". Create an title using this format: video game title (platforms like PS4, Xbox, PS3, Nintendo, etc.), other good info.`;
        } else if ($('#category_id').val() === '261186') {
            prompt = `Based on "${cleanedText}". Create an title using this format: book title (author, publisher, year, nb pages), other good info.`;
        }  else {
            prompt = `Create a 80 characters product title Based on "${cleanedText}". `;
        }
    
       /* var system_prompt = $('#category_id').val() == 617
            ? "The title should be a minLength=70 maxLength=80. Provide titles use this format: movie title (dvd or bluray, years of the movie, widescreen or full screen) actors or productor, production keep the number of disc set and if it's a Canadian version."
            : "The title should be a minLength=70 maxLength=80. Provide concise and accurate product titles. ";*/
            var system_prompt = $('#category_id').val() == 617
            ? "Return the value only"
            : "Return the value only";
        
            var data = buildAiData(prompt, system_prompt, 100, 0.3);
             console.log('prompt:' + prompt);

    
        try {
            var aiResponse = await fetchAi(data);
            var aiResultElement = document.getElementById('input-name');
            var aiResultElementCount = document.getElementById('ai-result-name-count');
            if (aiResponse.length > 80) {
                var prompt = `Shorten this "${aiResponse}" to be between 70 and 80 characters while keeping the format title: `;
                data = buildAiData(prompt, system_prompt, 100, 0.3);
                aiResponse = await fetchAi(data);
               
                // Vérifiez de nouveau si la longueur est correcte
                if (aiResponse.length > 80) {
                    aiResponse = aiResponse.substring(0, 80);
                }
            }
            console.log('aiResponse:' + aiResponse);
            if (aiResultElement) {
                aiResultElement.value = aiResponse;
                aiResultElementCount.textContent = ' (' + aiResponse.length + ') ';
                aiResultElement.style.display = 'inline';
                aiResultElementCount.style.display = 'inline';
                aiSuggestDescriptionSupp('ocrForm');
            } else {
                console.error('AI result element not found');
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
    async function aiSuggestDescriptionSupp(fieldName = 'product') {
        var Button = $('#ai-suggest-description-supp-btn1');
        Button.prop('disabled', true).text('Generating...');
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
        var cleanedText = recognizedTextElement.value.replace(/[\r\n]+/g, ' ');
        var productName = document.getElementById('input-name').value;

        if (!productName) {
            console.error('Product name not found');
            return;
        }
        
    
        var prompt = getAiPromptForDescription(cleanedText, productName);
        console.log('prompt:' + prompt);
        var data = buildAiData(prompt, "Provide a general product description.", 500, 0.7);
    
        try {
    
            
            const aiResponse = await fetchAi(data);
            console.log('aiResponse:' + aiResponse);
            var textareaId = `input-description-supp`;
            var aiResultElement = document.getElementById(textareaId);
           
            if (aiResultElement) {
                const formattedText = await getFormattedText(aiResponse);
                var aiFormatResponse = formatAiResponse(formattedText);
                var decodedText = htmlDecode(aiFormatResponse);
                $(`#${textareaId}`).summernote('code', decodedText);

                const Button = $('#ai-suggest-description-supp-btn');
                Button.prop('disabled', false).text('Generated');
                Button.removeClass('btn-primary').addClass('btn-success');
              
                // Ajouter un délai de 3 secondes (3000 millisecondes)
                setTimeout(function() {
                    // Changer à nouveau le texte après 3 secondes
                    Button.removeClass("btn btn-success").addClass("btn btn-primary");
                    Button.prop('disabled', false).html('<i class="fa-solid fa-robot"></i> <i class="fa-solid fa-question"></i>');
                  
            
                }, 3000); // 3000 millisecondes = 3 secondes 
            } else {
                console.error('AI result element not found');
            }
        } catch (error) {
            console.error('Error:', error);
        }
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

async function getFormattedText(description) {
    var prompt = `Format the following text with HTML tags for bold, italics, and paragraphs where appropriate: "${description}"`;
    var data = buildAiData(prompt, "Provide HTML formatted text.", 500, 0.7);

    return await fetchAi(data);
}

function buildAiData(prompt, systemPrompt, maxTokens, temperature) {
    return {
        prompt: prompt,
        system_prompt: systemPrompt,
        max_tokens: maxTokens,
        temperature: temperature
    };
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

    if (json.success) {
        return json.message;
    } else {
        throw new Error(json.message || 'Unknown error');
    }
}


