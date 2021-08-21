<?php


namespace App\Tests\unit\Service;


use App\Command\CoffeeListConverter;
use App\Service\XmlFileLoaderService;
use Exception;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class XmlFileLoaderServiceTest
 * @package App\Tests\unit\Service
 */
class XmlFileLoaderServiceTest extends KernelTestCase
{
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

        $this->fileLoader = $container->get(XmlFileLoaderService::class);
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
            CoffeeListConverter::LOCATION_LOCAL,
            realpath('./tests/files/coffee/coffee_feed.xml')
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
            CoffeeListConverter::LOCATION_LOCAL,
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
            CoffeeListConverter::LOCATION_LOCAL,
            realpath('./tests/files/invalid_xml_file.xml')
        );
    }
}