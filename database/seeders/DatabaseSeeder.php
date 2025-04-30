<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Client;
use App\Models\Module;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = User::factory()->create([
            'name' => 'Jasper Brussel',
            'email' => 'jasperbrussel@gmail.com',
            'password' => bcrypt('eey6t5VC'),
            'is_super_admin' => true,
        ]);

        $bek = Client::factory()->create([
            'name' => 'Bek 1 op 1 Publiceren',
            'slug' => 'bek',
            'erp_id' => '011',
            'created_by_user' => $admin->id,
        ]);

        $package_module = Module::factory()->create([
            'name' => 'Pakket Manager',
            'slug' => 'package-manager',
            'created_by_user' => $admin->id,
        ]);

        $stock_module = Module::factory()->create([
            'name' => 'Stock Manager',
            'slug' => 'stock-manager',
            'created_by_user' => $admin->id,
        ]);

        $calloff_article_import_config = [
            'spreadsheet_id' => '1Dp8jN1iScI1pGi0dEvG5uFmQlcYspXgsfQLSs3ZYD8Q',
            'sheet_index' => 1,
            'external_connector_key' => 'Artikelnummer',
            'internal_connector_key' => 'external_id'
        ];

        $jumbo = Account::factory()->create([
            'client_id' => $bek->id,
            'name' => 'Jumbo Supermarkten',
            'slug' => 'jumbo',
            'erp_id' => '631511',
            'environment' => 'development',
            'calloff_article_import_config' => $calloff_article_import_config,
            'erp_status' => '200',
            'created_by_user' => $admin->id
        ]);

        $jumbo->modules()->attach($package_module);
        $bek->users()->attach($admin);

        $ddj = Client::factory()->create([
            'name' => 'Drukkerij Em De Jong',
            'slug' => 'ddj',
            'erp_id' => '001',
            'created_by_user' => $admin->id,
        ]);

        $coop = Account::factory()->create([
            'client_id' => $ddj->id,
            'name' => 'Coop Supermarkten',
            'slug' => 'coop',
            'erp_id' => '626380',
            'environment' => 'development',
            'erp_status' => '200',
            'created_by_user' => $admin->id
        ]);

        $ddj->users()->attach($admin);
    }
}
