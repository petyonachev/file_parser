<?php


namespace App\Commands;


use App\Entity\CoffeeItem;
use App\Validators\ItemValidator;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * Class CoffeeListParser
 * @package App\Commands
 */
class CoffeeListParser extends Command
{

    private const LOCATION_LOCAL = 'local';
    private const LOCATION_REMOTE = 'remote';

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
     * CoffeeListParser constructor.
     * @param LoggerInterface $logger
     * @param ItemValidator $itemValidator
     * @param string|null $name
     */
    public function __construct(LoggerInterface $logger, ItemValidator $itemValidator, string $name = null)
    {
        parent::__construct($name);

        $this->logger = $logger;
        $this->itemValidator = $itemValidator;
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
        ini_set('memory_limit', '4096M');
        $output->write('Beginning file parsing');


        $validatedArguments = $this->validateArguments($input);

        if (!$validatedArguments) {
            $output->write('Invalid arguments');
            return Command::FAILURE;
        }

        $catalog = [];
        try {
            $catalog = simplexml_load_string(file_get_contents($validatedArguments['filepath']));
        } catch (FileNotFoundException $exception) {
            $this->logger->error('Failed reading file. File not found.');
        } catch (Exception $exception) {
            $this->logger->error('Failed reading file. Invalid file contents.');
        }

        $items = $this->parseCatalog($catalog);


        return Command::SUCCESS;
    }

    protected function parseCatalog(array $catalog)
    {
        $items = [];

        foreach ($catalog as $item) {
            $coffeeItem = new CoffeeItem();
            $coffeeItem
                ->setEntityId($item->entityId)
                ->setCategoryName($item->categoryName)
                ->setSku($item->sku)
                ->setName($item->name)
                ->setDescription($item->description)
                ->setShortDescription($item->shortDescription)
                ->setPrice($item->price)
                ->setLink($item->link)
                ->setImage($item->image)
                ->setBrand($item->brand)
                ->setRating($item->rating)
                ->setCaffeinetype($item->caffeineType)
                ->setCount($item->count)
                ->setFlavoured($item->flavoured)
                ->setSeasonal($item->seasonal)
                ->setInStock($item->inStock)
                ->setFacebook($item->facebook)
                ->setIsKCup($item->isKCup);

            $this->itemValidator->validateItem($coffeeItem);

            $items[] = $coffeeItem;
        }

        return $items;
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