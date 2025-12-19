<?php

return [
    // Module Information
    'module_name' => '库存重置',
    'module_description' => '只需几次点击即可将所有库存数量重置为零',

    // Dashboard
    'dashboard_subtitle' => '管理和跟踪您的库存重置操作',
    'inventory_reset' => '库存重置',

    // Statistics
    'total_products' => '总产品数',
    'products_with_stock' => '有非零库存的产品',
    'products_without_stock' => '零库存产品',
    'total_stock_value' => '总库存价值',
    'last_reset_date' => '上次重置日期',
    'never' => '从未',

    // Quick Actions
    'products_with_stock_warning' => '您有 :count 个产品具有非零库存（正数或负数），可以重置。',
    'all_stock_zero_info' => '所有产品目前都是零库存。您仍然可以执行重置以创建审计跟踪记录。',

    // Reset History
    'recent_reset_history' => '最近重置历史',
    'no_reset_history' => '无重置历史',
    'no_reset_history_desc' => '您还没有执行任何库存重置。',
    'reason' => '原因',
    'items_reset' => '已重置项目',
    'filter_by_user' => '按用户筛选',
    'filter_by_status' => '按状态筛选',
    'filter_by_type' => '按类型筛选',
	'filter_by_reset_mode' => '按重置模式过滤',
	'filter_by_operation_type' => '按操作类型过滤',
    'clear_filters' => '清除筛选器',

    // Reset Form
    'inventory_reset_form' => '库存重置表单',
    'reset_type' => '重置类型',
    'reset_mode' => '重置模式',

    // Form Fields
    'select_location' => '选择位置',
    'select_products' => '选择产品',
    'selected_products' => '已选产品',
    'sku' => '产品编号',
    'reason_placeholder' => '请提供此次库存重置的原因（如：年度盘点、库存审计、系统迁移）...',
    'reason_help' => '此原因将被记录并在报告中显示，用于审计目的。',
    'confirm_reset_checkbox' => '我理解此操作将永久重置所有选定库存为零且无法撤销。',

    // Stock Fix Options
    'fix_stock_mismatches' => '重置前修复库存不匹配',
    'fix_stock_mismatches_desc' => '在执行重置前自动纠正库存差异',
    'fix_stock_mismatches_help' => '这将确保所有计算库存在重置前与实际库存匹配',

    // Summary Panel
    'execute_reset' => '执行重置',

    // Help
    'help' => '帮助和提示',
    'help_tip_1' => '在执行重置前确保备份您的数据。',
    'help_tip_2' => '将创建库存调整来跟踪重置。',
    'help_tip_3' => '您可以查看所有重置操作的详细报告。',

    // Danger Zone
    'danger_zone_description' => '库存重置操作将永久将指定位置的所有选定产品数量设置为零。',
    'warning_irreversible' => '此操作不可逆！',

    // Processing
    'processing_status' => '正在处理重置...',
    'final_confirmation' => '您确定要继续进行此库存重置吗？此操作无法撤销。',

    // Messages
    'reset_completed_successfully' => '库存重置成功完成！',
    'reset_failed' => '库存重置失败',
    'success' => '成功',
    'error' => '错误',

    // Reset Details
    'reset_details' => '重置详情',
    'reset_information' => '重置信息',
    'reset_statistics' => '重置统计',
    'reset_id' => '重置ID',
    'performed_by' => '执行者',
    'started_at' => '开始时间',
    'completed_at' => '完成时间',
    'location' => '位置',
    'status' => '状态',
    'error_message' => '错误消息',
    'items_processed' => '已处理项目',
    'total_quantity_reset' => '总数量',
    'unique_products' => '唯一产品',
    'duration' => '持续时间',
    'back_to_dashboard' => '返回仪表板',

    // Reset Items Table
    'reset_items_details' => '重置项目详情',
    'product' => '产品',
    'quantity_before' => '重置前数量',
    'quantity_after' => '重置后数量',
    'quantity_reset' => '重置数量',
    'variable_product' => '可变产品',

    // Permissions
    'inventory_reset_module' => '库存重置模块',
    'access_inventory_reset' => '访问库存重置模块',
    'view_inventory_reset_history' => '查看库存重置历史',
    'perform_inventory_reset' => '执行库存重置',
    'delete_inventory_reset_records' => '删除库存重置记录',
    'inventory_management' => '库存管理',
    'perform_reset' => '执行重置',

    // Configuration
    'default_reset_reason' => '默认重置原因',
    'default_reset_reason_desc' => '将在重置表单中预填的默认原因文本',
    'require_confirmation' => '需要确认',
    'require_confirmation_desc' => '要求用户在执行重置前勾选确认框',
    'auto_create_adjustments' => '自动创建库存调整',
    'auto_create_adjustments_desc' => '自动为重置创建库存调整交易',
    'max_products_per_reset' => '每次重置的最大产品数',
    'max_products_per_reset_desc' => '单次操作中可重置的最大产品数量',

    // Installation
    'module_installed_successfully' => '库存重置模块安装成功！',
    'module_updated_successfully' => '库存重置模块成功更新到版本 ',
    'module_uninstalled_successfully' => '库存重置模块卸载成功！',

    // Status
    'completed' => '已完成',
    'processing' => '处理中',
    'failed' => '失败',
    'pending' => '等待中',

    // Alert
    'info_alert' => '您即将对 :type 在 :location 执行 :operation 操作，模式为 :mode。',
    'confirm_alert' => '此操作将影响您库存中的所有匹配产品且无法撤销！',
    'final_confirmation_alert' => '需要最终确认',
    'type_reset_hint' => '输入 RESET 以确认此破坏性操作：',
    'reset_placeholder' => '在此输入 RESET...',
    'confirm_reset' => '确认重置',
    'must_type_reset_error' => '您必须输入 RESET 来确认！',
    'processing_plz_wait' => '请稍候，我们正在重置您的库存...',
    'reload_page' => '重新加载页面',
    'select_atleast_product' => '请至少选择一个产品进行重置。',

    // Negative Inventory Display
    'products_with_negative_inventory' => '负库存产品',
    'loading_negative_products' => '正在加载负库存产品...',
    'no_negative_inventory_found' => '未找到负库存！所有产品都有非负数量。',
    'error_loading_negative_products' => '加载负库存产品时出错',
    'negative_quantity' => '负数量',
    'locations' => '位置',
    'products_with_negative_inventory_count' => '个产品存在负库存',
    'select_specific_products_above' => '请在上方选择特定产品，然后仅为选定产品显示负库存。',
    'no_products_have_negative' => '所选产品中没有负库存。',

    // Reset Type Section
    'all_products' => '所有产品',
    'all_products_desc' => '将操作应用于业务/位置中的所有产品',
    'selected_products_type' => '选定产品',
    'selected_products_type_desc' => '仅将操作应用于特别选择的产品',

    // Reset Mode Section
    'all_stock_levels' => '所有库存水平',
    'all_stock_levels_desc' => '修改任何库存水平（正数、负数或零）',
    'positive_stock_only' => '仅正库存',
    'positive_stock_only_desc' => '仅修改具有正库存数量的产品',
    'negative_stock_only' => '仅负库存',
    'negative_stock_only_desc' => '仅修改具有负库存数量的产品',
    'zero_stock_only' => '仅零库存',
    'zero_stock_only_desc' => '仅修改库存恰好为零的产品',

    // Operation Type Section
    'operation_type' => '操作类型',
    'reset_to_zero' => '重置为零',
    'reset_to_zero_desc' => '将所有匹配的库存数量设置为零',
    'set_to_quantity' => '设置为自定义数量',
    'set_to_quantity_desc' => '将所有匹配的库存设置为特定数量',

    // Target Quantity Section
    'target_quantity' => '目标数量：',
    'target_quantity_placeholder' => '输入要设置的数量',
    'target_quantity_desc' => '所有匹配的产品将设置为此数量',
    'error_fetching_negative_products' => '获取负库存产品时出错',

    // Display Labels
    'stock_adjustment' => 'Stock Adjustment',
    'inventory_reset_label' => 'Inventory Reset',
    'not_available' => 'N/A',
];