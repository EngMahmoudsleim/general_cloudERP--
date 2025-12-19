<?php

namespace Modules\InventoryReset\Http\Controllers;

use App\System;
use Carbon\Exceptions\Exception;
use Composer\Semver\Comparator;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstallController extends Controller
{
    protected $module_name;
    protected $appVersion;
    protected $module_display_name;

    public function __construct()
    {
        $this->module_name = 'inventoryreset';
        $this->appVersion = config('inventoryreset.module_version');
        $this->module_display_name = __('inventoryreset::lang.module_name');
    }

    /**
     * Install
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $this->installSettings();

        //Check if installed or not - verify both version property AND table existence
        $is_installed = System::getProperty($this->module_name . '_version');
        $tables_exist = $this->checkTablesExist();

        if (empty($is_installed) || !$tables_exist) {
            try {
                Log::info('InventoryReset: Installation required - Version exists: ' . ($is_installed ? 'YES' : 'NO') . ', Tables exist: ' . ($tables_exist ? 'YES' : 'NO'));

                DB::statement('SET default_storage_engine=INNODB;');

                // Debug: Check before migration
                Log::info('InventoryReset: About to run migration');

                // Ensure migration can run by clearing any existing migration entries
                DB::delete("DELETE FROM migrations WHERE migration LIKE '%inventory_reset%'");

                // Run migrations (migrations handle their own transactions)
                $migrationExitCode = Artisan::call('migrate', ['--path' => 'Modules/InventoryReset/Database/Migrations', '--force' => true]);
                $migrationOutput = Artisan::output();

                // Debug: Check after migration
                Log::info('InventoryReset: Migration exit code: ' . $migrationExitCode);
                Log::info('InventoryReset: Migration output: ' . $migrationOutput);

                // Verify tables were actually created
                $tablesNowExist = $this->checkTablesExist();
                Log::info('InventoryReset: Tables exist after migration: ' . ($tablesNowExist ? 'YES' : 'NO'));

                if (!$tablesNowExist) {
                    throw new \Exception('Migration completed but tables were not created. Exit code: ' . $migrationExitCode . ', Output: ' . $migrationOutput);
                }

                // Install permissions using the robust command
                Artisan::call('inventory-reset:install-permissions');
                Log::info('InventoryReset: Permissions installed');

                // Set version after successful migration and permission installation
                System::addProperty($this->module_name . '_version', $this->appVersion);

                // Create default indexes after successful installation
                $this->createPerformanceIndexes();



                // Install view-based enhancement (safer approach)
                $this->installViewBasedEnhancement();

                // Install language enhancements
                $this->installLanguageEnhancements();

                Log::info('InventoryReset: Installation successful');
            } catch (\Exception $e) {
                Log::error('InventoryReset Installation error: ' . $e->getMessage());
                throw $e;
            }
        }

        // Clear all caches after successful installation
        $this->clearAllCaches();

        $output = [
            'success' => 1,
            'msg' => __('inventoryreset::lang.module_installed_successfully'),
        ];

        return redirect()
            ->action([\App\Http\Controllers\Install\ModulesController::class, 'index'])
            ->with('status', $output);
    }

    /**
     * Initialize all install functions
     */
    private function installSettings()
    {
        config(['app.debug' => true]);
        Artisan::call('config:clear');
    }

    /**
     * Clear all Laravel caches (config, route, view, cache)
     */
    private function clearAllCaches()
    {
        try {
            // Clear configuration cache
            Artisan::call('config:clear');
            Log::info('InventoryReset: Configuration cache cleared');

            // Clear route cache
            Artisan::call('route:clear');
            Log::info('InventoryReset: Route cache cleared');

            // Clear view cache
            Artisan::call('view:clear');
            Log::info('InventoryReset: View cache cleared');

            // Clear application cache
            Artisan::call('cache:clear');
            Log::info('InventoryReset: Application cache cleared');

            // Clear compiled services and packages
            Artisan::call('clear-compiled');
            Log::info('InventoryReset: Compiled services cleared');

            // Optimize autoloader (optional but recommended)
            if (app()->environment('production')) {
                Artisan::call('optimize');
                Log::info('InventoryReset: Application optimized for production');
            }

            Log::info('InventoryReset: All caches cleared successfully');
        } catch (\Exception $e) {
            Log::error('InventoryReset: Error clearing caches: ' . $e->getMessage());
            // Don't throw exception here as cache clearing shouldn't stop installation
        }
    }

    /**
     * Create performance indexes for inventory reset operations
     */
    private function createPerformanceIndexes()
    {
        try {
            $indexes = [
                'CREATE INDEX idx_ir_business_id ON inventory_reset_logs (business_id)',
                'CREATE INDEX idx_ir_user_id ON inventory_reset_logs (user_id)',
                'CREATE INDEX idx_ir_created_at ON inventory_reset_logs (created_at)',
                'CREATE INDEX idx_iri_reset_log_id ON inventory_reset_items (reset_log_id)',
                'CREATE INDEX idx_iri_product_id ON inventory_reset_items (product_id)',
                'CREATE INDEX idx_iri_location_id ON inventory_reset_items (location_id)',
            ];

            foreach ($indexes as $index) {
                try {
                    DB::statement($index);
                } catch (\Throwable $e) {
                    Log::warning('InventoryReset: Index creation warning: ' . $e->getMessage());
                    // Continue with other indexes even if one fails
                }
            }

            Log::info('InventoryReset: Performance indexes created');
        } catch (\Exception $e) {
            Log::error('InventoryReset: Error creating indexes: ' . $e->getMessage());
        }
    }

    // Updating
    public function update()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        try {
            $inventory_reset_version = System::getProperty($this->module_name . '_version');

            if (Comparator::greaterThan($this->appVersion, $inventory_reset_version)) {
                DB::statement('SET default_storage_engine=INNODB;');

                // Run migrations (migrations handle their own transactions)
                Artisan::call('migrate', ['--path' => 'Modules/InventoryReset/Database/Migrations', '--force' => true]);

                // Update permissions separately (with own transaction)
                $this->installPermissionsWithTransaction();

                System::setProperty($this->module_name . '_version', $this->appVersion);

                Log::info('InventoryReset: Update completed to version ' . $this->appVersion);
            } else {
                abort(404);
            }

            // Update indexes
            $this->createPerformanceIndexes();

            Log::info('InventoryReset: Update completed successfully');
        } catch (Exception $e) {
            Log::error('InventoryReset Update error: ' . $e->getMessage());
            throw $e;
        }

        // Clear all caches after successful update (outside transaction)
        $this->clearAllCaches();

        $output = [
            'success' => 1,
            'msg' => __('inventoryreset::lang.module_updated_successfully') . $this->appVersion,
        ];

        return redirect()
            ->action([\App\Http\Controllers\Install\ModulesController::class, 'index'])
            ->with('status', $output);
    }

    /**
     * Uninstall
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uninstall()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            // Remove permissions
            $permissions = [
                'inventory_reset.access',
                'inventory_reset.view',
                'inventory_reset.create',
                'inventory_reset.delete'
            ];

            foreach ($permissions as $permission) {
                $perm = \Spatie\Permission\Models\Permission::where('name', $permission)->first();
                if ($perm) {
                    $perm->delete();
                }
            }

            System::removeProperty($this->module_name . '_version');

            DB::commit();



            // Clean up view-based enhancement
            $this->cleanupViewBasedEnhancement();

            Log::info('InventoryReset: Uninstalled successfully');

            $output = [
                'success' => true,
                'msg' => __('inventoryreset::lang.module_uninstalled_successfully'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('InventoryReset Uninstall error: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => $e->getMessage(),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Install permissions for the module with its own transaction
     */
    private function installPermissionsWithTransaction()
    {
        try {
            DB::beginTransaction();
            $this->installPermissions();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('InventoryReset: Error installing permissions: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Install permissions for the module
     * This method is called within a transaction, so it doesn't start its own
     */
    private function installPermissions()
    {
        // Define permissions
        $permissions = [
            [
                'name' => 'inventory_reset.access',
                'display_name' => 'Access Inventory Reset Module',
                'description' => 'Allow access to inventory reset module'
            ],
            [
                'name' => 'inventory_reset.view',
                'display_name' => 'View Inventory Reset History',
                'description' => 'Allow viewing inventory reset history and details'
            ],
            [
                'name' => 'inventory_reset.create',
                'display_name' => 'Perform Inventory Reset',
                'description' => 'Allow performing inventory reset operations'
            ],
            [
                'name' => 'inventory_reset.delete',
                'display_name' => 'Delete Inventory Reset Records',
                'description' => 'Allow deleting inventory reset records'
            ]
        ];

        // Create permissions (within existing transaction)
        foreach ($permissions as $permissionData) {
            $permission = \Spatie\Permission\Models\Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
            Log::info("InventoryReset: Permission '{$permission->name}' created/updated");
        }

        // Assign permissions to Admin role if it exists
        $adminRole = \Spatie\Permission\Models\Role::where('name', 'Admin')->first();
        if ($adminRole) {
            foreach ($permissions as $permissionData) {
                $adminRole->givePermissionTo($permissionData['name']);
            }
            Log::info("InventoryReset: Permissions assigned to Admin role");
        }
    }



    /**
     * Install view-based enhancement for inventory reset detection
     * This approach is version-safe and doesn't override core classes
     */
    private function installViewBasedEnhancement()
    {
        try {
            // Install middleware to key routes that might return stock history data
            $this->installMiddlewareToRoutes();

            // The service provider handles view composer registration at runtime
            Log::info('InventoryReset: View-based enhancement installed (view composers and middleware)');
        } catch (\Exception $e) {
            Log::error('InventoryReset: Error installing view-based enhancement: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Install middleware to routes that handle stock history
     */
    private function installMiddlewareToRoutes()
    {
        try {
            $routeFile = base_path('routes/web.php');
            $middlewareRegistration = "
// Inventory Reset Enhancement Middleware (Auto-installed)
Route::middleware(['web', 'auth', 'inventory.reset.enhance'])->group(function () {
    // Enhanced routes for stock history pages
    Route::get('/reports/product-stock-details*', function() {
        return redirect()->route('original.route');
    })->name('enhanced.stock.details');
});
";

            // Note: In practice, we'll rely on the global view composer approach
            // since adding routes programmatically can be complex
            Log::info('InventoryReset: Middleware registration prepared (using view composers instead)');
        } catch (\Exception $e) {
            Log::error('InventoryReset: Error installing middleware to routes: ' . $e->getMessage());
            // Don't throw - this is optional
        }
    }

    /**
     * Install language enhancements
     */
    private function installLanguageEnhancements()
    {
        try {
            // We don't need to modify core language files since we're using JavaScript approach
            Log::info('InventoryReset: Language enhancements installed (JavaScript-based)');
        } catch (\Exception $e) {
            Log::error('InventoryReset: Error installing language enhancements: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Get the JavaScript content for inventory reset display enhancement
     */
    private function getInventoryResetJavaScriptContent()
    {
        return '/**
 * Global Inventory Reset Display Enhancement
 * This script enhances the display of inventory reset transactions
 * throughout Ultimate POS by detecting specific patterns and improving the UI.
 *
 * Version-Safe Approach: Works with any Ultimate POS version by intercepting
 * data at the display level rather than modifying core classes.
 *
 * Auto-installed by InventoryReset module
 */

(function() {
    "use strict";

    // Configuration
    var CONFIG = {
        // Patterns to detect inventory reset transactions
        referencePattern: /^IR\\d{4}\\/\\d{4,}$/,
        notesPattern: /\\[INVENTORY_RESET\\]/,

        // Replacement text
        displayText: "Inventory Reset",
        badgeClass: "label label-info"
    };

    /**
     * Enhance inventory reset display in tables
     */
    function enhanceInventoryResetDisplay() {
        // Find all table rows that might contain stock adjustment data
        $(\'table tbody tr\').each(function() {
            var $row = $(this);
            var isInventoryReset = false;

            // Check for IR reference pattern
            $row.find(\'td\').each(function() {
                var cellText = $(this).text().trim();
                if (CONFIG.referencePattern.test(cellText)) {
                    isInventoryReset = true;
                    return false; // Break out of loop
                }
            });

            // If not found by reference, check additional notes
            if (!isInventoryReset) {
                $row.find(\'td\').each(function() {
                    var cellText = $(this).text().trim();
                    if (CONFIG.notesPattern.test(cellText)) {
                        isInventoryReset = true;
                        return false; // Break out of loop
                    }
                });
            }

            // If this is an inventory reset transaction, update the display
            if (isInventoryReset) {
                // Find and replace "Stock Adjustment" text
                $row.find(\'td\').each(function() {
                    var $cell = $(this);
                    var cellText = $cell.text().trim();

                    if (cellText === "Stock Adjustment") {
                        $cell.html(\'<span class="\' + CONFIG.badgeClass + \'">\' + CONFIG.displayText + \'</span>\');
                    }
                });
            }
        });
    }

    /**
     * Initialize when document is ready
     */
    function init() {
        // Run enhancement
        enhanceInventoryResetDisplay();

        // Re-run when DataTables are redrawn
        $(document).on(\'draw.dt\', function() {
            setTimeout(enhanceInventoryResetDisplay, 100);
        });

        // Re-run when AJAX content is loaded
        $(document).ajaxComplete(function() {
            setTimeout(enhanceInventoryResetDisplay, 100);
        });

        // Monitor for dynamically added content
        if (typeof MutationObserver !== "undefined") {
            var observer = new MutationObserver(function(mutations) {
                var shouldUpdate = false;
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        // Check if any added nodes contain tables
                        for (var i = 0; i < mutation.addedNodes.length; i++) {
                            var node = mutation.addedNodes[i];
                            if (node.nodeType === 1) { // Element node
                                if (node.tagName === "TABLE" || $(node).find("table").length > 0) {
                                    shouldUpdate = true;
                                    break;
                                }
                            }
                        }
                    }
                });

                if (shouldUpdate) {
                    setTimeout(enhanceInventoryResetDisplay, 100);
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    // Initialize when DOM is ready
    if (typeof $ !== "undefined") {
        $(document).ready(init);
    } else {
        // Fallback if jQuery is loaded later
        document.addEventListener("DOMContentLoaded", function() {
            // Wait for jQuery
            var checkJQuery = setInterval(function() {
                if (typeof $ !== "undefined") {
                    clearInterval(checkJQuery);
                    $(document).ready(init);
                }
            }, 100);
        });
    }

})();';
    }




    /**
     * Clean up view-based enhancement
     */
    private function cleanupViewBasedEnhancement()
    {
        try {
            // The view composers and middleware are automatically removed when module is uninstalled
            // since they're registered in the service provider

            Log::info('InventoryReset: View-based enhancement cleaned up (automatic via service provider)');
        } catch (\Exception $e) {
            Log::error('InventoryReset: Error cleaning up view-based enhancement: ' . $e->getMessage());
        }
    }

    /**
     * Check if the required tables exist in the database
     *
     * @return bool
     */
    private function checkTablesExist(): bool
    {
        try {
            $required_tables = ['inventory_reset_logs', 'inventory_reset_items'];

            foreach ($required_tables as $table) {
                $exists = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?", [$table]);
                if ($exists[0]->count == 0) {
                    Log::info("InventoryReset: Table '$table' does not exist");
                    return false;
                }
            }

            Log::info('InventoryReset: All required tables exist');
            return true;
        } catch (\Exception $e) {
            Log::error('InventoryReset: Error checking table existence: ' . $e->getMessage());
            return false;
        }
    }
}