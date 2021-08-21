<?php


namespace App\Entity;

/**
 * Class CoffeeItem
 * @package App
 */
class CoffeeItem extends Item
{
    /**
     * @var int To be provided in documentation
     */
    private int $sku;

    /**
     * @var string Caffeine type
     */
    private string $caffeineType;

    /**
     * @var string Whether item is flavoured
     */
    private string $flavoured;

    /**
     * @var bool To be provided by documentation
     */
    private bool $isKCup;

    /**
     * @return int
     */
    public function getSku(): int
    {
        return $this->sku;
    }

    /**
     * @param int $sku
     * @return CoffeeItem
     */
    public function setSku(int $sku): CoffeeItem
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return string
     */
    public function getCaffeineType(): string
    {
        return $this->caffeineType;
    }

    /**
     * @param string $caffeineType
     * @return CoffeeItem
     */
    public function setCaffeineType(string $caffeineType): CoffeeItem
    {
        $this->caffeineType = $caffeineType;
        return $this;
    }

    /**
     * @return string
     */
    public function getFlavoured(): string
    {
        return $this->flavoured;
    }

    /**
     * @param string $flavoured
     * @return CoffeeItem
     */
    public function setFlavoured(string $flavoured): CoffeeItem
    {
        $this->flavoured = $flavoured;
        return $this;
    }

    /**
     * @return bool
     */
    public function isKCup(): bool
    {
        return $this->isKCup;
    }

    /**
     * @param bool $isKCup
     * @return CoffeeItem
     */
    public function setIsKCup(bool $isKCup): CoffeeItem
    {
        $this->isKCup = $isKCup;
        return $this;
    }
}