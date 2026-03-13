<?php
require_once __DIR__ . '/../../config/database.php';

class Mosque {
    public static function getAll() {
        return getAllDB("SELECT * FROM mosques ORDER BY name ASC");
    }

    public static function getById($id) {
        return getOneDB("SELECT * FROM mosques WHERE id = ?", [$id]);
    }

    public static function getWithPrayerTimes($id, $date = null) {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $mosque = self::getById($id);
        if (!$mosque) return null;

        $prayerTimes = getAllDB(
            "SELECT * FROM prayertimes WHERE mosque_id = ? AND date = ? LIMIT 1",
            [$id, $date]
        );

        $mosque['prayer_times'] = $prayerTimes[0] ?? null;
        return $mosque;
    }

    public static function update($id, $data) {
        $sql = "UPDATE mosques SET name = ?, address = ?, city = ?, latitude = ?, longitude = ?, imam = ?, contact = ? WHERE id = ?";
        queryDb($sql, [
            $data['name'],
            $data['address'],
            $data['city'],
            $data['latitude'],
            $data['longitude'],
            $data['imam'],
            $data['contact'],
            $id
        ]);
        return true;
    }

    public static function search($query) {
        $query = '%' . $query . '%';
        return getAllDB(
            "SELECT * FROM mosques WHERE name LIKE ? OR city LIKE ? OR address LIKE ? ORDER BY name ASC",
            [$query, $query, $query]
        );
    }
}
