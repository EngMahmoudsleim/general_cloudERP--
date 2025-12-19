<?php

return [
    // Module Information
    'module_name' => 'Lager Zurücksetzen',
    'module_description' => 'Alle Lagermengen mit nur wenigen Klicks auf Null zurücksetzen',

    // Dashboard
    'dashboard_subtitle' => 'Verwalten und verfolgen Sie Ihre Lager-Zurücksetzungsvorgänge',
    'inventory_reset' => 'Lager Zurücksetzen',

    // Statistics
    'total_products' => 'Gesamt Produkte',
    'products_with_stock' => 'Produkte mit Lagerbestand ungleich Null',
    'products_without_stock' => 'Produkte mit Null-Lagerbestand',
    'total_stock_value' => 'Gesamter Lagerwert',
    'last_reset_date' => 'Letztes Zurücksetzungsdatum',
    'never' => 'Niemals',

    // Quick Actions
    'products_with_stock_warning' => 'Sie haben :count Produkte mit Lagerbestand ungleich Null (positiv oder negativ), die zurückgesetzt werden können.',
    'all_stock_zero_info' => 'Alle Produkte haben derzeit Null-Lagerbestand. Sie können trotzdem eine Zurücksetzung durchführen, um einen Audit-Trail-Datensatz zu erstellen.',

    // Reset History
    'recent_reset_history' => 'Aktuelle Zurücksetzungshistorie',
    'no_reset_history' => 'Keine Zurücksetzungshistorie',
    'no_reset_history_desc' => 'Sie haben noch keine Lager-Zurücksetzungen durchgeführt.',
    'reason' => 'Grund',
    'items_reset' => 'Zurückgesetzte Artikel',
    'filter_by_user' => 'Nach Benutzer filtern',
    'filter_by_status' => 'Nach Status filtern',
    'filter_by_type' => 'Nach Typ filtern',
	'filter_by_reset_mode' => 'Nach Rücksetzmodus filtern',
	'filter_by_operation_type' => 'Nach Vorgangstyp filtern',
    'clear_filters' => 'Filter löschen',

    // Reset Form
    'inventory_reset_form' => 'Lager-Zurücksetzungsformular',
    'reset_type' => 'Zurücksetzungstyp',
    'reset_mode' => 'Zurücksetzungsmodus',

    // Form Fields
    'select_location' => 'Standort auswählen',
    'select_products' => 'Produkte auswählen',
    'selected_products' => 'Ausgewählte Produkte',
    'sku' => 'Artikelnummer',
    'reason_placeholder' => 'Geben Sie einen Grund für diese Lager-Zurücksetzung an (z.B. Jahresinventur, Lagerprüfung, Systemmigration)...',
    'reason_help' => 'Dieser Grund wird protokolliert und in Berichten zu Prüfzwecken sichtbar sein.',
    'confirm_reset_checkbox' => 'Ich verstehe, dass diese Aktion alle ausgewählten Lagerbestände dauerhaft auf Null zurücksetzt und nicht rückgängig gemacht werden kann.',

    // Stock Fix Options
    'fix_stock_mismatches' => 'Lager-Diskrepanzen vor Zurücksetzung beheben',
    'fix_stock_mismatches_desc' => 'Lager-Ungereimtheiten automatisch vor der Zurücksetzung korrigieren',
    'fix_stock_mismatches_help' => 'Dies stellt sicher, dass alle berechneten Lagerbestände vor der Zurücksetzung mit den tatsächlichen Lagerbeständen übereinstimmen',

    // Summary Panel
    'execute_reset' => 'Zurücksetzung ausführen',

    // Help
    'help' => 'Hilfe & Tipps',
    'help_tip_1' => 'Stellen Sie sicher, dass Sie Ihre Daten vor einer Zurücksetzung sichern.',
    'help_tip_2' => 'Lageranpassungen werden erstellt, um die Zurücksetzung zu verfolgen.',
    'help_tip_3' => 'Sie können detaillierte Berichte aller Zurücksetzungsvorgänge anzeigen.',

    // Danger Zone
    'danger_zone_description' => 'Der Lager-Zurücksetzungsvorgang wird alle ausgewählten Produktmengen an den angegebenen Standorten dauerhaft auf Null setzen.',
    'warning_irreversible' => 'Diese Aktion ist unwiderruflich!',

    // Processing
    'processing_status' => 'Zurücksetzung wird verarbeitet...',
    'final_confirmation' => 'Sind Sie absolut sicher, dass Sie mit dieser Lager-Zurücksetzung fortfahren möchten? Diese Aktion kann nicht rückgängig gemacht werden.',

    // Messages
    'reset_completed_successfully' => 'Lager-Zurücksetzung erfolgreich abgeschlossen!',
    'reset_failed' => 'Lager-Zurücksetzung fehlgeschlagen',
    'success' => 'Erfolg',
    'error' => 'Fehler',

    // Reset Details
    'reset_details' => 'Zurücksetzungsdetails',
    'reset_information' => 'Zurücksetzungsinformationen',
    'reset_statistics' => 'Zurücksetzungsstatistiken',
    'reset_id' => 'Zurücksetzungs-ID',
    'performed_by' => 'Durchgeführt von',
    'started_at' => 'Gestartet um',
    'completed_at' => 'Abgeschlossen um',
    'location' => 'Standort',
    'status' => 'Status',
    'error_message' => 'Fehlermeldung',
    'items_processed' => 'Verarbeitete Artikel',
    'total_quantity_reset' => 'Gesamtmenge',
    'unique_products' => 'Eindeutige Produkte',
    'duration' => 'Dauer',
    'back_to_dashboard' => 'Zurück zum Dashboard',

    // Reset Items Table
    'reset_items_details' => 'Details der zurückgesetzten Artikel',
    'product' => 'Produkt',
    'quantity_before' => 'Menge vorher',
    'quantity_after' => 'Menge nachher',
    'quantity_reset' => 'Zurückgesetzte Menge',
    'variable_product' => 'Variables Produkt',

    // Permissions
    'inventory_reset_module' => 'Lager-Zurücksetzungsmodul',
    'access_inventory_reset' => 'Zugang zum Lager-Zurücksetzungsmodul',
    'view_inventory_reset_history' => 'Lager-Zurücksetzungshistorie anzeigen',
    'perform_inventory_reset' => 'Lager-Zurücksetzung durchführen',
    'delete_inventory_reset_records' => 'Lager-Zurücksetzungsaufzeichnungen löschen',
    'inventory_management' => 'Lagerverwaltung',
    'perform_reset' => 'Zurücksetzung durchführen',

    // Configuration
    'default_reset_reason' => 'Standard-Zurücksetzungsgrund',
    'default_reset_reason_desc' => 'Standard-Grundtext, der in Zurücksetzungsformularen vorausgefüllt wird',
    'require_confirmation' => 'Bestätigung erforderlich',
    'require_confirmation_desc' => 'Benutzer müssen vor der Ausführung von Zurücksetzungen ein Bestätigungsfeld ankreuzen',
    'auto_create_adjustments' => 'Lageranpassungen automatisch erstellen',
    'auto_create_adjustments_desc' => 'Automatisch Lageranpassungstransaktionen für Zurücksetzungen erstellen',
    'max_products_per_reset' => 'Maximale Produkte pro Zurücksetzung',
    'max_products_per_reset_desc' => 'Maximale Anzahl von Produkten, die in einem einzigen Vorgang zurückgesetzt werden können',

    // Installation
    'module_installed_successfully' => 'Lager-Zurücksetzungsmodul erfolgreich installiert!',
    'module_updated_successfully' => 'Lager-Zurücksetzungsmodul erfolgreich auf Version aktualisiert ',
    'module_uninstalled_successfully' => 'Lager-Zurücksetzungsmodul erfolgreich deinstalliert!',

    // Status
    'completed' => 'Abgeschlossen',
    'processing' => 'Verarbeitung',
    'failed' => 'Fehlgeschlagen',
    'pending' => 'Ausstehend',

    // Alert
    'info_alert' => 'Sie sind dabei, :operation für :type mit :mode bei :location durchzuführen.',
    'confirm_alert' => 'Diese Aktion wird ALLE passenden Produkte in Ihrem Lager betreffen und kann nicht rückgängig gemacht werden!',
    'final_confirmation_alert' => 'Endgültige Bestätigung erforderlich',
    'type_reset_hint' => 'Geben Sie RESET ein, um diese destruktive Aktion zu bestätigen:',
    'reset_placeholder' => 'Geben Sie hier RESET ein...',
    'confirm_reset' => 'Zurücksetzung bestätigen',
    'must_type_reset_error' => 'Sie müssen RESET eingeben, um zu bestätigen!',
    'processing_plz_wait' => 'Bitte warten Sie, während wir Ihr Lager zurücksetzen...',
    'reload_page' => 'Seite neu laden',
    'select_atleast_product' => 'Bitte wählen Sie mindestens ein Produkt zum Zurücksetzen aus.',

    // Negative Inventory Display
    'products_with_negative_inventory' => 'Produkte mit negativem Lagerbestand',
    'loading_negative_products' => 'Lade Produkte mit negativem Lagerbestand...',
    'no_negative_inventory_found' => 'Kein negativer Lagerbestand gefunden! Alle Produkte haben nicht-negative Mengen.',
    'error_loading_negative_products' => 'Fehler beim Laden von Produkten mit negativem Lagerbestand',
    'negative_quantity' => 'Negative Menge',
    'locations' => 'Standorte',
    'products_with_negative_inventory_count' => 'Produkte mit negativem Lagerbestand gefunden',
    'select_specific_products_above' => 'Bitte wählen Sie oben spezifische Produkte aus, dann wird der negative Lagerbestand nur für ausgewählte Produkte angezeigt.',
    'no_products_have_negative' => 'Keines der ausgewählten Produkte hat negativen Lagerbestand.',

    // Reset Type Section
    'all_products' => 'Alle Produkte',
    'all_products_desc' => 'Vorgang auf alle Produkte im Unternehmen/Standort anwenden',
    'selected_products_type' => 'Ausgewählte Produkte',
    'selected_products_type_desc' => 'Vorgang nur auf spezifisch ausgewählte Produkte anwenden',

    // Reset Mode Section
    'all_stock_levels' => 'Alle Lagerbestände',
    'all_stock_levels_desc' => 'Jeden Lagerbestand ändern (positiv, negativ oder null)',
    'positive_stock_only' => 'Nur positiver Lagerbestand',
    'positive_stock_only_desc' => 'Nur Produkte mit positiven Lagerbestandsmengen ändern',
    'negative_stock_only' => 'Nur negativer Lagerbestand',
    'negative_stock_only_desc' => 'Nur Produkte mit negativen Lagerbestandsmengen ändern',
    'zero_stock_only' => 'Nur Null-Lagerbestand',
    'zero_stock_only_desc' => 'Nur Produkte mit exakt null Lagerbestand ändern',

    // Operation Type Section
    'operation_type' => 'Vorgangstyp',
    'reset_to_zero' => 'Auf Null zurücksetzen',
    'reset_to_zero_desc' => 'Alle passenden Lagerbestandsmengen auf null setzen',
    'set_to_quantity' => 'Auf benutzerdefinierte Menge setzen',
    'set_to_quantity_desc' => 'Alle passenden Lagerbestände auf eine spezifische Menge setzen',

    // Target Quantity Section
    'target_quantity' => 'Zielmenge:',
    'target_quantity_placeholder' => 'Zu setzende Menge eingeben',
    'target_quantity_desc' => 'Alle passenden Produkte werden auf diese Menge gesetzt',
    'error_fetching_negative_products' => 'Fehler beim Abrufen von Produkten mit negativem Lagerbestand',

    // Display Labels
    'stock_adjustment' => 'Stock Adjustment',
    'inventory_reset_label' => 'Inventory Reset',
    'not_available' => 'N/A',
];