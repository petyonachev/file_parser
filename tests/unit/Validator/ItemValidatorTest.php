<?php


namespace App\Tests\unit\Validator;


use App\Entity\CoffeeItem;
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
     * @return CoffeeItem
     */
    private function createValidItem(): CoffeeItem
    {
        $coffeeItem = new CoffeeItem();

        $coffeeItem
            ->setEntityId(5)
            ->setCategoryName('category')
            ->setSku(10)
            ->setName('name')
            ->setDescription('full desc')
            ->setShortDescription('short desc')
            ->setPrice(20)
            ->setLink('http link')
            ->setImage('image link')
            ->setBrand('brand')
            ->setRating(3)
            ->setCaffeineType('caffeine type')
            ->setCount(10)
            ->setFlavoured('Yes')
            ->setSeasonal('Yes')
            ->setInStock('Yes')
            ->setFacebook(5)
            ->setIsKCup(true);

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
        $item->setEntityId(-1);

        // use validator interface to get the error for invalid entity id
        $errors = $this->validator->validate($item);
        $this->assertNotEmpty($errors);

        // run the item validator and check that item has errors now
        $response = $this->itemValidator->validateItem($item);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals((string) $errors, $response->getContent());
    }
}