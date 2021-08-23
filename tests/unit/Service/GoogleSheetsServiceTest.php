<?php


namespace App\Tests\unit\Service;


use App\Command\CoffeeListConverter;
use App\Parser\CoffeeCatalogParser;
use App\Service\GoogleSheetsService;
use App\Service\XmlFileLoaderService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class GoogleSheetsServiceTest
 * @package App\Tests\unit\Service
 */
class GoogleSheetsServiceTest extends KernelTestCase
{
    /**
     * @var GoogleSheetsService|object|null
     */
    private GoogleSheetsService $sheetsService;

    /**
     * @var CoffeeCatalogParser|object|null
     */
    private CoffeeCatalogParser $catalogParser;

    /**
     * @var XmlFileLoaderService|object|null
     */
    private XmlFileLoaderService $fileLoader;

    private string $spreadsheetId;
    private string $sheetName;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = static::getContainer();

        $this->spreadsheetId = $container->getParameter('spreadsheet_id');
        $this->sheetName = $container->getParameter('sheet_name');

        $this->sheetsService = $container->get(GoogleSheetsService::class);
        $this->catalogParser = $container->get(CoffeeCatalogParser::class);
        $this->fileLoader = $container->get(XmlFileLoaderService::class);
    }

    /**
     * Tests the updating,retrieving and clearing of google sheet values
     *
     * @throws Exception
     */
    public function testUpdatingAndClearingSheet()
    {
        // load catalog
        $catalog = $this->fileLoader->loadFile(
            CoffeeListConverter::LOCATION_LOCAL,
            realpath('./tests/files/coffee/coffee_feed.xml'));

        // parse catalog items
        $items = $this->catalogParser->parseCatalog($catalog, new ConsoleOutput());

        // update sheet with the items
        $this->sheetsService->updateSheet($this->spreadsheetId, $this->sheetName, $items);

        // get sheet values
        $values = $this->sheetsService->getSheetValues($this->spreadsheetId, 'A1:Z');

        // check that catalog items are as expected on the sheet (first row is header row)
        $this->assertEquals('entityId', $values[0][0]);
        $this->assertEquals($items[0]->entityId, $values[1][0]);
        $this->assertEquals($items[1]->entityId, $values[2][0]);

        // clear sheet
        $this->sheetsService->clearSheet($this->spreadsheetId, $this->sheetName, 'A1:Z');

        // get sheet values and check that it is empty
        $values = $this->sheetsService->getSheetValues($this->spreadsheetId, 'A1:Z');
        $this->assertNull($values);
    }
}