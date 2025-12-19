<?php

return [
    // Module Information
    'module_name' => "Réinitialisation d\'Inventaire",
    'module_description' => "Réinitialisez toutes les quantités d\'inventaire à zéro en quelques clics seulement",

    // Dashboard
    'dashboard_subtitle' => "Gérez et suivez vos opérations de réinitialisation d\'inventaire",
    'inventory_reset' => "Réinitialisation d\'Inventaire",

    // Statistics
    'total_products' => "Total des Produits",
    'products_with_stock' => "Produits avec Stock Non-Zéro",
    'products_without_stock' => "Produits avec Stock Zéro",
    'total_stock_value' => "Valeur Totale du Stock",
    'last_reset_date' => "Date de Dernière Réinitialisation",
    'never' => "Jamais",

    // Quick Actions
    'products_with_stock_warning' => "Vous avez :count produits avec un stock non-zéro (positif ou négatif) qui peuvent être réinitialisés.",
    'all_stock_zero_info' => "Tous les produits ont actuellement un stock zéro. Vous pouvez toujours effectuer une réinitialisation pour créer un enregistrement d\'audit.",

    // Reset History
    'recent_reset_history' => "Historique de Réinitialisation Récent",
    'no_reset_history' => "Aucun Historique de Réinitialisation",
    'no_reset_history_desc' => "Vous n\'avez encore effectué aucune réinitialisation d\'inventaire.",
    'reason' => "Raison",
    'items_reset' => "Éléments Réinitialisés",
    'filter_by_user' => "Filtrer par Utilisateur",
    'filter_by_status' => "Filtrer par Statut",
    'filter_by_type' => "Filtrer par Type",
    'filter_by_reset_mode' => "Filtrer par mode de réinitialisation",
    'filter_by_operation_type' => "Filtrer par type d'opération",
    'clear_filters' => "Effacer les Filtres",

    // Reset Form
    'inventory_reset_form' => "Formulaire de Réinitialisation d\'Inventaire",
    'reset_type' => "Type de Réinitialisation",
    'reset_mode' => "Mode de Réinitialisation",

    // Form Fields
    'select_location' => "Sélectionner l\'Emplacement",
    'select_products' => "Sélectionner les Produits",
    'selected_products' => "Produits Sélectionnés",
    'sku' => "SKU",
    'reason_placeholder' => "Fournissez une raison pour cette réinitialisation d\'inventaire (ex., Inventaire annuel, Audit de stock, Migration système)...",
    'reason_help' => "Cette raison sera enregistrée et visible dans les rapports à des fins d\'audit.",
    'confirm_reset_checkbox' => "Je comprends que cette action réinitialisera définitivement tout l\'inventaire sélectionné à zéro et ne peut pas être annulée.",

    // Stock Fix Options
    'fix_stock_mismatches' => "Corriger les Incohérences de Stock Avant Réinitialisation",
    'fix_stock_mismatches_desc' => "Corriger automatiquement les divergences de stock avant d\'effectuer la réinitialisation",
    'fix_stock_mismatches_help' => "Cela garantira que tout le stock calculé correspond au stock réel avant la réinitialisation",

    // Summary Panel
    'execute_reset' => "Exécuter la Réinitialisation",

    // Help
    'help' => "Aide et Conseils",
    'help_tip_1' => "Assurez-vous de sauvegarder vos données avant d\'effectuer une réinitialisation.",
    'help_tip_2' => "Des ajustements de stock seront créés pour suivre la réinitialisation.",
    'help_tip_3' => "Vous pouvez consulter des rapports détaillés de toutes les opérations de réinitialisation.",

    // Danger Zone
    'danger_zone_description' => "L\'opération de réinitialisation d\'inventaire définira définitivement toutes les quantités de produits sélectionnés à zéro dans les emplacements spécifiés.",
    'warning_irreversible' => "Cette action est irréversible !",

    // Processing
    'processing_status' => "Traitement de la Réinitialisation...",
    'final_confirmation' => "Êtes-vous absolument sûr de vouloir procéder à cette réinitialisation d\'inventaire ? Cette action ne peut pas être annulée.",

    // Messages
    'reset_completed_successfully' => "Réinitialisation d\'inventaire terminée avec succès !",
    'reset_failed' => "Échec de la réinitialisation d\'inventaire",
    'success' => "Succès",
    'error' => "Erreur",

    // Reset Details
    'reset_details' => "Détails de Réinitialisation",
    'reset_information' => "Informations de Réinitialisation",
    'reset_statistics' => "Statistiques de Réinitialisation",
    'reset_id' => "ID de Réinitialisation",
    'performed_by' => "Effectué Par",
    'started_at' => "Démarré À",
    'completed_at' => "Terminé À",
    'location' => "Emplacement",
    'status' => "Statut",
    'error_message' => "Message d\'Erreur",
    'items_processed' => "Éléments Traités",
    'total_quantity_reset' => "Quantité Totale",
    'unique_products' => "Produits Uniques",
    'duration' => "Durée",
    'back_to_dashboard' => "Retour au Tableau de Bord",

    // Reset Items Table
    'reset_items_details' => "Détails des Éléments Réinitialisés",
    'product' => "Produit",
    'quantity_before' => "Quantité Avant",
    'quantity_after' => "Quantité Après",
    'quantity_reset' => "Quantité Réinitialisée",
    'variable_product' => "Produit Variable",

    // Permissions
    'inventory_reset_module' => "Module de Réinitialisation d\'Inventaire",
    'access_inventory_reset' => "Accéder au Module de Réinitialisation d\'Inventaire",
    'view_inventory_reset_history' => "Voir l\'Historique de Réinitialisation d\'Inventaire",
    'perform_inventory_reset' => "Effectuer une Réinitialisation d\'Inventaire",
    'delete_inventory_reset_records' => "Supprimer les Enregistrements de Réinitialisation d\'Inventaire",
    'inventory_management' => "Gestion d\'Inventaire",
    'perform_reset' => "Effectuer la Réinitialisation",

    // Configuration
    'default_reset_reason' => "Raison de Réinitialisation par Défaut",
    'default_reset_reason_desc' => "Texte de raison par défaut qui sera prérempli dans les formulaires de réinitialisation",
    'require_confirmation' => "Exiger une Confirmation",
    'require_confirmation_desc' => "Exiger des utilisateurs qu\'ils cochent une case de confirmation avant d\'exécuter les réinitialisations",
    'auto_create_adjustments' => "Créer Automatiquement les Ajustements de Stock",
    'auto_create_adjustments_desc' => "Créer automatiquement des transactions d\'ajustement de stock pour les réinitialisations",
    'max_products_per_reset' => "Maximum de Produits par Réinitialisation",
    'max_products_per_reset_desc' => "Nombre maximum de produits pouvant être réinitialisés en une seule opération",

    // Installation
    'module_installed_successfully' => "Module de Réinitialisation d\'Inventaire installé avec succès !",
    'module_updated_successfully' => "Module de Réinitialisation d\'Inventaire mis à jour avec succès vers la version ",
    'module_uninstalled_successfully' => "Module de Réinitialisation d\'Inventaire désinstallé avec succès !",

    // Status
    'completed' => "Terminé",
    'processing' => "En cours",
    'failed' => "Échoué",
    'pending' => "En attente",

    // Alert
    'info_alert' => "Vous êtes sur le point d\'effectuer :operation pour :type avec :mode à :location.",
    'confirm_alert' => "Cette action affectera TOUS les produits correspondants dans votre inventaire et ne peut pas être annulée !",
    'final_confirmation_alert' => "Confirmation Finale Requise",
    'type_reset_hint' => "Tapez RESET pour confirmer cette action destructrice :",
    'reset_placeholder' => "Tapez RESET ici...",
    'confirm_reset' => "Confirmer la Réinitialisation",
    'must_type_reset_error' => "Vous devez taper RESET pour confirmer !",
    'processing_plz_wait' => "Veuillez patienter pendant que nous réinitialisons votre inventaire...",
    'reload_page' => "Recharger la Page",
    'select_atleast_product' => "Veuillez sélectionner au moins un produit à réinitialiser.",

    // Negative Inventory Display
    'products_with_negative_inventory' => "Produits avec Inventaire Négatif",
    'loading_negative_products' => "Chargement des produits avec inventaire négatif...",
    'no_negative_inventory_found' => "Aucun inventaire négatif trouvé ! Tous les produits ont des quantités non négatives.",
    'error_loading_negative_products' => "Erreur lors du chargement des produits avec inventaire négatif",
    'negative_quantity' => "Quantité Négative",
    'locations' => "Emplacements",
    'products_with_negative_inventory_count' => "produits trouvés avec inventaire négatif",
    'select_specific_products_above' => "Veuillez sélectionner des produits spécifiques ci-dessus, puis le stock négatif sera affiché uniquement pour les produits sélectionnés.",
    'no_products_have_negative' => "Aucun des produits sélectionnés n\'a d\'inventaire négatif.",

    // Reset Type Section
    'all_products' => "Tous les Produits",
    'all_products_desc' => "Appliquer l\'opération à tous les produits de l\'entreprise/emplacement",
    'selected_products_type' => "Produits Sélectionnés",
    'selected_products_type_desc' => "Appliquer l\'opération uniquement aux produits spécifiquement choisis",

    // Reset Mode Section
    'all_stock_levels' => "Tous les Niveaux de Stock",
    'all_stock_levels_desc' => "Modifier tout niveau de stock (positif, négatif, ou zéro)",
    'positive_stock_only' => "Stock Positif Seulement",
    'positive_stock_only_desc' => "Modifier uniquement les produits avec des quantités de stock positives",
    'negative_stock_only' => "Stock Négatif Seulement",
    'negative_stock_only_desc' => "Modifier uniquement les produits avec des quantités de stock négatives",
    'zero_stock_only' => "Stock Zéro Seulement",
    'zero_stock_only_desc' => "Modifier uniquement les produits avec exactement zéro stock",

    // Operation Type Section
    'operation_type' => "Type d\'Opération",
    'reset_to_zero' => "Remettre à Zéro",
    'reset_to_zero_desc' => "Définir toutes les quantités d\'inventaire correspondantes à zéro",
    'set_to_quantity' => "Définir à une Quantité Personnalisée",
    'set_to_quantity_desc' => "Définir tout l\'inventaire correspondant à une quantité spécifique",

    // Target Quantity Section
    'target_quantity' => "Quantité Cible :",
    'target_quantity_placeholder' => "Entrez la quantité à définir",
    'target_quantity_desc' => "Tous les produits correspondants seront définis à cette quantité",
    'error_fetching_negative_products' => "Erreur lors de la récupération des produits avec inventaire négatif",

    // Display Labels
    'stock_adjustment' => 'Ajustement de Stock',
    'inventory_reset_label' => 'Réinitialisation d\'Inventaire',
    'not_available' => 'N/D',
];