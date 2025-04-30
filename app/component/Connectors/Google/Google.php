<?php

namespace App\component\Connectors\Google;

use Google_Service_Sheets;

class Google
{
    protected $client;

    public $sheets;

    public function __construct()
    {
        $this->client = new \Google_Client();
        $this->client->setApplicationName('Oculus Manager');
        $this->client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $this->client->setAccessType('offline');
        $this->client->setAuthConfig('C:\xampp\htdocs\oculus-manager\app\component\Connectors\Google\google_credentials.json');
        $this->sheets = new Google_Service_Sheets($this->client);
    }

    public function getSpreadSheet($spreadsheetId = false)
    {
        return $this->sheets->spreadsheets->get($spreadsheetId);
    }

    public function getSheet($spreadsheet_id = false, $range = false)
    {
        $data = $this->sheets->spreadsheets_values->get($spreadsheet_id, $range);
        return $data->getValues();
    }

}
