<?php

return [
    // Module Information
    'module_name' => 'Đặt lại tồn kho',
    'module_description' => 'Đặt lại toàn bộ số lượng tồn kho về 0 chỉ với vài cú nhấp chuột',

    // Dashboard
    'dashboard_subtitle' => 'Quản lý và theo dõi hoạt động đặt lại tồn kho của bạn',
    'inventory_reset' => 'Đặt lại tồn kho',

    // Statistics
    'total_products' => 'Tổng sản phẩm',
    'products_with_stock' => 'Sản phẩm có tồn kho khác 0',
    'products_without_stock' => 'Sản phẩm có tồn kho bằng 0',
    'total_stock_value' => 'Tổng giá trị tồn kho',
    'last_reset_date' => 'Ngày đặt lại cuối cùng',
    'never' => 'Không bao giờ',

    // Quick Actions
    'products_with_stock_warning' => 'Bạn có :count sản phẩm có tồn kho khác 0 (dương hoặc âm) có thể được đặt lại.',
    'all_stock_zero_info' => 'Tất cả sản phẩm hiện có tồn kho bằng 0. Bạn vẫn có thể thực hiện đặt lại để tạo bản ghi theo dõi kiểm toán.',

    // Reset History
    'recent_reset_history' => 'Lịch sử đặt lại gần đây',
    'no_reset_history' => 'Không có lịch sử đặt lại',
    'no_reset_history_desc' => 'Bạn chưa thực hiện bất kỳ lần đặt lại tồn kho nào.',
    'reason' => 'Lý do',
    'items_reset' => 'Số mặt hàng đặt lại',
    'filter_by_user' => 'Lọc theo Người thực hiện',
    'filter_by_status' => 'Lọc theo Trạng thái',
    'filter_by_type' => 'Lọc theo Loại',
	'filter_by_reset_mode' => 'Lọc theo Chế độ',
    'filter_by_operation_type' => 'Lọc theo Thao tác',
    'clear_filters' => 'Xóa Bộ lọc',

    // Reset Form
    'inventory_reset_form' => 'Biểu mẫu đặt lại tồn kho',
    'reset_type' => 'Loại Đặt lại',
    'reset_mode' => 'Chế độ Đặt lại',

    // Form Fields
    'select_location' => 'Chọn kho hàng',
    'select_products' => 'Chọn sản phẩm',
    'selected_products' => 'Sản phẩm được chọn',
    'sku' => 'Mã SKU',
    'reason_placeholder' => 'Cung cấp lý do cho việc đặt lại tồn kho này (ví dụ: Kiểm kê hàng năm, Kiểm kê kho, Di chuyển hệ thống)...',
    'reason_help' => 'Lý do này sẽ được ghi lại và hiển thị trong báo cáo cho mục đích kiểm kê.',
    'confirm_reset_checkbox' => 'Tôi hiểu rằng hành động này sẽ đặt lại vĩnh viễn tất cả tồn kho đã chọn về 0 và không thể hoàn tác.',

    // Stock Fix Options
    'fix_stock_mismatches' => 'Sửa lỗi tồn kho trước khi đặt lại',
    'fix_stock_mismatches_desc' => 'Tự động sửa các sự khác biệt về tồn kho trước khi thực hiện đặt lại',
    'fix_stock_mismatches_help' => 'Điều này sẽ đảm bảo tất cả tồn kho được tính toán khớp với tồn kho thực tế trước khi đặt lại',

    // Summary Panel
    'execute_reset' => 'Thực hiện đặt lại',

    // Help
    'help' => 'Trợ giúp & Mẹo',
    'help_tip_1' => 'Hãy đảm bảo sao lưu dữ liệu của bạn trước khi thực hiện đặt lại.',
    'help_tip_2' => 'Các điều chỉnh tồn kho sẽ được tạo để theo dõi việc đặt lại.',
    'help_tip_3' => 'Bạn có thể xem báo cáo chi tiết về tất cả các hoạt động đặt lại.',

    // Danger Zone
    'danger_zone_description' => 'Đặt lại tồn kho sẽ đặt vĩnh viễn tất cả số lượng sản phẩm đã chọn về 0 tại các kho hàng được chỉ định.',
    'warning_irreversible' => 'Hành động này là không thể đảo ngược!',

    // Processing
    'processing_status' => 'Đang xử lý đặt lại...',
    'final_confirmation' => 'Bạn có chắc chắn muốn tiếp tục đặt lại kho này không? Không thể hoàn tác hành động này.',

    // Messages
    'reset_completed_successfully' => 'Đã đặt lại tồn kho thành công!',
    'reset_failed' => 'Đặt lại tồn kho không thành công',
    'success' => 'Thành công',
    'error' => 'Lỗi',

    // Reset Details
    'reset_details' => 'Chi tiết đặt lại',
    'reset_information' => 'Thông tin đặt lại',
    'reset_statistics' => 'Thống kê đặt lại',
    'reset_id' => 'ID Đặt lại',
    'performed_by' => 'Người thực hiện',
    'started_at' => 'Bắt ​​đầu lúc',
    'completed_at' => 'Hoàn thành lúc',
    'location' => 'Kho hàng',
    'status' => 'Trạng thái',
    'error_message' => 'Thông báo lỗi',
    'items_processed' => 'Số mặt hàng đã xử lý',
    'total_quantity_reset' => 'Tổng số lượng',
    'unique_products' => 'Sản phẩm duy nhất',
    'duration' => 'Thời lượng',
    'back_to_dashboard' => 'Quay lại Bảng điều khiển',

    // Reset Items Table
    'reset_items_details' => 'Chi tiết đặt lại mặt hàng',
    'product' => 'Sản phẩm',
    'quantity_before' => 'Số lượng trước',
    'quantity_after' => 'Số lượng sau',
    'quantity_reset' => 'Số lượng đặt lại',
    'variable_product' => 'Biến thể sản phẩm',

    // Permissions
    'inventory_reset_module' => 'Mô-đun Đặt lại tồn kho',
    'access_inventory_reset' => 'Truy cập Mô-đun Đặt lại tồn kho',
    'view_inventory_reset_history' => 'Xem Lịch sử Đặt lại tồn kho',
    'perform_inventory_reset' => 'Thực hiện Đặt lại tồn kho',
    'delete_inventory_reset_records' => 'Xóa Bản ghi Đặt lại tồn kho',
    'inventory_management' => 'Quản lý Tồn kho',
    'perform_reset' => 'Thực hiện Đặt lại tồn kho',

    // Configuration
    'default_reset_reason' => 'Lý do đặt lại mặc định',
    'default_reset_reason_desc' => 'Nội dung lý do mặc định sẽ được điền sẵn trong biểu mẫu đặt lại',
    'require_confirmation' => 'Yêu cầu xác nhận',
    'require_confirmation_desc' => 'Yêu cầu người dùng đánh dấu vào ô xác nhận trước khi thực hiện đặt lại',
    'auto_create_adjustments' => 'Tự động tạo Điều chỉnh tồn kho',
    'auto_create_adjustments_desc' => 'Tự động tạo giao dịch điều chỉnh tồn kho cho các lần đặt lại',
    'max_products_per_reset' => 'Số lượng sản phẩm tối đa cho mỗi lần đặt lại',
    'max_products_per_reset_desc' => 'Số lượng sản phẩm tối đa có thể được đặt lại trong một thao tác',

    // Installation
    'module_installed_successfully' => 'Mô-đun Đặt lại tồn kho đã được cài đặt thành công!',
    'module_updated_successfully' => 'Mô-đun Đặt lại tồn kho đã được cập nhật thành công lên phiên bản ',
    'module_uninstalled_successfully' => 'Mô-đun Đặt lại tồn kho đã được gỡ cài đặt thành công!',

    // Status
    'completed' => 'Hoàn thành',
    'processing' => 'Đang xử lý',
    'failed' => 'Thất bại',
    'pending' => 'Đang chờ xử lý',

    // Alert
    'info_alert' => 'Bạn sắp thực hiện :operation cho :type với :mode tại :location.',
    'confirm_alert' => 'Hành động này sẽ ảnh hưởng đến TẤT CẢ tồn kho của số sản phẩm đã khớp trong kho hàng của bạn và không thể hoàn tác!',
    'final_confirmation_alert' => 'Yêu cầu xác nhận cuối cùng',
    'type_reset_hint' => 'Nhập RESET để xác nhận hành động hủy diệt này:',
    'reset_placeholder' => 'Nhập RESET ở đây...',
    'confirm_reset' => 'Xác nhận đặt lại',
    'must_type_reset_error' => 'Bạn phải nhập RESET để xác nhận!',
    'processing_plz_wait' => 'Vui lòng đợi trong khi chúng tôi đặt lại tồn kho của bạn...',
    'reload_page' => 'Tải lại trang',
    'select_atleast_product' => 'Vui lòng chọn ít nhất một sản phẩm để đặt lại.',

    // Negative Inventory Display
    'products_with_negative_inventory' => 'Sản phẩm có tồn kho âm',
    'loading_negative_products' => 'Đang tải sản phẩm tồn kho âm...',
    'no_negative_inventory_found' => 'Không tìm thấy tồn kho âm! Tất cả sản phẩm đều có số lượng không âm.',
    'error_loading_negative_products' => 'Lỗi khi tải sản phẩm tồn kho âm',
    'negative_quantity' => 'Số lượng âm',
    'locations' => 'Kho hàng',
    'products_with_negative_inventory_count' => 'sản phẩm được tìm thấy có tồn kho âm',
    'select_specific_products_above' => 'Vui lòng chọn các sản phẩm cụ thể ở trên, sau đó tình trạng tồn kho âm sẽ chỉ hiển thị cho các sản phẩm đã chọn.',
    'no_products_have_negative' => 'Không có sản phẩm nào được chọn có tồn kho âm.',

    // Reset Type Section
    'all_products' => 'Tất cả sản phẩm',
    'all_products_desc' => 'Áp dụng thao tác cho tất cả sản phẩm trong doanh nghiệp',
    'selected_products_type' => 'Sản phẩm đã chọn',
    'selected_products_type_desc' => 'Áp dụng thao tác chỉ cho các sản phẩm được chọn cụ thể',

    // Reset Mode Section
    'all_stock_levels' => 'Tất cả mức tồn kho',
    'all_stock_levels_desc' => 'Sửa đổi bất kỳ mức kho nào (dương, âm, hoặc không)',
    'positive_stock_only' => 'Chỉ tồn kho Dương',
    'positive_stock_only_desc' => 'Chỉ sửa đổi sản phẩm có số lượng kho dương',
    'negative_stock_only' => 'Chỉ tồn kho Âm',
    'negative_stock_only_desc' => 'Chỉ sửa đổi sản phẩm có số lượng kho âm',
    'zero_stock_only' => 'Chỉ tồn kho bằng 0',
    'zero_stock_only_desc' => 'Chỉ sửa đổi sản phẩm có chính xác không kho',

    // Operation Type Section
    'operation_type' => 'Loại Thao tác',
    'reset_to_zero' => 'Đặt tồn kho về 0',
    'reset_to_zero_desc' => 'Đặt tất cả tồn kho của số sản phẩm đã khớp về 0',
    'set_to_quantity' => 'Đặt tồn kho về số tùy chỉnh',
    'set_to_quantity_desc' => 'Đặt tất cả tồn kho của số sản phẩm đã khớp về một số lượng cụ thể',

    // Target Quantity Section
    'target_quantity' => 'Số lượng mục tiêu:',
    'target_quantity_placeholder' => 'Nhập số lượng để đặt',
    'target_quantity_desc' => 'Tất cả tồn kho của số sản phẩm đã khớp sẽ được đặt về số lượng này',
    'error_fetching_negative_products' => 'Lỗi khi lấy sản phẩm có kho âm',

    // Display Labels
    'stock_adjustment' => 'Stock Adjustment',
    'inventory_reset_label' => 'Inventory Reset',
    'not_available' => 'N/A',
];