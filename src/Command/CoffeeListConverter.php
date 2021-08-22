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

    protected static $defaultName = 'app:parse-coffee-catalog';

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
     * CoffeeListParser constructor.
     * @param LoggerInterface $logger
     * @param XmlFileLoaderService $xmlFileLoader
     * @param CoffeeCatalogParser $catalogParser
     * @param string|null $name
     */
    public function __construct(
        LoggerInterface $logger,
        XmlFileLoaderService $xmlFileLoader,
        CoffeeCatalogParser $catalogParser,
        string $name = null
    ){
        parent::__construct($name);

        $this->logger = $logger;
        $this->xmlFileLoader = $xmlFileLoader;
        $this->catalogParser = $catalogParser;
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
        $output->write('Beginning file parsing');

        $validatedArguments = $this->validateArguments($input);

        if (!$validatedArguments) {
            $output->write('Invalid arguments');
            return Command::FAILURE;
        }

        try {
            $catalog = $this->xmlFileLoader->loadFile($validatedArguments['location'], $validatedArguments['filepath']);
        } catch (Exception $exception) {
            $output->write('Failed loading file.');
            $this->logger->error($exception->getMessage());
            return Command::FAILURE;
        }

        $items = $this->catalogParser->parseCatalog($catalog, $output);

        $test = new GoogleSheetsService();
        $test->updateSheet($items);

        return Command::SUCCESS;
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

        if (!in_array($location, self::SUPPORTED_LOCATIONS)) {
            $this->logger->error('Invalid file location');
            return false;
        }

        if (!is_string($filepath)) {
            $this->logger->error('Invalid filepath');
            return false;
        }

        return compact('location', 'filepath');
    }
}