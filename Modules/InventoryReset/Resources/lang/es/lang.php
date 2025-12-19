<?php

return [
    // Module Information
    'module_name' => 'Reinicio de Inventario',
    'module_description' => 'Reinicia todas las cantidades de inventario a cero con solo unos pocos clics',

    // Dashboard
    'dashboard_subtitle' => 'Administra y rastrea tus operaciones de reinicio de inventario',
    'inventory_reset' => 'Reinicio de Inventario',

    // Statistics
    'total_products' => 'Total de Productos',
    'products_with_stock' => 'Productos con Stock No Cero',
    'products_without_stock' => 'Productos con Stock Cero',
    'total_stock_value' => 'Valor Total del Stock',
    'last_reset_date' => 'Fecha del Último Reinicio',
    'never' => 'Nunca',

    // Quick Actions
    'products_with_stock_warning' => 'Tienes :count productos con stock no cero (positivo o negativo) que pueden ser reiniciados.',
    'all_stock_zero_info' => 'Todos los productos actualmente tienen stock cero. Aún puedes realizar un reinicio para crear un registro de auditoría.',

    // Reset History
    'recent_reset_history' => 'Historial de Reinicio Reciente',
    'no_reset_history' => 'Sin Historial de Reinicio',
    'no_reset_history_desc' => 'No has realizado ningún reinicio de inventario aún.',
    'reason' => 'Razón',
    'items_reset' => 'Elementos Reiniciados',
    'filter_by_user' => 'Filtrar por Usuario',
    'filter_by_status' => 'Filtrar por Estado',
    'filter_by_type' => 'Filtrar por Tipo',
	'filter_by_reset_mode' => 'Filtrar por modo de reinicio',
	'filter_by_operation_type' => 'Filtrar por tipo de operación',
    'clear_filters' => 'Limpiar Filtros',

    // Reset Form
    'inventory_reset_form' => 'Formulario de Reinicio de Inventario',
    'reset_type' => 'Tipo de Reinicio',
    'reset_mode' => 'Modo de Reinicio',

    // Form Fields
    'select_location' => 'Seleccionar Ubicación',
    'select_products' => 'Seleccionar Productos',
    'selected_products' => 'Productos Seleccionados',
    'sku' => 'SKU',
    'reason_placeholder' => 'Proporciona una razón para este reinicio de inventario (ej., Inventario anual, Auditoría de stock, Migración del sistema)...',
    'reason_help' => 'Esta razón será registrada y visible en reportes para propósitos de auditoría.',
    'confirm_reset_checkbox' => 'Entiendo que esta acción reiniciará permanentemente todo el inventario seleccionado a cero y no se puede deshacer.',

    // Stock Fix Options
    'fix_stock_mismatches' => 'Corregir Desajustes de Stock Antes del Reinicio',
    'fix_stock_mismatches_desc' => 'Corregir automáticamente las discrepancias de stock antes de realizar el reinicio',
    'fix_stock_mismatches_help' => 'Esto asegurará que todo el stock calculado coincida con el stock real antes del reinicio',

    // Summary Panel
    'execute_reset' => 'Ejecutar Reinicio',

    // Help
    'help' => 'Ayuda y Consejos',
    'help_tip_1' => 'Asegúrate de respaldar tus datos antes de realizar un reinicio.',
    'help_tip_2' => 'Se crearán ajustes de stock para rastrear el reinicio.',
    'help_tip_3' => 'Puedes ver reportes detallados de todas las operaciones de reinicio.',

    // Danger Zone
    'danger_zone_description' => 'La operación de reinicio de inventario establecerá permanentemente todas las cantidades de productos seleccionados a cero en las ubicaciones especificadas.',
    'warning_irreversible' => '¡Esta acción es irreversible!',

    // Processing
    'processing_status' => 'Procesando Reinicio...',
    'final_confirmation' => '¿Estás absolutamente seguro de que quieres proceder con este reinicio de inventario? Esta acción no se puede deshacer.',

    // Messages
    'reset_completed_successfully' => '¡Reinicio de inventario completado exitosamente!',
    'reset_failed' => 'Falló el reinicio de inventario',
    'success' => 'Éxito',
    'error' => 'Error',

    // Reset Details
    'reset_details' => 'Detalles del Reinicio',
    'reset_information' => 'Información del Reinicio',
    'reset_statistics' => 'Estadísticas del Reinicio',
    'reset_id' => 'ID del Reinicio',
    'performed_by' => 'Realizado Por',
    'started_at' => 'Iniciado En',
    'completed_at' => 'Completado En',
    'location' => 'Ubicación',
    'status' => 'Estado',
    'error_message' => 'Mensaje de Error',
    'items_processed' => 'Elementos Procesados',
    'total_quantity_reset' => 'Cantidad Total',
    'unique_products' => 'Productos Únicos',
    'duration' => 'Duración',
    'back_to_dashboard' => 'Volver al Panel',

    // Reset Items Table
    'reset_items_details' => 'Detalles de Elementos Reiniciados',
    'product' => 'Producto',
    'quantity_before' => 'Cantidad Antes',
    'quantity_after' => 'Cantidad Después',
    'quantity_reset' => 'Cantidad Reiniciada',
    'variable_product' => 'Producto Variable',

    // Permissions
    'inventory_reset_module' => 'Módulo de Reinicio de Inventario',
    'access_inventory_reset' => 'Acceder al Módulo de Reinicio de Inventario',
    'view_inventory_reset_history' => 'Ver Historial de Reinicio de Inventario',
    'perform_inventory_reset' => 'Realizar Reinicio de Inventario',
    'delete_inventory_reset_records' => 'Eliminar Registros de Reinicio de Inventario',
    'inventory_management' => 'Gestión de Inventario',
    'perform_reset' => 'Realizar Reinicio',

    // Configuration
    'default_reset_reason' => 'Razón de Reinicio por Defecto',
    'default_reset_reason_desc' => 'Texto de razón por defecto que será prellenado en formularios de reinicio',
    'require_confirmation' => 'Requerir Confirmación',
    'require_confirmation_desc' => 'Requerir que los usuarios marquen una casilla de confirmación antes de ejecutar reinicios',
    'auto_create_adjustments' => 'Crear Ajustes de Stock Automáticamente',
    'auto_create_adjustments_desc' => 'Crear automáticamente transacciones de ajuste de stock para reinicios',
    'max_products_per_reset' => 'Máximo de Productos por Reinicio',
    'max_products_per_reset_desc' => 'Número máximo de productos que pueden ser reiniciados en una sola operación',

    // Installation
    'module_installed_successfully' => '¡Módulo de Reinicio de Inventario instalado exitosamente!',
    'module_updated_successfully' => 'Módulo de Reinicio de Inventario actualizado exitosamente a la versión ',
    'module_uninstalled_successfully' => '¡Módulo de Reinicio de Inventario desinstalado exitosamente!',

    // Status
    'completed' => 'Completado',
    'processing' => 'Procesando',
    'failed' => 'Fallido',
    'pending' => 'Pendiente',

    // Alert
    'info_alert' => 'Estás a punto de :operation para :type con :mode en :location.',
    'confirm_alert' => '¡Esta acción afectará TODOS los productos coincidentes en tu inventario y no se puede deshacer!',
    'final_confirmation_alert' => 'Confirmación Final Requerida',
    'type_reset_hint' => 'Escribe RESET para confirmar esta acción destructiva:',
    'reset_placeholder' => 'Escribe RESET aquí...',
    'confirm_reset' => 'Confirmar Reinicio',
    'must_type_reset_error' => '¡Debes escribir RESET para confirmar!',
    'processing_plz_wait' => 'Por favor espera mientras reiniciamos tu inventario...',
    'reload_page' => 'Recargar Página',
    'select_atleast_product' => 'Por favor selecciona al menos un producto para reiniciar.',

    // Negative Inventory Display
    'products_with_negative_inventory' => 'Productos con Inventario Negativo',
    'loading_negative_products' => 'Cargando productos con inventario negativo...',
    'no_negative_inventory_found' => '¡No se encontró inventario negativo! Todos los productos tienen cantidades no negativas.',
    'error_loading_negative_products' => 'Error cargando productos con inventario negativo',
    'negative_quantity' => 'Cantidad Negativa',
    'locations' => 'Ubicaciones',
    'products_with_negative_inventory_count' => 'productos encontrados con inventario negativo',
    'select_specific_products_above' => 'Por favor selecciona productos específicos arriba, luego el stock negativo se mostrará solo para los productos seleccionados.',
    'no_products_have_negative' => 'Ninguno de los productos seleccionados tiene inventario negativo.',

    // Reset Type Section
    'all_products' => 'Todos los Productos',
    'all_products_desc' => 'Aplicar operación a todos los productos en el negocio/ubicación',
    'selected_products_type' => 'Productos Seleccionados',
    'selected_products_type_desc' => 'Aplicar operación solo a productos específicamente elegidos',

    // Reset Mode Section
    'all_stock_levels' => 'Todos los Niveles de Stock',
    'all_stock_levels_desc' => 'Modificar cualquier nivel de stock (positivo, negativo, o cero)',
    'positive_stock_only' => 'Solo Stock Positivo',
    'positive_stock_only_desc' => 'Solo modificar productos con cantidades de stock positivas',
    'negative_stock_only' => 'Solo Stock Negativo',
    'negative_stock_only_desc' => 'Solo modificar productos con cantidades de stock negativas',
    'zero_stock_only' => 'Solo Stock Cero',
    'zero_stock_only_desc' => 'Solo modificar productos con exactamente cero stock',

    // Operation Type Section
    'operation_type' => 'Tipo de Operación',
    'reset_to_zero' => 'Resetear a Cero',
    'reset_to_zero_desc' => 'Establecer todas las cantidades de inventario coincidentes a cero',
    'set_to_quantity' => 'Establecer a Cantidad Personalizada',
    'set_to_quantity_desc' => 'Establecer todo el inventario coincidente a una cantidad específica',

    // Target Quantity Section
    'target_quantity' => 'Cantidad Objetivo:',
    'target_quantity_placeholder' => 'Ingrese la cantidad a establecer',
    'target_quantity_desc' => 'Todos los productos coincidentes se establecerán a esta cantidad',
    'error_fetching_negative_products' => 'Error obteniendo productos con inventario negativo',

    // Display Labels
    'stock_adjustment' => 'Ajuste de Inventario',
    'inventory_reset_label' => 'Reinicio de Inventario',
    'not_available' => 'N/D',
];