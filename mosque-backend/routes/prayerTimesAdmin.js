const express = require('express');
const router = express.Router();
const db = require('../db');
// routes/prayerTimes.js
router.get('/by-mosque/:mosqueId', async (req, res) => {
    const { mosqueId } = req.params;
    const { month, year } = req.query;
  
    if (!mosqueId || !month || !year) {
      return res.status(400).json({ message: 'Missing mosqueId, month, or year' });
    }
  
    try {
      const [rows] = await db.query(
        'SELECT * FROM prayertimes WHERE mosque_id = ? AND MONTH(date) = ? AND YEAR(date) = ? ORDER BY date ASC',
        [mosqueId, month, year]
      );
  
      if (rows.length === 0) {
        return res.status(404).json({ message: 'No prayer times found for the specified date' });
      }
  
      res.json(rows);
    } catch (err) {
      res.status(500).json({ error: err.message });
    }
  });

  // Update a single prayer time by ID
router.put('/:id', async (req, res) => {
    const id = req.params.id;
    const {
      fajar_start,
      fajar_jamaat,
      zuhr_start,
      zuhr_jamaat,
      asr_start,
      asr_jamaat,
      maghrib,
      isha_start,
      isha_jamaat
    } = req.body;
  
    try {
      const [result] = await db.query(
        `UPDATE prayertimes SET 
          fajar_start = ?, fajar_jamaat = ?, 
          zuhr_start = ?, zuhr_jamaat = ?, 
          asr_start = ?, asr_jamaat = ?, 
          maghrib = ?, 
          isha_start = ?, isha_jamaat = ? 
        WHERE id = ?`,
        [
          fajar_start,
          fajar_jamaat,
          zuhr_start,
          zuhr_jamaat,
          asr_start,
          asr_jamaat,
          maghrib,
          isha_start,
          isha_jamaat,
          id
        ]
      );
  
      if (result.affectedRows === 0) {
        return res.status(404).json({ message: 'Prayer time not found' });
      }
  
      res.json({ message: 'Prayer time updated successfully' });
    } catch (err) {
      console.error('Error updating prayer time:', err);
      res.status(500).json({ message: 'Failed to update prayer time' });
    }
  });
  

  

module.exports = router;
