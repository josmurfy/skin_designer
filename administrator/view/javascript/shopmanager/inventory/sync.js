/**
 * Performance Analytics JavaScript
 * 
 * Gère les interactions du tableau de bord analytique
 */
(function() {
    'use strict';

    // Variables globales
    let currentPeriod = 'month';
    let isLoading = false;

    /**
     * Initialisation au chargement de la page
     */
    $(document).ready(function() {
        initPeriodSelector();
        initRefreshButton();
        initExportButton();
        initSyncMarketplaceButton();
        initUpdateEbayQuantityButtons();
        initSyncProductButtons();
        initPrintReportButtons();
        initTooltips();
        
        // Get initial period from URL or default
        const urlParams = new URLSearchParams(window.location.search);
        currentPeriod = urlParams.get('period') || 'month';
        
    });

    /**
     * Initialise le sélecteur de période
     */
    function initPeriodSelector() {
        $('#period-selector input[type="radio"]').on('change', function() {
            currentPeriod = $(this).val();
            loadAnalyticsData(currentPeriod);
        });
    }

    /**
     * Initialise le bouton de rafraîchissement
     */
    function initRefreshButton() {
        $('#button-refresh').on('click', function() {
            loadAnalyticsData(currentPeriod);
        });
    }

    /**
     * Initialise le bouton d'export
     */
    function initExportButton() {
        $('#button-export').on('click', function() {
            const url = $('#url_export').val();
            const userToken = $('#user_token').val();
            const exportUrl = url + '&period=' + currentPeriod;
            
            window.open(exportUrl, '_blank');
        });
    }

    /**
     * Initialise le bouton de synchronisation marketplace
     */
    function initSyncMarketplaceButton() {
        const button = $('#button-sync-marketplace');
        const syncUrl = $('#url_sync_marketplace');
        
        
        if (button.length === 0) {
            console.error('Sync marketplace button not found!');
            return;
        }
        
        button.on('click', function() {
            if (confirm(TEXT_CONFIRM_SYNC_ALL)) {
                startMarketplaceSync(false);
            }
        });

        // Force full refresh button
        $('#button-force-refresh').on('click', function() {
            if (confirm(TEXT_CONFIRM_FORCE_REFRESH)) {
                startMarketplaceSync(true);
            }
        });

        // Scan image_backup button
        $('#button-scan-backup').on('click', function() {
            if (!confirm(TEXT_CONFIRM_SCAN_BACKUP)) return;
            var $btn = $(this);
            $btn.prop('disabled', true).html(
                '<i class="fa-solid fa-spinner fa-spin"></i> Scanning…'
            );
            $.ajax({
                url: $('#url_scan_image_backup').val(),
                type: 'GET',
                dataType: 'json',
                success: function(json) {
                    $btn.prop('disabled', false).html(
                        '<i class="fa-solid fa-folder-magnifying-glass"></i> ' + $btn.data('label')
                    );
                    if (json.error) {
                        alert('Error: ' + json.error);
                        return;
                    }
                    // Refresh the image mismatch tab if visible
                    if ($('#image-mismatch').length) {
                        $('#image-mismatch').html(
                            '<div class="text-center p-4"><i class="fa-solid fa-spinner fa-spin fa-2x"></i></div>'
                        );
                        setTimeout(function() {
                            $.ajax({
                                url: 'index.php?route=shopmanager/inventory/sync.getImageMismatchTab&user_token=' + $('#user_token').val(),
                                type: 'GET',
                                dataType: 'html',
                                success: function(html) { $('#image-mismatch').html(html); }
                            });
                        }, 300);
                    }
                    alert(
                        'Scan terminé ✓\n' +
                        '• Produits scannés : ' + json.scanned + '\n' +
                        '• Avec images backup : ' + json.with_images + '\n' +
                        '• Répertoires vides : ' + json.empty + '\n' +
                        '• Non trouvés : ' + json.not_found
                    );
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html(
                        '<i class="fa-solid fa-folder-magnifying-glass"></i> ' + $btn.data('label')
                    );
                    alert('AJAX error: ' + xhr.statusText);
                }
            });
        });
        // Store label for restore after spinner
        $('#button-scan-backup').data('label', $('#button-scan-backup').text().trim());
    }

    /**
     * Démarre la synchronisation marketplace page par page
     */
    function startMarketplaceSync(forceRefresh) {
        forceRefresh = forceRefresh || false;
        const urlInput = forceRefresh ? '#url_force_refresh_marketplace' : '#url_sync_marketplace';
        const url = $(urlInput).val();
        const userToken = $('#user_token').val();
        let currentPage = 1;
        let totalPages = 1;
        let totalProcessed = 0;
        let currentOffset = 0; // sous-batch offset dans une page eBay
        let isSyncing = true;


        if (!url) {
            console.error('Sync URL is empty!');
            alert(TEXT_ERROR_SYNC_URL);
            return;
        }

        // Show progress container
        $('#sync-progress-container').removeClass('d-none');
        $('#button-sync-marketplace, #button-force-refresh').prop('disabled', true);
        $('#sync-progress-bar').css('width', '0%').text('0%')
            .removeClass('bg-danger bg-success').addClass('progress-bar-animated');

        function syncPage() {
            if (!isSyncing) {
                return;
            }


            $.ajax({
                url: url + '&page=' + currentPage + '&offset=' + currentOffset + '&account_id=1',
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $('#sync-message').text(`Syncing page ${currentPage}${totalPages > 1 ? ' of ' + totalPages : ''}...`);
                },
                success: function(response) {

                    if (response.success) {
                        // Update totals if available
                        if (response.total_pages) {
                            totalPages = response.total_pages;
                        }
                        if (response.processed) {
                            totalProcessed += response.processed;
                        }

                        // Calculate and update progress
                        const progress = totalPages > 0 ? Math.round((currentPage / totalPages) * 100) : 0;
                        $('#sync-progress-bar').css('width', progress + '%')
                            .text(progress + '%');
                        
                        // Show message with skipped count if available
                        let messageText = response.message || `Processed ${totalProcessed} products (page ${currentPage}/${totalPages})`;
                        $('#sync-message').text(messageText);

                        if (response.completed) {
                            // Sync complet (dernière page, dernier batch)
                            $('#sync-progress-bar').removeClass('progress-bar-animated')
                                .addClass('bg-success')
                                .css('width', '100%')
                                .text('100%');
                            $('#sync-message').text(`Synchronization complete! Total: ${totalProcessed} products updated`);
                            $('#button-sync-marketplace, #button-force-refresh').prop('disabled', false);
                            showSuccessMessage(`eBay sync completed: ${totalProcessed} products updated`);
                            
                            // Refresh analytics data after 2 seconds
                            setTimeout(function() {
                                loadAnalyticsData(currentPeriod);
                                setTimeout(function() {
                                    $('#sync-progress-container').fadeOut();
                                }, 1000);
                            }, 2000);
                            
                            isSyncing = false;
                        } else if (response.page_complete) {
                            // Page eBay terminée → passer à la page suivante
                            currentPage++;
                            currentOffset = 0;
                            setTimeout(syncPage, 500);
                        } else {
                            // Batch partiel → continuer sur la même page avec le prochain offset
                            currentOffset = response.next_offset || (currentOffset + 20);
                            setTimeout(syncPage, 300);
                        }
                    } else {
                        // Error
                        $('#sync-progress-bar').removeClass('progress-bar-animated')
                            .addClass('bg-danger');
                        $('#sync-message').text('Error: ' + (response.error || 'Unknown error'));
                        $('#button-sync-marketplace, #button-force-refresh').prop('disabled', false);
                        showErrorMessage(response.error || 'Sync failed');
                        isSyncing = false;
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Sync error:', error);
                    console.error('XHR:', xhr);
                    $('#sync-progress-bar').removeClass('progress-bar-animated')
                        .addClass('bg-danger');
                    $('#sync-message').text('Network error occurred');
                    $('#button-sync-marketplace, #button-force-refresh').prop('disabled', false);
                    showErrorMessage('Failed to sync with marketplace. Check console for details.');
                    isSyncing = false;
                }
            });
        }

        // Start first page
        syncPage();
    }

    /**
     * Initialise les tooltips Bootstrap
     */
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Initialise les boutons de mise à jour de quantité eBay
     */
    function initUpdateEbayQuantityButtons() {
        $(document).on('click', '.update-ebay-qty', function() {
            const button = $(this);
            const productId = button.data('product-id');
            const quantity = button.data('quantity');
            
            // Get language strings
            const confirmMsg = $('#lang_update_confirm').val().replace('%s', quantity);
            const updatingMsg = $('#lang_updating').val();
            const successMsg = $('#lang_update_success').val();
            const errorMsg = $('#lang_update_error').val();
            
            if (!confirm(confirmMsg)) {
                return;
            }
            
            button.prop('disabled', true);
            button.html('<i class="fa-solid fa-spinner fa-spin"></i> ' + updatingMsg);
            
            $.ajax({
                url: 'index.php?route=shopmanager/inventory/sync.updateEbayQuantity&user_token=' + $('#user_token').val(),
                type: 'POST',
                data: { product_id: productId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message OpenCart 4 style
                        $('#alert').prepend('<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check"></i> ' + response.message + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                        
                        // Remove the row from the table
                        button.closest('tr').fadeOut(300, function() {
                            $(this).remove();
                            
                            // Update the mismatch count in the stat card only (no count in tab anymore)
                            const statCard = $('.stat-card[data-tab="mismatch"] .stat-number');
                            const currentCount = parseInt(statCard.text()) || 0;
                            if (currentCount > 0) {
                                const newCount = currentCount - 1;
                                statCard.text(newCount);
                            }
                            
                            // Update the print button count
                            const remainingRows = $('#quantity-mismatch-table tbody tr:visible').length;
                            $('.print-all-report').text('Print All (' + remainingRows + ')');
                            
                            // If no more rows, show a message
                            if (remainingRows === 0) {
                                $('#quantity-mismatch-table tbody').html('<tr><td colspan="8" class="text-center">No quantity mismatches found</td></tr>');
                            }
                        });
                    } else {
                        $('#alert').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i> ' + errorMsg.replace('%s', response.error) + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                        button.prop('disabled', false);
                        button.html('<i class="fa-solid fa-sync"></i>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    let errorMessage = error;
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.error || error;
                    } catch(e) {
                        // If response is not JSON, show first 200 chars
                        errorMessage = xhr.responseText.substring(0, 200);
                    }
                    $('#alert').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i> ' + errorMsg.replace('%s', errorMessage) + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                    button.prop('disabled', false);
                    button.html('<i class="fa-solid fa-sync"></i>');
                }
            });
        });
    }

    /**
     * Initialise les boutons de synchronisation individuelle des produits
     */
    function initSyncProductButtons() {
        $(document).on('click', '.sync-product-btn', function() {
            const button = $(this);
            const productId = button.data('product-id');
            const productName = button.data('product-name');
            
            if (!confirm(TEXT_CONFIRM_SYNC_PRODUCT.replace('%s', productName))) {
                return;
            }
            
            button.prop('disabled', true);
            button.html('<i class="fa-solid fa-spinner fa-spin"></i> Syncing...');
            
            $.ajax({
                url: 'index.php?route=shopmanager/inventory/sync.syncSingleProduct&user_token=' + $('#user_token').val(),
                type: 'POST',
                data: { product_id: productId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#alert').prepend('<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check"></i> ' + response.message + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                        
                        // Remove the row from the table
                        button.closest('tr').fadeOut(300, function() {
                            $(this).remove();
                            
                            // Check if table is empty
                            const tbody = $('#not-synced tbody');
                            if (tbody.find('tr').length === 0) {
                                tbody.parent().parent().html('<div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> All products are synced!</div>');
                            }
                        });
                    } else {
                        $('#alert').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i> Error: ' + response.error + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                        button.prop('disabled', false);
                        button.html('<i class="fa-solid fa-sync"></i> Sync');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    let errorMessage = error;
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.error || error;
                    } catch(e) {
                        errorMessage = xhr.responseText.substring(0, 200);
                    }
                    $('#alert').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i> Error: ' + errorMessage + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                    button.prop('disabled', false);
                    button.html('<i class="fa-solid fa-sync"></i> Sync');
                }
            });
        });
    }

    /**
     * Initialise les boutons d'impression de rapport
     */
    function initPrintReportButtons() {
        $(document).on('click', '.print-report', function() {
            const userToken = $('#user_token').val();
            
            // Open print window for ALL mismatch products (no product_id = all)
            const printUrl = 'index.php?route=shopmanager/inventory/sync.printMismatchReport&user_token=' + userToken;
            window.open(printUrl, '_blank', 'width=800,height=600');
        });
    }

    /**
     * Charge les données analytiques via AJAX
     * 
     * @param {string} period - Période sélectionnée (today, week, month, year)
     */
    function loadAnalyticsData(period) {
        if (isLoading) {
            return;
        }

        isLoading = true;
        showLoadingState();

        const url = $('#url_get_data').val();
        const userToken = $('#user_token').val();

        $.ajax({
            url: url + '&period=' + period,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#button-refresh').prop('disabled', true)
                    .html('<i class="fa-solid fa-spinner fa-spin"></i> Loading...');
            },
            success: function(data) {
                
                if (data.success) {
                    updateDashboard(data);
                    
                    // Update URL without reload
                    const newUrl = window.location.pathname + '?route=shopmanager/inventory/sync&user_token=' + userToken + '&period=' + period;
                    window.history.pushState({period: period}, '', newUrl);
                    
                    showSuccessMessage('Analytics data refreshed successfully!');
                } else {
                    showErrorMessage(data.error || 'Failed to load analytics data');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showErrorMessage('Failed to load analytics data. Please try again.');
            },
            complete: function() {
                isLoading = false;
                hideLoadingState();
                $('#button-refresh').prop('disabled', false)
                    .html('<i class="fa-solid fa-refresh"></i> Refresh');
            }
        });
    }

    /**
     * Met à jour le tableau de bord avec les nouvelles données
     * 
     * @param {object} data - Données analytiques
     */
    function updateDashboard(data) {
        // Update overview stats
        if (data.overview) {
            updateValue('#stat-total-products', data.overview.total_products);
            updateValue('#stat-revenue', '$' + formatNumber(data.overview.revenue, 2));
            updateValue('#stat-orders', data.overview.orders_count);
            updateValue('#stat-inventory-value', '$' + formatNumber(data.overview.inventory_value, 2));
        }

        // Animation de mise à jour
        animateStats();
    }

    /**
     * Met à jour une valeur avec animation
     * 
     * @param {string} selector - Sélecteur jQuery
     * @param {string|number} value - Nouvelle valeur
     */
    function updateValue(selector, value) {
        $(selector).fadeOut(200, function() {
            $(this).text(value).fadeIn(200);
        });
    }

    /**
     * Anime les cartes de statistiques
     */
    function animateStats() {
        $('#overview-section .card').each(function(index) {
            $(this).addClass('pulse-animation');
            setTimeout(() => {
                $(this).removeClass('pulse-animation');
            }, 500);
        });
    }

    /**
     * Affiche l'état de chargement
     */
    function showLoadingState() {
        // Add loading overlay
        if ($('#analytics-loading-overlay').length === 0) {
            $('<div id="analytics-loading-overlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.8); z-index: 9999;">' +
                '<div class="text-center">' +
                '<div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">' +
                '<span class="visually-hidden">Loading...</span>' +
                '</div>' +
                '<p class="mt-3 text-muted">Loading analytics data...</p>' +
                '</div>' +
                '</div>').appendTo('body');
        }
    }

    /**
     * Masque l'état de chargement
     */
    function hideLoadingState() {
        $('#analytics-loading-overlay').fadeOut(300, function() {
            $(this).remove();
        });
    }

    /**
     * Affiche un message de succès
     * 
     * @param {string} message - Message à afficher
     */
    function showSuccessMessage(message) {
        showToast(message, 'success');
    }

    /**
     * Affiche un message d'erreur
     * 
     * @param {string} message - Message à afficher
     */
    function showErrorMessage(message) {
        showToast(message, 'danger');
    }

    /**
     * Affiche un toast notification
     * 
     * @param {string} message - Message à afficher
     * @param {string} type - Type de toast (success, danger, warning, info)
     */
    function showToast(message, type) {
        const toast = $('<div class="toast align-items-center text-white bg-' + type + ' border-0 position-fixed top-0 end-0 m-3" role="alert" style="z-index: 10000;">' +
            '<div class="d-flex">' +
            '<div class="toast-body">' + message + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
            '</div>' +
            '</div>');
        
        $('body').append(toast);
        
        const bsToast = new bootstrap.Toast(toast[0], {
            autohide: true,
            delay: 3000
        });
        
        bsToast.show();
        
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    /**
     * Formate un nombre avec séparateurs de milliers
     * 
     * @param {number} number - Nombre à formater
     * @param {number} decimals - Nombre de décimales
     * @return {string} - Nombre formaté
     */
    function formatNumber(number, decimals = 0) {
        if (isNaN(number) || number === null) return '0';
        
        return parseFloat(number).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    /**
     * Export de fonctions pour utilisation externe
     */
    window.analyticsModule = {
        loadData: loadAnalyticsData,
        refresh: function() {
            loadAnalyticsData(currentPeriod);
        },
        getCurrentPeriod: function() {
            return currentPeriod;
        },
        importMarketplace: startMarketplaceSync
    };

    /**
     * Initialise les boutons de synchronisation bidirectionnelle Price
     */
    $(document).on('click', '.sync-price-btn', function() {
        const productId = $(this).data('product-id');
        const direction = $(this).data('direction');
        const userToken = $('#user_token').val();
        
        const confirmMsg = direction === 'to_ebay' 
            ? 'Sync local price to eBay?'
            : 'Update local price from eBay?';
        
        if (!confirm(confirmMsg)) return;
        
        const route = direction === 'to_ebay' 
            ? 'shopmanager/inventory/sync.syncPriceToEbay'
            : 'shopmanager/inventory/sync.syncPriceFromEbay';
        
        $.ajax({
            url: 'index.php?route=' + route + '&user_token=' + userToken,
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            beforeSend: function() {
                showLoadingState();
            },
            success: function(json) {
                hideLoadingState();
                if (json.success) {
                    showSuccessMessage(json.success);
                    loadAnalyticsData(currentPeriod); // Reload data
                } else if (json.error) {
                    showErrorMessage(json.error);
                }
            },
            error: function(xhr, status, error) {
                hideLoadingState();
                showErrorMessage('Error: ' + error);
            }
        });
    });

    /**
     * Initialise les boutons de synchronisation bidirectionnelle Quantity
     */
    $(document).on('click', '.sync-quantity-btn', function() {
        const productId = $(this).data('product-id');
        const direction = $(this).data('direction');
        const userToken = $('#user_token').val();
        
        const confirmMsg = direction === 'to_ebay' 
            ? 'Sync local quantity to eBay?'
            : 'Update local quantity from eBay?';
        
        if (!confirm(confirmMsg)) return;
        
        const route = direction === 'to_ebay' 
            ? 'shopmanager/inventory/sync.syncQuantityToEbay'
            : 'shopmanager/inventory/sync.syncQuantityFromEbay';
        
        $.ajax({
            url: 'index.php?route=' + route + '&user_token=' + userToken,
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            beforeSend: function() {
                showLoadingState();
            },
            success: function(json) {
                hideLoadingState();
                if (json.success) {
                    showSuccessMessage(json.success);
                    // Supprimer la ligne visuellement (feedback immédiat)
                    $('[data-product-id="' + productId + '"].sync-quantity-btn').closest('tr').fadeOut(300, function() {
                        $(this).remove();
                    });
                    // Recharger le tab qty mismatch (produit fixé = disparaît de la liste)
                    $.ajax({
                        url: 'index.php?route=shopmanager/inventory/sync.getQtyMismatchTab&user_token=' + userToken,
                        type: 'GET',
                        data: { page: 1, sort: 'product_id', order: 'ASC' },
                        dataType: 'html',
                        success: function(html) {
                            $('#qty-mismatch').html(html);
                        }
                    });
                    loadAnalyticsData(currentPeriod); // Reload compteurs analytics
                } else if (json.error) {
                    showErrorMessage(json.error);
                }
            },
            error: function(xhr, status, error) {
                hideLoadingState();
                showErrorMessage('Error: ' + error);
            }
        });
    });

    /**
     * Initialise les boutons de synchronisation bidirectionnelle Specifics
     */
    $(document).on('click', '.sync-specifics-btn', function() {
        const productId = $(this).data('product-id');
        const direction = $(this).data('direction');
        const userToken = $('#user_token').val();
        
        const confirmMsg = direction === 'to_ebay' 
            ? 'Sync local specifics to eBay?'
            : 'Update local specifics from eBay?';
        
        if (!confirm(confirmMsg)) return;
        
        const route = direction === 'to_ebay' 
            ? 'shopmanager/inventory/sync.syncSpecificsToEbay'
            : 'shopmanager/inventory/sync.syncSpecificsFromEbay';
        
        $.ajax({
            url: 'index.php?route=' + route + '&user_token=' + userToken,
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            beforeSend: function() {
                showLoadingState();
            },
            success: function(json) {
                hideLoadingState();
                if (json.success) {
                    showSuccessMessage(json.success);
                    loadAnalyticsData(currentPeriod); // Reload data
                } else if (json.error) {
                    showErrorMessage(json.error);
                }
            },
            error: function(xhr, status, error) {
                hideLoadingState();
                showErrorMessage('Error: ' + error);
            }
        });
    });

    /**
     * Initialise le bouton Refresh Item - Récupère toutes les infos eBay d'un seul coup
     */
    $(document).on('click', '.refresh-item-btn', function() {
        const productId = $(this).data('product-id');
        const userToken = $('#user_token').val();
        const $button = $(this);
        
        if (!confirm(TEXT_CONFIRM_REFRESH_ALL)) {
            return;
        }
        
        $.ajax({
            url: 'index.php?route=shopmanager/inventory/sync.refreshItemFromEbay&user_token=' + userToken,
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            beforeSend: function() {
                $button.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Loading...');
                showLoadingState();
            },
            success: function(json) {
                hideLoadingState();
                $button.prop('disabled', false).html('<i class="fa-solid fa-rotate"></i> Refresh');
                
                if (json.success) {
                    let message = 'Item refreshed successfully from eBay!';
                    if (json.data) {
                        message += `<br><small>Price: $${json.data.price} ${json.data.currency} | Qty: ${json.data.quantity_available}`;
                        if (json.data.specifics_updated) {
                            message += ' | Specifics: ✓';
                        }
                        message += '</small>';
                    }
                    showSuccessMessage(message);
                    
                    // Reload data to show updated values
                    setTimeout(function() {
                        loadAnalyticsData(currentPeriod);
                    }, 1000);
                } else if (json.error) {
                    showErrorMessage('Error: ' + json.error);
                }
            },
            error: function(xhr, status, error) {
                hideLoadingState();
                $button.prop('disabled', false).html('<i class="fa-solid fa-rotate"></i> Refresh');
                showErrorMessage('Error: ' + error);
            }
        });
    });

})();
