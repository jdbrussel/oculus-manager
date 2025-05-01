<?php

namespace App\component;

use Filament\Notifications\Notification;
use App\Enums;


trait customHelpers
{

    /*
     * Synch Data
     */

    public static function checkRequiredData($required_fields = [], $data = [], $debug = false) : array
    {
        $pass = true;
        foreach($required_fields as $_required_key => $_required_value) {
            $found_value = array_key_exists("{$_required_key}", $data) ? $data[$_required_key] : false;
            if(is_bool($_required_value)) {
                if($_required_value === false) {
                    if(!empty($found_value)) {
                        $pass=false;
                        break;
                    }
                }
                if($_required_value === true) {
                    if(!$found_value) {
                        $pass=false;
                        break;
                    }
                }
            }
            else if('". $_required_value . "' !== '" . $data[$_required_key]. "') {
                if(!$found_value) {
                    $pass=false;
                    break;
                }
            }
        }
        if(!$pass) {
            return [];
        }
        return $data;
    }
    public static function checkAccountPackageItemType($data = []) : array
    {

        $data['type'] = null;

        /*
         *  "num_versions" => x
         *  "num_per_version" => x
         */

        $num_versions = (array_key_exists('num_versions', $data) && !empty($data['num_versions']) ) ? (integer)  $data['num_versions'] : false;
        $num_per_version = (array_key_exists('num_per_version', $data) && !empty($data['num_per_version'])) ? (integer) $data['num_per_version'] : false;

        /*
         * Get Enum
         */

        $type_enum = null;

        if($num_versions === 1 && !$num_per_version) {
            $data['num_per_version'] = 1;
            $num_per_version = 1;
        }

        $type_enum = Enums\AccountPackageItem\TypeEnum::ITEM;

        if($num_versions === 1 && $num_per_version === 1) {
            $type_enum = Enums\AccountPackageItem\TypeEnum::ITEM;
        }
        else if($num_versions > 1 && $num_per_version >= 1) {
            $type_enum = Enums\AccountPackageItem\TypeEnum::SET;
        }
        else if($num_versions === 1 && $num_per_version > 1) {
            $type_enum = Enums\AccountPackageItem\TypeEnum::BUNDLE;
        }
        else if($num_versions === 1 && !$num_per_version) {
            $type_enum = Enums\AccountPackageItem\TypeEnum::SHOP_SPECIFIC_SET;
        }

        if($type_enum) {
            $data['type'] = $type_enum;
        }

        return $data;

    }
    /*
     * Translate Data
     */
    public static function translateData($data_source = [], $import_fields = [], $data = [], $debug = false) : array {

        foreach($import_fields as $key => $import_field) {

            $definition = [
                'name' => [
                    'type' => 'string',
                    'transform' => 'capitalize',
                    'operator' => false,
                    'glue' => false,
                    'keys' => [
                        [
                            'key' => 'Naam',
                            'transform' => false
                        ],
                    ],
                ]
            ];

            $type = array_key_exists('type', $import_field) ? $import_field['type'] : 'string';
            $requested_keys = array_key_exists('keys', $import_field) ? $import_field['keys'] : false;

            if($type === 'foreign_key') {
                $local_key = array_key_exists('key', $requested_keys) ? $requested_keys['key'] : false;
                $key_value = (array_key_exists($local_key, $data_source) && !empty($data_source["{$local_key}"])) ? $data_source["{$local_key}"] : false;
                if($key_value) {
                    $foreign_model = array_key_exists('foreign_model', $requested_keys) ? $requested_keys['foreign_model'] : false;
                    $foreign_key = array_key_exists('foreign_key', $requested_keys) ? $requested_keys['foreign_key'] : false;
                    if($foreign_model && $foreign_key) {
                        $db_result = $foreign_model::where($foreign_key, $key_value)->get()->first();
                        if($db_result && $db_result->exists()) {
                            $db_result = $db_result->toArray();
                            $data[$key] = (integer) $db_result['id'];
                            continue;
                        }


                    }
                } else {
                    $data[$key] = null;
                    continue;
                }
            }


            if(!$requested_keys || empty($requested_keys)) {
                $data[$key] = null;
                continue;
            }
            else {
                $results = [];
                foreach ($requested_keys as $requested_key) {
                    if(!is_array($requested_key)) {
                        continue;
                    }
                    $requested_value_key = array_key_exists('key', $requested_key) ? $requested_key['key'] : false;
                    $transform = array_key_exists('transform', $requested_key) ? $requested_key['transform'] : false;
                    if (array_key_exists($requested_value_key, $data_source) && !empty($data_source[$requested_value_key])) {
                        $value = self::transformData($data_source[$requested_value_key], $transform);
                        $results[] = $value;
                    }
                }
            }

            if(empty($results)) {
                $data[$key] = null;
                continue;
            }

            $transform = array_key_exists('transform', $import_field) ? $import_field['transform'] : false;
            $glue = array_key_exists('glue', $import_field) ? $import_field['glue'] : '/';
            $operator = array_key_exists('operator', $import_field) ? $import_field['operator'] : false;

            if(!$operator && count($results) > 1)  {
                $data[$key] = implode('', $results);
            }
            if($operator === 'OR' || !$operator && count($results) === 1) {
                $data[$key] = self::transformData($results[0], $transform);
            }
            if($operator === 'AND') {
                $data[$key] = self::transformData(implode($glue, $results), $transform);
            }

            if($type === 'enum') {
                $enumClass = array_key_exists('enum', $import_field) ? $import_field['enum'] : false;
                if(class_exists($enumClass)) {
                    $enumCase = strtoupper($data[$key]);
                    $enum = $enumClass::from($enumCase);
                   // $enum = constant("{$enumClass}::{$enumCase}");
                    $data[$key] = $enum;
                }

            }
        }

        return $data;
    }
    public static function transformData($value = '',  $transform = false) {
        if($transform) {
            if($transform === 'uppercase') {
                $value = strtoupper($value);
            }
            if($transform === 'lowercase') {
                $value = strtolower($value);
            }
            if($transform === 'capitalize') {
                $value = ucfirst($value);
            }
            if($transform === 'capitals') {
                $value = ucwords($value);
            }
            if($transform === 'integer') {
                $value = (integer) $value;
            }
            if(strpos($transform, 'datetime') > -1) {

                $format = 'Y-m-d H:i:s';
                preg_match("/\('([^']+)'\)/", $transform, $matches);
               IF(count($matches) > 1) {
                   $format = $matches[1];
               }
                $value = date($format, strtotime($value));
            }
            if($transform === 'timestamp') {
                $value = strtotime($value);
            }
        }
        return $value;
    }

    public static function getAddressType($address = []) {

        $address['address_type'] = false;

        $haystack = array_key_exists('name', $address) ? $address['name'] : false;
        if(!$haystack) {
            return $address;
        }

        if(strpos(strtolower($haystack), 'hq') > -1) {
            $address['address_type'] = "headquarter";
        }
        else if(strpos(strtolower($haystack), 'dc') > -1 || strpos(strtolower($haystack), 'dv') > -1) {
            $address['address_type'] = "disribution center";
        }
        else if(!empty($haystack)) {
            $address['address_type'] = "location";
        }

        return $address;
    }
    public static function getAllocationType($package = false, $package_item = [], $address = []) : array {
        $allocation_type = '00';
        $external_id = array_key_exists('external_id', $address) ? $address['external_id'] : false;
        $quantity_reserved = array_key_exists('quantity_reserved', $address) ? $address['quantity_reserved'] : false;
        if($quantity_reserved) {
            $allocation_type = 'RB';
        }
        if($external_id && strlen($external_id) >= 6) {
            $allocation_type_key = substr($external_id,0,2);
            if(strlen($external_id) === 6 && array_key_exists("{$allocation_type_key}", ['88','99'])) {
                $allocation_type = "{$allocation_type_key}";
            }
        }
        $address['allocation_type'] = Enums\AccountPackageItemAddress\AllocationTypeEnum::from("{$allocation_type}");
        return $address;
    }

    /*
     * Notifications
     */
    public static function sendSynchableNotification($reason = false) {
        $title = __('Synchronisatie fout!');
        $body = __(':reason.', ['reason' => $reason]);
        Notification::make()->title($title)->color('oculus')->warning()->body($body)->send();
    }
    public static function sendStatsNotification($stats = [], $object_name = false) {
        $body = [];
        $notifications = [
            'created' => 'aangemaakt',
            'updated' => 'gewijzigd',
            'deleted' => 'verwijderd',
            'restored' => 'hersteld',
        ];
        foreach($notifications as $name => $label) {
            if(array_key_exists($name, $stats) && $stats[$name] > 0) {
                $num = (integer) $stats[$name];
                $body[] = "{$num} " . __(($num===1 ? 'record' : 'records')) . " " . __("{$label}");
            }
        }
        $title = __($object_name) . " " . __('gesynchroniseerd');
        if(!empty($body)) {
            $notification = Notification::make()->title($title)->color('oculus');
            $body = implode(", ", $body);
            $notification->success()->body($body);
        } else {
            $notification = Notification::make()->title($title)->color('oculus');
            $notification->warning()->body(__('geen records gewijzigd'));
        }
        $notification->send();
    }

}
