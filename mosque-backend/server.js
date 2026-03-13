// server.js
const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');

// Import route files
const mosquesRoutes = require('./routes/mosques');
const prayerTimesRoutes = require('./routes/prayerTimes');
const parkingRoutes = require('./routes/parking');
const authRoutes = require('./routes/auth');
const prayerTimesAdmin = require('./routes/prayerTimesAdmin');

const app = express();

// Middleware setup
app.use(cors());
app.use(express.json());
app.use(bodyParser.json());

// Route setup
app.use('/api/mosques', mosquesRoutes);
app.use('/api/prayertimes', prayerTimesRoutes);
app.use('/api/prayertimes', prayerTimesAdmin);
app.use('/api/parking', parkingRoutes);
app.use('/api', authRoutes);

// Health check
app.get('/', (req, res) => {
  res.send('API is working');
});

app.get('/api', (req, res) => {
  res.send('API is working ✅');
});

// Error handler
app.use((err, req, res, next) => {
  console.error(err.stack);
  res.status(500).json({ error: 'Something went wrong! Please try again later.' });
});

// Start the server
const PORT = process.env.PORT || 3001;
app.listen(PORT, () => console.log(`✅ Server running on http://localhost:${PORT}`));

// ✅ Export for CPanel/other deployments
module.exports = app;
