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
    }

    /**
     * Clears the specified ranged of the given sheet
     *
     * @param string $spreadsheetId
     * @param string $sheetName
     * @param string $range
     */
    public function clearSheet(string $spreadsheetId, string $sheetName, string $range)
    {
        // set range and body
        $range = $sheetName . '!' . $range;

        $requestBody = new \Google_Service_Sheets_ClearValuesRequest();

        // clear the specified range
        $this->service->spreadsheets_values->clear($spreadsheetId, $range, $requestBody);
    }

    /**
     * Gets the specified range of values from the given spreadsheet
     *
     * @param string $spreadsheetId
     * @param string $range
     * @return mixed
     */
    public function getSheetValues(string $spreadsheetId, string $range)
    {
        // get and return values
        $response = $this->service->spreadsheets_values->get($spreadsheetId, $range);
        return $response->getValues();
    }

    /**
     * Update google sheet with items
     *
     * @param string $spreadsheetId
     * @param string $sheetName
     * @param array $items
     * @return mixed
     */
    public function updateSheet(string $spreadsheetId, string $sheetName, array $items)
    {
        // get the last column based on number of item values
        $firstRow = (array) $items[0];
        $lastColumn = $this->numToExcelAlpha(count($firstRow));

        // empty sheet before inserting new information
        $this->clearSheet($spreadsheetId, $sheetName, 'A1:' . $lastColumn);

        // specify the range for update
        $range = $sheetName . '!' . 'A1:' . $lastColumn . (count($items) + 1);

        // set first row to be the column headers
        $values = [];
        $values[] = array_keys($firstRow);

        // extract values from all objects
        foreach ($items as $item) {
            $values[] = array_values((array) $item);
        }

        // prepare body and params
        $body = new \Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        // send request to update the sheet
        return $this->service->spreadsheets_values->update(
            $spreadsheetId,
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