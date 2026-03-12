const express = require('express');
const router = express.Router();
const db = require('../db');

router.get('/:date', async (req, res) => {
  const isoDate = req.params.date; // Keep it in ISO format, no need for reformatting.
  
  if (!isoDate) {
    return res.status(400).json({ error: "Date parameter is missing" });
  }

  try {
    const [rows] = await db.query(
      'SELECT * FROM prayertimes WHERE date = ?',
      [isoDate]
    );
    
    if (rows.length === 0) {
      return res.status(404).json({ message: "No prayer times found for the specified date" });
    }

    res.json(rows);
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// POST /api/prayertimes/upload
router.post('/upload', async (req, res) => {
    const { prayerTimes } = req.body;
  
    if (!Array.isArray(prayerTimes) || prayerTimes.length === 0) {
      return res.status(400).json({ message: 'No prayer times provided.' });
    }
  
    try {
      const insertPromises = prayerTimes.map(prayer => {
        return db.query(
          `INSERT INTO prayertimes (
            mosque_id,
            date,
            fajar_start,
            fajar_jamaat,
            zuhr_start,
            zuhr_jamaat,
            asr_start,
            asr_jamaat,
            maghrib,
            isha_start,
            isha_jamaat
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
            isha_jamaat = VALUES(isha_jamaat)
          `,
          [
            prayer.mosque_id,
            prayer.date.split("T")[0], // keep date only
            prayer.fajar_start,
            prayer.fajar_jamaat,
            prayer.zuhr_start,
            prayer.zuhr_jamaat,
            prayer.asr_start,
            prayer.asr_jamaat,
            prayer.maghrib,
            prayer.isha_start,
            prayer.isha_jamaat
          ]
        );
      });
  
      await Promise.all(insertPromises);
  
      res.status(200).json({ message: 'Prayer times uploaded successfully' });
    } catch (err) {
      console.error('Error uploading prayer times:', err);
      res.status(500).json({ message: 'Failed to upload prayer times' });
    }
  });
  

module.exports = router;