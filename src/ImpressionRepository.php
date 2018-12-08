<?php

namespace Scanner;

class ImpressionRepository
{
    /**
     * @var DatabaseConnection
     */
    private $db;

    public function __construct(DatabaseConnection $db)
    {
        $this->db = $db;
    }

    /**
     * @param \DateTime $date
     * @param string $geo
     * @param int $zone
     * @return array|null
     */
    public function find(\DateTime $date, string $geo, int $zone): ?array
    {
        $select = $this->db->prepare('SELECT * FROM scanner.public.impressions 
            WHERE date = :date AND geo = :geo AND zone = :zone
            FOR UPDATE 
            LIMIT 1');

        $select->execute([
            'date' => $date->format('Y-m-d'),
            'geo' => $geo,
            'zone' => $zone
        ]);

        $results = $select->fetchAll(\PDO::FETCH_ASSOC);

        if (count($results) === 0) {
            return null;
        }

        return $results[0];
    }

    /**
     * @param \DateTime $date
     * @param string $geo
     * @param int $zone
     * @param int $impressions
     * @param float $revenue
     */
    public function insert(\DateTime $date, string $geo, int $zone, int $impressions, float $revenue)
    {
        $insert = $this->db->prepare('INSERT INTO scanner.public.impressions
            (date, geo, zone, impressions, revenue)
            VALUES (:date, :geo, :zone, :impressions, :revenue)');

        $insert->execute([
            'date' => $date->format('Y-m-d'),
            'geo' => $geo,
            'zone' => $zone,
            'impressions' => $impressions,
            'revenue' => $revenue
        ]);
    }

    /**
     * @param int $id
     * @param int $impressions
     * @param float $revenue
     */
    public function update(int $id, int $impressions, float $revenue)
    {
        $update = $this->db->prepare('UPDATE scanner.public.impressions
            SET impressions = :impressions, revenue = :revenue 
            WHERE id = :id');

        $update->execute([
            'id' => $id,
            'impressions' => $impressions,
            'revenue' => $revenue
        ]);
    }
}
