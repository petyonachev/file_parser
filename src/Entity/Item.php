<?php


namespace App\Entity;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Item
 * @package App\Entity
 */
abstract class Item
{
    /**
     * @Assert\NotNull
     * @Assert\GreaterThan(0)
     * @var int Id of the item
     */
    private int $entityId;

    /**
     * @Assert\NotBlank
     * @var string Item category
     */
    private string $categoryName;

    /**
     * @Assert\NotBlank
     * @var string Name of item
     */
    private string $name;

    /**
     * @var string|null Long description of the item
     */
    private string $description;

    /**
     * @var string|null Short description of the item
     */
    private string $shortDescription;

    /**
     * @var float Price of the item
     */
    private float $price;

    /**
     * @var string|null Web link of the item
     */
    private string $link;

    /**
     * @var string|null Image of the item
     */
    private string $image;

    /**
     * @var string Brand of the item
     */
    private string $brand;

    /**
     * @var float|null Rating of the item
     */
    private float $rating;

    /**
     * @Assert\GreaterThanOrEqual(0)
     * @var int Remaining stock
     */
    private int $count;

    /**
     * @var string Whether item is seasonal
     */
    private string $seasonal;

    /**
     * @Assert\NotBlank
     * @var string Whether item is in stock
     */
    private string $inStock;

    /**
     * @Assert\NotNull
     * @var int Whether item is posted on facebook?
     */
    private int $facebook;

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }

    /**
     * @param int $entityId
     * @return Item
     */
    public function setEntityId(int $entityId): Item
    {
        $this->entityId = $entityId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    /**
     * @param string $categoryName
     * @return Item
     */
    public function setCategoryName(string $categoryName): Item
    {
        $this->categoryName = $categoryName;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Item
     */
    public function setName(string $name): Item
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Item
     */
    public function setDescription(string $description): Item
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     * @return Item
     */
    public function setShortDescription(string $shortDescription): Item
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return Item
     */
    public function setPrice(float $price): Item
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return Item
     */
    public function setLink(string $link): Item
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     * @return Item
     */
    public function setImage(string $image): Item
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     * @return Item
     */
    public function setBrand(string $brand): Item
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return float
     */
    public function getRating(): float
    {
        return $this->rating;
    }

    /**
     * @param float $rating
     * @return Item
     */
    public function setRating(float $rating): Item
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     * @return Item
     */
    public function setCount(int $count): Item
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeasonal(): string
    {
        return $this->seasonal;
    }

    /**
     * @param string $seasonal
     * @return Item
     */
    public function setSeasonal(string $seasonal): Item
    {
        $this->seasonal = $seasonal;
        return $this;
    }

    /**
     * @return string
     */
    public function getInStock(): string
    {
        return $this->inStock;
    }

    /**
     * @param string $inStock
     * @return Item
     */
    public function setInStock(string $inStock): Item
    {
        $this->inStock = $inStock;
        return $this;
    }

    /**
     * @return int
     */
    public function getFacebook(): int
    {
        return $this->facebook;
    }

    /**
     * @param int $facebook
     * @return Item
     */
    public function setFacebook(int $facebook): Item
    {
        $this->facebook = $facebook;
        return $this;
    }
}