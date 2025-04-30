<?php

namespace App\component\Connectors\Google;

use App\component\Connectors\Google\Google;
use App\component\customHelpers;
use App\Models\AccountCalloffArticle;
class GoogleSheetsSyncher extends Google
{
    use customHelpers;
    protected static function alpha2num($a) {
        $l = strlen($a);
        $n = 0;
        for($i = 0; $i < $l; $i++)
            $n = $n*26 + ord($a[$i]) - 0x40;
        return $n-1;
    }


    protected static function num2alpha($n) {
        for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
            $r = chr($n%26 + 0x41) . $r;
        return $r;
    }
    public static function synchAccountCalloffArticles($account)
    {
        $config = $account->calloff_article_import_config;

        $google = new Google();
        $spreadsheet = $google->getSpreadSheet($config['spreadsheet_id']);
//        dd($spreadsheet->spreadsheetUrl);
        $properties = $spreadsheet->getProperties();
        $sheets = $spreadsheet->getSheets();
        $sheet = $sheets[$config['sheet_index']];
        $sheet_id = $sheet['properties']['sheetId'];
        $sheet_title = $sheet['properties']['title'];
        $row_count = $sheet['properties']['gridProperties']['rowCount'];
        $column_count = $sheet['properties']['gridProperties']['columnCount'];
        $range =  $sheet_title . '!A1:'.self::num2alpha($column_count).$row_count;

        $rows = $google->getSheet($config['spreadsheet_id'], $range);
        $column_names = array_flip($rows[0]);
        $external_connector_key_column_index = $column_names[$config['external_connector_key']];
        $rows = array_slice($rows, 1);

        foreach($rows as $row) {
            if(!array_key_exists($external_connector_key_column_index, $row)) {
                continue;
            }
            $external_connector_key = $row[$external_connector_key_column_index];
            $calloffArtile = AccountCalloffArticle::where("{$config['internal_connector_key']}", "LIKE", "{$external_connector_key}%")->get()->first();
               if($calloffArtile) {
                   dd($calloffArtile);
               }
        }
dd('synchAccountCalloffArticles');
    }
}
