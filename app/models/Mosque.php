<?php
/**
 * Mosque Model - Handles mosque data operations
 * 
 * Security: All queries use prepared statements
 */

class Mosque {
    /**
     * Get all mosques sorted by name
     * 
     * @return array
     */
    public static function getAll() {
        try {
            return Database::getInstance()->fetchAll(
                "SELECT * FROM mosques ORDER BY name ASC"
            );
        } catch (Exception $e) {
            Logger::error("Error fetching all mosques: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get mosque by ID
     * 
     * @param string $id
     * @return array|false
     */
    public static function getById($id) {
        try {
            if (empty($id)) {
                throw new Exception("Mosque ID is required");
            }

            return Database::getInstance()->fetch(
                "SELECT * FROM mosques WHERE id = ?",
                [$id]
            );
        } catch (Exception $e) {
            Logger::error("Error fetching mosque by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get mosque with prayer times for specific date
     * 
     * @param string $id
     * @param string|null $date (format: Y-m-d)
     * @return array|false
     */
    public static function getWithPrayerTimes($id, $date = null) {
        try {
            if (empty($id)) {
                throw new Exception("Mosque ID is required");
            }

            if (!$date) {
                $date = date('Y-m-d');
            } else if (!validateDate($date)) {
                throw new Exception("Invalid date format");
            }

            $mosque = self::getById($id);
            if (!$mosque) {
                return false;
            }

            $prayerTimes = Database::getInstance()->fetchAll(
                "SELECT * FROM prayertimes WHERE mosque_id = ? AND date = ? LIMIT 1",
                [$id, $date]
            );

            $mosque['prayer_times'] = $prayerTimes[0] ?? null;
            return $mosque;
        } catch (Exception $e) {
            Logger::error("Error fetching mosque with prayer times: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update mosque information
     * 
     * @param string $id
     * @param array $data
     * @return bool
     */
    public static function update($id, $data) {
        try {
            if (empty($id)) {
                throw new Exception("Mosque ID is required");
            }

            if (empty($data['name']) || empty($data['address'])) {
                throw new Exception("Name and address are required");
            }

            // Validate coordinates if provided
            if (!empty($data['latitude']) && (!is_numeric($data['latitude']) || $data['latitude'] < -90 || $data['latitude'] > 90)) {
                throw new Exception("Invalid latitude");
            }
            if (!empty($data['longitude']) && (!is_numeric($data['longitude']) || $data['longitude'] < -180 || $data['longitude'] > 180)) {
                throw new Exception("Invalid longitude");
            }

            Database::getInstance()->execute(
                "UPDATE mosques SET name = ?, address = ?, postcode = ?, latitude = ?, longitude = ? WHERE id = ?",
                [
                    sanitize($data['name']),
                    sanitize($data['address']),
                    sanitize($data['postcode'] ?? ''),
                    $data['latitude'] ?? null,
                    $data['longitude'] ?? null,
                    $id
                ]
            );

            Logger::info("Mosque updated: " . sanitize($data['name']));
            return true;
        } catch (Exception $e) {
            Logger::error("Error updating mosque: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Search mosques by name, address, or postcode
     * 
     * @param string $query
     * @param int $limit
     * @return array
     */
    public static function search($query, $limit = 50) {
        try {
            if (empty($query)) {
                return self::getAll();
            }

            // Limit query length to prevent performance issues
            $query = mb_substr(trim($query), 0, 100);
            $searchTerm = '%' . $query . '%';

            return Database::getInstance()->fetchAll(
                "SELECT * FROM mosques WHERE name LIKE ? OR address LIKE ? OR postcode LIKE ? 
                 ORDER BY name ASC LIMIT ?",
                [$searchTerm, $searchTerm, $searchTerm, $limit]
            );
        } catch (Exception $e) {
            Logger::error("Error searching mosques: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get mosques sorted by distance from coordinates
     * 
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    public static function getNearby($latitude, $longitude, $maxDistance = 50) {
        try {
            if (!is_numeric($latitude) || !is_numeric($longitude)) {
                throw new Exception("Invalid coordinates");
            }

            $mosques = self::getAll();
            
            foreach ($mosques as $key => $mosque) {
                $distance = getDistance(
                    $latitude, $longitude,
                    $mosque['latitude'], $mosque['longitude']
                );
                
                if ($distance > $maxDistance) {
                    unset($mosques[$key]);
                    continue;
                }
                
                $mosques[$key]['distance'] = round($distance, 2);
            }

            // Sort by distance
            usort($mosques, function($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });

            return $mosques;
        } catch (Exception $e) {
            Logger::error("Error finding nearby mosques: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get mosque count
     * 
     * @return int
     */
    public static function count() {
        try {
            $result = Database::getInstance()->fetch(
                "SELECT COUNT(*) as count FROM mosques"
            );
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            Logger::error("Error counting mosques: " . $e->getMessage());
            return 0;
        }
    }
}
