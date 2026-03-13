<?php
/**
 * Mosque Model
 * Handles all mosque-related database operations
 */

class Mosque
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get all mosques
     */
    public function getAll()
    {
        return $this->db->fetchAll('SELECT * FROM mosques');
    }

    /**
     * Get mosque by ID
     */
    public function getById($id)
    {
        return $this->db->fetchOne(
            'SELECT * FROM mosques WHERE id = ?',
            [$id]
        );
    }

    /**
     * Update mosque
     */
    public function update($id, $name, $address, $location_url)
    {
        $sql = 'UPDATE mosques SET name = ?, address = ?, location_url = ? WHERE id = ?';
        $stmt = $this->db->query($sql, [$name, $address, $location_url, $id]);
        return $stmt->rowCount();
    }

    /**
     * Get mosque with prayer times
     */
    public function getWithPrayerTimes($id)
    {
        $mosque = $this->getById($id);
        if (!$mosque) {
            return null;
        }

        $prayer_times = $this->db->fetchAll(
            'SELECT * FROM prayertimes WHERE mosque_id = ? ORDER BY date ASC',
            [$id]
        );

        $mosque['prayer_times'] = $prayer_times;
        return $mosque;
    }

    /**
     * Get all mosques with parking info
     */
    public function getAllWithParking()
    {
        $mosques = $this->getAll();
        foreach ($mosques as &$mosque) {
            $parking = $this->db->fetchOne(
                'SELECT * FROM parking WHERE mosque_id = ?',
                [$mosque['id']]
            );
            $mosque['parking'] = $parking;
        }
        return $mosques;
    }
}
