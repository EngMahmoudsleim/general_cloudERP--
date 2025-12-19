#JOCANI

1. SellPosController_Module is a copy of the original SellPosController
In this module SellPosController_Module will be copy like app/Http/controllers/SellPosController.php
and rename the original like SellPosController_ori


2. tailwind_app_Module its convert to css/tailwind/app.css
and rename the original like css/tailwind/app_ori.css

3. Views must be copy to custom_views (Better way, the original view it will be untouched)
* Settings
    SOURCE resources/views/business/partials/settings_pos.blade.php
    FROM Modules/PosCustom/Resources/tbcopy/business/partials/settings_pos.blade_Module.php
    TO custom_views/business/partials/settings_pos.blade.php
* Modifiers
    SOURCE resources/views/restaurant/product_modifier_set/add_selected_modifiers.blade.php
    FROM Modules/PosCustom/Resources/tbcopy/modifiers/add_selected_modifiers.blade_Module.php
    TO custom_views/restaurant/product_modifier_set/add_selected_modifiers.blade.php
* Taxonomy copy the entire folder
    SOURCE resources/views/taxonomy/*.*
    FROM Modules/PosCustom/Resources/tbcopy/modifiers/*.*
    TO custom_views/taxonomy/*.*    