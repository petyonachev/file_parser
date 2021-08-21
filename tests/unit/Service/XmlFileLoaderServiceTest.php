<?php


namespace App\Tests\unit\Service;


use App\Command\CoffeeListParser;
use App\Service\XmlFileLoaderService;
use Exception;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

/**
 * Class XmlFileLoaderServiceTest
 * @package App\Tests\unit\Service
 */
class XmlFileLoaderServiceTest extends TestCase
{
    private XmlFileLoaderService $fileLoader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileLoader = new XmlFileLoaderService();
    }

    /**
     * Test successfully loading local XML file
     *
     * @throws Exception
     * @link XmlFileLoaderService::loadLocalFile()
     */
    public function testLoadLocalFile()
    {
        // load file
        $catalog = $this->fileLoader->loadFile(
            CoffeeListParser::LOCATION_LOCAL,
            realpath('./../../files/coffee/coffee_feed.xml')
        );

        // check that file is correctly loaded and contains data
        $this->assertInstanceOf(SimpleXMLElement::class, $catalog);
        $this->assertNotEmpty($catalog->children());
    }

    /**
     * test loading local file that does not exist at the location
     *
     * @throws Exception
     * @link XmlFileLoaderService::loadLocalFile()
     */
    public function testLocalFileNotFound()
    {
        // check that file not found exception is generated
        $this->expectExceptionMessage('Failed reading file. File not found.');

        // try to load file
        $this->fileLoader->loadFile(
            CoffeeListParser::LOCATION_LOCAL,
            realpath('coffee_feed.xml')
        );
    }

    /**
     * test loading local file that contains invalid xml data
     *
     * @throws Exception
     * @link XmlFileLoaderService::loadLocalFile()
     */
    public function testLoadLocalFileWithInvalidData()
    {
        $this->expectExceptionMessage('Failed reading file. Invalid file contents.');

        // try to load file
        $this->fileLoader->loadFile(
            CoffeeListParser::LOCATION_LOCAL,
            realpath('./../../files/invalid_xml_file.xml')
        );
    }
}