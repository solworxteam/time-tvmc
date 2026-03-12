const express = require('express');
const router = express.Router();
const db = require('../db');

// Get parking info by mosque_id
router.get('/:mosque_id', async (req, res) => {
  const mosqueId = req.params.mosque_id;

  try {
    const [rows] = await db.query(
      'SELECT * FROM parking WHERE mosque_id = ?',
      [mosqueId]
    );

    if (rows.length === 0) {
      return res.status(404).json({ error: 'No parking info found' });
    }

    res.json(rows[0]); // return single object
  } catch (err) {
    console.error('Error fetching parking info:', err);
    res.status(500).json({ error: err.message });
  }
});

// Update or insert parking info
router.put('/:mosque_id', async (req, res) => {
  const mosqueId = req.params.mosque_id;
  const {
    onsite_parking,
    disable_bays,
    off_street_parking,
    road_name,
    address,
    distance_to_mosque
  } = req.body;

  try {
    // Try to update existing parking info
    const [updateResult] = await db.query(
      `UPDATE parking SET 
        onsite_parking = ?, 
        disable_bays = ?, 
        off_street_parking = ?, 
        road_name = ?, 
        address = ?, 
        distance_to_mosque = ?
       WHERE mosque_id = ?`,
      [
        onsite_parking,
        disable_bays,
        off_street_parking,
        road_name,
        address,
        distance_to_mosque,
        mosqueId
      ]
    );

    if (updateResult.affectedRows === 0) {
      // If no record updated, insert new record
      const [insertResult] = await db.query(
        `INSERT INTO parking (
          id,
          mosque_id,
          onsite_parking,
          disable_bays,
          off_street_parking,
          road_name,
          address,
          distance_to_mosque
        ) VALUES (
          UUID(), ?, ?, ?, ?, ?, ?, ?
        )`,
        [
          mosqueId,
          onsite_parking,
          disable_bays,
          off_street_parking,
          road_name,
          address,
          distance_to_mosque
        ]
      );
      

      return res.status(201).json({
        message: 'Parking info inserted',
        id: insertResult.insertId
      });
    }

    res.json({ message: 'Parking info updated' });
  } catch (err) {
    console.error('Error saving parking info:', err);
    res.status(500).json({ error: 'Failed to save parking info' });
  }
});

module.exports = router;
