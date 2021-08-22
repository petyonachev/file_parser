<?php


namespace App\Service;

use Google\Exception;
use Google_Client;

/**
 * Class GoogleSheetsService
 * @package App\Service
 */
class GoogleSheetsService
{

    private \Google_Service_Sheets $service;
    private string $spreadsheetId;
    private string $sheetName;

    /**
     * GoogleSheetsService constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $client = new Google_Client();
        $client->setApplicationName('List Coffee Items');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig(realpath('./google-credentials.json'));
        $this->service = new \Google_Service_Sheets($client);

        $this->spreadsheetId = '1p_Y-Qk4eUnNy76Yll0ZyDw3Ox8AlbgsL3U8HpDOogjE';
        $this->sheetName = 'coffeelist';
    }

    /**
     * Update google sheet with items
     *
     * @param array $items
     */
    public function updateSheet(array $items)
    {
        // get the last column based on number of item values
        $firstRow = (array) $items[0];
        $lastColumn = $this->numToExcelAlpha(count(array_values($firstRow)));

        // specify the range for update
        $range = $this->sheetName . '!' . 'A1:' . $lastColumn . (count($items) + 1);

        // extract values from all objects
        $values = [];
        $values[] = [
            'Entity Id', 'Category Name', 'Sku', 'Name', 'Description', 'Short Description', 'Price',
            'Link', 'Image', 'Brand', 'Rating', 'Caffeine type', 'Count', 'Flavored', 'Seasonal',
            'Instock', 'Facebook', 'IsKCup'
        ];
        foreach ($items as $item) {
            $values[] = array_values((array) $item);
        }

        $body = new \Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        $result = $this->service->spreadsheets_values->update(
            $this->spreadsheetId,
            $range,
            $body,
            $params
        );
    }

    /**
     * Get excel column name based on column number
     *
     * @param $columnNumber
     * @return int|string
     */
    private function numToExcelAlpha($columnNumber) {
        $columnName = 'A';
        while ($columnNumber-- > 1) {
            $columnName++;
        }
        return $columnName;
    }
}