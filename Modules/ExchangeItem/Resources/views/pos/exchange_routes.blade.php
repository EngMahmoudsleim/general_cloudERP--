<script>
// Exchange routes for POS functionality
window.exchangeRoutes = {
    searchTransaction: "{{ route('exchangeitem.search_transaction') }}",
    store: "{{ route('exchangeitem.store') }}",
    show: "{{ url('exchange') }}/"
};

// Exchange translations for POS
window.exchangeLang = {
    exchange: "@lang('exchangeitem::lang.exchange')",
    add_exchange: "@lang('exchangeitem::lang.add_exchange')",
    invoice_number: "@lang('exchangeitem::lang.invoice_number')",
    search_invoice: "@lang('exchangeitem::lang.search_invoice')",
    add_selected_items: "@lang('exchangeitem::lang.add_selected_items')",
    complete_exchange: "@lang('exchangeitem::lang.complete_exchange')",
    step_1_search_invoice: "@lang('exchangeitem::lang.step_1_search_invoice')",
    step_2_select_items: "@lang('exchangeitem::lang.step_2_select_items')",
    step_3_select_new_items: "@lang('exchangeitem::lang.step_3_select_new_items')",
    exchange_summary: "@lang('exchangeitem::lang.exchange_summary')",
    total_return_value: "@lang('exchangeitem::lang.total_return_value')",
    total_new_value: "@lang('exchangeitem::lang.total_new_value')",
    net_exchange_amount: "@lang('exchangeitem::lang.net_exchange_amount')",
    notes: "@lang('exchangeitem::lang.notes')",
    original_transaction: "@lang('exchangeitem::lang.original_transaction')",
    transaction_not_found: "@lang('exchangeitem::lang.transaction_not_found')",
    no_items_available_for_exchange: "@lang('exchangeitem::lang.no_items_available_for_exchange')",
    exchange_completed_successfully: "@lang('exchangeitem::lang.exchange_completed_successfully')"
};
</script>