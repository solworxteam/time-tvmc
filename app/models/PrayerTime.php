<?php
/**
 * PrayerTime Model - Handles prayer time data operations
 * 
 * Security: All queries use prepared statements with input validation
 */

class PrayerTime {
    /**
     * Get prayer times by date
     * 
     * @param string $date (format: Y-m-d)
     * @param string|null $mosqueId
     * @return array
     */
    public static function getByDate($date, $mosqueId = null) {
        try {
            if (!validateDate($date)) {
                throw new Exception("Invalid date format");
            }

            if ($mosqueId) {
                return Database::getInstance()->fetchAll(
                    "SELECT * FROM prayertimes WHERE date = ? AND mosque_id = ? ORDER BY mosque_id",
                    [$date, $mosqueId]
                );
            }
            
            return Database::getInstance()->fetchAll(
                "SELECT * FROM prayertimes WHERE date = ? ORDER BY mosque_id",
                [$date]
            );
        } catch (Exception $e) {
            Logger::error("Error fetching prayer times by date: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get today's prayer times
     * 
     * @param string|null $mosqueId
     * @return array
     */
    public static function getTodayPrayerTimes($mosqueId = null) {
        return self::getByDate(date('Y-m-d'), $mosqueId);
    }

    /**
     * Get prayer times for mosque in a specific month
     * 
     * @param string $mosqueId
     * @param int $month
     * @param int $year
     * @return array
     */
    public static function getByMosqueAndMonth($mosqueId, $month, $year) {
        try {
            if (empty($mosqueId)) {
                throw new Exception("Mosque ID is required");
            }

            $month = str_pad((int)$month, 2, '0', STR_PAD_LEFT);
            $year = (int)$year;

            if ($month < 1 || $month > 12) {
                throw new Exception("Invalid month");
            }

            $start = "$year-$month-01";
            $end = date('Y-m-t', strtotime($start));

            return Database::getInstance()->fetchAll(
                "SELECT * FROM prayertimes WHERE mosque_id = ? AND date BETWEEN ? AND ? ORDER BY date ASC",
                [$mosqueId, $start, $end]
            );
        } catch (Exception $e) {
            Logger::error("Error fetching prayer times by month: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get prayer time by ID
     * 
     * @param string $id
     * @return array|false
     */
    public static function getById($id) {
        try {
            if (empty($id)) {
                throw new Exception("Prayer time ID is required");
            }

            return Database::getInstance()->fetch(
                "SELECT * FROM prayertimes WHERE id = ?",
                [$id]
            );
        } catch (Exception $e) {
            Logger::error("Error fetching prayer time by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update prayer times
     * 
     * @param string $id
     * @param array $data
     * @return bool
     */
    public static function update($id, $data) {
        try {
            if (empty($id)) {
                throw new Exception("Prayer time ID is required");
            }

            // Validate time format for all fields
            $requiredFields = ['fajar_start', 'fajar_jamaat', 'zuhr_start', 'zuhr_jamaat', 'asr_start', 'asr_jamaat', 'maghrib', 'isha_start', 'isha_jamaat'];
            foreach ($requiredFields as $field) {
                if (!empty($data[$field]) && !validateTime($data[$field])) {
                    throw new Exception("Invalid time format for $field");
                }
            }

            Database::getInstance()->execute(
                "UPDATE prayertimes SET 
                    fajar_start = ?, fajar_jamaat = ?,
                    zuhr_start = ?, zuhr_jamaat = ?,
                    asr_start = ?, asr_jamaat = ?,
                    maghrib = ?,
                    isha_start = ?, isha_jamaat = ?
                WHERE id = ?",
                [
                    $data['fajar_start'] ?? null,
                    $data['fajar_jamaat'] ?? null,
                    $data['zuhr_start'] ?? null,
                    $data['zuhr_jamaat'] ?? null,
                    $data['asr_start'] ?? null,
                    $data['asr_jamaat'] ?? null,
                    $data['maghrib'] ?? null,
                    $data['isha_start'] ?? null,
                    $data['isha_jamaat'] ?? null,
                    $id
                ]
            );

            Logger::info("Prayer time updated: $id");
            return true;
        } catch (Exception $e) {
            Logger::error("Error updating prayer time: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Insert or update prayer times
     * 
     * @param string $mosqueId
     * @param string $date (format: Y-m-d)
     * @param array $times
     * @return bool
     */
    public static function insertOrUpdate($mosqueId, $date, $times) {
        try {
            if (empty($mosqueId) || !validateDate($date)) {
                throw new Exception("Invalid mosque ID or date");
            }

            // Validate time fields
            foreach ($times as $time) {
                if (!empty($time) && !validateTime($time)) {
                    throw new Exception("Invalid time format");
                }
            }

            Database::getInstance()->execute(
                "INSERT INTO prayertimes (mosque_id, date, fajar_start, fajar_jamaat, zuhr_start, zuhr_jamaat, asr_start, asr_jamaat, maghrib, isha_start, isha_jamaat) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE 
                    fajar_start=VALUES(fajar_start),
                    fajar_jamaat=VALUES(fajar_jamaat),
                    zuhr_start=VALUES(zuhr_start),
                    zuhr_jamaat=VALUES(zuhr_jamaat),
                    asr_start=VALUES(asr_start),
                    asr_jamaat=VALUES(asr_jamaat),
                    maghrib=VALUES(maghrib),
                    isha_start=VALUES(isha_start),
                    isha_jamaat=VALUES(isha_jamaat)",
                [
                    $mosqueId, $date,
                    $times['fajar_start'] ?? null,
                    $times['fajar_jamaat'] ?? null,
                    $times['zuhr_start'] ?? null,
                    $times['zuhr_jamaat'] ?? null,
                    $times['asr_start'] ?? null,
                    $times['asr_jamaat'] ?? null,
                    $times['maghrib'] ?? null,
                    $times['isha_start'] ?? null,
                    $times['isha_jamaat'] ?? null
                ]
            );

            Logger::info("Prayer time inserted/updated for mosque: $mosqueId on $date");
            return true;
        } catch (Exception $e) {
            Logger::error("Error inserting/updating prayer time: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete prayer times for date range
     * 
     * @param string $startDate
     * @param string|null $endDate
     * @return bool
     */
    public static function deletePrayerTimes($startDate, $endDate = null) {
        try {
            if (!validateDate($startDate)) {
                throw new Exception("Invalid start date format");
            }

            if (!$endDate) {
                $endDate = $startDate;
            } else if (!validateDate($endDate)) {
                throw new Exception("Invalid end date format");
            }

            Database::getInstance()->execute(
                "DELETE FROM prayertimes WHERE date BETWEEN ? AND ?",
                [$startDate, $endDate]
            );

            Logger::info("Prayer times deleted: $startDate to $endDate");
            return true;
        } catch (Exception $e) {
            Logger::error("Error deleting prayer times: " . $e->getMessage());
            return false;
        }
    }
}
