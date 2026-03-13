<?php
require_once __DIR__ . '/../../config/database.php';

class PrayerTime {
    public static function getByDate($date, $mosqueId = null) {
        if ($mosqueId) {
            return getAllDB(
                "SELECT * FROM prayertimes WHERE date = ? AND mosque_id = ? ORDER BY mosque_id",
                [$date, $mosqueId]
            );
        }
        return getAllDB("SELECT * FROM prayertimes WHERE date = ? ORDER BY mosque_id", [$date]);
    }

    public static function getTodayPrayerTimes($mosqueId = null) {
        return self::getByDate(date('Y-m-d'), $mosqueId);
    }

    public static function getByMosqueAndMonth($mosqueId, $month, $year) {
        $start = "$year-$month-01";
        $end = date('Y-m-t', strtotime($start));
        
        return getAllDB(
            "SELECT * FROM prayertimes WHERE mosque_id = ? AND date BETWEEN ? AND ? ORDER BY date ASC",
            [$mosqueId, $start, $end]
        );
    }

    public static function getById($id) {
        return getOneDB("SELECT * FROM prayertimes WHERE id = ?", [$id]);
    }

    public static function update($id, $data) {
        $sql = "UPDATE prayertimes SET fajr = ?, zuhr = ?, asr = ?, maghrib = ?, isha = ? WHERE id = ?";
        queryDb($sql, [
            $data['fajr'],
            $data['zuhr'],
            $data['asr'],
            $data['maghrib'],
            $data['isha'],
            $id
        ]);
        return true;
    }

    public static function insertOrUpdate($mosqueId, $date, $times) {
        $sql = "INSERT INTO prayertimes (mosque_id, date, fajr, zuhr, asr, maghrib, isha) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE fajr=?, zuhr=?, asr=?, maghrib=?, isha=?";
        
        queryDb($sql, [
            $mosqueId, $date,
            $times['fajr'], $times['zuhr'], $times['asr'], $times['maghrib'], $times['isha'],
            $times['fajr'], $times['zuhr'], $times['asr'], $times['maghrib'], $times['isha']
        ]);
        return true;
    }

    public static function deletePrayerTimes($startDate, $endDate = null) {
        if (!$endDate) $endDate = $startDate;
        queryDb("DELETE FROM prayertimes WHERE date BETWEEN ? AND ?", [$startDate, $endDate]);
        return true;
    }
}
