<?php

namespace Database\Seeders;

use App\Enums\ModulesEnum;
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

        $config = [];
        $config['package-manager'] = [
            'packages_days_ahead' => 14
        ];

        $modules = [];
        foreach(ModulesEnum::cases() as $module) {
            $modules[] = Module::factory()->create([
                'name' => $module->getLabel(),
                'slug' => $module->value,
                'config' => $config[$module->value] ?? null,
                'created_by_user' => $admin->id,
            ]);
        }

        $config = [
            'calloff_articles' => [
                'external_synchronization' => [
                    'google_sheets' => [
                        'spreadsheet_id' => '1Dp8jN1iScI1pGi0dEvG5uFmQlcYspXgsfQLSs3ZYD8Q',
                        'sheet_index' => 1,
                        'external_connector_key' => 'Artikelnummer',
                        'internal_connector_key' => 'external_id',
                        'import_fields' => [
                            'external_name' => [
                                'keys' => [
                                    [ 'key' => 'Naam' ],
                                ],
                            ],
                            'min_stock' => [
                                'keys' => [
                                    ['key' => 'IJzeren voorraad']
                                ]
                            ],
                            'online' => [
                                'keys' => [
                                    ['key' => 'Online datum']
                                ]
                            ],
                            'offline' => [
                                'keys' => [
                                    ['key' => 'Offline datum"']
                                ]
                            ],
                            'campagne_manager' => [
                                'keys' => [
                                    ['key' => 'Jumbo CM']
                                ]
                            ],
                            'external_project_manager' => [
                                'keys' => [
                                    ['key' => 'PM HH Global']
                                ]
                            ],
                        ]
                    ]
                ]
            ]
        ];



        $jumbo = Account::factory()->create([
            'client_id' => $bek->id,
            'name' => 'Jumbo Supermarkten',
            'slug' => 'jumbo',
            'erp_id' => '631511',
            'environment' => 'development',
            'config' => $config,
            'erp_status' => '200',
            'created_by_user' => $admin->id
        ]);

        foreach($modules as $module) {
            $jumbo->modules()->attach($module);
        }

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

        foreach($modules as $module) {
            $coop->modules()->attach($module);
        }

        $ddj->users()->attach($admin);
    }
}
