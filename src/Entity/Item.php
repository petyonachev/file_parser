<?php


namespace App\Entity;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Item
 * @package App\Entity
 */
class Item
{
    /**
     * @Assert\NotNull
     * @Assert\GreaterThan(0)
     * @var int Id of the item
     */
    public int $entityId;

    /**
     * @Assert\NotBlank
     * @var string Item category
     */
    public string $categoryName;

    /**
     * @var int To be provided in documentation
     */
    public int $sku;

    /**
     * @Assert\NotBlank
     * @var string Name of item
     */
    public string $name;

    /**
     * @var string|null Long description of the item
     */
    public string $description;

    /**
     * @var string|null Short description of the item
     */
    public string $shortDescription;

    /**
     * @var float Price of the item
     */
    public float $price;

    /**
     * @var string|null Web link of the item
     */
    public string $link;

    /**
     * @var string|null Image of the item
     */
    public string $image;

    /**
     * @var string Brand of the item
     */
    public string $brand;

    /**
     * @var float|null Rating of the item
     */
    public float $rating;

    /**
     * @var string Caffeine type
     */
    public string $caffeineType;

    /**
     * @Assert\GreaterThanOrEqual(0)
     * @var int Remaining stock
     */
    public int $count;

    /**
     * @var string Whether item is flavoured
     */
    public string $flavoured;

    /**
     * @var string Whether item is seasonal
     */
    public string $seasonal;

    /**
     * @Assert\NotBlank
     * @var string Whether item is in stock
     */
    public string $inStock;

    /**
     * @Assert\NotNull
     * @var int Whether item is posted on facebook?
     */
    public int $facebook;

    /**
     * @var bool To be provided by documentation
     */
    public bool $isKCup;
}