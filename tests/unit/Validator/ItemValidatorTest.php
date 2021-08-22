<?php


namespace App\Tests\unit\Validator;


use App\Entity\Item;
use App\Validators\ItemValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ItemValidatorTest
 * @package App\Tests\unit\Validator
 */
class ItemValidatorTest extends KernelTestCase
{

    /**
     * @var ValidatorInterface|object|null
     */
    private ValidatorInterface $validator;

    private ItemValidator $itemValidator;

    /**
     * Runs before every test
     */
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = static::getContainer();

        $this->validator = $container->get(ValidatorInterface::class);
        $this->itemValidator = $container->get(ItemValidator::class);
    }

    /**
     * Creates a valid coffee item object
     *
     * @return Item
     */
    private function createValidItem(): Item
    {
        $coffeeItem = new Item();

        $coffeeItem->entityId = 5;
        $coffeeItem->categoryName = 'category';
        $coffeeItem->sku = 10;
        $coffeeItem->name = 'name';
        $coffeeItem->description = 'full desc';
        $coffeeItem->shortDescription = 'short desc';
        $coffeeItem->price = 20;
        $coffeeItem->link = 'http link';
        $coffeeItem->image = 'image link';
        $coffeeItem->brand = 'brand';
        $coffeeItem->rating = 3;
        $coffeeItem->caffeineType = 'caffeine type';
        $coffeeItem->count = 10;
        $coffeeItem->flavoured = 'Yes';
        $coffeeItem->seasonal = 'Yes';
        $coffeeItem->inStock = 'Yes';
        $coffeeItem->facebook = 5;
        $coffeeItem->isKCup = true;

        return $coffeeItem;
    }

    /**
     * Test validation of valid item
     * @link ItemValidator::validateItem()
     */
    public function testValidItem()
    {
        // create valid item
        $item = $this->createValidItem();

        // validate item and check that validation passed
        $response = $this->itemValidator->validateItem($item);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('valid', $response->getContent());
    }

    /**
     * Test validation of invalid item
     * @link ItemValidator::validateItem()
     */
    public function testInvalidItem()
    {
        // create valid item and set the entity id to invalid value
        $item = $this->createValidItem();
        $item->entityId = -1;

        // use validator interface to get the error for invalid entity id
        $errors = $this->validator->validate($item);
        $this->assertNotEmpty($errors);

        // run the item validator and check that item has errors now
        $response = $this->itemValidator->validateItem($item);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals((string) $errors, $response->getContent());
    }
}