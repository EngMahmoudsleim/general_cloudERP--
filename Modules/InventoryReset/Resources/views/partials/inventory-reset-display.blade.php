{{-- JavaScript to enhance display of inventory reset transactions --}}
<script type="text/javascript">
    $(document).ready(function() {
    // Localized strings
    var lang_stock_adjustment = "{{ __('inventoryreset::lang.stock_adjustment') }}";
    var lang_inventory_reset = "{{ __('inventoryreset::lang.inventory_reset_label') }}";

    // Function to enhance inventory reset display
    function enhanceInventoryResetDisplay() {
        // Always run on all pages since inventory resets can appear anywhere

            // Look for tables that might contain stock adjustment data
            $('table tbody tr').each(function() {
                var $row = $(this);
                var refNoCell = $row.find('td').filter(function() {
                    return $(this).text().trim().match(/^IR\d{4}\/\d{4,}$/);
                });

                if (refNoCell.length > 0) {
                    // This row contains an inventory reset transaction
                    // Find the type cell and update it
                    var typeCell = $row.find('td').filter(function() {
                        return $(this).text().trim() === lang_stock_adjustment;
                    });

                    if (typeCell.length > 0) {
                        typeCell.html('<span class="label label-info">' + lang_inventory_reset + '</span>');
                    }
                }
            });

            // Handle stock history modals and embedded tables specifically
            $('table').each(function() {
                var $table = $(this);
                // Look for stock history table structure
                $table.find('tbody tr').each(function() {
                    var $row = $(this);
                    var cells = $row.find('td');

                    // Check if this looks like a stock history row with IR reference
                    cells.each(function(index) {
                        var cellText = $(this).text().trim();
                        if (cellText.match(/^IR\d{4}\/\d{4,}$/)) {
                            // Found IR reference, look for Stock Adjustment in the same row
                            $row.find('td').each(function() {
                                if ($(this).text().trim() === lang_stock_adjustment) {
                                    $(this).html('<span class="label label-info">' + lang_inventory_reset + '</span>');
                                }
                            });
                        }
                    });
                });
            });

            // Also look for DataTables if they exist
            if (typeof $.fn.DataTable !== 'undefined') {
                $('table.dataTable').each(function() {
                    var table = $(this).DataTable();
                    if (table) {
                        // Redraw to trigger our custom formatting
                        setTimeout(function() {
                            enhanceInventoryResetDisplay();
                        }, 500);
                    }
                });
            }
        }
    }

    // Run enhancement on page load
    enhanceInventoryResetDisplay();

    // Run enhancement when DataTables are redrawn
    $(document).on('draw.dt', function() {
        setTimeout(enhanceInventoryResetDisplay, 100);
    });

    // Run enhancement when AJAX content is loaded
    $(document).ajaxComplete(function() {
        setTimeout(enhanceInventoryResetDisplay, 100);
    });

    // Monitor for dynamically added content
    if (typeof MutationObserver !== 'undefined') {
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    setTimeout(enhanceInventoryResetDisplay, 100);
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
});
</script>

<style>
    /* Custom styling for Inventory Reset labels */
    .label-inventory-reset {
        background-color: #337ab7;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
    }
</style>