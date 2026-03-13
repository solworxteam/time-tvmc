<?php
/**
 * PrayerTime Model
 * Handles all prayer time-related database operations
 */

class PrayerTime
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get prayer times by date
     */
    public function getByDate($date)
    {
        return $this->db->fetchAll(
            'SELECT * FROM prayertimes WHERE date = ?',
            [$date]
        );
    }

    /**
     * Get prayer times by mosque and month/year
     */
    public function getByMosqueAndMonth($mosque_id, $month, $year)
    {
        return $this->db->fetchAll(
            'SELECT * FROM prayertimes WHERE mosque_id = ? AND MONTH(date) = ? AND YEAR(date) = ? ORDER BY date ASC',
            [$mosque_id, $month, $year]
        );
    }

    /**
     * Get prayer time by ID
     */
    public function getById($id)
    {
        return $this->db->fetchOne(
            'SELECT * FROM prayertimes WHERE id = ?',
            [$id]
        );
    }

    /**
     * Update prayer time
     */
    public function update($id, $data)
    {
        $sql = 'UPDATE prayertimes SET 
            fajar_start = ?, 
            fajar_jamaat = ?, 
            zuhr_start = ?, 
            zuhr_jamaat = ?, 
            asr_start = ?, 
            asr_jamaat = ?, 
            maghrib = ?, 
            isha_start = ?, 
            isha_jamaat = ? 
        WHERE id = ?';

        $params = [
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
        ];

        $stmt = $this->db->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Insert or update prayer time (bulk upload)
     */
    public function insertOrUpdate($prayer)
    {
        $sql = 'INSERT INTO prayertimes (
            mosque_id, date, fajar_start, fajar_jamaat,
            zuhr_start, zuhr_jamaat, asr_start, asr_jamaat,
            maghrib, isha_start, isha_jamaat
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            fajar_start = VALUES(fajar_start),
            fajar_jamaat = VALUES(fajar_jamaat),
            zuhr_start = VALUES(zuhr_start),
            zuhr_jamaat = VALUES(zuhr_jamaat),
            asr_start = VALUES(asr_start),
            asr_jamaat = VALUES(asr_jamaat),
            maghrib = VALUES(maghrib),
            isha_start = VALUES(isha_start),
            isha_jamaat = VALUES(isha_jamaat)';

        $params = [
            $prayer['mosque_id'],
            $prayer['date'],
            $prayer['fajar_start'] ?? null,
            $prayer['fajar_jamaat'] ?? null,
            $prayer['zuhr_start'] ?? null,
            $prayer['zuhr_jamaat'] ?? null,
            $prayer['asr_start'] ?? null,
            $prayer['asr_jamaat'] ?? null,
            $prayer['maghrib'] ?? null,
            $prayer['isha_start'] ?? null,
            $prayer['isha_jamaat'] ?? null,
        ];

        return $this->db->query($sql, $params);
    }

    /**
     * Get prayer times by mosque ID
     */
    public function getByMosqueId($mosque_id)
    {
        return $this->db->fetchAll(
            'SELECT * FROM prayertimes WHERE mosque_id = ? ORDER BY date ASC',
            [$mosque_id]
        );
    }
}
