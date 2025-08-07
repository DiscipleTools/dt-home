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
  var typeSelect = document.getElementById("type");
  var urlFieldRow = document.getElementById("urlFieldRow");

  if (!typeSelect || !urlFieldRow) {
    return;
  }

  if (typeSelect.value === "Custom") {
    urlFieldRow.style.display = "none";
  } else {
    urlFieldRow.style.display = "";
  }
}

document.addEventListener("DOMContentLoaded", function () {
  toggleURLField();
});

document.addEventListener("DOMContentLoaded", function () {
  var nameInput = document.getElementById("name");
  var slugInput = document.getElementById("slug");

  if (!slugInput || !nameInput) {
    return;
  }

  if (slugInput.readOnly) {
    return;
  }

  if ((nameInput && slugInput.value) || !slugInput.value) {
    nameInput.addEventListener("input", function () {
      // Convert to lowercase and replace spaces with underscores

      var slug = nameInput.value
        .toLowerCase()
        .replace(/\s+/g, "_")
        // Remove characters that are not alphanumeric, underscores, or dashes
        .replace(/[^a-z0-9_-]/g, "");
      slugInput.value = slug;
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  var inputField = document.getElementById("slug");

  if (!inputField) {
    return;
  }

  inputField.addEventListener("keydown", function (event) {
    // Allow controls such as backspace
    if (
      event.key === "Backspace" ||
      event.key === "ArrowLeft" ||
      event.key === "ArrowRight" ||
      event.key === "Tab"
    ) {
      return; // Allow these keys
    }

    // Build the allowed pattern
    var regex = /^[a-zA-Z0-9-_]+$/;

    // Check if the pressed key combined with the current value matches the allowed pattern
    // Create the future value of the input to test with regex
    var futureValue = inputField.value + event.key;
    if (!regex.test(futureValue)) {
      event.preventDefault(); // Prevent the character if it does not match
    }
  });
});

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
        console.log('SortableTable: Starting initialization...'); // Debug log
        this.addDragHandles();
        this.bindEvents();
        console.log('SortableTable: Initialization complete');
    }

    addDragHandles() {
        console.log('SortableTable: Adding drag handles...'); // Debug log
        const tbody = this.table.querySelector('tbody');
        const thead = this.table.querySelector('thead');

        if (!tbody || !thead) {
            console.log('SortableTable: Missing tbody or thead'); // Debug log
            return;
        }

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
        console.log('SortableTable: Found', rows.length, 'rows'); // Debug log

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
        console.log('SortableTable: Drag start event'); // Debug log
        // Check if we're dragging a table row
        if (e.target.tagName.toLowerCase() === 'tr') {
            this.draggedRow = e.target;
            console.log('SortableTable: Dragging row:', e.target); // Debug log

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
        console.log('SortableTable: Updating order...'); // Debug log
        const tbody = this.table.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr:not(.drag-placeholder)');
        const orderedIds = [];
        console.log('SortableTable: Processing', rows.length, 'rows'); // Debug log
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

        // Auto-hide after 3 seconds
        setTimeout(() => {
            messageEl.style.display = 'none';
        }, 3000);
    }
}

// DT Home Admin JavaScript

document.addEventListener('DOMContentLoaded', function() {
  var mediaUploader;

  // Check if we're on the dt_home admin page
  const urlParams = new URLSearchParams(window.location.search);
  const page = urlParams.get('page');
  const tab = urlParams.get('tab');
  const action = urlParams.get('action');

  // Initialize sortable table functionality for admin pages
  if (page === 'dt_home' && (tab === 'app' || tab === 'training') && action !== 'create' && action !== 'available_app' && !action?.startsWith('edit')) {
    // Wait a bit for the DOM to fully load
    setTimeout(() => {
      // Find table
      const table = document.querySelector('.widefat');

      if (table) {
        console.log('Initializing SortableTable for:', tab); // Debug log
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
      } else {
        console.log('Table not found for SortableTable initialization'); // Debug log
      }
    }, 100);
  }

  // Add click event listener to the upload image button
  document
    .getElementById("upload_image_button")
    .addEventListener("click", function (e) {
      e.preventDefault(); // Prevent the default form submission behavior

      // If the media uploader already exists, open it
      if (mediaUploader) {
        mediaUploader.open();
        return;
      }

      // Create a new media uploader instance
      mediaUploader = wp.media.frames.file_frame = wp.media({
        title: "Choose Image", // Title of the media uploader
        button: {
          text: "Choose Image", // Text of the select button
        },
        multiple: false, // Disable multiple file selection
        library: {
          type: "image", // Ensure only images are selectable
        },
      });

      // Handle the image selection event
      mediaUploader.on("select", function () {
        var attachment = mediaUploader
          .state()
          .get("selection")
          .first()
          .toJSON(); // Get the selected image details

        // Check if the selected file is an image
        if (attachment.type !== "image") {
          alert("Please select an image file.");
          return;
        }

        // Set the selected image URL to the input field
        document.getElementById("dt_home_file_upload").value = attachment.url;
        // Display the selected image as a preview
        document.getElementById("image_preview").innerHTML =
          '<img src="' + attachment.url + '" class="image-preview">';
      });

      // Open the media uploader
      mediaUploader.open();
    });
});

/**
 * Handle bulk selecting/deselecting of Apps user role options.
 *
 * @function
 * @name bulkAppsUserRoleSelections
 * @returns {void}
 */
document.addEventListener("DOMContentLoaded", function () {
  const select_all = document.getElementById("select_all_user_roles");

  if (!select_all) {
    return;
  }

  // Check if all roles are checked on document load
  const roles = document.querySelectorAll("input.apps-user-role");
  const allSelected = Array.from(roles).every((role) => role.checked);
  select_all.checked = allSelected;

  // Listen for select all app user role clicks.
  select_all.addEventListener("click", (e) => {
    const roles = document.querySelectorAll("input.apps-user-role");
    for (let i = 0; i < roles.length; i++) {
      // Accordingly select/deselect user roles.
      roles[i].checked = select_all.checked;
    }
  });

  // Listen for individual user role clicks and update parent all option accordingly.
  document.querySelectorAll("input.apps-user-role").forEach((role) => {
    role.addEventListener("click", () => {
      if (!role.checked) {
        select_all.checked = false;
      } else {
        const allSelected = Array.from(
          document.querySelectorAll("input.apps-user-role"),
        ).every((role) => role.checked);
        select_all.checked = allSelected;
      }
    });
  });

  // Execute final pre-submission tasks.
  document.getElementById("submit").addEventListener("click", (e) => {
    // Capture unselected roles, to ensure they are removed within the backend.
    const deleted_roles_element = document.getElementById("deleted_roles");
    if (deleted_roles_element) {
      let deleted_roles = [];
      for (const role of document.querySelectorAll("input.apps-user-role")) {
        if (!role.checked) {
          deleted_roles.push(role.value);
        }
      }

      // Update deleted roles hidden field, ahead of final submission.
      deleted_roles_element.value = JSON.stringify(deleted_roles);
    }
  });
});

/**
 * Handles the selection and deselection of checkboxes for exporting apps.
 * Updates the state of the "Select All" checkbox and the export button.
 *
 * @function
 * @name handleCheckboxSelection
 * @returns {void}
 */
document.addEventListener("DOMContentLoaded", function () {
  // Get references to the DOM elements
  const selectAllCheckbox = document.getElementById("select_all_checkbox");
  const checkboxes = document.querySelectorAll(".app-checkbox");
  const exportButton = document.getElementById("exportButton");
  const exportPopup = document.getElementById("exportPopup");
  const exportTextarea = document.getElementById("exportTextarea");
  const copyButton = document.getElementById("copyButton");
  const closeButtons = document.querySelectorAll(".close-button");
  const overlay = document.getElementById("overlay");
  const shareTab = document.getElementById("shareTab");
  const copyTab = document.getElementById("copyTab");
  const shareContent = document.getElementById("shareContent");
  const copyContent = document.getElementById("copyContent");
  const exportLink = document.getElementById("exportLink");

  const qrCodeImage = document.getElementById("qrCodeImage");

  // Parse the apps data from the exportPopup element's data attribute
  const appsArray = JSON.parse(exportPopup.getAttribute("data-apps"));
  const appsData = Object.values(appsArray);

  // Function to update the state of the export button based on checkbox selection
  const updateExportButtonState = () => {
    const isAnyCheckboxChecked = Array.from(checkboxes).some(
      (checkbox) => checkbox.checked,
    );
    exportButton.disabled = !isAnyCheckboxChecked;
  };

  // Function to update the state of the "Select All" checkbox based on individual checkbox selection
  const updateSelectAllCheckboxState = () => {
    const areAllCheckboxesChecked = Array.from(checkboxes).every(
      (checkbox) => checkbox.checked,
    );
    selectAllCheckbox.checked = areAllCheckboxesChecked;
  };

  // Event listener for the "Select All" checkbox
  selectAllCheckbox.addEventListener("change", () => {
    checkboxes.forEach((checkbox) => {
      checkbox.checked = selectAllCheckbox.checked;
    });
    updateExportButtonState();
  });

  // Event listeners for individual checkboxes
  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      updateSelectAllCheckboxState();
      updateExportButtonState();
    });
  });

  // Event listener for the export button
  exportButton.addEventListener("click", () => {
    // Get the slugs of the selected apps
    const selectedSlugs = Array.from(checkboxes)
      .filter((checkbox) => checkbox.checked)
      .map((checkbox) => checkbox.value);

    // Filter the apps data to include only the selected apps
    const filteredApps = appsData.filter((app) =>
      selectedSlugs.includes(app.slug),
    );
    const siteDomain = exportPopup.getAttribute("data-site-domain");

    // Convert the selected apps data to a JSON string and include the site domain in the icon URLs
    const selectedValues = JSON.stringify(
      filteredApps.map((app) => {
        if (
          app.icon &&
          app.icon.startsWith("/") &&
          !app.icon.startsWith("mdi")
        ) {
          app.icon = siteDomain + app.icon;
        }
        return app;
      }),
      null,
      2,
    );

    // Generate the endpoint URL with the selected slugs as query parameters

    const endpointUrl = `${exportPopup.getAttribute("data-site-domain")}/apps/json?${selectedSlugs.map((slug) => `slugs[]=${encodeURIComponent(slug)}`).join("&")}`;

    // Set the value of the export link input field
    exportLink.value = endpointUrl;

    // Generate the QR code URL
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&color=323a68&data=${encodeURIComponent(endpointUrl)}`;
    qrCodeImage.src = qrCodeUrl;
    // Display the JSON representation of the selected apps in the textarea
    exportTextarea.value = selectedValues;

    // Show the import modal with tabs
    exportPopup.style.display = "block";
    overlay.style.display = "block";
    document.body.style.overflow = "hidden";

    // Set the default tab to Share
    shareTab.classList.add("active");
    copyTab.classList.remove("active");
    shareContent.style.display = "block";
    copyContent.style.display = "none";
  });

  // Event listener for the copy button
  copyButton.addEventListener("click", async () => {
    try {
      // Copy the JSON representation of the selected apps to the clipboard
      await navigator.clipboard.writeText(exportTextarea.value);
      exportTextarea.classList.add("copied");
    } catch (err) {
      console.error("Failed to copy the Apps: ", err);
    }
  });

  // Event listeners for the close buttons
  closeButtons.forEach((button) => {
    button.addEventListener("click", () => {
      // Close the export popup and reset the styles
      exportPopup.style.display = "none";
      overlay.style.display = "none";
      document.body.style.overflow = "auto";
      exportTextarea.classList.remove("copied");
    });
  });

  // Event listeners for the tabs
  shareTab.addEventListener("click", () => {
    shareTab.classList.add("active");
    copyTab.classList.remove("active");
    shareContent.style.display = "block";
    copyContent.style.display = "none";
  });

  copyTab.addEventListener("click", () => {
    copyTab.classList.add("active");
    shareTab.classList.remove("active");
    shareContent.style.display = "none";
    copyContent.style.display = "block";
  });
});

/**
 * Handles copying the app data to the clipboard.
 *
 * @function
 * @name copyApp
 * @param {string} slug - The slug of the app to copy.
 * @param {HTMLElement} element - The element that triggered the copy action.
 * @returns {void}
 */
document.addEventListener("DOMContentLoaded", function () {
  const exportPopup = document.getElementById("exportPopup");
  const appsArray = JSON.parse(exportPopup.getAttribute("data-apps"));
  const appsData = Object.values(appsArray);

  window.copyApp = function (slug, element) {
    const app = appsData.find(function (app) {
      return app.slug === slug;
    });
    if (app) {
      const appJson = JSON.stringify(app, null, 2);
      const textarea = document.createElement("textarea");
      textarea.value = appJson;
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand("copy");
      document.body.removeChild(textarea);
      element.innerHTML = '<i class="fas fa-check action-icon"></i>';
      setTimeout(() => {
        element.innerHTML = '<i class="fas fa-copy action-icon"></i>';
      }, 5000);
    }
  };
});

/**
 * Handle apps importing flow functionality.
 *
 * @function
 * @name importApps
 * @returns {void}
 */
jQuery(document).ready(function ($) {
  const import_apps_but = $("#import_apps_but");

  if (!import_apps_but) {
    return;
  }

  // Listen for import apps button clicks.
  $(import_apps_but).click(function (e) {
    const dialog = $("#apps_settings_dialog_placeholder");
    if (dialog) {
      // Configure new dialog instance.
      dialog.dialog({
        modal: true,
        autoOpen: false,
        hide: "fade",
        show: "fade",
        height: "auto",
        width: "auto",
        resizable: false,
        title: "Import Apps",
        buttons: [
          {
            text: "Cancel",
            icon: "ui-icon-close",
            click: function (e) {
              $(this).dialog("close");
            },
          },
          {
            text: "Import",
            icon: "ui-icon-circle-arrow-n",
            click: function (e) {
              import_apps($(this));
            },
          },
        ],
        open: function (event, ui) {},
        close: function (event, ui) {},
      });

      // Populate main dialog body.
      dialog.html(build_dialog_import_apps_html());

      // Display configured dialog.
      dialog.dialog("open");
    }
  });

  function build_dialog_import_apps_html() {
    return `
        <p>Please enter below the apps settings json structure to be imported.</p>
        <textarea id="import_apps_textarea" rows="25" cols="75"></textarea>
    `;
  }

  function import_apps(dialog) {
    // Obtain handle to textarea and fetch contents.
    const import_apps_textarea = $("#import_apps_textarea");

    try {
      // Sanity check by parsing submitted content; which should be a json structure.
      const json = $.parseJSON(import_apps_textarea.val());

      // On a successful parse, proceed with import post request.
      $.ajax({
        type: "POST",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        data: JSON.stringify(json),
        url: `${window.dt_admin_scripts.site_url}/apps/json/`,
        beforeSend: (xhr) => {
          xhr.setRequestHeader("X-WP-Nonce", window.dt_admin_scripts.nonce);
        },
      })
        .done(function (response) {
          window.location.reload();
        })
        .fail(function (fail) {
          window.location.reload();
        });
    } catch (err) {
      // Return focus to textarea, to prompt admin of error.
      import_apps_textarea.focus();
    }
  }
});

/**
 * Handle apps icons toggle display.
 *
 * @function
 * @name appsIconTabsToggle
 * @returns {void}
 */
jQuery(document).ready(function ($) {
  $("a.app-icon-tab").click(function (e) {
    // Deactivate all tabs and activate selected tab.
    const selected_tab = $(e.currentTarget);
    $(selected_tab)
      .parent()
      .find(".nav-tab-active")
      .removeClass("nav-tab-active");
    $(selected_tab).addClass("nav-tab-active");

    // Toggle tab content.
    $(selected_tab)
      .parent()
      .parent()
      .find("div.app-icon-tab-content")
      .children()
      .slideUp("fast", function () {
        // Obtain handle onto tab div by specified class id and fade in.
        $(`div.${$(selected_tab).data("tab")}`).slideDown("fast");
      });
  });

  $("i.app-color-reset").click(function (e) {
    const color_id = $(e.currentTarget).data("color");
    const color_input = $(`#${color_id}`);
    const color_input_hidden = $(`#${color_id}_hidden`);

    /**
     * Remove color value; which will most likely revert to black (#000000);
     * therefore, also signal with `delete` flag.
     */
    $(color_input).val("");
    $(color_input_hidden).val("deleted");
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const scrollableTable = document.querySelector(".scrollable-table");
  const scrollTip = createScrollTip();

  function createScrollTip() {
    const tip = document.createElement("div");
    tip.classList.add("scroll-tip");
    tip.innerHTML = `<i class="fas fa-arrow-right" style="font-size: 10px; color: white;"></i>`;

    const tooltip = document.createElement("span");
    tooltip.classList.add("tooltip-text");
    tooltip.innerText = "You can scroll the table";
    tip.appendChild(tooltip);

    return tip;
  }

  function handleResize() {
    if (window.innerWidth <= 767) {
      if (!scrollableTable.contains(scrollTip)) {
        scrollableTable.appendChild(scrollTip);
      }
    } else {
      if (scrollableTable.contains(scrollTip)) {
        scrollTip.remove();
      }
    }
  }

  window.addEventListener("resize", handleResize);
  handleResize(); // Initial check

  // Add touch event listeners for iOS devices
  scrollTip.addEventListener("touchstart", function () {
    scrollTip.classList.add("touch-active");
  });

  scrollTip.addEventListener("touchend", function () {
    setTimeout(function () {
      scrollTip.classList.remove("touch-active");
    }, 3000); // Adjust the timeout as needed
  });

  // Add click event listener to nudge the table slightly to the right
  scrollTip.addEventListener("click", function () {
    scrollableTable.scrollBy({
      left: 100, // Adjust the value to control the nudge distance
      behavior: "smooth",
    });
  });
});
