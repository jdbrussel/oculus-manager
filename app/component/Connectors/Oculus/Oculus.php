<?php

namespace App\component\Connectors\Oculus;

use App\Enums\EnvironmentEnum;
use App\Models\Account;
use Illuminate\Support\Facades\Http;

class Oculus extends Http
{
    protected static ?string $baseUrl;
    private static ?string $serviceUrl;
    private static ?string $username;
    private static ?string $password;
    private static ?string $connectorKey = '';
    private static ?array $connectorKeys = [];
    private static ?string $connectorId = '';
    private static ?string $resultSetKey;
    private static function setEnvironment(EnvironmentEnum $environment) {

        if($environment->value === 'production') {
            self::$baseUrl = getenv('OCULUS_PROD_URL');
            self::$username = getenv('OCULUS_PROD_USERNAME');
            self::$password = getenv('OCULUS_PROD_PASSWORD');
        }
        else if($environment->value === 'development') {
            self::$baseUrl = getenv('OCULUS_DEV_URL');
            self::$username = getenv('OCULUS_DEV_USERNAME');
            self::$password = getenv('OCULUS_DEV_USERWORD');
        }
    }
    private static function setServiceUrl($serviceName = false) {
        self::$serviceUrl = self::$baseUrl . $serviceName;
    }
    private static function getResults(): array
    {

        $request_params = [];
        if(is_array(self::$connectorKeys) && !empty(self::$connectorKeys)) {
            $request_params = self::$connectorKeys;
        } else if(!empty(self::$connectorKey ) && !empty(self::$connectorId)) {
            $request_params = [
                self::$connectorKey => self::$connectorId
            ];
        }
        set_time_limit(3600);
        $response = self::timeout(180)
            ->withBasicAuth(self::$username, self::$password)
            ->get(self::$serviceUrl, $request_params)
            ->json();

        $results = [];
        if(is_array($response)) {
            $results = array_key_exists(self::$resultSetKey , $response) ? $response[self::$resultSetKey] : [];
        }

        return $results;
    }
    public static function checkAccountStatus($connectorId = false, $environment = false) : array {
        if(!empty($connectorId) && !empty($environment)) {

            $account = self::getAccount($connectorId, $environment);

            if(empty($account['error'])) {
                return [
                    'error' => false,
                    'status' => $account['status_code'],
                    'result' => $account['result']
                ];
            } else {
                return [
                    'error' => $account['error'],
                    'status' => $account['status_code'],
                    'result' => []
                ];
            }
        }
        return [
            'error' => 'No Account Data Provided',
            'status' => 300,
            'result' => []
        ];
    }
    public static function getAccount($connectorId = false,  $environment = false) : array
    {
        if(!$connectorId || !$environment) {
            return [
                'error' => 'Missing connectorId or environment',
                'status_code' => 300.
            ];
        }
        if(is_string($environment)) {
            $environment = EnvironmentEnum::from($environment);
        }

        self::setEnvironment($environment);
        self::setServiceUrl('GetKlantData');
        self::$connectorKey = 'RelatieNr';
        self::$resultSetKey = 'subfile';

        $response = self::withBasicAuth(self::$username, self::$password)->get(self::$serviceUrl, [
            self::$connectorKey => "{$connectorId}"
        ]);

        if($response->failed()) {
            if($response->getStatusCode() == '401') {
                return [
                    'error' => $response->getReasonPhrase(),
                    'status_code' => $response->getStatusCode()
                ];
            }
        }
        else {
            $response = $response->json();
        }

        if(is_array($response)) {
            $results = array_key_exists(self::$resultSetKey , $response) ? $response[self::$resultSetKey ] : [];
            if (count($results) === 1) {
                return [
                    'error' => false,
                    'status_code' => 200,
                    'result' => $results[0]
                ];
            }
        }
        return [
            'error' => 'No results found',
            'status_code' => 300,
            'response' => $response
        ];
    }
    public static function getAccountAddresses($connectorId = false,  $environment = false) : array
    {
        if(!$connectorId || !$environment) {
            return [];
        }

        $account = Account::where('erp_id', $connectorId)->first();
        if(!$account->id || !$account->erp_id) {
            return [];
        }

        self::setEnvironment($environment);
        self::setServiceUrl('GetOndernemers');
        self::$connectorKey = 'RelatieNr';
        self::$resultSetKey = 'subfile';
        self::$connectorId = $account->erp_id;

        $results = self::getResults();
        return $results;
    }
    public static function getAccountContacts($connectorId = false,  $environment = false) : array
    {
        if(!$connectorId || !$environment) {
            return [];
        }

        $account = Account::where('erp_id', $connectorId)->first();
        if(!$account->id || !$account->erp_id) {
            return [];
        }

        self::setEnvironment($environment);
        self::setServiceUrl('GetContactData');
        self::$connectorKey = 'RelNum';
        self::$resultSetKey = 'subfile';
        self::$connectorId = $account->erp_id;

        $results = self::getResults();
        return $results;
    }
    public static function getAccountPackages($connectorId = false,  $environment = false) : array
    {
        if(!$connectorId || !$environment) {
            return [];
        }

        $account = Account::where('erp_id', $connectorId)->first();
        if(!$account->id || !$account->erp_id) {
            return [];
        }

        self::setEnvironment($environment);
        self::setServiceUrl('GetPeriodeDozen');
        self::$connectorKey = 'RelatieNr';
        self::$connectorId = $account->erp_id;
        self::$resultSetKey = 'subfile';

        $results = self::getResults();
        return $results;
    }
    public static function getAccountPackageItems($account_package = false) : array
    {
        if(!$account_package) {
            return [];
        }
        self::setEnvironment($account_package->environment);
        self::setServiceUrl('GetPakketOnderdelen');
        self::$connectorKeys = [
            'PakketBj' => $account_package->year,
            'PakketNr' => $account_package->erp_id
        ];
        self::$resultSetKey = 'subfile';
        $results = self::getResults();
        return $results;
    }
    public static function getAccountCalloffArticles($connectorId = false,  $environment = false) : array
    {
        if(!$connectorId || !$environment) {
            return [];
        }

        $account = Account::where('erp_id', $connectorId)->first();
        if(!$account->id || !$account->erp_id) {
            return [];
        }

        self::setEnvironment($environment);
        self::setServiceUrl('GetArtikelBEK');
        self::$connectorKey = 'RelatieNr';
        self::$connectorId = $account->erp_id;
        self::$resultSetKey = 'subfile';

        $results = self::getResults();
        return $results;
    }
    public static function getAccountPackageItemAllocation($account_package = false, $item = false) : array
    {
        if(!$item) {
            return [];
        }

        self::setEnvironment($item['environment']);
        self::setServiceUrl('GetOplagOrdOndernmr');
        self::$connectorKeys = [
            'OrdJaar' => $item['year'],
            'OrdNmr' => $item['erp_id']
        ];
        self::$resultSetKey = 'subfile';
        $results = self::getResults();

        return $results;
    }

}
