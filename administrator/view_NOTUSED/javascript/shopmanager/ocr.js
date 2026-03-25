document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('toggle-button');
    const ocrDiv = document.getElementById('ocr');
    const icons = toggleButton.querySelectorAll('.fa');

    toggleButton.addEventListener('click', function () {
        // Vérifie si le div est actuellement visible ou masqué
        if (ocrDiv.style.maxHeight && ocrDiv.style.maxHeight !== '0px') {
            // Réduit le div
            ocrDiv.style.maxHeight = '0px';
            ocrDiv.style.display = 'none';
            ocrDiv.style.overflow = 'hidden';
            icons.forEach(icon => {
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            });
        } else {
            // Agrandit le div
           
            ocrDiv.style.display = 'block';
            ocrDiv.style.overflow = 'visible';
            icons.forEach(icon => {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            });
            ocrDiv.style.maxHeight = `${ocrDiv.scrollHeight}px`;
        }
    });
});





document.addEventListener('DOMContentLoaded', function () {
    const dropArea = document.getElementById('drop-area');
    const fileInputDrop = document.getElementById('file-input-drop');
    var canvas = document.getElementById('imageCanvas');
    var ctx = canvas.getContext('2d');
 //   const recognizedText = document.getElementById('recognizedText');
   
// const image = new Image();
 let image = null; // Variable globale pour stocker l'image
 let isSelecting = false;
 let startX, startY, endX, endY;

    // Drag-and-drop handlers
    dropArea.addEventListener('click', () => fileInputDrop.click());

    dropArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropArea.style.backgroundColor = '#f0f0f0';
    });

    dropArea.addEventListener('dragleave', () => (dropArea.style.backgroundColor = ''));


    dropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        dropArea.style.backgroundColor = '';
        const files = e.dataTransfer.files;
    
        // Log des fichiers
   //     console.log('Liste des fichiers :');
        for (let i = 0; i < files.length; i++) {
     //       console.log(`Fichier ${i + 1} :`);
      //      console.log(`- Nom : ${files[i].name}`);
      //      console.log(`- Taille : ${files[i].size} octets`);
      //      console.log(`- Type MIME : ${files[i].type}`);
      //      console.log(`- Dernière modification : ${files[i].lastModifiedDate}`);
        }
    
        if (files.length > 0) {
            const file = files[0];
            if (file) {
           //     console.log('Chargement du fichier :', file.name);
                loadAndDrawImage(file); // Charger et afficher l'image
                handleFile(file);       // Passer le fichier directement
            }
        }
    });
    
  /*  fileInputDrop.addEventListener('change', (e) => {
        const file = e.target.files[0];
        var files = e.dataTransfer.files;
        console.log('change_files : ' + JSON.stringify(files));
        if (files.length > 0) {
            handleFile(files);
        }
        if (file) loadAndDrawImage(file);
       
    });*/


 
    

    function loadAndDrawImage(file) {
        image = new Image(); 
        const ocrDiv = document.getElementById('ocr_image');
        const canvas = document.getElementById('imageCanvas');
        const recognizedText = document.getElementById('recognizedText');
        const ctx = canvas.getContext('2d');
      
    
        // Rendre le div visible
        ocrDiv.style.display = 'block';
        ocrDiv.style.overflow = 'visible';
    
        // Lecture du fichier et chargement de l'image
        const reader = new FileReader();
        reader.onload = (e) => {
            image.onload = () => {
                // Limiter la taille de l'image pour qu'elle ne prenne pas trop d'espace
                const maxWidth = 600; // Largeur maximale
                const maxHeight = 600; // Hauteur maximale
                let width = image.width;
                let height = image.height;
    
                // Ajuster les dimensions pour respecter les limites tout en conservant le ratio
                if (width > maxWidth) {
                    height = (height * maxWidth) / width;
                    width = maxWidth;
                }
                if (height > maxHeight) {
                    width = (width * maxHeight) / height;
                    height = maxHeight;
                }
    
                // Ajuster le canvas aux dimensions de l'image redimensionnée
                canvas.style.display = 'block';
                canvas.width = width;
                canvas.height = height;
           //     console.log(`image_onload:  width=${width}, height=${height}`);
                // Dessiner l'image redimensionnée sur le canvas
                ctx.drawImage(image, 0, 0, width, height);
    
                // Mettre à jour le champ texte pour indiquer le statut
                recognizedText.value = 'Image loaded and resized. Ready for processing.';
            };
            image.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
    
// Gestion des événements de sélection
  canvas = document.getElementById('imageCanvas');
 ctx = canvas.getContext('2d');

// Commencer la sélection
canvas.addEventListener('mousedown', (e) => {
    if (!image) {
        console.error('No image loaded!');
        return;
    }

    isSelecting = true;

     // Réinitialiser l'affichage
     //ctx.clearRect(0, 0, canvas.width, canvas.height);
     //ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
 
     // Obtenir les coordonnées de départ
     const rect = canvas.getBoundingClientRect();

     startX = Math.min(Math.max(e.clientX - rect.left, 0), canvas.width);
     startY = Math.min(Math.max(e.clientY - rect.top, 0), canvas.height);

    // Réinitialiser les variables de fin pour une nouvelle sélection
    endX = startX;
    endY = startY;

  //  console.log(`mousedown: canvas.width=${canvas.width}, canvas.height=${canvas.height}`);
  //  console.log(`mousedown: rect.left=${rect.left}, rect.top=${rect.top}`);
  //  console.log('mousedown sélection');
});


let lastValidEndX = 0; // Dernière valeur valide pour endX
let lastValidEndY = 0; // Dernière valeur valide pour endY

canvas.addEventListener('mousemove', (e) => {
    if (!isSelecting) return;

    const rect = canvas.getBoundingClientRect();

    // Calculer les coordonnées de la souris par rapport au canvas
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    // Limiter les coordonnées pour rester dans les limites du canvas
    endX = Math.min(Math.max(mouseX, 0), canvas.width);
    endY = Math.min(Math.max(mouseY, 0), canvas.height);
    if (endX > 0 && endX < canvas.width && endY > 0 && endY < canvas.height) {
        lastValidEndX = endX;
        lastValidEndY = endY;
    }
    // Débogage des coordonnées
 //   console.log(`mousemove: mouseX=${mouseX}, mouseY=${mouseY}`);
 //   console.log(`mousemove: endX=${endX}, endY=${endY}`);
  //  console.log(`mousemove: canvas.width=${canvas.width}, canvas.height=${canvas.height}`);
  if (endX === 0 || endX === canvas.width || endY === 0 || endY === canvas.height) {
   // console.log(`mousemove: endX=${endX}, endY=${endY}, lastValidEndX=${lastValidEndX}, lastValidEndY=${lastValidEndY}`);
    // Utiliser les dernières coordonnées valides pour la sélection
    endX = lastValidEndX;
    endY = lastValidEndY;
    canvas.dispatchEvent(new MouseEvent('mouseup'));
    return;
}
    // Nettoyer le canvas
  //  ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Redessiner l'image de fond
    if (image && image.complete) {
        
        ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
    } else {
        console.error('Image is not loaded or available');
        return; // Si l'image n'est pas disponible, quittez l'événement
    }

    // Dessiner le rectangle de sélection
    ctx.strokeStyle = 'red';
    ctx.lineWidth = 2;
    ctx.strokeRect(startX, startY, endX - startX, endY - startY);
});



// Terminer la sélection// Terminer la sélection

canvas.addEventListener('mouseup', () => {
    if (!isSelecting) return;
    isSelecting = false;

    const rectStartX = Math.min(startX, endX); // Le coin supérieur gauche
    const rectStartY = Math.min(startY, endY);
    const width = Math.abs(endX - startX); // Largeur positive
    const height = Math.abs(endY - startY); // Hauteur positive
   // Vérifier si la sélection est valide
   if (width > 0 && height > 0) {
 //   console.log(`Selection: rectStartX=${rectStartX}, rectStartY=${rectStartY}, width=${width}, height=${height}`);
    const ocrDiv = document.getElementById('ocr_image_cropped');
    ocrDiv.style.display = 'block';
    ocrDiv.style.overflow = 'visible';
    analyzeSelectedArea(rectStartX, rectStartY, width, height);
    } else {
        console.error('Invalid selection');
    }
});
function analyzeSelectedArea(rectStartX, rectStartY, width, height) {
    const croppedCanvas = document.createElement('canvas');
    const croppedCtx = croppedCanvas.getContext('2d');
    croppedCanvas.width = width;
    croppedCanvas.height = height;

    // Dessiner la zone sélectionnée sur un canvas temporaire
    croppedCtx.drawImage(canvas, rectStartX, rectStartY, width, height, 0, 0, width, height);

    // Convertir la zone sélectionnée en Base64
    const imageData = croppedCanvas.toDataURL('image/png');

    // Afficher l'image découpée dans un élément <img> pour prévisualisation
    const croppedImagePreview = document.getElementById('croppedImagePreview');
    if (croppedImagePreview) {
        croppedImagePreview.src = imageData;
        croppedImagePreview.style.display = 'block';
    } else {
        console.warn('croppedImagePreview element not found');
    }

    // Convertir Base64 en Blob pour upload
    const imageBlob = dataURLToBlob(imageData);
    // Log détaillé de l'objet Blob
   // console.log('Image Blob :');
 //   console.log(`- Taille : ${imageBlob.size} octets`);
  //  console.log(`- Type MIME : ${imageBlob.type}`);
  //  console.log('- Blob en JSON :', JSON.stringify(imageBlob));
    // Appeler la fonction pour envoyer le fichier
    handleFile([imageBlob]);
}

function dataURLToBlob(dataURL) {
    const parts = dataURL.split(';base64,');
    const byteString = atob(parts[1]);
    const mimeString = parts[0].split(':')[1];
    const arrayBuffer = new ArrayBuffer(byteString.length);
    const uintArray = new Uint8Array(arrayBuffer);

    for (let i = 0; i < byteString.length; i++) {
        uintArray[i] = byteString.charCodeAt(i);
    }

    return new Blob([arrayBuffer], { type: mimeString });
}

async function handleFile(fileOrDataURL) {
    $('#loadingModal').modal('show');

    const token = document.querySelector('input[name="token"]').value;
    const formData = new FormData();

  //  console.log('Données reçues dans handleFile :', fileOrDataURL);

    // Vérifier et traiter le paramètre fileOrDataURL
    if (fileOrDataURL instanceof File || fileOrDataURL instanceof Blob) {
     //   console.log('Type de donnée : File ou Blob');
    //    console.log(`- Nom du fichier : ${fileOrDataURL.name || 'Nom non disponible'}`);
     //   console.log(`- Taille : ${fileOrDataURL.size} octets`);
     //   console.log(`- Type MIME : ${fileOrDataURL.type}`);

        // Convertir en Blob si nécessaire pour garantir une compatibilité uniforme
        const blob = new Blob([fileOrDataURL], { type: fileOrDataURL.type || 'application/octet-stream' });
   //     console.log('Blob généré à partir du fichier ou Blob d\'origine :');
    //    console.log(`- Taille : ${blob.size} octets`);
    //    console.log(`- Type MIME : ${blob.type}`);
        formData.append('image', blob, fileOrDataURL.name || 'blob'); // Ajouter le Blob généré
    } else if (fileOrDataURL instanceof FileList || Array.isArray(fileOrDataURL)) {
   //     console.log('Type de donnée : FileList ou Array');
        if (fileOrDataURL.length > 0) {
            const file = fileOrDataURL[0];
   //         console.log(`- Nom du fichier : ${file.name || 'Nom non disponible'}`);
     //       console.log(`- Taille : ${file.size} octets`);
     //       console.log(`- Type MIME : ${file.type}`);

            // Convertir en Blob avant de l'ajouter
            const blob = new Blob([file], { type: file.type || 'application/octet-stream' });
      //      console.log('Blob généré à partir du fichier :');
        //    console.log(`- Taille : ${blob.size} octets`);
       //     console.log(`- Type MIME : ${blob.type}`);
            formData.append('image', blob, file.name || 'blob');
        } else {
            console.error('Le FileList ou Array est vide.');
            return;
        }
    } else if (typeof fileOrDataURL === 'string' && fileOrDataURL.startsWith('data:image')) {
    //    console.log('Type de donnée : Base64');
    //    console.log(`- Longueur de la chaîne Base64 : ${fileOrDataURL.length}`);
        const blob = dataURLToBlob(fileOrDataURL);
    //    console.log('Blob généré à partir de la chaîne Base64 :');
     //   console.log(`- Taille : ${blob.size} octets`);
     //   console.log(`- Type MIME : ${blob.type}`);
        formData.append('image', blob, 'cropped-image.png'); // Ajouter le Blob généré
    } else {
     //   console.log('Type de donnée non supporté :');
     //   console.log(`- Nom du fichier : ${fileOrDataURL.name || 'Nom non disponible'}`);
     //   console.log(`- Taille : ${fileOrDataURL.size || 'Taille non disponible'} octets`);
      //  console.log(`- Type MIME : ${fileOrDataURL.type || 'Type non disponible'}`);
        console.error('Type de donnée non supporté. Reçu :', fileOrDataURL);
        return;
    }
    // Log du contenu de formData
//console.log('Contenu de formData avant l\'envoi :');
for (let [key, value] of formData.entries()) {
    if (value instanceof Blob) {
     //   console.log(`- ${key}: [Blob]`);
     //   console.log(`  - Nom : ${value.name || 'Nom non disponible'}`);
     //   console.log(`  - Taille : ${value.size} octets`);
     //   console.log(`  - Type MIME : ${value.type}`);
    } else {
    //    console.log(`- ${key}: ${value}`);
    }
}
    // Envoyer la requête
    try {
        const response = await fetch(`index.php?route=shopmanager/ocr.upload&token=${token}`, {
            method: 'POST',
            body: formData,
        });

        const text = await response.text();
      //  console.log('Réponse brute :', text);

        // Extraire uniquement la partie JSON si du HTML parasite est inclus
        const jsonStartIndex = text.indexOf('{');
        const jsonEndIndex = text.lastIndexOf('}');
        if (jsonStartIndex !== -1 && jsonEndIndex !== -1) {
            const jsonText = text.substring(jsonStartIndex, jsonEndIndex + 1);
            const data = JSON.parse(jsonText);

      //      console.log('Données JSON reçues :', data);

            if (data.success) {
            //    console.log(`Texte reconnu : ${data.text}`);
                document.getElementById('recognizedText').value = data.text;
         //       correctSpellingAndFormat(); // Appel sans `await`, car cette version est en mode promise.
                suggestEntryNameFromProductSearch(); // Idem, sans `await`.
                await suggestManufactuer();
                await suggestModel();
                toggleButtons();
                $('#loadingModal').modal('hide');

            } else if (data.error) {
                $('#loadingModal').modal('hide');
                alert(`Erreur retournée par le serveur : ${data.error}`);
            } else {
                $('#loadingModal').modal('hide');
                alert('Une erreur inconnue s\'est produite.');
            }
        } else {
            $('#loadingModal').modal('hide');
            throw new Error('Le format de la réponse est incorrect. Impossible d\'extraire les données JSON.');
        }
    } catch (error) {
        $('#loadingModal').modal('hide');
        console.error('Erreur lors de la requête fetch :', error);
        const errorDisplay = document.getElementById('errorDisplay');
        if (errorDisplay) {
            errorDisplay.innerHTML = `<p class="text-danger">${error.message}</p>`;
        } else {
            alert(error.message);
        }
    }
}




});


function switchEntryName(fieldName = 'product',specifics_row) {

    var form = document.getElementById('form-product' + productId);
    if (!form) {
        console.error('Form element not found');
        return;
    }
    // Obtenir les éléments concernés
    var label = document.getElementById('ai-result-name');
    var labelcount = document.getElementById('ai-result-name-count');
   

    var element = form.querySelector('input[name="title_search"]');
    var search =form.querySelector('input[name="search"]');
 //   var input=element.value;

   // console.log('input:' + input);
    // Vérifier si le label contient du texte
    if (label && label.style.display !== 'none' && label.innerText.trim() !== "") {
        // Si le label contient du texte, on le copie dans l'input et cache le label
       // console.log('label:' +  label.textContent);
       var labelText = label.textContent.trim();

       if (element) {
           element.value = labelText;
       }

       if (search) {
           search.value = labelText;
           toggleButtons();
         //  updateCharacterCount()
           updateCharacterCount(search,'char-count-search');

       }
        label.style.display = 'none';
        labelcount.style.display = 'none';
     
    } else {
        // Si le label est vide ou caché, on affiche un message dans la console (ou une autre action)
        console.log('Le label est vide ou caché.');
    }
}
