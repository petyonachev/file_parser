<?php


namespace App\Parser;


use App\Entity\CoffeeItem;
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
     * @param SimpleXMLElement $catalog
     * @param OutputInterface $output
     * @return array
     */
    public function parseCatalog(SimpleXMLElement $catalog, OutputInterface $output): array
    {
        $coffeeItems = [];


        $catalogItems = $catalog->children();
        $totalItems = count($catalogItems);
        $itemCount = 1;
        foreach ($catalogItems as $item) {
            $output->writeln('Parsing item ' . $itemCount . ' of ' . $totalItems);
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
            } else {
                $output->write('Item is skipped because it contains invalid data.');
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