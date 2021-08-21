<?php


namespace App\Command;


use App\Entity\CoffeeItem;
use App\Service\XmlFileLoaderService;
use App\Validators\ItemValidator;
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
class CoffeeListParser extends Command
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
     * @var ItemValidator
     */
    private ItemValidator $itemValidator;

    /**
     * @var XmlFileLoaderService
     */
    private XmlFileLoaderService $xmlFileLoader;

    /**
     * CoffeeListParser constructor.
     * @param LoggerInterface $logger
     * @param ItemValidator $itemValidator
     * @param XmlFileLoaderService $xmlFileLoader
     * @param string|null $name
     */
    public function __construct(
        LoggerInterface $logger,
        ItemValidator $itemValidator,
        XmlFileLoaderService $xmlFileLoader,
        string $name = null
    ){
        parent::__construct($name);

        $this->logger = $logger;
        $this->itemValidator = $itemValidator;
        $this->xmlFileLoader = $xmlFileLoader;
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

        $items = $this->parseCatalog($catalog);
        $test = 0;

        return Command::SUCCESS;
    }

    protected function parseCatalog($catalog)
    {
        $coffeeItems = [];

        foreach ($catalog->children() as $item) {
            $coffeeItem = new CoffeeItem();

            $coffeeItem
                ->setEntityId((int) $item->entity_id)
                ->setCategoryName((string) $item->CategoryName)
                ->setSku((int) $item->sku)
                ->setName((string) $item->name)
                ->setDescription((string) $item->description)
                ->setShortDescription((string) $item->shortdesc)
                ->setPrice((float) $item->price)
                ->setLink((string) $item->link)
                ->setImage((string) $item->image)
                ->setBrand((string) $item->Brand)
                ->setRating((int) $item->Rating)
                ->setCaffeinetype((string) $item->CaffeineType)
                ->setCount((int) $item->Count)
                ->setFlavoured((string) $item->Flavored)
                ->setSeasonal((string) $item->Seasonal)
                ->setInStock((string) $item->Instock)
                ->setFacebook((int) $item->Facebook)
                ->setIsKCup((bool) $item->IsKCup);

            $validation = $this->itemValidator->validateItem($coffeeItem);
            if ($validation->getContent() === 'valid') {
                $coffeeItems[] = $coffeeItem;
            }
        }

        return $coffeeItems;
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