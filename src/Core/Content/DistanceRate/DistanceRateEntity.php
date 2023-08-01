<?php
namespace DistanceRateShipping\Core\Content\DistanceRate;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

/**
 * @Entity(tableName="distance_rate")
 */
class DistanceRateEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $rangeFrom;

    /**
     * @var int
     */
    protected $rangeTo;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var bool
     */
    protected $active;


    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getRangeFrom(): int
    {
        return $this->rangeFrom;
    }

    public function setRangeFrom(int $rangeFrom): void
    {
        $this->rangeFrom = $rangeFrom;
    }

    public function getRangeTo(): int
    {
        return $this->rangeTo;
    }

    public function setRangeTo(int $rangeTo): void
    {
        $this->rangeTo = $rangeTo;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function getStatus(): string
    {
        return $this->active;
    }

    public function setStatus(string $active): void
    {
        $this->active = $active;
    }

}