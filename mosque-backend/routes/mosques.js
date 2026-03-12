// routes/mosques.js
const express = require('express');
const router = express.Router();
const db = require('../db');

// Get all mosques
router.get('/', async (req, res) => {
  try {
    const [rows] = await db.query('SELECT * FROM mosques');
    res.json(rows);
  } catch (err) {
    console.error('Error fetching mosques:', err);
    res.status(500).json({ error: 'Internal server error while fetching mosques.' });
  }
});

// Update mosque by ID
router.put('/:id', async (req, res) => {
  const mosqueId = req.params.id;
  const { name, address, location_url } = req.body;

  try {
    const [result] = await db.query(
      'UPDATE mosques SET name = ?, address = ?, location_url = ? WHERE id = ?',
      [name, address, location_url, mosqueId]
    );

    if (result.affectedRows === 0) {
      return res.status(404).json({ error: 'Mosque not found or no changes made.' });
    }

    res.json({ message: 'Mosque updated successfully' });
  } catch (err) {
    console.error('Error updating mosque:', err);
    res.status(500).json({ error: 'Failed to update mosque.' });
  }
});

// Get mosque by ID with prayer times
router.get('/:id', async (req, res) => {
  const mosqueId = req.params.id;

  try {
    const [mosqueRows] = await db.query('SELECT * FROM mosques WHERE id = ?', [mosqueId]);

    if (mosqueRows.length === 0) {
      return res.status(404).json({ error: 'Mosque not found' });
    }

    const [prayerTimesRows] = await db.query(
      'SELECT * FROM prayertimes WHERE mosque_id = ? ORDER BY date ASC',
      [mosqueId]
    );

    const mosque = mosqueRows[0];
    mosque.prayertimes = prayerTimesRows;

    res.json(mosque);
  } catch (err) {
    console.error('Error fetching mosque by ID:', err);
    res.status(500).json({ error: 'Internal server error while fetching mosque details.' });
  }
});

module.exports = router;
