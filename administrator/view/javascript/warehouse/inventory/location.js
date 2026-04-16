// Original: shopmanager/inventory/location.js
// ============================================
// FILTER LOCATION HANDLERS (from Twig)
// ============================================
var hasScannedProducts = false;

// Prevent accidental page leave
window.addEventListener('beforeunload', function(e) {
    if (hasScannedProducts) {
        e.preventDefault();
        e.returnValue = TEXT_UNSAVED_CHANGES;
        return TEXT_UNSAVED_CHANGES;
    }
});

$(document).ready(function() {
    // Check location and set focus
    var filterLocation = $('#input-location').val().trim();
    if (filterLocation === '') {
        // No location: focus on location field and make it red
        $('#input-location').addClass('border-danger').focus();
    }
    
    // Transform location to uppercase on input and remove red border when typing
    $('#input-location').on('input', function() {
        this.value = this.value.toUpperCase();
        if (this.value.trim() !== '') {
            $(this).removeClass('border-danger');
        } else {
            $(this).addClass('border-danger');
        }
    });
    
    // Auto-submit filter on change (when user manually types location)
    $('#input-location').on('change', function() {
        if (this.value.trim() !== '') {
            $('#button-filter').trigger('click');
        }
    });
});

// Global initialization function
window.reinitInventory = function() {
    
    let scanTimeout = null;
    let lastScannedSku = '';

    const newLocationInput = document.getElementById('input-new-location');
    const submitButton = document.getElementById('button-submit');
    const skuInput = document.getElementById('input-sku');
    const selectAllCheckbox = document.getElementById('select-all');
    const form = document.getElementById('form-inventory');

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
            const isChecked = selectAllCheckbox.checked;
            
            // If checking all: validate all countries first
            if (isChecked) {
                const productsWithoutCountry = [];
                document.querySelectorAll('.product-checkbox').forEach(function(checkbox) {
                    const productId = checkbox.value;
                    const countrySelect = document.getElementById('input-made-in-country-id-' + productId);
                    const madeInCountryId = countrySelect ? countrySelect.value : '0';
                    
                    if (!madeInCountryId || madeInCountryId === '0') {
                        productsWithoutCountry.push({
                            checkbox: checkbox,
                            productId: productId,
                            row: checkbox.closest('tr')
                        });
                    }
                });
                
                // If any products missing country, process them sequentially with popup
                if (productsWithoutCountry.length > 0) {
                    selectAllCheckbox.checked = false;
                    
                    // Process each product without country sequentially
                    let currentIndex = 0;
                    
                    function processNextProduct() {
                        if (currentIndex >= productsWithoutCountry.length) {
                            // All countries set, now trigger select-all again
                            setTimeout(function() {
                                selectAllCheckbox.click();
                            }, 500);
                            return;
                        }
                        
                        const product = productsWithoutCountry[currentIndex];
                        currentIndex++;
                        
                        // Show popup for this product
                        showCountryPopupForScan(product.productId, product.row, function() {
                            // After country set, wait a bit before processing next product
                            setTimeout(function() {
                                processNextProduct();
                            }, 300);
                        });
                    }
                    
                    processNextProduct();
                    return;
                }
            }
            
            document.querySelectorAll('.product-checkbox').forEach(function(checkbox) {
                checkbox.checked = isChecked;
                
                // If checking: auto-scan all products with their full quantity
                if (isChecked) {
                    const row = checkbox.closest('tr');
                    const productId = checkbox.value;
                    const currentQuantity = parseInt(row.dataset.quantity) || 0;
                    
                    // Set scanned count to full quantity
                    row.dataset.scanned = currentQuantity;
                    
                    // Update badge
                    const scannedBadge = document.getElementById('scanned-' + productId);
                    if (scannedBadge) {
                        scannedBadge.textContent = currentQuantity;
                        scannedBadge.classList.remove('bg-light', 'bg-warning', 'bg-danger');
                        scannedBadge.classList.add('bg-success', 'text-white');
                    }
                    
                    // Update row colors to green (fully scanned)
                    row.style.backgroundColor = '#d4edda';
                    row.style.color = '#155724';
                    row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
                        td.style.backgroundColor = '#d4edda';
                        td.style.color = '#155724';
                    });
                    
                    // Mark that we have scanned products
                    hasScannedProducts = true;
                } else {
                    // If unchecking: reset scanned count to 0
                    const row = checkbox.closest('tr');
                    const productId = checkbox.value;
                    
                    // Reset scanned count
                    row.dataset.scanned = 0;
                    
                    // Update badge
                    const scannedBadge = document.getElementById('scanned-' + productId);
                    if (scannedBadge) {
                        scannedBadge.textContent = '0';
                        scannedBadge.classList.remove('bg-success', 'bg-warning', 'bg-danger', 'text-white');
                        scannedBadge.classList.add('bg-light');
                    }
                    
                    // Reset row colors
                    row.style.backgroundColor = '';
                    row.style.color = '';
                    row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
                        td.style.backgroundColor = '';
                        td.style.color = '';
                    });
                    
                    // Check if any products are still scanned
                    const anyScanned = Array.from(document.querySelectorAll('tr[data-product-id]')).some(function(r) {
                        return parseInt(r.dataset.scanned) > 0;
                    });
                    hasScannedProducts = anyScanned;
                }
            });
            
            toggleSubmitButton();
        };
    }

    // Checkboxes - Individual product selection with auto-scan
    document.querySelectorAll('.product-checkbox').forEach(function(checkbox) {
        checkbox.onchange = function() {
            const isChecked = this.checked;
            const row = this.closest('tr');
            const productId = this.value;
            const currentQuantity = parseInt(row.dataset.quantity) || 0;
            
            if (isChecked) {
                // Check if made_in_country_id is set
                const countrySelect = document.getElementById('input-made-in-country-id-' + productId);
                const madeInCountryId = countrySelect ? countrySelect.value : '0';
                
                if (!madeInCountryId || madeInCountryId === '0') {
                    // Uncheck immediately
                    checkbox.checked = false;
                    // Show country selection popup
                    showCountryPopupForScan(productId, row, function() {
                        // After country is set, check again and auto-scan
                        checkbox.checked = true;
                        autoScanProduct(row, productId, currentQuantity);
                    });
                    return;
                }
                
                // Country is set, proceed with auto-scan
                autoScanProduct(row, productId, currentQuantity);
            } else {
                // When unchecking: reset scanned count to 0
                row.dataset.scanned = 0;
                
                // Update badge
                const scannedBadge = document.getElementById('scanned-' + productId);
                if (scannedBadge) {
                    scannedBadge.textContent = '0';
                    scannedBadge.classList.remove('bg-success', 'bg-warning', 'bg-danger', 'text-white');
                    scannedBadge.classList.add('bg-light');
                }
                
                // Reset row colors
                row.style.backgroundColor = '';
                row.style.color = '';
                row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
                    td.style.backgroundColor = '';
                    td.style.color = '';
                });
                
                // Check if any products are still scanned
                const anyScanned = Array.from(document.querySelectorAll('tr[data-product-id]')).some(function(r) {
                    return parseInt(r.dataset.scanned) > 0;
                });
                hasScannedProducts = anyScanned;
            }
            
            toggleSubmitButton();
        };
    });

    // Helper function to auto-scan a product
    function autoScanProduct(row, productId, quantity) {
        row.dataset.scanned = quantity;
        
        // Update badge
        const scannedBadge = document.getElementById('scanned-' + productId);
        if (scannedBadge) {
            scannedBadge.textContent = quantity;
            scannedBadge.classList.remove('bg-light', 'bg-warning', 'bg-danger');
            scannedBadge.classList.add('bg-success', 'text-white');
        }
        
        // Update row colors to green (fully scanned)
        row.style.backgroundColor = '#d4edda';
        row.style.color = '#155724';
        row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
            td.style.backgroundColor = '#d4edda';
            td.style.color = '#155724';
        });
        
        // Mark that we have scanned products
        hasScannedProducts = true;
        toggleSubmitButton();
    }


    // New location input
    newLocationInput.oninput = function() {
        this.value = this.value.toUpperCase();
        toggleSubmitButton();
    };

    // Form submit handler - AJAX like OpenCart 4
    form.onsubmit = function(e) {
        e.preventDefault();
        
        // Close any open input fields before submitting
        document.querySelectorAll('input.form-control-sm[type="number"]').forEach(function(openInput) {
            openInput.blur(); // Trigger blur to save the value
        });
        
        const checkedRows = document.querySelectorAll('.product-checkbox:checked');
        
        if (checkedRows.length === 0) {
            alert(TEXT_SELECT_AT_LEAST_ONE);
            return false;
        }

        const newLocation = newLocationInput.value.trim();
        const filterLocation = document.getElementById('hidden-filter-location')?.value || '';
        const locationToUse = newLocation || filterLocation;

        if (!locationToUse) {
            alert(TEXT_PLEASE_ENTER_LOCATION);
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

        // Reset flag before submitting (allow navigation after submit)
        hasScannedProducts = false;

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            return response.text();
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    // Show success message
                    const alert = document.getElementById('alert');
                    if (alert) {
                        alert.innerHTML = '<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-check-circle"></i> ' + data.success + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    }
                    
                    // Remove submitted products from the list
                    const checkedRows = document.querySelectorAll('.product-checkbox:checked');
                    checkedRows.forEach(function(checkbox) {
                        const row = checkbox.closest('tr');
                        row.remove();
                    });
                    
                    // Clear new location input
                    newLocationInput.value = '';
                    
                    // Reset submit button
                    toggleSubmitButton();
                    
                    // Focus back to SKU input
                    skuInput.focus();
                    
                } else if (data.error) {
                    const alert = document.getElementById('alert');
                    if (alert) {
                        alert.innerHTML = '<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-exclamation-circle"></i> ' + data.error + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
                    }
                }
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                console.error('Raw response:', text);
                const alert = document.getElementById('alert');
                if (alert) {
                    alert.innerHTML = '<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-exclamation-circle"></i> Server error - check console for details. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
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

    // Make scanned badges editable
    document.addEventListener('click', function(e) {
        // Close any open input fields first
        const openInputs = document.querySelectorAll('input.form-control-sm[type="number"]');
        openInputs.forEach(function(openInput) {
            try {
                const parentTd = openInput.parentNode;
                if (parentTd) {
                    const badge = parentTd.querySelector('.badge, .quantity-display');
                    if (badge && badge.style.display === 'none') {
                        badge.style.display = '';
                    }
                }
                // Check if input still has a parent before removing
                if (openInput && openInput.parentNode) {
                    openInput.parentNode.removeChild(openInput);
                }
            } catch (e) {
                // Input already removed, ignore
            }
        });
        
        // Edit scanned count
        if (e.target.classList.contains('badge') && e.target.id.startsWith('scanned-')) {
            const badge = e.target;
            const productId = badge.id.replace('scanned-', '');
            const row = document.querySelector('tr[data-product-id="' + productId + '"]');
            const currentValue = badge.textContent;
            
            // Create input
            const input = document.createElement('input');
            input.type = 'number';
            input.value = currentValue;
            input.min = '0';
            input.className = 'form-control form-control-sm';
            input.style.width = '60px';
            input.style.textAlign = 'center';
            
            // Replace badge with input
            badge.style.display = 'none';
            badge.parentNode.insertBefore(input, badge);
            input.focus();
            input.select();
            
            // Function to update scanned count
            function updateScannedCount() {
                const newValue = parseInt(input.value) || 0;
                const quantity = parseInt(row.dataset.quantity) || 0;
                
                // Update badge
                badge.textContent = newValue;
                badge.style.display = '';
                input.remove();
                
                // Update row dataset
                row.dataset.scanned = newValue;
                
                // Update row colors based on scanned count
                if (newValue === quantity) {
                    row.style.backgroundColor = '#d4edda';
                    row.style.color = '#155724';
                    row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
                        td.style.backgroundColor = '#d4edda';
                        td.style.color = '#155724';
                    });
                    badge.classList.remove('bg-light', 'bg-warning', 'bg-danger');
                    badge.classList.add('bg-success', 'text-white');
                } else if (newValue < quantity) {
                    row.style.backgroundColor = '#fff3cd';
                    row.style.color = '#856404';
                    row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
                        td.style.backgroundColor = '#fff3cd';
                        td.style.color = '#856404';
                    });
                    badge.classList.remove('bg-light', 'bg-success', 'bg-danger');
                    badge.classList.add('bg-warning', 'text-dark');
                } else {
                    row.style.backgroundColor = '#f8d7da';
                    row.style.color = '#721c24';
                    row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
                        td.style.backgroundColor = '#f8d7da';
                        td.style.color = '#721c24';
                    });
                    badge.classList.remove('bg-light', 'bg-success', 'bg-warning');
                    badge.classList.add('bg-danger', 'text-white');
                }
            }
            
            // Update on Enter or blur
            input.onkeydown = function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    updateScannedCount();
                }
                if (e.key === 'Escape' || e.keyCode === 27) {
                    badge.style.display = '';
                    input.remove();
                }
            };
            
            input.onblur = function() {
                updateScannedCount();
            };
        }
        
        // Edit quantity
        if (e.target.classList.contains('quantity-display')) {
            const badge = e.target;
            const productId = badge.id.replace('quantity-', '');
            const row = document.querySelector('tr[data-product-id="' + productId + '"]');
            const currentValue = badge.textContent;
            
            // Create input
            const input = document.createElement('input');
            input.type = 'number';
            input.value = currentValue;
            input.min = '0';
            input.className = 'form-control form-control-sm';
            input.style.width = '60px';
            input.style.textAlign = 'center';
            
            // Replace badge with input
            badge.style.display = 'none';
            badge.parentNode.insertBefore(input, badge);
            input.focus();
            input.select();
            
            // Function to update quantity
            function updateQuantity() {
                const newValue = parseInt(input.value) || 0;
                
                // Update badge
                badge.textContent = newValue;
                badge.style.display = '';
                input.remove();
                
                // Update row dataset
                row.dataset.quantity = newValue;
                
                // Update row colors if scanned count changed relative to new quantity
                const scannedCount = parseInt(row.dataset.scanned) || 0;
                const scannedBadge = document.getElementById('scanned-' + productId);
                
                if (scannedCount === newValue) {
                    row.style.backgroundColor = '#d4edda';
                    row.style.color = '#155724';
                    row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
                        td.style.backgroundColor = '#d4edda';
                        td.style.color = '#155724';
                    });
                    if (scannedBadge) {
                        scannedBadge.classList.remove('bg-light', 'bg-warning', 'bg-danger');
                        scannedBadge.classList.add('bg-success', 'text-white');
                    }
                } else if (scannedCount < newValue) {
                    row.style.backgroundColor = '#fff3cd';
                    row.style.color = '#856404';
                    row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
                        td.style.backgroundColor = '#fff3cd';
                        td.style.color = '#856404';
                    });
                    if (scannedBadge) {
                        scannedBadge.classList.remove('bg-light', 'bg-success', 'bg-danger');
                        scannedBadge.classList.add('bg-warning', 'text-dark');
                    }
                } else {
                    row.style.backgroundColor = '#f8d7da';
                    row.style.color = '#721c24';
                    row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
                        td.style.backgroundColor = '#f8d7da';
                        td.style.color = '#721c24';
                    });
                    if (scannedBadge) {
                        scannedBadge.classList.remove('bg-light', 'bg-success', 'bg-warning');
                        scannedBadge.classList.add('bg-danger', 'text-white');
                    }
                }
            }
            
            // Update on Enter or blur
            input.onkeydown = function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    updateQuantity();
                }
                if (e.key === 'Escape' || e.keyCode === 27) {
                    badge.style.display = '';
                    input.remove();
                }
            };
            
            input.onblur = function() {
                updateQuantity();
            };
        }
    });

    // SKU scan - prevent Enter from submitting form
    skuInput.onkeydown = function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            return false;
        }
    };

    // Process SKU scan function
    function processSKUScan() {
        const inputSku = skuInput.value.trim();
        if (inputSku === '' || inputSku === lastScannedSku) return;
        
        lastScannedSku = inputSku;
        let skuFound = false;
        
        document.querySelectorAll('tr[data-product-id]').forEach(function(row) {
            const productSku = row.querySelector('.sku-cell').textContent.trim();
            
            if (productSku === inputSku) {
                skuFound = true;
                const productId = row.dataset.productId;
                const currentQuantity = parseInt(row.dataset.quantity);
                
                // Check if made_in_country_id is set
                const countrySelect = document.getElementById('input-made-in-country-id-' + productId);
                const madeInCountryId = countrySelect ? countrySelect.value : '0';
                
                if (!madeInCountryId || madeInCountryId === '0') {
                    // Show country selection popup
                    showCountryPopupForScan(productId, row, function() {
                        // After country is set, continue with scan
                        continueScanProcess(row, productId, currentQuantity);
                    });
                    skuInput.value = '';
                    lastScannedSku = '';
                    return;
                }
                
                continueScanProcess(row, productId, currentQuantity);
                skuInput.value = '';
                lastScannedSku = '';
                skuInput.focus();
                return;
            }
        });
        
        if (!skuFound && inputSku !== '') {
            fetch('index.php?route=shopmanager/inventory/location.searchProduct&user_token=' + document.querySelector('input[name="user_token"]').value + '&sku=' + encodeURIComponent(inputSku))
            .then(response => response.json())
            .then(data => {
                    if (data.success && data.product) {
                        const product = data.product;
                        if (confirm(TEXT_PRODUCT_FOUND_CONFIRM.replace('%s', product.location).replace('%s', product.quantity))) {
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
                            skuInput.value = '';
                            lastScannedSku = '';
                            skuInput.focus();
                        }
                    } else {
                        playErrorSound();
                    }
                    skuInput.value = '';
                    lastScannedSku = '';
                    skuInput.focus();
                })
                .catch(error => {
                    playErrorSound();
                    skuInput.value = '';
                    lastScannedSku = '';
                    skuInput.focus();
                });
        }
    }

    // SKU scan on input with debounce
    skuInput.oninput = function() {
        const inputSku = this.value.trim();
        if (inputSku === lastScannedSku) return;
        
        clearTimeout(scanTimeout);
        scanTimeout = setTimeout(function() {
            processSKUScan();
        }, 100);
    };
    
    // SKU scan on blur
    skuInput.onblur = function() {
        clearTimeout(scanTimeout);
        processSKUScan();
    };

    toggleSubmitButton();
};

// Session variable for auto-accepting AI country suggestions
var autoAcceptAICountry = false;

// Function to continue scan process after country validation
function continueScanProcess(row, productId, currentQuantity) {
    row.parentNode.insertBefore(row, row.parentNode.firstChild);
    
    let scannedCount = parseInt(row.dataset.scanned) || 0;
    
    // Check if we're exceeding quantity BEFORE incrementing
    if (scannedCount >= currentQuantity) {
        const productName = row.querySelector('.sku-cell').textContent.trim();
        if (!confirm(TEXT_SCAN_EXCEEDS_QUANTITY.replace('%d', scannedCount + 1).replace('%d', currentQuantity).replace('%s', productName))) {
            playErrorSound();
            return;
        }
    }
    
    scannedCount++;
    row.dataset.scanned = scannedCount;
    
    const scannedBadge = document.getElementById('scanned-' + productId);
    scannedBadge.textContent = scannedCount;
    
    row.querySelector('.product-checkbox').checked = true;
    
    // Mark that we have scanned products (enable beforeunload warning)
    hasScannedProducts = true;
    
    // Toggle submit button
    const newLocationInput = document.getElementById('input-new-location');
    const submitButton = document.getElementById('button-submit');
    if (newLocationInput && submitButton) {
        const hasCheckedRows = document.querySelectorAll('.product-checkbox:checked').length > 0;
        const hasNewLocation = newLocationInput.value.trim() !== '';
        const hasFilterLocation = document.getElementById('hidden-filter-location') && document.getElementById('hidden-filter-location').value.trim() !== '';
        submitButton.disabled = !hasCheckedRows || (!hasNewLocation && !hasFilterLocation);
    }
    
    playSuccessSound();
    
    if (scannedCount === currentQuantity) {
        row.style.backgroundColor = '#d4edda';
        row.style.color = '#155724';
        row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
            td.style.backgroundColor = '#d4edda';
            td.style.color = '#155724';
        });
        scannedBadge.classList.remove('bg-light', 'bg-warning', 'bg-danger');
        scannedBadge.classList.add('bg-success', 'text-white');
    } else if (scannedCount < currentQuantity) {
        row.style.backgroundColor = '#fff3cd';
        row.style.color = '#856404';
        row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
            td.style.backgroundColor = '#fff3cd';
            td.style.color = '#856404';
        });
        scannedBadge.classList.remove('bg-light', 'bg-success', 'bg-danger');
        scannedBadge.classList.add('bg-warning', 'text-dark');
    } else {
        row.style.backgroundColor = '#f8d7da';
        row.style.color = '#721c24';
        row.querySelectorAll('td:not(.country-cell)').forEach(function(td) {
            td.style.backgroundColor = '#f8d7da';
            td.style.color = '#721c24';
        });
        scannedBadge.classList.remove('bg-light', 'bg-success', 'bg-warning');
        scannedBadge.classList.add('bg-danger', 'text-white');
    }
}

// Function to show country popup for scan
function showCountryPopupForScan(productId, row, callback) {
    const productName = row.querySelector('td:nth-child(4)').textContent.trim();
    const productSku = row.querySelector('.sku-cell').textContent.trim();
    
    // Get the select from the row
    const countrySelect = document.getElementById('input-made-in-country-id-' + productId);
    if (!countrySelect) {
        console.error('Country select not found for product ' + productId);
        return;
    }
    
    // If auto-accept is enabled, don't show modal - wait for AI and auto-apply
    if (autoAcceptAICountry) {
        // Store callback
        window.scanCallback = callback;
        
        // Call AI to get country suggestion and auto-apply
        const user_token = document.querySelector('input[name="user_token"]').value;
        
        
        fetch('index.php?route=shopmanager/ai.getMadeInCountry&user_token=' + user_token, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            
            // Check if AI returned success with country data
            if (data.success && data.success.country_id && data.success.country_id > 0) {
                
                // Auto-apply the country
                countrySelect.value = data.success.country_id;
                
                // Save to database via AJAX
                fetch('index.php?route=shopmanager/catalog/product.editMadeInCountry&user_token=' + user_token, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId + '&made_in_country_id=' + data.success.country_id
                })
                .then(response => response.json())
                .then(json => {
                    if (json.success) {
                        const cell = document.getElementById('check-made-in-country-id-' + productId);
                        if (cell) {
                            cell.style.setProperty('background-color', 'green', 'important');
                        }
                        
                        // Call callback to continue scan
                        if (window.scanCallback) {
                            window.scanCallback();
                            window.scanCallback = null;
                        }
                        
                        playSuccessSound();
                    } else {
                        console.error('Error saving country:', json.error);
                        playErrorSound();
                    }
                })
                .catch(error => {
                    console.error('Error saving country:', error);
                    playErrorSound();
                });
            } else {
                // AI couldn't determine - show modal anyway
                showModalWithAI(productId, row, callback, productName, productSku, countrySelect, null);
            }
        })
        .catch(error => {
            console.error('Error calling AI:', error);
            // Show modal on error
            showModalWithAI(productId, row, callback, productName, productSku, countrySelect, null);
        });
        
        return; // Exit early in auto-accept mode
    }
    
    // Normal mode: show modal with AI suggestion
    showModalWithAI(productId, row, callback, productName, productSku, countrySelect, null);
}

// Helper function to show the modal with AI loading
function showModalWithAI(productId, row, callback, productName, productSku, countrySelect, preloadedData) {
    // Play warning sound
    playWarningSound();
    
    // Clone the select options for the modal
    const modalSelect = document.getElementById('scan-country-select');
    if (modalSelect) {
        modalSelect.innerHTML = countrySelect.innerHTML;
        modalSelect.value = '0'; // Reset to default
    }
    
    // Update modal content
    document.getElementById('scan-product-info').innerHTML = '<strong>SKU:</strong> ' + productSku + '<br><strong>Name:</strong> ' + productName;
    
    // Hide AI result and show loader
    document.getElementById('scan-ai-result').style.display = 'none';
    document.getElementById('scan-ai-loader').style.display = 'block';
    
    // Store product info in modal data
    const modal = document.getElementById('scanCountryModal');
    modal.dataset.productId = productId;
    modal.dataset.callback = 'scanCallback';
    
    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Store callback
    window.scanCallback = callback;
    
    // Call AI to get country suggestion
    const user_token = document.querySelector('input[name="user_token"]').value;
    
    
    fetch('index.php?route=shopmanager/ai.getMadeInCountry&user_token=' + user_token, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => {
        return response.json();
    })
    .then(data => {
        
        // Hide loader
        document.getElementById('scan-ai-loader').style.display = 'none';
        
        // Check if AI returned success with country data
        if (data.success && data.success.country_id && data.success.country_id > 0) {
            // AI found a country
            modalSelect.value = data.success.country_id;
            
            // Show AI result with reasoning
            const aiResult = document.getElementById('scan-ai-result');
            const aiReasoning = document.getElementById('scan-ai-reasoning');
            aiReasoning.innerHTML = data.success.reasoning || 'AI determined this country based on product information.';
            aiResult.style.display = 'block';
        } else {
            // AI couldn't determine or error
        }
    })
    .catch(error => {
        console.error('Error calling AI:', error);
        document.getElementById('scan-ai-loader').style.display = 'none';
    });
}

// ============================================
// HANDLE APPLY BUTTON IN COUNTRY MODAL
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const applyButton = document.getElementById('scan-apply-country');
    if (applyButton) {
        applyButton.addEventListener('click', function() {
            const selectedCountry = document.getElementById('scan-country-select').value;
            const modal = document.getElementById('scanCountryModal');
            const productId = modal.dataset.productId;
            
            if (!selectedCountry || selectedCountry == '0') {
                alert(TEXT_SELECT_COUNTRY);
                return;
            }
            
            // Check if auto-accept checkbox is checked
            const autoAcceptCheckbox = document.getElementById('auto-accept-ai-country');
            if (autoAcceptCheckbox && autoAcceptCheckbox.checked) {
                window.autoAcceptAICountry = true;
            }
            
            // Update the country select in the row
            const countrySelect = document.getElementById('input-made-in-country-id-' + productId);
            if (countrySelect) {
                countrySelect.value = selectedCountry;
                
                // Save to database via AJAX
                const user_token = document.querySelector('input[name="user_token"]').value;
                
                fetch('index.php?route=shopmanager/catalog/product.editMadeInCountry&user_token=' + user_token, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId + '&made_in_country_id=' + selectedCountry
                })
                .then(response => response.json())
                .then(json => {
                    if (json.success) {
                        const cell = document.getElementById('check-made-in-country-id-' + productId);
                        if (cell) {
                            cell.style.setProperty('background-color', 'green', 'important');
                        }
                        
                        // Blur button to remove focus before closing modal (prevent aria-hidden warning)
                        applyButton.blur();
                        
                        // Close modal
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        bsModal.hide();
                        
                        // Call callback to continue scan/auto-select
                        if (window.scanCallback) {
                            window.scanCallback();
                            window.scanCallback = null;
                        }
                        
                        playSuccessSound();
                    } else {
                        alert(TEXT_ERROR_SAVE_COUNTRY.replace('%s', json.error || 'Unknown error'));
                        playErrorSound();
                    }
                })
                .catch(error => {
                    console.error('Error saving country:', error);
                    alert(TEXT_ERROR_SAVE_COUNTRY_GENERIC);
                    playErrorSound();
                });
            }
        });
    }
});

// Init on page load
document.addEventListener('DOMContentLoaded', function() {
    window.reinitInventory();
});
