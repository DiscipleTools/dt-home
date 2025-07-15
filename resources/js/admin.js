import '../css/admin.css'
import '@disciple.tools/web-components'

/**
 * Toggles the visibility of the URL field based on the selected type.
 *
 * @function
 * @name toggleURLField
 * @returns {void}
 */

function toggleURLField() {
    var typeSelect = document.getElementById('type')
    var urlFieldRow = document.getElementById('urlFieldRow')

    if (!typeSelect || !urlFieldRow) {
        return
    }

    if (typeSelect.value === 'Custom') {
        urlFieldRow.style.display = 'none'
    } else {
        urlFieldRow.style.display = ''
    }
}

document.addEventListener('DOMContentLoaded', function () {
    toggleURLField()
})

document.addEventListener('DOMContentLoaded', function () {

    var nameInput = document.getElementById('name')
    var slugInput = document.getElementById('slug')

    if (!slugInput || !nameInput) {
        return
    }

    if (slugInput.readOnly) {
        return
    }

    if (nameInput && slugInput.value || !slugInput.value) {
        nameInput.addEventListener('input', function () {
            // Convert to lowercase and replace spaces with underscores

            var slug = nameInput.value
                .toLowerCase()
                .replace(/\s+/g, '_')
                // Remove characters that are not alphanumeric, underscores, or dashes
                .replace(/[^a-z0-9_\-]/g, '')
            slugInput.value = slug
        })
    }
})

document.addEventListener('DOMContentLoaded', function () {
    var inputField = document.getElementById('slug')

    if (!inputField) {
        return
    }

    inputField.addEventListener('keydown', function (event) {
        // Allow controls such as backspace
        if (
            event.key === 'Backspace' ||
            event.key === 'ArrowLeft' ||
            event.key === 'ArrowRight' ||
            event.key === 'Tab'
        ) {
            return // Allow these keys
        }

        // Build the allowed pattern
        var regex = /^[a-zA-Z0-9-_]+$/

        // Check if the pressed key combined with the current value matches the allowed pattern
        // Create the future value of the input to test with regex
        var futureValue = inputField.value + event.key
        if (!regex.test(futureValue)) {
            event.preventDefault() // Prevent the character if it does not match
        }
    })
})

/**
 * SortableTable class for drag & drop reordering
 */
class SortableTable {
    constructor(selector, config) {
        this.table = document.querySelector(selector);
        this.config = config;
        this.draggedRow = null;
        this.placeholder = null;
        
        if (this.table) {
            this.init();
        }
    }
    
    init() {
        this.addDragHandles();
        this.bindEvents();
        console.log('SortableTable: Initialization complete');
    }
    
    addDragHandles() {
        const tbody = this.table.querySelector('tbody');
        const thead = this.table.querySelector('thead');
        
        if (!tbody || !thead) return;
        
        // Add header column for drag handle
        const headerRow = thead.querySelector('tr');
        if (headerRow) {
            const handleHeader = document.createElement('th');
            handleHeader.style.border = '1px solid #ddd';
            handleHeader.style.width = '40px';
            handleHeader.style.textAlign = 'center';
            handleHeader.textContent = '';
            headerRow.insertBefore(handleHeader, headerRow.firstChild);

        }
        
        // Add drag handles to each row and make rows draggable
        const rows = tbody.querySelectorAll('tr');

        
        rows.forEach((row, index) => {
            // Make the entire row draggable
            row.draggable = true;
            row.style.cursor = 'move';
            
            // Add drag handle cell
            const handleCell = document.createElement('td');
            handleCell.className = 'drag-handle';
            handleCell.style.border = '1px solid #ddd';
            handleCell.style.textAlign = 'center';
            handleCell.style.cursor = 'grab';
            handleCell.style.backgroundColor = '#f9f9f9';
            
            const handleIcon = document.createElement('span');
            handleIcon.className = 'drag-icon';
            handleIcon.style.fontSize = '14px';
            handleIcon.style.color = '#666';
            handleIcon.style.cursor = 'grab';
            handleIcon.textContent = '⋮⋮';
            
            handleCell.appendChild(handleIcon);
            row.insertBefore(handleCell, row.firstChild);
            

        });
        

    }
    
    bindEvents() {
        const tbody = this.table.querySelector('tbody');
        if (!tbody) return;
        
        tbody.addEventListener('dragstart', this.handleDragStart.bind(this));
        tbody.addEventListener('dragover', this.handleDragOver.bind(this));
        tbody.addEventListener('dragenter', this.handleDragEnter.bind(this));
        tbody.addEventListener('drop', this.handleDrop.bind(this));
        tbody.addEventListener('dragend', this.handleDragEnd.bind(this));
    }
    
    handleDragStart(e) {
        // Check if we're dragging a table row
        if (e.target.tagName.toLowerCase() === 'tr') {
            this.draggedRow = e.target;
            
            // Create placeholder
            this.placeholder = document.createElement('tr');
            this.placeholder.className = 'drag-placeholder';
            this.placeholder.style.height = '2px';
            this.placeholder.style.backgroundColor = '#007cba';
            this.placeholder.innerHTML = '<td colspan="100%" style="height: 2px; padding: 0; border: none;"></td>';
            
            // Add some visual feedback to the dragged row
            e.target.style.opacity = '0.5';
            

            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', e.target.outerHTML);
        } else {
            // Prevent drag if not initiated from a row
            e.preventDefault();
        }
    }
    
    handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        
        if (!this.draggedRow || !this.placeholder) return;
        
        const afterElement = this.getDragAfterElement(e.clientY);
        const tbody = this.table.querySelector('tbody');
        
        if (!tbody) return;
        
        // Remove placeholder if it exists in DOM
        if (this.placeholder.parentNode) {
            this.placeholder.parentNode.removeChild(this.placeholder);
        }
        
        // Insert placeholder
        if (afterElement == null) {
            tbody.appendChild(this.placeholder);
        } else {
            tbody.insertBefore(this.placeholder, afterElement);
        }
    }
    
    handleDragEnter(e) {
        e.preventDefault();
    }
    
    handleDrop(e) {
        e.preventDefault();
        
        if (!this.draggedRow || !this.placeholder) return;
        
        // Get the parent of the placeholder
        const tbody = this.placeholder.parentNode;
        if (!tbody) return;
        
        // Replace placeholder with dragged row
        tbody.insertBefore(this.draggedRow, this.placeholder);
        tbody.removeChild(this.placeholder);
        
        // Reset styles
        this.draggedRow.style.opacity = '';
        

        this.updateOrder();
    }
    
    handleDragEnd(e) {
        // Clean up
        if (this.draggedRow) {
            this.draggedRow.style.opacity = '';
        }
        
        // Remove placeholder if it still exists
        if (this.placeholder && this.placeholder.parentNode) {
            this.placeholder.parentNode.removeChild(this.placeholder);
        }
        
        this.draggedRow = null;
        this.placeholder = null;
    }
    
    getDragAfterElement(y) {
        const tbody = this.table.querySelector('tbody');
        const draggableElements = [...tbody.querySelectorAll('tr:not(.drag-placeholder)')];
        
        return draggableElements.reduce((closest, child) => {
            if (child === this.draggedRow) return closest;
            
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }
    
    updateOrder() {
        const tbody = this.table.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr:not(.drag-placeholder)');
        const orderedIds = [];
        rows.forEach((row, index) => {
            if (this.config.type === 'app') {
                // For apps, look for slug in the action links
                const actionCell = row.cells[row.cells.length - 1]; // Last cell contains actions
                const actionLinks = actionCell.querySelectorAll('a[href*="action="]');
                
                for (let link of actionLinks) {
                    const href = link.getAttribute('href');
                    const match = href.match(/action=\w+\/([^&]+)/);
                    if (match) {
                        orderedIds.push(match[1]);
                        break;
                    }
                }
            } else if (this.config.type === 'training') {
                // For training, look for ID in the action links
                const actionCell = row.cells[row.cells.length - 1]; // Last cell contains actions
                const actionLinks = actionCell.querySelectorAll('a[href*="action="]');
                
                for (let link of actionLinks) {
                    const href = link.getAttribute('href');
                    const match = href.match(/action=\w+\/(\d+)/);
                    if (match) {
                        orderedIds.push(parseInt(match[1]));
                        break;
                    }
                }
            }
        });
        this.sendOrderUpdate(orderedIds);
    }
    
    sendOrderUpdate(orderedIds) {
        // Use GET request with query parameters (AJAX)
        const params = new URLSearchParams();
        params.append('ordered_ids', orderedIds.join(','));
        const url = `${this.config.endpoint}&${params.toString()}`;

        // Show a loading message
        this.showMessage('Saving order...', 'success');

        fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(response => {
            if (response.ok) {
                this.showMessage('Order saved!', 'success');
            } else {
                this.showMessage('Failed to save order.', 'error');
            }
        })
        .catch(() => {
            this.showMessage('Failed to save order.', 'error');
        });
    }
    
    showMessage(text, type) {
        // Create or update message element
        let messageEl = document.querySelector('.sortable-message');
        if (!messageEl) {
            messageEl = document.createElement('div');
            messageEl.className = 'sortable-message';
            messageEl.style.position = 'fixed';
            messageEl.style.bottom = '20px'; // Move to bottom
            messageEl.style.right = '20px';
            messageEl.style.top = '';
            messageEl.style.padding = '14px 28px';
            messageEl.style.borderRadius = '6px';
            messageEl.style.color = 'white';
            messageEl.style.fontWeight = 'bold';
            messageEl.style.fontSize = '1.25rem'; // Larger font
            messageEl.style.zIndex = '9999';
            document.body.appendChild(messageEl);
        }
        
        messageEl.textContent = text;
        messageEl.style.backgroundColor = type === 'success' ? '#46b450' : '#dc3232';
        messageEl.style.display = 'block';
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            messageEl.style.display = 'none';
        }, 10000);
    }
}

// DT Home Admin JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the dt_home admin page
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');
    const tab = urlParams.get('tab');
    const action = urlParams.get('action');
    
    // Skip drag and drop functionality when creating new apps or editing
    if (page === 'dt_home' && (tab === 'app' || tab === 'training') && action !== 'create' && action !== 'available_app' && !action?.startsWith('edit')) {
        // Wait a bit for the DOM to fully load
        setTimeout(() => {
            // Find table
            const table = document.querySelector('.widefat');
            
            if (table) {
                // Initialize sortable functionality
                if (tab === 'app') {
                    new SortableTable('.widefat', {
                        type: 'app',
                        endpoint: 'admin.php?page=dt_home&tab=app&action=reorder'
                    });
                } else if (tab === 'training') {
                    new SortableTable('.widefat', {
                        type: 'training',
                        endpoint: 'admin.php?page=dt_home&tab=training&action=reorder'
                    });
                }
            }
        }, 100); // Close setTimeout
    }
});
