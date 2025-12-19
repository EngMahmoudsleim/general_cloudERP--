<?php

namespace Modules\InventoryReset\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory-reset:install-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Inventory Reset module permissions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Installing Inventory Reset module permissions...');
        
        try {
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

            // Only start transaction if not already in one
            $wasInTransaction = DB::transactionLevel() > 0;
            if (!$wasInTransaction) {
                DB::beginTransaction();
            }

            foreach ($permissions as $permissionData) {
                $permission = Permission::firstOrCreate(
                    ['name' => $permissionData['name']],
                    $permissionData
                );

                $this->line("✓ Permission '{$permission->name}' created/updated");
            }

            // Assign permissions to Admin role if it exists
            $adminRole = \Spatie\Permission\Models\Role::where('name', 'Admin')->first();
            if ($adminRole) {
                foreach ($permissions as $permissionData) {
                    $adminRole->givePermissionTo($permissionData['name']);
                }
                $this->info("✓ Permissions assigned to Admin role");
            }

            // Only commit if we started the transaction
            if (!$wasInTransaction) {
                DB::commit();
            }

            $this->info('Inventory Reset module permissions installed successfully!');
            return 0;

        } catch (\Exception $e) {
            // Only rollback if we started the transaction
            if (!$wasInTransaction && DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $this->error('Failed to install permissions: ' . $e->getMessage());
            return 1;
        }
    }
}