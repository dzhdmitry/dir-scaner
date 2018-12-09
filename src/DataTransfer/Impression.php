<?php

namespace Scanner\DataTransfer;

class Impression
{
    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $geo;

    /**
     * @var int
     */
    private $zone;

    /**
     * @var int
     */
    private $impressions;

    /**
     * @var Money
     */
    private $revenue;

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return Impression
     */
    public function setDate(\DateTime $date): Impression
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getGeo(): string
    {
        return $this->geo;
    }

    /**
     * @param string $geo
     * @return Impression
     */
    public function setGeo(string $geo): Impression
    {
        $this->geo = $geo;

        return $this;
    }

    /**
     * @return int
     */
    public function getZone(): int
    {
        return $this->zone;
    }

    /**
     * @param int $zone
     * @return Impression
     */
    public function setZone(int $zone): Impression
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * @return int
     */
    public function getImpressions(): int
    {
        return $this->impressions;
    }

    /**
     * @param int $impressions
     * @return Impression
     */
    public function setImpressions(int $impressions): Impression
    {
        $this->impressions = $impressions;

        return $this;
    }

    /**
     * @return Money
     */
    public function getRevenue(): Money
    {
        return $this->revenue;
    }

    /**
     * @param Money $revenue
     * @return Impression
     */
    public function setRevenue(Money $revenue): Impression
    {
        $this->revenue = $revenue;

        return $this;
    }

    /**
     * @return string
     */
    public function getHashKey(): string
    {
        return sprintf(
            '%s#%s#%d',
            $this->date->format('Y-m-d'),
            $this->geo,
            $this->zone
        );
    }

    /**
     * @param Impression $impression
     * @return Impression
     */
    public function add(Impression $impression): Impression
    {
        $this->setImpressions($this->impressions + $impression->getImpressions());
        $this->revenue->modify($impression->getRevenue());

        return $this;
    }
}
