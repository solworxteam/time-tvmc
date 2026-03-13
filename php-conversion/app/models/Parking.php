<?php
/**
 * Parking Model
 * Handles all parking-related database operations
 */

class Parking
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get parking info by mosque ID
     */
    public function getByMosqueId($mosque_id)
    {
        return $this->db->fetchOne(
            'SELECT * FROM parking WHERE mosque_id = ?',
            [$mosque_id]
        );
    }

    /**
     * Update or insert parking info
     */
    public function updateOrInsert($mosque_id, $data)
    {
        // Try to update first
        $update_sql = 'UPDATE parking SET 
            onsite_parking = ?, 
            disable_bays = ?, 
            off_street_parking = ?, 
            road_name = ?, 
            address = ?, 
            distance_to_mosque = ? 
        WHERE mosque_id = ?';

        $params = [
            $data['onsite_parking'] ?? null,
            $data['disable_bays'] ?? null,
            $data['off_street_parking'] ?? null,
            $data['road_name'] ?? null,
            $data['address'] ?? null,
            $data['distance_to_mosque'] ?? null,
            $mosque_id
        ];

        $stmt = $this->db->query($update_sql, $params);

        // If no rows updated, insert
        if ($stmt->rowCount() === 0) {
            $insert_sql = 'INSERT INTO parking (
                id, mosque_id, onsite_parking, disable_bays,
                off_street_parking, road_name, address, distance_to_mosque
            ) VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?)';

            $insert_params = [
                $mosque_id,
                $data['onsite_parking'] ?? null,
                $data['disable_bays'] ?? null,
                $data['off_street_parking'] ?? null,
                $data['road_name'] ?? null,
                $data['address'] ?? null,
                $data['distance_to_mosque'] ?? null
            ];

            $this->db->query($insert_sql, $insert_params);
            return 'inserted';
        }

        return 'updated';
    }
}
