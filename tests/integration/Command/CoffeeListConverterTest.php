<?php


namespace App\Tests\integration\Command;

use App\Command\CoffeeListConverter;
use App\Service\GoogleSheetsService;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Command\Command;

/**
 * Class CoffeeListConverterTest
 * @package App\Tests\integration\Command
 */
class CoffeeListConverterTest extends KernelTestCase
{
    /**
     * @var CommandTester
     */
    private CommandTester $commandTester;

    /**
     * @var GoogleSheetsService
     */
    private GoogleSheetsService $sheetsService;

    /**
     * @var string
     */
    private string $spreadsheetId;

    /**
     * @var string
     */
    private string $sheetName;

    /**
     * Runs before every test
     */
    protected function setUp(): void
    {
        parent::setUp();

        $kernel = static::createKernel();
        $container = static::getContainer();
        $application = new Application($kernel);

        $this->spreadsheetId = $container->getParameter('spreadsheet_id');
        $this->sheetName = $container->getParameter('sheet_name');

        $command = $application->find('app:convert-coffee-catalog');
        $this->commandTester = new CommandTester($command);
        $this->sheetsService = $container->get(GoogleSheetsService::class);
    }

    /**
     * Tests command with both arguments being invalid
     */
    public function testInvalidArguments()
    {
        // execute command
        $response = $this->commandTester->execute([
            'location' => 'test',
            'filepath' => 10
        ]);

        // check that output is as expected
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Invalid arguments', $output);
        $this->assertEquals(Command::FAILURE, $response);
    }

    /**
     * Tests command with filepath that doesn't exist
     */
    public function testFileNotLoaded()
    {
        // execute command
        $response = $this->commandTester->execute([
            'location' => CoffeeListConverter::LOCATION_LOCAL,
            'filepath' => 'dummy'
        ]);

        // check that output is as expected
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Failed loading file.', $output);
        $this->assertEquals(Command::FAILURE, $response);
    }

    /**
     * Test updating sheet
     */
    public function testUpdateSheet()
    {
        // execute command
        $response = $this->commandTester->execute([
            'location' => CoffeeListConverter::LOCATION_LOCAL,
            'filepath' => realpath('./tests/files/coffee/coffee_feed.xml')
        ]);

        // check that command was successful
        $this->assertEquals(Command::SUCCESS, $response);

        // check that sheet is not empty
        $values = $this->sheetsService->getSheetValues($this->spreadsheetId, 'A1:Z');
        $this->assertNotEmpty($values);
    }
}