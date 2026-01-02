/**
 * PJLV Export Utilities
 * Provides retry logic and visual feedback for export operations.
 */

(function () {
    'use strict';

    // Configuration
    const MAX_RETRIES = 3;
    const RETRY_DELAY = 1000; // ms

    /**
     * Show a toast notification
     */
    function showToast(message, type = 'info') {
        // Use bootstrap-notify if available
        if (typeof $.notify !== 'undefined') {
            $.notify({
                message: message
            }, {
                type: type,
                placement: { from: 'top', align: 'right' },
                delay: 3000,
                animate: { enter: 'animated fadeInDown', exit: 'animated fadeOutUp' }
            });
            return;
        }

        // Fallback: create simple toast
        var toast = document.createElement('div');
        toast.className = 'pjlv-toast pjlv-toast-' + type;
        toast.textContent = message;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 4px;
            color: white;
            font-size: 14px;
            z-index: 10000;
            animation: fadeIn 0.3s ease;
        `;

        var colors = {
            info: '#00bcd4',
            success: '#4caf50',
            warning: '#ff9800',
            danger: '#f44336'
        };
        toast.style.backgroundColor = colors[type] || colors.info;

        document.body.appendChild(toast);

        setTimeout(function () {
            toast.style.animation = 'fadeOut 0.3s ease';
            setTimeout(function () {
                toast.remove();
            }, 300);
        }, 3000);
    }

    /**
     * Show spinner on button
     */
    function showSpinner(button) {
        var originalContent = button.innerHTML;
        button.setAttribute('data-original-content', originalContent);
        button.innerHTML = '<i class="material-icons spin">autorenew</i> Exporting...';
        button.disabled = true;
        button.classList.add('btn-loading');
    }

    /**
     * Hide spinner on button
     */
    function hideSpinner(button) {
        var originalContent = button.getAttribute('data-original-content');
        if (originalContent) {
            button.innerHTML = originalContent;
        }
        button.disabled = false;
        button.classList.remove('btn-loading');
    }

    /**
     * Export with retry logic
     */
    function exportWithRetry(url, retries = 0) {
        return fetch(url, {
            method: 'GET',
            credentials: 'same-origin'
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('Export failed: ' + response.statusText);
            }
            return response.blob();
        }).catch(function (error) {
            if (retries < MAX_RETRIES) {
                showToast('Retrying export... (Attempt ' + (retries + 2) + '/' + (MAX_RETRIES + 1) + ')', 'warning');
                return new Promise(function (resolve) {
                    setTimeout(function () {
                        resolve(exportWithRetry(url, retries + 1));
                    }, RETRY_DELAY);
                });
            }
            throw error;
        });
    }

    /**
     * Download blob as file
     */
    function downloadBlob(blob, filename) {
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        a.remove();
    }

    /**
     * Handle export click
     */
    function handleExportClick(event) {
        var link = event.currentTarget;
        var url = link.href;
        var isPdf = url.indexOf('pdf') > -1;
        var filename = isPdf ? 'report.pdf' : 'report.csv';

        // Prevent default link behavior
        event.preventDefault();

        // Show loading state
        showSpinner(link);
        showToast('Starting export...', 'info');

        // Export with retry
        exportWithRetry(url)
            .then(function (blob) {
                downloadBlob(blob, filename);
                showToast('Export completed successfully!', 'success');
            })
            .catch(function (error) {
                console.error('Export failed:', error);
                showToast('Export failed after ' + (MAX_RETRIES + 1) + ' attempts. Please try again.', 'danger');
            })
            .finally(function () {
                hideSpinner(link);
            });
    }

    /**
     * Initialize export handlers
     */
    function init() {
        // Find all export links
        var exportLinks = document.querySelectorAll('a[href*="/export"], a[href*="export-pdf"]');

        exportLinks.forEach(function (link) {
            link.addEventListener('click', handleExportClick);
        });
    }

    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose API globally
    window.PJLVExport = {
        showToast: showToast,
        exportWithRetry: exportWithRetry,
        init: init
    };

    // Add CSS for spinner animation
    var style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .spin {
            animation: spin 1s linear infinite;
        }
        .btn-loading {
            opacity: 0.8;
            pointer-events: none;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
    `;
    document.head.appendChild(style);

})();
