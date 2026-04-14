// Global initialization function
window.reinitInventory = function() {
    
    let scanTimeout = null;
    let lastScannedSku = '';

    const newLocationInput = document.getElementById('input-new-location');
    const submitButton = document.getElementById('button-submit');
    const skuInput = document.getElementById('input-sku');
    const selectAllCheckbox = document.getElementById('select-all');
    const form = document.getElementById('form-inventory');

        newLocationInput: !!newLocationInput,
        submitButton: !!submitButton,
        skuInput: !!skuInput,
        form: !!form
    });

    if (!newLocationInput || !submitButton || !skuInput || !form) {
        console.error('Missing required elements!');
        return;
    }

    // Toggle submit button
    function toggleSubmitButton() {
        const hasCheckedRows = document.querySelectorAll('.product-checkbox:checked').length > 0;
        const hasNewLocation = newLocationInput.value.trim() !== '';
        const hasFilterLocation = document.getElementById('hidden-filter-location') && document.getElementById('hidden-filter-location').value.trim() !== '';
        submitButton.disabled = !hasCheckedRows || (!hasNewLocation && !hasFilterLocation);
    }

    // Select all
    if (selectAllCheckbox) {
        selectAllCheckbox.onclick = function() {
            document.querySelectorAll('.product-checkbox').forEach(function(checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
            toggleSubmitButton();
        };
    }

    // Checkboxes
    document.querySelectorAll('.product-checkbox').forEach(function(checkbox) {
        checkbox.onchange = toggleSubmitButton;
    });

    // New location input
    newLocationInput.oninput = function() {
        this.value = this.value.toUpperCase();
        toggleSubmitButton();
    };

    // Form submit handler - AJAX like OpenCart 4
    form.onsubmit = function(e) {
        e.preventDefault();
        
        const checkedRows = document.querySelectorAll('.product-checkbox:checked');
        
        if (checkedRows.length === 0) {
            alert(lang.alert_select_product);
            return false;
        }

        const newLocation = newLocationInput.value.trim();
        const filterLocation = document.getElementById('hidden-filter-location')?.value || '';
        const locationToUse = newLocation || filterLocation;

        if (!locationToUse) {
            alert(lang.alert_enter_location);
            return false;
        }

        // Build product IDs and scanned quantities
        const productData = [];
        checkedRows.forEach(function(checkbox) {
            const row = checkbox.closest('tr');
            const productId = checkbox.value;
            const scannedCount = parseInt(row.dataset.scanned) || 0;
            productData.push({id: productId, scanned: scannedCount});
        });


        // Submit via AJAX
        const formData = new FormData();
        formData.append('user_token', document.querySelector('input[name="user_token"]').value);
        productData.forEach(function(item) {
            formData.append('product_id[]', item.id);
            formData.append('scanned_quantity[' + item.id + ']', item.scanned);
        });
        formData.append('new_location', locationToUse);

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const alert = document.getElementById('alert');
                if (alert) {
                    alert.innerHTML = '<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-check-circle"></i> ' + data.success + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                }
                
                // Reload the list with current location filter
                const currentLocation = document.getElementById('input-location')?.value || '';
                let reloadUrl = 'index.php?route=shopmanager/inventory.list&user_token=' + document.querySelector('input[name="user_token"]').value;
                if (currentLocation) {
                    reloadUrl += '&filter_location=' + encodeURIComponent(currentLocation);
                }
                
                const inventoryDiv = document.getElementById('inventory');
                if (inventoryDiv) {
                    fetch(reloadUrl)
                        .then(response => response.text())
                        .then(html => {
                            inventoryDiv.innerHTML = html;
                            if (typeof window.reinitInventory === 'function') {
                                window.reinitInventory();
                            }
                        });
                }
            } else if (data.error) {
                const alert = document.getElementById('alert');
                if (alert) {
                    alert.innerHTML = '<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-exclamation-circle"></i> ' + data.error + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const alert = document.getElementById('alert');
            if (alert) {
                alert.innerHTML = '<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-exclamation-circle"></i> An error occurred. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            }
        });

        return false;
    };

    // SKU scan
    skuInput.oninput = function() {
        const inputSku = this.value.trim();
        if (inputSku === lastScannedSku) return;
        
        clearTimeout(scanTimeout);
        scanTimeout = setTimeout(function() {
            if (inputSku === '') return;
            lastScannedSku = inputSku;
            let skuFound = false;
            
            document.querySelectorAll('tr[data-product-id]').forEach(function(row) {
                const productSku = row.querySelector('.sku-cell').textContent.trim();
                
                if (productSku === inputSku) {
                    skuFound = true;
                    const productId = row.dataset.productId;
                    const currentQuantity = parseInt(row.dataset.quantity);
                    
                    row.parentNode.insertBefore(row, row.parentNode.firstChild);
                    
                    let scannedCount = parseInt(row.dataset.scanned) || 0;
                    
                    // Check if we're exceeding quantity BEFORE incrementing
                    if (scannedCount >= currentQuantity) {
                        const productName = row.querySelector('.sku-cell').textContent.trim();
                        if (!confirm(lang.alert_scan_exceeds.replace('%s', (scannedCount + 1)).replace('%s', currentQuantity).replace('%s', productName))) {
                            playErrorSound();
                            skuInput.value = '';
                            lastScannedSku = '';
                            return;
                        }
                    }
                    
                    scannedCount++;
                    row.dataset.scanned = scannedCount;
                    
                    const scannedBadge = document.getElementById('scanned-' + productId);
                    scannedBadge.textContent = scannedCount;
                    
                    row.querySelector('.product-checkbox').checked = true;
                    toggleSubmitButton();
                    playSuccessSound();
                    
                    if (scannedCount === currentQuantity) {
                        row.style.backgroundColor = '#d4edda';
                        row.style.color = '#155724';
                        row.querySelectorAll('td').forEach(function(td) {
                            td.style.backgroundColor = '#d4edda';
                            td.style.color = '#155724';
                        });
                        scannedBadge.classList.remove('bg-light', 'bg-warning', 'bg-danger');
                        scannedBadge.classList.add('bg-success', 'text-white');
                    } else if (scannedCount < currentQuantity) {
                        row.style.backgroundColor = '#fff3cd';
                        row.style.color = '#856404';
                        row.querySelectorAll('td').forEach(function(td) {
                            td.style.backgroundColor = '#fff3cd';
                            td.style.color = '#856404';
                        });
                        scannedBadge.classList.remove('bg-light', 'bg-success', 'bg-danger');
                        scannedBadge.classList.add('bg-warning', 'text-dark');
                    } else {
                        row.style.backgroundColor = '#f8d7da';
                        row.style.color = '#721c24';
                        row.querySelectorAll('td').forEach(function(td) {
                            td.style.backgroundColor = '#f8d7da';
                            td.style.color = '#721c24';
                        });
                        scannedBadge.classList.remove('bg-light', 'bg-success', 'bg-warning');
                        scannedBadge.classList.add('bg-danger', 'text-white');
                    }
                    
                    skuInput.value = '';
                    lastScannedSku = '';
                    return;
                }
            });
            
            if (!skuFound && inputSku !== '') {
                fetch('index.php?route=shopmanager/inventory.searchProduct&user_token=' + document.querySelector('input[name="user_token"]').value + '&sku=' + encodeURIComponent(inputSku))
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.product) {
                        const product = data.product;
                        if (confirm(lang.confirm_product_found.replace('%s', product.location).replace('%s', product.quantity))) {
                            const tbody = document.querySelector('tbody');
                            const newRow = document.createElement('tr');
                            newRow.dataset.productId = product.product_id;
                            newRow.dataset.sku = product.sku;
                            newRow.dataset.quantity = product.quantity;
                            newRow.dataset.scanned = '1';
                            newRow.dataset.location = product.location;
                            
                            newRow.innerHTML = `
                                <td class="text-center"><input type="checkbox" name="product_id[${product.product_id}]" value="${product.product_id}" class="form-check-input product-checkbox" checked /></td>
                                <td class="text-center"><img src="${product.image}" alt="${product.name}" class="img-thumbnail" /></td>
                                <td class="sku-cell">${product.sku}</td>
                                <td>${product.name}</td>
                                <td class="text-center"><span class="quantity-display badge bg-primary" id="quantity-${product.product_id}">${product.quantity}</span></td>
                                <td class="text-center"><span class="badge bg-secondary">${product.unallocated_quantity}</span></td>
                                <td class="text-center"><span class="badge bg-warning text-dark" id="scanned-${product.product_id}">1</span></td>
                                <td class="text-center location-cell">${product.location}</td>
                            `;
                            
                            tbody.insertBefore(newRow, tbody.firstChild);
                            newRow.style.backgroundColor = '#fff3cd';
                            newRow.style.color = '#856404';
                            newRow.querySelectorAll('td').forEach(function(td) {
                                td.style.backgroundColor = '#fff3cd';
                                td.style.color = '#856404';
                            });
                            
                            newRow.querySelector('.product-checkbox').onchange = toggleSubmitButton;
                            toggleSubmitButton();
                            playSuccessSound();
                        }
                    } else {
                        playErrorSound();
                    }
                    skuInput.value = '';
                    lastScannedSku = '';
                })
                .catch(error => {
                    playErrorSound();
                    skuInput.value = '';
                    lastScannedSku = '';
                });
            }
        }, 100);
    };

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
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        oscillator.start(audioContext.currentTime);
        oscillator2.start(audioContext.currentTime);
        
        gainNode.gain.setValueAtTime(0, audioContext.currentTime + 0.1);
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime + 0.15);
        gainNode.gain.setValueAtTime(0, audioContext.currentTime + 0.25);
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime + 0.3);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
        
        oscillator.stop(audioContext.currentTime + 0.5);
        oscillator2.stop(audioContext.currentTime + 0.5);
    }

    function playSuccessSound() {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 600;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.2);
    }

    toggleSubmitButton();
};

// Init on page load
document.addEventListener('DOMContentLoaded', function() {
    window.reinitInventory();
});
