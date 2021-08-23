<?php


namespace App\Command;

use App\Parser\CoffeeCatalogParser;
use App\Service\GoogleSheetsService;
use App\Service\XmlFileLoaderService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

/**
 * Class CoffeeListParser
 * @package App\Commands
 */
class CoffeeListConverter extends Command
{

    public const LOCATION_LOCAL = 'local';
    public const LOCATION_REMOTE = 'remote';

    private const SUPPORTED_LOCATIONS = [
        self::LOCATION_LOCAL,
        self::LOCATION_REMOTE
    ];

    protected static $defaultName = 'app:convert-coffee-catalog';

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var XmlFileLoaderService
     */
    private XmlFileLoaderService $xmlFileLoader;

    /**
     * @var CoffeeCatalogParser
     */
    private CoffeeCatalogParser $catalogParser;

    /**
     * @var GoogleSheetsService
     */
    private GoogleSheetsService $sheetsService;

    /**
     * @var ContainerBagInterface
     */
    private ContainerBagInterface $containerBag;

    /**
     * CoffeeListParser constructor.
     * @param LoggerInterface $logger
     * @param XmlFileLoaderService $xmlFileLoader
     * @param CoffeeCatalogParser $catalogParser
     * @param GoogleSheetsService $sheetsService
     * @param ContainerBagInterface $containerBag
     * @param string|null $name
     */
    public function __construct(
        LoggerInterface $logger,
        XmlFileLoaderService $xmlFileLoader,
        CoffeeCatalogParser $catalogParser,
        GoogleSheetsService $sheetsService,
        ContainerBagInterface $containerBag,
        string $name = null
    ){
        parent::__construct($name);

        $this->logger = $logger;
        $this->xmlFileLoader = $xmlFileLoader;
        $this->catalogParser = $catalogParser;
        $this->sheetsService = $sheetsService;
        $this->containerBag = $containerBag;
    }

    protected function configure()
    {
        $this
            ->addArgument('location', InputArgument::REQUIRED, 'File location: remote, local')
            ->addArgument('filepath', InputArgument::REQUIRED, 'Full path to the file')
            ->setDescription('Parses XML file containing catalog of coffee items')
            ->setHelp('This command allows you to parse XML catalog of coffee items');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Beginning file parsing');

        // validate the arguments passed to the command
        $validatedArguments = $this->validateArguments($input);
        if (!$validatedArguments) {
            $output->writeln('Invalid arguments.');
            return Command::FAILURE;
        }

        try {
            // load file
            $catalog = $this->xmlFileLoader->loadFile($validatedArguments['location'], $validatedArguments['filepath']);
        } catch (Exception $exception) {
            $output->writeln('Failed loading file.');
            $this->logger->error($exception->getMessage());
            return Command::FAILURE;
        }

        // parse the catalog while remove any items containing invalid data
        $items = $this->catalogParser->parseCatalog($catalog, $output);

        $output->writeln('Finished parsing file.');

        // update sheet with items
        $result = $this->sheetsService->updateSheet(
            $this->containerBag->get('spreadsheet_id'),
            $this->containerBag->get('sheet_name'),
            $items
        );

        // if rows were updated, command was successful
        if ($result->updatedRows > 0) {
            $output->writeln('Google sheet updated!');
            return Command::SUCCESS;
        }

        $output->writeln('Google sheet update failed!');
        return Command::FAILURE;
    }

    /**
     * This method validates the command arguments
     *
     * @param InputInterface $input
     * @return array|false
     */
    protected function validateArguments(InputInterface $input)
    {
        $location = $input->getArgument('location');
        $filepath = $input->getArgument('filepath');

        $validArguments = true;
        if (!in_array($location, self::SUPPORTED_LOCATIONS)) {
            $this->logger->error('Invalid file location');
            $validArguments = false;
        }

        if (!is_string($filepath)) {
            $this->logger->error('Invalid filepath');
            $validArguments = false;
        }

        if (!$validArguments) {
            return false;
        }

        return compact('location', 'filepath');
    }
}