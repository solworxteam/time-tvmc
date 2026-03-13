<?php
/**
 * Parking Model - Handles parking information for mosques
 */

class Parking {
    /**
     * Get parking information by mosque ID
     * 
     * @param string $mosqueId
     * @return array|false
     */
    public static function getByMosqueId($mosqueId) {
        try {
            if (empty($mosqueId)) {
                throw new Exception("Mosque ID is required");
            }

            return Database::getInstance()->fetch(
                "SELECT * FROM parking WHERE mosque_id = ?",
                [$mosqueId]
            );
        } catch (Exception $e) {
            Logger::error("Error fetching parking information: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update or insert parking information
     * 
     * @param string $mosqueId
     * @param array $data
     * @return bool
     */
    public static function update($mosqueId, $data) {
        try {
            if (empty($mosqueId)) {
                throw new Exception("Mosque ID is required");
            }

            // Validate data
            if (!empty($data['onsite_parking']) && !is_numeric($data['onsite_parking'])) {
                throw new Exception("Invalid onsite parking value");
            }

            $existing = self::getByMosqueId($mosqueId);
            
            if ($existing) {
                Database::getInstance()->execute(
                    "UPDATE parking SET 
                        onsite_parking = ?, 
                        disable_bays = ?, 
                        off_street_parking = ?,
                        road_name = ?,
                        distance_to_mosque = ?
                     WHERE mosque_id = ?",
                    [
                        $data['onsite_parking'] ?? null,
                        $data['disable_bays'] ?? 'no',
                        $data['off_street_parking'] ?? 'no',
                        $data['road_name'] ?? '',
                        $data['distance_to_mosque'] ?? 0,
                        $mosqueId
                    ]
                );
            } else {
                Database::getInstance()->execute(
                    "INSERT INTO parking (mosque_id, onsite_parking, disable_bays, off_street_parking, road_name, distance_to_mosque) 
                     VALUES (?, ?, ?, ?, ?, ?)",
                    [
                        $mosqueId,
                        $data['onsite_parking'] ?? null,
                        $data['disable_bays'] ?? 'no',
                        $data['off_street_parking'] ?? 'no',
                        $data['road_name'] ?? '',
                        $data['distance_to_mosque'] ?? 0
                    ]
                );
            }

            Logger::info("Parking information updated for mosque: $mosqueId");
            return true;
        } catch (Exception $e) {
            Logger::error("Error updating parking information: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete parking information
     * 
     * @param string $mosqueId
     * @return bool
     */
    public static function delete($mosqueId) {
        try {
            if (empty($mosqueId)) {
                throw new Exception("Mosque ID is required");
            }

            Database::getInstance()->execute(
                "DELETE FROM parking WHERE mosque_id = ?",
                [$mosqueId]
            );

            Logger::info("Parking information deleted for mosque: $mosqueId");
            return true;
        } catch (Exception $e) {
            Logger::error("Error deleting parking information: " . $e->getMessage());
            return false;
        }
    }
}
