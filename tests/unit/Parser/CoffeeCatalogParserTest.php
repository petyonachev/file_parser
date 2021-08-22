<?php


namespace App\Tests\unit\Parser;


use App\Command\CoffeeListConverter;
use App\Entity\Item;
use App\Parser\CoffeeCatalogParser;
use App\Service\XmlFileLoaderService;
use Exception;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class CoffeeCatalogParserTest
 * @package App\Tests\unit\Parser
 */
class CoffeeCatalogParserTest extends KernelTestCase
{
    /**
     * @var CoffeeCatalogParser|object|null
     */
    private CoffeeCatalogParser $catalogParser;

    /**
     * @var XmlFileLoaderService|object|null
     */
    private XmlFileLoaderService $fileLoader;

    /**
     * Runs before every test
     */
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = static::getContainer();

        $this->catalogParser = $container->get(CoffeeCatalogParser::class);
        $this->fileLoader = $container->get(XmlFileLoaderService::class);
    }

    /**
     * @return bool|SimpleXMLElement
     * @throws Exception
     */
    private function loadTestFile()
    {
        return $this->fileLoader->loadFile(
            CoffeeListConverter::LOCATION_LOCAL,
            realpath('./tests/files/coffee/coffee_feed.xml')
        );
    }

    /**
     * Tests successful parsing of all items in the file
     *
     * @throws Exception
     * @link CoffeeCatalogParser::parseCatalog()
     */
    public function testSuccessfulParsing()
    {
        // load file
        $catalog = $this->loadTestFile();

        // parse the catalog
        $items = $this->catalogParser->parseCatalog($catalog, new ConsoleOutput());

        // check that both items from the file have been correctly parsed
        $this->assertCount(2, $items);
        $this->assertInstanceOf(Item::class, $items[0]);
        $this->assertInstanceOf(Item::class, $items[1]);
        $this->assertNotEquals($items[0]->getEntityId(), $items[1]->getEntityId());

    }

    /**
     * Test parsing where 1 of the items contains invalid data
     *
     * @throws Exception
     * @link CoffeeCatalogParser::parseCatalog()
     */
    public function testParsingWithInvalidData()
    {
        // load the file and set the first item entity id to invalid value
        $catalog = $this->loadTestFile();
        $catalog->item->entity_id = -5;

        // parse the catalog
        $items = $this->catalogParser->parseCatalog($catalog, new ConsoleOutput());

        // check that the item with invalid entity id was skipped
        $this->assertCount(1, $items);
        $this->assertInstanceOf(Item::class, $items[0]);
        $this->assertNotEquals(-5, $items[0]->getEntityId());
    }
}