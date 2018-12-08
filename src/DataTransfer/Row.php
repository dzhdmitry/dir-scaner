<?php

namespace Scanner\DataTransfer;

use Symfony\Component\Validator\Constraints as Assert;

class Row
{
    /**
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $date;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\S{2}$/")
     */
    private $geo;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    private $zone;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    private $impressions;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+\.\d{2}$/")
     */
    private $revenue;

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     * @return Row
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGeo()
    {
        return $this->geo;
    }

    /**
     * @param mixed $geo
     * @return Row
     */
    public function setGeo($geo)
    {
        $this->geo = $geo;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param mixed $zone
     * @return Row
     */
    public function setZone($zone)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @param mixed $impressions
     * @return Row
     */
    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRevenue()
    {
        return $this->revenue;
    }

    /**
     * @param mixed $revenue
     * @return Row
     */
    public function setRevenue($revenue)
    {
        $this->revenue = $revenue;

        return $this;
    }
}
