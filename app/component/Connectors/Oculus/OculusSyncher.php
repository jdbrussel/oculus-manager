<?php

namespace App\component\Connectors\Oculus;

use App\component\customHelpers;
use App\Enums\Account\ErpStatusEnum;
use App\Enums\CountriesEnum;
use App\Enums\AccountPackage\TypeEnum;
use App\Models\AccountAddress;
use App\Models\AccountCalloffArticle;
use App\Models\AccountContact;
use App\Models\AccountPackage;
use App\Models\AccountPackageItem;
use App\Models\User;
use Filament\Notifications\Notification;
use Google\Service\IdentityToolkit\EmailLinkSigninResponse;

class OculusSyncher extends Oculus
{
    use customHelpers;

    public static function synchAccountData($data = false) {

        $erp_id_Key = 'Relatie_nr';

        if(!$data) {
            return [];
        }

        $erp_response = self::checkAccountStatus($data['erp_id'], $data['environment']);

        $data['erp_status'] = ErpStatusEnum::from($erp_response['status']);

        /*
         * UpDATE tHE aCCOUNT
         */

        if($erp_response['status'] === 200) {
            $erp_result = $erp_response['result'];
            $erp_definitions = [
                  "record_number" => "1",
                  "Relatie_nr" => "913509",
                  "Naam" => "Dirk van den Broek",
                  "Adres" => "Flemingweg 1",
                  "Postcode" => "2408 AV",
                  "Plaats" => "Alphen aan den Rijn",
                  "Land" => "NL",
                  "Telefoonnr" => "088 3136000",
                  "Faxnr" => "",
                  "Vertegenwoordiger" => "Dennis van der Hagen",
                  "Vertegenwoordiger_Email" => "",
                  "Orderbegeleider" => "Mathijs Moeskops",
                  "Orderbegeleider_Email" => "mathijs-moeskops@emdejong.nl",
                  "BEK_KlantId" => "DDH",
                  "Betaaltermijn" => "14",
                  "BTW_Nummer" => "NLNL008492529B",
                  "Hoofdrelatie" => "000174",
                  "VerkortNaam" => "dirk",
                  "Switch_Mail1" => "orders@bek.nl",
                  "Switch_Mail2" => "",
                  "Switch_Mail3" => ""
            ];
            $import_fields = [
                'name' => [
                    'transform' => 'capitalize',
                    'operator' => false,
                    'glue' => false,
                    'keys' => [
                         [
                             'key' => 'Naam',
                             'transform' => false
                         ],
                    ],
                ],
                'slug' => [
                    'transform' => false,
                    'operator' => 'OR',
                    'glue' => false,
                    'keys' => [
                        [
                            'key' => 'BEK_KlantId',
                            'transform' => 'uppercase'
                        ],
                        [
                            'key' => 'VerkortNaam',
                            'transform' => 'capitalize'
                        ]
                    ],
                ]
            ];
            $data = self::translateData($erp_result, $import_fields, $data);
        }
        return $data;
    }
    public static function synchAccountCalloffArticles($account = false) {

        if(!$account) {
            return false;
        }
        if(is_array($account)) {
            $account = (object) $account;
        }

        $erp_results = Oculus::getAccountCalloffArticles(
            $account->erp_id,
            $account->environment
        );

        $object_name = 'Afroep artikelen';
        $erp_id_Key = 'ArtikelNummer';

        if($account->id) {

            $db_results = AccountCalloffArticle::withTrashed()
                ->whereNotNull('erp_id')
                ->where('account_id', $account->id)
                ->where('environment', $account->environment)
                ->get();

            $erp_definitions = [
                "record_nummer_artikel" => "1",
                "id" => "JUM008",
                "ArtikelNummer" => "10000052",
                "artikeltype" => "06",
                "artikelVrdtype" => "N",
                "artikelOrdtype" => "N",
                "artikelWebtype" => "N",
                "ArtikelNrKlant" => "JWP016",
                "titel" => "Stoepbordposter print 700x1000",
                "omschrijving" => "Stoepbord Print",
                "rekenqualifier" => "100",
                "image" => "",
                "stuksPerEenheid" => "0",
                "eenheidOmschrijving" => "",
                "Techvoorraad" => "0",
                "voorraadMinimaal" => "0",
                "MinimaleBestelHoev" => "0",
                "MaximaleBestelHoev" => "0",
                "MinimaleProdHoev" => "0",
                "MaximaleProdHoev" => "0",
                "mapid" => "",
                "mapomschrijving" => "",
                "mapgroep" => "",
                "vastekosten" => "0",
                "DirectOrder" => "N",
                "Link" => "",
                "Ordersoort" => "",
                "Levertijd_afroep" => "0",
                "Levertijd_order" => "0",
                "EigendomKlant" => "J",
                "SmartProductCode" => "",
                "EindDatum" => "0",
                "SmartProductBedrukking" => "Eenzijdig",
                "SmartProductFormaat" => "998 mm x 38 mm",
                "SmartProductMachine" => "",
                "EigenaarSmartProductMachine" => "",
                "Vestiging" => "0001",
                "IndicatieAdministratief" => "",
                "prijs" => "0,00"
            ];

            $import_fields = [
                'erp_id' => [
                    'keys' => [
                        ['key' => $erp_id_Key],
                    ],
                ],
                'name' => [
                    'transform' => 'capitalize',
                    'keys' => [
                        ['key' => 'titel'],
                    ],
                ],
                'external_id' => [
                    'keys' => [
                        ['key' => 'ArtikelNrKlant'],
                    ],
                ],
                'external_name' => [
                    'keys' => [
                        ['key' => 'omschrijving'],
                    ],
                ],
            ];

            $stats = [ 'created' => 0, 'updated' => 0, 'restored' => 0 ];

            foreach($erp_results as $erp_result) {

                $data = [
                    'account_id'=> $account->id,
                    'environment' => $account->environment,
                    'synched_at' => now(),
                    'synched_at_user' => auth()->id(),
                ];

                $data = self::translateData($erp_result, $import_fields, $data);

                $required_fields = [
                    'erp_id' => true,
                ];

                $data = self::checkRequiredData($required_fields, $data);

                $erp_id = array_key_exists('erp_id', $data) ? $data['erp_id'] : false;
                if($erp_id) {
                    $db_result = $db_results->where('erp_id', $data['erp_id'])->first();
                    if(empty($db_result)) {
                        $data['created_by_user']  = auth()->id();
                        $new_entity = AccountCalloffArticle::factory()->create($data);
                        $stats['created']++;
                    } else {
                        $data['updated_by_user']  = auth()->id();
                        $entity = AccountCalloffArticle::where('erp_id', $data['erp_id']);
                        if($entity) {
                            $entity->update($data);
                            if (!empty($db_result['deleted_at'])) {
                                $entity->restore();
                                $stats['restored']++;
                            } else {
                                $stats['updated']++;
                            }
                        }
                    }
                }
            }

            self::sendStatsNotification($stats, $object_name);
        }
    }
    public static function synchAccountContacts($account) {

        $erp_results = Oculus::getAccountContacts(
            $account->erp_id,
            $account->environment
        );

        $object_name = 'Account contactpersonen';
        $erp_id_Key = 'ContactPersoon';

        if($account->id) {

            $db_results = AccountContact::withTrashed()
                ->whereNotNull('erp_id')
                ->where('account_id', $account->id)
                ->where('environment', $account->environment)
                ->get();

            $erp_definitions = [
                "record_number" => "1",
                "RelatieNummer" => "631511",
                "ContactPersoon" => "002",
                "BedrijfsNummer" => "002",
                "ContactNaam" => "S. Suppers",
                "ContactAfdeling" => "Account Manager",
                "ContactInfo" => "",
                "ContactTelefoonNummer" => "0031413380200",
                "ContactMobielNummer" => "",
                "ContactEmailAdres" => "S.Suppers@jumbo.nl",
                "ContactFunctie" => "Accountmanager",
                "ContactAanhef" => "",
                "MailingCode01" => "",
                "MailingCode01Omschr" => "",
                "MailingCode02" => "",
                "MailingCode02Omschr" => "",
                "MailingCode03" => "",
                "MailingCode03Omschr" => "",
                "MailingCode04" => "",
                "MailingCode04Omschr" => "",
                "MailingCode05" => "",
                "MailingCode05Omschr" => "",
                "MailingCode06" => "",
                "MailingCode06Omschr" => "",
                "MailingCode07" => "",
                "MailingCode07Omschr" => "",
                "MailingCode08" => "",
                "MailingCode08Omschr" => "",
                "MailingCode09" => "",
                "MailingCode09Omschr" => "",
                "MailingCode10" => "",
                "MailingCode10Omschr" => "",
                "GebruikersNaam" => "",
                "Wachtwoord" => "",
                "ExternTaalCode" => "",
                "AutorisatieGroep" => ""
            ];

            $import_fields = [
                'erp_id' => [
                    'keys' => [
                        ['key' => $erp_id_Key],
                    ],
                ],
                'name' => [
                    'keys' => [
                        ['key' => 'ContactNaam'],
                    ],
                ],
                'email' => [
                    'transform' => 'lowercase',
                    'keys' => [
                        ['key' => 'ContactEmailAdres'],
                    ],
                ],
                'phone' => [
                    'keys' => [
                        ['key' => 'ContactTelefoonNummer'],
                    ],
                ],
                'mobile' => [
                    'keys' => [
                        ['key' => 'ContactMobielNummer'],
                    ],
                ],
                'department' => [
                    'keys' => [
                        ['key' => 'ContactAfdeling'],
                    ],
                ],
                'function' => [
                    'keys' => [
                        ['key' => 'ContactFunctie'],
                    ],
                ],
            ];

            $stats = [ 'created' => 0, 'updated' => 0, 'restored' => 0 ];

            foreach($erp_results as $erp_result) {

                $data = [
                    'account_id'=> $account->id,
                    'environment' => $account->environment,
                    'synched_at' => now(),
                    'synched_at_user' => auth()->id(),
                ];

                $data = self::translateData($erp_result, $import_fields, $data);

                $required_fields = [
                    'email' => true,
//                    'department' => 'Account Manager'
                ];

                $data = self::checkRequiredData($required_fields, $data);

                $erp_id = array_key_exists('erp_id', $data) ? $data['erp_id'] : false;
                if($erp_id) {
                    $db_result = $db_results->where('erp_id', $data['erp_id'])->first();
                    if(empty($db_result)) {
                        $data['created_by_user']  = auth()->id();
                        $new_entity = AccountContact::factory()->create($data);
                        $stats['created']++;
                    } else {
                        $data['updated_by_user']  = auth()->id();
                        $entity = AccountContact::where('erp_id', $data['erp_id']);
                        if($entity) {
                            $entity->update($data);
                            if (!empty($db_result['deleted_at'])) {
                                $entity->restore();
                                $stats['restored']++;
                            } else {
                                $stats['updated']++;
                            }
                        }
                    }
                }
            }

            self::sendStatsNotification($stats, $object_name);

        }
    }
    public static function synchAccountUsers($account = false) {

        $object_name = 'Account gebruikers';
        $erp_account = Oculus::getAccount($account->erp_id, $account->environment);
        $client = $account->client;
        if(!$erp_account['error']) {

            $db_results = User::withTrashed()
                ->whereNotNull('created_by_user')
                ->where('environment', $account->environment);
//          ->whereRelation('accounts', 'account_id', $account->id);

            foreach ($db_results->get()->toArray() as $db_result) {
                User::where('id', $db_result['id'])->update(['pre_delete' => true]);
            }

            $erp_definitions = [
                "record_number" => "1",
                "Relatie_nr" => "631511",
                "Naam" => "Jumbo Supermarkten bv",
                "Adres" => "Rijksweg 15",
                "Postcode" => "5462 CE",
                "Plaats" => "Veghel",
                "Land" => "NL",
                "Telefoonnr" => "0031413380200",
                "Faxnr" => "",
                "Vertegenwoordiger" => "M. Kammeijer",
                "Vertegenwoordiger_Email" => "martijn-kammeijer@emdejong.nl",
                "Orderbegeleider" => "Danae Schriks",
                "Orderbegeleider_Email" => "danae.schriks@bek.nl",
                "BEK_KlantId" => "JWP",
                "Betaaltermijn" => "90",
                "BTW_Nummer" => "NL001172359B01",
                "Hoofdrelatie" => "000163",
                "VerkortNaam" => "Jumbo",
                "Switch_Mail1" => "jumbo@bek.nl",
                "Switch_Mail2" => "jumbo-pos@emdejong.nl",
                "Switch_Mail3" => ""
            ];

            $user_types = [
                'order_manager' => [
                    'name_key' => 'Orderbegeleider',
                    'email_key' => 'Orderbegeleider_Email',
                ],
                'account_manager' => [
                    'name_key' => 'Vertegenwoordiger',
                    'email_key' => 'Vertegenwoordiger_Email'
                ]
            ];

            $stats = ['created' => 0, 'updated' => 0, 'restored' => 0];

            foreach ($user_types as $user_type => $user_type_keys) {

                $name = array_key_exists($user_type_keys['name_key'], $erp_account['result']) ? $erp_account['result'][$user_type_keys['name_key']] : false;
                $email = array_key_exists($user_type_keys['email_key'], $erp_account['result']) ? $erp_account['result'][$user_type_keys['email_key']] : false;

                if ($name && $email) {

                    $db_result = User::withTrashed()
                        ->where('email', $email)
                        ->where('environment', $account->environment);
                    $num_results = $db_result->get()->count();

                    if ($num_results === 0) {
                        $new_entity = User::factory()->create([
                            'name' => $name,
                            'email' => strtolower($email),
                            'password' => '1234',
                            'created_by_user' => auth()->id(),
                            'environment' => $account->environment,
                            'synched_at' => now(),
                            'synched_at_user' => auth()->id(),
                        ]);

                        $client->users()->attach($new_entity->id);
                        $account->users()->attach($new_entity->id);

                        $stats['created']++;

                    } else {

                        $entity = $db_result->first();
                        $entity->update([
                            'name' =>  $name,
                            'environment' => $account->environment,
                            'pre_delete' => null,
                            'synched_at' => now(),
                            'synched_at_user' => auth()->id(),
                        ]);

                        $client->users()->detach($entity->id);
                        $client->users()->attach($entity->id);

                        $account->users()->detach($entity->id);
                        $account->users()->attach($entity->id);

                        if (!empty($entity['deleted_at'])) {
                            $entity->restore();
                            $stats['restored']++;
                        } else {
                            $stats['updated']++;
                        }
                    }

                }
            }

            $db_results = User::withTrashed()
                ->whereNotNull('pre_delete')
                ->whereRelation('accounts', 'account_id', $account->id);
            foreach ($db_results->get()->toArray() as $db_result) {
                User::where('id', $db_result['id'])->delete();
            }

            self::sendStatsNotification($stats, $object_name);

        }
        else {
            Notification::make()
                ->title(__('Synchronisatie mislukt'))
                ->body($erp_account['error'])
                ->danger()
                ->color('oculus')
                ->send();
        }

    }
    public static function synchAccountAddresses($account = false) {

        $erp_results = Oculus::getAccountAddresses(
            $account->erp_id,
            $account->environment
        );

        $object_name = 'Account adressen';
        $erp_id_Key = 'OndernemerNummer';

        if($account->id) {
            $db_results = AccountAddress::withTrashed()
                ->whereNotNull('erp_id')
                ->where('account_id', $account->id)
                ->where('environment', $account->environment)
                ->get();

            $erp_definitions = [
                "record_number" => "1",
                "OndernemerNummer" => "000001",
                "Naam" => "Jumbo HQ WINKELSERVICEDESK",
                "Tweede_Naam" => "",
                "Adres" => "Rijksweg  15",
                "Huisnr" => "",
                "Huisnr_Toevoeging" => "",
                "Postcode" => "5462 CE",
                "Plaats" => "Veghel",
                "RayonCode" => "",
                "Land" => "NL",
                "Telefoonnr" => "",
                "Faxnr" => "",
                "Email" => "",
                "Referentie" => "103",
                "Afw_Naam" => "",
                "Afw_Tweede_Naam" => "",
                "Afw_Adres" => "",
                "Afw_Huisnr" => "",
                "Afw_Huisnr_Toevoeging" => "",
                "Afw_Postcode" => "",
                "Afw_FPlaats" => "",
                "Afw_land" => "",
                "Opmerking1" => "",
                "Opmerking2" => "",
                "Datum_Laatst_Gebruik" => "20240322",
                "Indicatie_Naamindruk" => "",
                "Pallettype" => "",
                "Indicatie_Vaste_Verpakking" => "",
                "EANnummer" => "",
                "Debiteur" => "",
                "Klantenkaart" => "",
                "Adrestype" => "H",
                "Formule" => "",
                "Taal" => "",
                "Regio" => "",
                "DC_themapakket" => "",
                "DC_weekpakket" => "",
                "DC_dagpakket" => "",
                "DC_directe_levering" => "",
                "alternatievereferentie" => "",
                "Contactpersoon" => "",
                "CMYK_kleur" => "",
                "Background_color" => "",
                "Fillcolor" => "",
                "Labeltekst_dagpakket" => "",
                "Labeltekst_weekpakket" => "",
                "Labeltekst_campagnepakket" => "",
                "Sluitingsdatum" => "0",
                "Openingsdatum" => "0"
            ];

            $import_fields = [
                'erp_id' => [
                    'keys' => [
                        ['key' => $erp_id_Key],
                    ],
                ],
                'external_id' => [
                    'keys' => [
                        ['key' => 'Referentie'],
                    ],
                ],
                'name' => [
                    'keys' => [
                        ['key' => 'Naam'],
                    ],
                ],
                'street' => [
                    'keys' => [
                        ['key' => 'Adres'],
                    ],
                ],
                'house_number' => [
                    'keys' => [
                        ['key' => 'Huisnr'],
                    ],
                ],
                'house_number_additional' => [
                    'keys' => [
                        ['key' => 'Huisnr_Toevoeging'],
                    ],
                ],
                'postal_code' => [
                    'keys' => [
                        [
                            'key' => 'Postcode',
                            'transform' => 'uppercase'
                        ]
                    ],
                ],
                'city' => [
                    'keys' => [
                        ['key' => 'Plaats'],
                    ],
                ],
                'country' => [
                    'type' => 'enum',
                    'enum' => 'App\Enums\CountriesEnumEnum',
                    'keys' => [
                        [
                            'key' => 'Land',
                            'transform' => 'Capitalize'
                        ],
                    ],
                ],
                'dc_day_id' => [
                    'keys' => [
                        [
                            'key' => 'DC_dagpakket'
                        ],
                    ],
                ],
                'dc_week_id' => [
                    'keys' => [
                        [
                            'key' => 'DC_weekpakket'
                        ],
                    ],
                ],
                'dc_theme_id' => [
                    'keys' => [
                        [
                            'key' => 'DC_dagpakket'
                        ],
                    ],
                ],
            ];

            $stats = [ 'created' => 0, 'updated' => 0, 'restored' => 0 ];

            foreach($erp_results as $erp_result) {

                $data = [
                    'account_id'=> $account->id,
                    'environment' => $account->environment,
                    'synched_at' => now(),
                    'synched_at_user' => auth()->id(),
                ];

                $data = self::translateData($erp_result, $import_fields, $data);
                $data = self::getAddressType($data);

                $required_fields = [
                    'external_id' => true,
                ];

                $data = self::checkRequiredData($required_fields, $data);

                $erp_id = array_key_exists('erp_id', $data) ? $data['erp_id'] : false;
                if($erp_id) {
                    $db_result = $db_results->where('erp_id', $data['erp_id'])->first();
                    if(empty($db_result)) {
                        $data['created_by_user']  = auth()->id();
                        $new_entitu = AccountAddress::factory()->create($data);
                        $stats['created']++;
                    } else {
                        $data['updated_by_user'] = auth()->id();
                        $entity = AccountAddress::where('erp_id', $data['erp_id']);
                        if($entity) {
                            $entity->update($data);
                            if(!empty($db_result['deleted_at'])) {
                                $entity->restore();
                                $stats['restored']++;
                            } else {
                                $stats['updated']++;
                            }
                        }

                    }
                }
            }

            self::sendStatsNotification($stats, $object_name);
        }
    }
    public static function synchAccountPackages($account = false, $synch_account_package_items = false) {

        if(!$account) {
            return false;
        }
        if(is_array($account)) {
            $account = (object) $account;
        }

        $erp_results = Oculus::getAccountPackages(
            $account->erp_id,
            $account->environment
        );

        $object_name = 'Account Pakketten';
        $erp_id_Key = 'Pakket_nr';

        if($account->id) {

            $db_items = AccountPackage::withoutTrashed()
                ->whereNotNull('erp_id')
                ->where('account_id', $account->id)
                ->where('environment', $account->environment);
            $db_results = $db_items->get();

            /*
             * pre_delete items
             * Will be set to Null
             * if entity is updated
             */
            $db_items->update([
                'pre_delete' => now()
            ]);

            $erp_definitions = [
                "record_nummer" => "1",
                "Pakket_boekjaar" => "2024",
                "Pakket_nr" => "3034821",
                "Klantnr" => "631511",
                "Type_doos" => "W",
                "Type_doos_omschrijving" => "Weekpakket",
                "Omschrijving" => "Weekpakket",
                "Editie" => "2025-11",
                "Datum_vanaf_bestel" => "20250227",
                "Tyd_vanaf_bestel" => "160000",
                "Datum_tm_bestel" => "20250306",
                "Tyd_tm_bestel" => "160000",
                "Datum_vanaf_verwerk" => "20250306",
                "Tyd_vanaf_verwerk" => "160000",
                "Datum_tm_verwerk" => "20250306",
                "Tyd_tm_verwerk" => "163000",
                "Datum_productie_gereed" => "20250306",
                "Tyd_productie_gereed" => "190000",
                "Datum_fulfilment" => "20250307",
                "Tyd_fulfilment" => "220000",
                "Datum_levering" => "20250310",
                "Tyd_levering" => "160000",
                "Datum_vanaf_promotielooptijd" => "20250312",
                "Tyd_vanaf_promotielooptijd" => "080000",
                "Datum_tm_promotielooptijd" => "20250318",
                "Tyd_tm_promotielooptijd" => "200000",
                "Verpakkingslocatie" => "0011"
            ];

            $import_fields = [
                'erp_id' => [
                    'keys' => [
                        ['key' => $erp_id_Key],
                    ],
                ],
                'year' => [
                    'keys' => [
                        ['key' => 'Pakket_boekjaar'],
                    ],
                ],
                'edition' => [
                    'keys' => [
                        ['key' => 'Editie'],
                    ],
                ],
                'external_name' => [
                    'keys' => [
                        ['key' => 'Omschrijving'],
                    ],
                ],
//                'type' => [
//                    'keys' => [
//                        ['key' => 'Type_doos'],
//                    ],
//                ],

                'type' => [
                    'type' => 'enum',
                    'enum' => 'App\Enums\AccountPackage\TypeEnum',
                    'keys' => [
                        [
                            'key' => 'Type_doos',
                            'transform' => 'Capitalize'
                        ],
                    ],
                ],

                'order_datetime_from' => [
                    'transform' => 'timestamp',
                    'operator' => 'AND',
                    'glue' => ' ',
                    'keys' => [
                        [
                            'key' => 'Datum_vanaf_bestel',
                            'transform' => 'datetime(\'Y-m-d\')'
                        ],
                        [
                            'key' => 'Tyd_vanaf_bestel',
                            'transform' => 'datetime(\'H:i:s\')'
                        ],
                    ],
                ],
                'order_datetime_until' => [
                    'transform' => 'timestamp',
                    'operator' => 'AND',
                    'glue' => ' ',
                    'keys' => [
                        [
                            'key' => 'Datum_tm_bestel',
                            'transform' => 'datetime(\'Y-m-d\')'
                        ],
                        [
                            'key' => 'Tyd_tm_bestel',
                            'transform' => 'datetime(\'H:i:s\')'
                        ],
                    ],
                ],
                'order_in_production_datetime_from' => [
                    'transform' => 'timestamp',
                    'operator' => 'AND',
                    'glue' => ' ',
                    'keys' => [
                        [
                            'key' => 'Datum_vanaf_verwerk',
                            'transform' => 'datetime(\'Y-m-d\')'
                        ],
                        [
                            'key' => 'Datum_vanaf_verwerk',
                            'transform' => 'datetime(\'H:i:s\')'
                        ],
                    ],
                ],
                'order_in_production_datetime_until' => [
                    'transform' => 'timestamp',
                    'operator' => 'AND',
                    'glue' => ' ',
                    'keys' => [
                        [
                            'key' => 'Datum_tm_verwerk',
                            'transform' => 'datetime(\'Y-m-d\')'
                        ],
                        [
                            'key' => 'Tyd_tm_verwerk',
                            'transform' => 'datetime(\'H:i:s\')'
                        ],
                    ],
                ],
                'order_production_ready_datetime' => [
                    'transform' => 'timestamp',
                    'operator' => 'AND',
                    'glue' => ' ',
                    'keys' => [
                        [
                            'key' => 'Datum_productie_gereed',
                            'transform' => 'datetime(\'Y-m-d\')'
                        ],
                        [
                            'key' => 'Tyd_productie_gereed',
                            'transform' => 'datetime(\'H:i:s\')'
                        ],
                    ],
                ],
                'scheduled_fulfilment_datetime' => [
                    'transform' => 'timestamp',
                    'operator' => 'AND',
                    'glue' => ' ',
                    'keys' => [
                        [
                            'key' => 'Datum_fulfilment',
                            'transform' => 'datetime(\'Y-m-d\')'
                        ],
                        [
                            'key' => 'Tyd_fulfilment',
                            'transform' => 'datetime(\'H:i:s\')'
                        ],
                    ],
                ],
                'scheduled_delivery_datetime' => [
                    'transform' => 'timestamp',
                    'operator' => 'AND',
                    'glue' => ' ',
                    'keys' => [
                        [
                            'key' => 'Datum_levering',
                            'transform' => 'datetime(\'Y-m-d\')'
                        ],
                        [
                            'key' => 'Tyd_levering',
                            'transform' => 'datetime(\'H:i:s\')'
                        ],
                    ],
                ],
                'run_time_datetime_from' => [
                    'transform' => 'timestamp',
                    'operator' => 'AND',
                    'glue' => ' ',
                    'keys' => [
                        [
                            'key' => 'Datum_vanaf_promotielooptijd',
                            'transform' => 'datetime(\'Y-m-d\')'
                        ],
                        [
                            'key' => 'Tyd_vanaf_promotielooptijd',
                            'transform' => 'datetime(\'H:i:s\')'
                        ],
                    ],
                ],
                'run_time_datetime_until'  => [
                    'transform' => 'timestamp',
                    'operator' => 'AND',
                    'glue' => ' ',
                    'keys' => [
                        [
                            'key' => 'Datum_tm_promotielooptijd',
                            'transform' => 'datetime(\'Y-m-d\')'
                        ],
                        [
                            'key' => 'Tyd_tm_promotielooptijd',
                            'transform' => 'datetime(\'H:i:s\')'
                        ],
                    ],
                ],
                'handling_location_id'  => [
                    'keys' => [
                        ['key' => 'Verpakkingslocatie'],
                    ],
                ],
            ];

            $stats = [ 'created' => 0, 'updated' => 0, 'restored' => 0, 'deleted' => 0 ];

            foreach($erp_results as $erp_result) {

                $data = [
                    'account_id'=> $account->id,
                    'environment' => $account->environment,
                    'pre_delete' => null,
                    'synched_at' => now(),
                    'synched_at_user' => auth()->id(),
                ];

                $data = self::translateData($erp_result, $import_fields, $data);

                $required_fields = [
                    'type' => true,
                ];

                $data = self::checkRequiredData($required_fields, $data);

                $erp_id = array_key_exists('erp_id', $data) ? $data['erp_id'] : false;
                if($erp_id) {
                    $db_result = $db_results->where('erp_id', $data['erp_id'])->first();
                    if(empty($db_result)) {
                        $data['created_by_user']  = auth()->id();
                        $entity = AccountPackage::factory()->create($data);
                        $stats['created']++;
                    } else {
                        $data['updated_by_user']  = auth()->id();
                        $entity = AccountPackage::where('erp_id', $data['erp_id']);
                        if($entity) {
                            $entity->update($data);
                            if (!empty($db_result['deleted_at'])) {
                                $entity->restore();
                                $stats['restored']++;
                            } else {
                                $stats['updated']++;
                            }
                        }
                    }
                    if($synch_account_package_items) {
                        self::synchAccountPackageItems($entity->first(), false);
                    }
                }
            }

            /*
             * (Soft) Delete
             * pre_deleted items
             */
            $pre_deleted_items = AccountPackage::withoutTrashed()
                ->whereNotNull('pre_delete')
                ->where('account_id', $account->id)
                ->where('environment', $account->environment);
            $stats['deleted'] = $pre_deleted_items->count();
            $pre_deleted_items->delete();
            //$pre_deleted_items->update(['deleted_at' => now(), 'deleted_at_user' => auth()->id()]);

            /*
             * Send Notification
             */
            self::sendStatsNotification($stats, $object_name);
        }
    }
    public static function synchAccountPackageItems($account_package = false, $notify = true) {

        $synchable = $account_package->status->isSynchable();
        if(array_key_exists('error', $synchable) && !empty($synchable['error'])) {
            self::sendSynchableNotification($synchable['error']);
            return $account_package;
        }

        $db_entities = AccountPackageItem::withTrashed()
            ->where('account_package_id' , $account_package->id)
            ->where('environment', $account_package->environment);
        $db_results = $db_entities->get();

        $erp_results = self::getAccountPackageItems($account_package);

        $object_name = 'Pakketonderdelen';
        $erp_id_key = 'Ordernummer';

        $erp_definitions = [
            "record_number" => "1",
            "Regelnummer" => "001",
            "Onderdeelomschrijving" => "A3 inlay PROMO-kop 1",
            "AantalVersies" => "1",
            "Klantreferentie" => "Methode 4_20250312-091845314",
            "Onderdeeltype" => "DDJ",
            "Type_order" => "Oculus",
            "Boekjaar" => "2024",
            "Ordernummer" => "6244263",
            "UitbesteedAan" => "",
            "Op_IGEN_Vergaard" => "",
            "Volgnummer" => "0010",
            "Setverdeling" => "",
            "ProductieOplage" => "671",
            "SmartProductCode" => "B0003",
            "Productformaat" => "297 mm x 420 mm",
            "KlaarVoorVergaar" => "N",
            "OrderType" => "V",
            "OrderCategorie" => "",
            "OndernemerNummer" => "",
            "OndernemerReferentie" => "",
            "Bestelnummer" => "",
            "alternatievereferentie" => "",
            "aantdeelnemendeondernemers" => "661",
            "aantalperversie" => "1",
            "VoorraadOplage" => "10",
            "AllocatieOplage" => "658",
            "ReserveOplage" => "3"
        ];

        $import_fields = [
            'erp_id' => [
                'keys' => [
                    ['key' => $erp_id_key],
                ],
            ],
            'year' => [
                'keys' => [
                    ['key' => 'Boekjaar'],
                ],
            ],
            'name' => [
                'keys' => [
                    [
                        'key' => 'Onderdeelomschrijving',
                        'transform' => 'ucfirst'
                    ],
                ],
            ],
            'external_id' => [
                'keys' => [
                    ['key' => 'Klantreferentie'],
                ],
            ],
            'external_name' => [
                'keys' => [
                    ['key' => 'Omschrijving'],
                ],
            ],
            'num_versions' => [
                'format' => 'integer',
                'keys' => [
                    ['key' => 'AantalVersies'],
                ],
            ],
            'num_per_version' => [
                'format' => 'integer',
                'keys' => [
                    ['key' => 'aantalperversie'],
                ],
            ],
            'quantity' => [
                'keys' => [
                    [
                        'key' => 'AllocatieOplage',
                        'transform' => 'integer'
                    ]
                ]
            ],
            'quantity_reserved' => [
                'keys' => [
                    [
                        'key' => 'ReserveOplage',
                        'transform' => 'integer'
                    ]
                ]
            ],
            'quantity_stock' => [
                'keys' => [
                    [
                        'key' => 'VoorraadOplage',
                        'transform' => 'integer'
                    ]
                ]
            ]
        ];

        $stats = [ 'created' => 0, 'updated' => 0, 'restored' => 0 ];

        foreach($erp_results as $erp_result) {

            $data = [
                'account_package_id' => $account_package->id,
                'environment' => $account_package->environment
            ];

            $data = self::translateData($erp_result, $import_fields, $data);

            $data['allocation'] = [];

            $required_fields = [
                'erp_id' => true,
                'year' => true,
            ];

            $data = self::checkRequiredData($required_fields, $data);
            $data = self::checkAccountPackageItemType($data);

            $erp_id = array_key_exists('erp_id', $data) ? $data['erp_id'] : false;

            if($erp_id) {

                $data['allocation'] = self::synchAccountPackageItemsAllocation($account_package, $data);

                $data['synched_at'] = now();
                $data['synched_at_user'] = auth()->id();

                $db_result = $db_results->where('erp_id', $data['erp_id'])->first();
                if (empty($db_result)) {
                    $data['created_by_user'] = auth()->id();
                    $entity = AccountPackageItem::factory()->create($data);
                    $stats['created']++;
                } else {
                    $data['updated_by_user'] = auth()->id();
                    $entity = AccountPackageItem::where('erp_id', $data['erp_id']);
                    if ($entity) {

                        $entity->update($data);
                        if (!empty($db_result['deleted_at'])) {
                            $entity->restore();
                            $stats['restored']++;
                        } else {
                            $stats['updated']++;
                        }
                    }
                }
            }
            $log["{$erp_id}"] = $data;
        }

        self::sendStatsNotification($stats, $object_name);

        return $account_package;
    }
    public static function synchAccountPackageItemsAllocation($account_package = false, $account_package_item = false) {

        if(!$account_package) {
            $allocation['error'] = __('No Package Found');
            dd($allocation);
            return $allocation;
        }

        if(!$account_package_item) {
            $allocation['error'] = __('No Package item Found');
            dd($allocation);
            return $allocation;
        }

        $erp_results = Oculus::getAccountPackageItemAllocation($account_package, $account_package_item);

        $erp_definitions = [
            "record_number" => "1",
            "Type_Levering" => "W",
            "Klantnummer" => "631511",
            "OndernemerNummer" => "000045",
            "DC_Thema_Pakket" => "",
            "DC_Week_Pakket" => "000008",
            "DC_Dag_Pakket" => "",
            "DC_Directe_levering" => "",
            "Naam" => "Pakket: 3034823",
            "Tweede_Naam" => "",
            "Adres" => "",
            "Huisnr" => "",
            "Huisnr_Toevoeging" => "",
            "Postcode" => "",
            "Plaats" => "",
            "RayonCode" => "",
            "Land" => "NL",
            "Oplage" => "1",
            "Referentie" => "3021",
            "artikelnummer" => "",
            "artikelomschrijving" => "",
            "artikelaantal" => "0",
            "alternatievereferentie" => "",
            "NaamOndernmr" => "Jumbo Emmen (Het Waal / Houtwe",
            "reserve" => "0"
        ];

        /*
        * if Reserve > 0
        * Distribution is Rebuild
        *
        * if strlen(external_id) === 6 && in_array(substr(external_id, -2), ['88','99'])
        * Distribution is Reserve
        */

        $import_fields = [
            'erp_id' => [
                'keys' => [
                    [ 'key' => 'OndernemerNummer' ],
                ],
            ],
            'external_id' => [
                'keys' => [
                    [ 'key' => 'Referentie' ],
                ],
            ],
            'external_name' => [
                'keys' => [
                    [ 'key' => 'NaamOndernmr' ],
                ],
            ],
            'formula' => [
                'keys' => false
            ],
            'rayon' => [
                'keys' => [
                    [
                        'key' => 'RayonCode'
                    ],
                ]
            ],
            'dc_day_id' => [
                'type' => 'foreign_key',
                'keys' => [
                    'key' => 'DC_Dag_Pakket',
                    'foreign_model' => 'App\Models\AccountAddress',
                    'foreign_key' => 'erp_id'
                ]
            ],
            'dc_week_id' => [
                'type' => 'foreign_key',
                'keys' => [
                    'key' => 'DC_Week_Pakket',
                    'foreign_model' => 'App\Models\AccountAddress',
                    'foreign_key' => 'erp_id'
                ]
            ],
            'dc_theme_id' => [
                'type' => 'foreign_key',
                'keys' => [
                    'key' => 'DC_Theme_Pakket',
                    'foreign_model' => 'App\Models\AccountAddress',
                    'foreign_key' => 'erp_id'
                ]
            ],
            'country' => [
                'type' => 'enum',
                'enum' => 'App\Enums\CountriesEnumEnum',
                'keys' => [
                    [
                        'key' => 'Land',
                        'transform' => 'Capitalize'
                    ],
                ],
            ],
            'quantity' => [
                'keys' => [
                    [ 'key' => 'Oplage', 'format' => 'integer'  ],
                ],
            ],
            'quantity_reserved' => [
                'keys' => [
                    [ 'key' => 'reserve' , 'format' => 'integer' ],
                ],
            ]
        ];

        $required_fields = [];

        /*
         * @todo: Add Conditions for required fields
                based on Package Distribution Type ($account_package->type);
         */

        $required_fields = [
            'external_id' => true,
        ];

        $allocation = [
            'error' => false,
            'data' => []
        ];

        foreach($erp_results as $erp_result) {

            $data = self::translateData($erp_result, $import_fields, [], true);
            $data = self::checkRequiredData($required_fields, $data);

            $allocation['data'][] = $data;
            continue;

            $data = self::getAllocationType($account_package, $account_package_item, $data);
            $allocation_keys = $data['allocation_type']->getAllocationArrayKeys();
            $level_1 = array_key_exists(0, $allocation_keys) ? $allocation_keys[0] : false;
            $level_2 = array_key_exists(1, $allocation_keys) ? $allocation_keys[1] : false;

            if($level_1 && !array_key_exists($level_1, $allocation)) {
                $allocation[$level_1] = [];
            }
            if(!$level_2) {
                $allocation[$level_1]["{$data['external_id']}"] = $data;
            }
            else if($level_2 && !array_key_exists($level_2, $allocation[$level_1])) {
                $allocation[$level_1][$level_2] = [];
            }
            if($level_2) {
                $allocation[$level_1][$level_2]["{$data['external_id']}"] = $data;
            }
        }

        return $allocation;
    }

}
