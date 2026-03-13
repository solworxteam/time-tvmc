<?php
require_once __DIR__ . '/../../config/database.php';

class Parking {
    public static function getByMosqueId($mosqueId) {
        return getOneDB("SELECT * FROM parking WHERE mosque_id = ?", [$mosqueId]);
    }

    public static function update($mosqueId, $data) {
        $existing = self::getByMosqueId($mosqueId);
        
        if ($existing) {
            $sql = "UPDATE parking SET spaces = ?, has_accessible = ?, price = ?, notes = ? WHERE mosque_id = ?";
            queryDb($sql, [
                $data['spaces'] ?? 0,
                $data['has_accessible'] ?? 0,
                $data['price'] ?? 0,
                $data['notes'] ?? '',
                $mosqueId
            ]);
        } else {
            $sql = "INSERT INTO parking (mosque_id, spaces, has_accessible, price, notes) VALUES (?, ?, ?, ?, ?)";
            queryDb($sql, [
                $mosqueId,
                $data['spaces'] ?? 0,
                $data['has_accessible'] ?? 0,
                $data['price'] ?? 0,
                $data['notes'] ?? ''
            ]);
        }
        
        return true;
    }
}
