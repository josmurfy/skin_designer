/**
 * Bootstrap 5 Helper Functions for OpenCart 4.x
 * Provides backward compatibility for modal operations
 */

/**
 * Show a Bootstrap modal (compatible with BS3/4/5)
 * @param {string|HTMLElement} modalSelector - Modal selector or element
 */
function resolveModalElement(modalSelector) {
    if (typeof modalSelector !== 'string') {
        return modalSelector;
    }

    var modalElement = document.querySelector(modalSelector);

    if (modalElement) {
        return modalElement;
    }

    var legacySelectorMap = {
        '#loadingModal': '#loading-popup',
        '#alert-popup': '#alert-error'
    };

    var fallbackSelector = legacySelectorMap[modalSelector];

    if (fallbackSelector) {
        return document.querySelector(fallbackSelector);
    }

    return null;
}

function showModal(modalSelector) {
    var modalElement = resolveModalElement(modalSelector);
    
    if (!modalElement) {
        console.warn('Modal element not found:', modalSelector);
        return;
    }

    // Non-Bootstrap overlay fallback (e.g., #loading-popup)
    if (!modalElement.classList.contains('modal')) {
        modalElement.style.display = 'block';
        return;
    }

    // Bootstrap 5
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
        var modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(modalElement);
        }
        modalInstance.show();
    }
    // Bootstrap 3/4 fallback
    else if (typeof $ === 'function' && typeof $(modalElement).modal === 'function') {
        $(modalElement).modal('show');
    }
    // Manual fallback
    else {
        modalElement.classList.add('show');
        modalElement.style.display = 'block';
        document.body.classList.add('modal-open');
        
        // Add backdrop
        var backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'modal-backdrop-' + (modalElement.id || 'temp');
        document.body.appendChild(backdrop);
    }
}

/**
 * Hide a Bootstrap modal (compatible with BS3/4/5)
 * @param {string|HTMLElement} modalSelector - Modal selector or element
 */
function hideModal(modalSelector) {
    var modalElement = resolveModalElement(modalSelector);
    
    if (!modalElement) {
        console.warn('Modal element not found:', modalSelector);
        return;
    }

    // Non-Bootstrap overlay fallback (e.g., #loading-popup)
    if (!modalElement.classList.contains('modal')) {
        modalElement.style.display = 'none';
        return;
    }

    // Bootstrap 5
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
        var modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
    }
    // Bootstrap 3/4 fallback
    else if (typeof $ === 'function' && typeof $(modalElement).modal === 'function') {
        $(modalElement).modal('hide');
    }
    // Manual fallback
    else {
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');
        
        // Remove backdrop
        var backdrop = document.getElementById('modal-backdrop-' + (modalElement.id || 'temp'));
        if (backdrop) {
            backdrop.remove();
        }
    }
}

/**
 * Toggle a Bootstrap modal (compatible with BS3/4/5)
 * @param {string|HTMLElement} modalSelector - Modal selector or element
 */
function toggleModal(modalSelector) {
    var modalElement = resolveModalElement(modalSelector);
    
    if (!modalElement) {
        console.warn('Modal element not found:', modalSelector);
        return;
    }

    // Check if modal is shown
    var isShown = modalElement.classList.contains('show');
    
    if (isShown) {
        hideModal(modalElement);
    } else {
        showModal(modalElement);
    }
}

/**
 * Dispose a Bootstrap modal instance (compatible with BS5)
 * @param {string|HTMLElement} modalSelector - Modal selector or element
 */
function disposeModal(modalSelector) {
    var modalElement = resolveModalElement(modalSelector);
    
    if (!modalElement) {
        return;
    }

    // Bootstrap 5
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
        var modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.dispose();
        }
    }
}

/**
 * Initialize tabs (compatible with BS3/4/5)
 * @param {string} tabSelector - Tab link selector
 */
function initializeTabs(tabSelector) {
    var tabs = document.querySelectorAll(tabSelector || '[data-bs-toggle="tab"]');
    
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Bootstrap 5
            if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tab === 'function') {
                var tabInstance = new bootstrap.Tab(tab);
                tabInstance.show();
            }
            // jQuery fallback
            else if (typeof $ === 'function') {
                $(tab).tab('show');
            }
            // Manual implementation
            else {
                var target = tab.getAttribute('href') || tab.getAttribute('data-bs-target');
                if (target) {
                    // Hide all tab panes
                    var tabContent = tab.closest('.nav-tabs')?.nextElementSibling;
                    if (tabContent) {
                        tabContent.querySelectorAll('.tab-pane').forEach(function(pane) {
                            pane.classList.remove('show', 'active');
                        });
                    }
                    
                    // Show target pane
                    var targetPane = document.querySelector(target);
                    if (targetPane) {
                        targetPane.classList.add('show', 'active');
                    }
                    
                    // Update tab active state
                    tab.closest('.nav-tabs')?.querySelectorAll('.nav-link').forEach(function(link) {
                        link.classList.remove('active');
                    });
                    tab.classList.add('active');
                }
            }
        });
    });
}

// Export for use in other modules if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        showModal: showModal,
        hideModal: hideModal,
        toggleModal: toggleModal,
        disposeModal: disposeModal,
        initializeTabs: initializeTabs
    };
}
