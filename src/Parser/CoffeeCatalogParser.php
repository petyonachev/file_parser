<?php


namespace App\Parser;

use App\Entity\Item;
use App\Validators\ItemValidator;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CoffeeCatalogParser
 * @package App\Parser
 */
class CoffeeCatalogParser
{
    /**
     * @var ItemValidator
     */
    private ItemValidator $itemValidator;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * CoffeeListParser constructor.
     * @param ItemValidator $itemValidator
     * @param LoggerInterface $logger
     */
    public function __construct(
        ItemValidator $itemValidator,
        LoggerInterface $logger
    ){
        $this->itemValidator = $itemValidator;
        $this->logger = $logger;
    }

    /**
     * Parse a catalog of items
     *
     * @param SimpleXMLElement $catalog
     * @param OutputInterface $output
     * @return array
     */
    public function parseCatalog(SimpleXMLElement $catalog, OutputInterface $output): array
    {
        $coffeeItems = [];

        // get child elements and parse them
        $catalogItems = $catalog->children();
        $totalItems = count($catalogItems);
        $itemCount = 1;
        foreach ($catalogItems as $item) {
            $output->writeln('Parsing item ' . $itemCount . ' of ' . $totalItems);
            $coffeeItem = new Item();

            $coffeeItem->entityId = (int) $item->entity_id;
            $coffeeItem->categoryName = (string) $item->CategoryName;
            $coffeeItem->sku = (int) $item->sku;
            $coffeeItem->name = (string) $item->name;
            $coffeeItem->description = (string) $item->description;
            $coffeeItem->shortDescription = (string) $item->shortdesc;
            $coffeeItem->price = (float) $item->price;
            $coffeeItem->link = (string) $item->link;
            $coffeeItem->image = (string) $item->image;
            $coffeeItem->brand = (string) $item->Brand;
            $coffeeItem->rating = (float) $item->Rating;
            $coffeeItem->caffeineType = (string) $item->CaffeineType;
            $coffeeItem->count = (int) $item->Count;
            $coffeeItem->flavoured = (string) $item->Flavored;
            $coffeeItem->seasonal = (string) $item->Seasonal;
            $coffeeItem->inStock = (string) $item->Instock;
            $coffeeItem->facebook = (int) $item->Facebook;
            $coffeeItem->isKCup = (bool) $item->IsKCup;

            // validate item
            $validation = $this->itemValidator->validateItem($coffeeItem);
            if ($validation->getContent() === 'valid') {
                $coffeeItems[] = $coffeeItem;
            } else {
                $output->writeln('Item is skipped because it contains invalid data.');
                $this->logger->warning(
                    'Item is not parsed because it contains the following invalid data: ' .
                    $validation->getContent()
                );
            }
            $itemCount++;
        }

        return $coffeeItems;
    }
}