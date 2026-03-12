// server.js
const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser'); // For parsing incoming JSON data

// Import route files
const mosquesRoutes = require('./routes/mosques');
const prayerTimesRoutes = require('./routes/prayerTimes');
const parkingRoutes = require('./routes/parking');
const authRoutes = require('./routes/auth');  // Add the auth route
const prayerTimesAdmin = require('./routes/prayerTimesAdmin'); // Corrected import for prayerTimesAdmin

const app = express();

// Middleware setup
app.use(cors()); // Allow all CORS requests (you can modify this for security later)
app.use(express.json()); // This will parse incoming JSON requests
app.use(bodyParser.json()); // Optional, ensures compatibility with JSON requests

// Route setup
app.use('/api/mosques', mosquesRoutes);
app.use('/api/prayertimes', prayerTimesRoutes);
app.use('/api/prayertimes', prayerTimesAdmin); // Added prayerTimesAdmin route here for by-mosque lookups
app.use('/api/parking', parkingRoutes);
app.use('/api', authRoutes); // Add the auth routes for login

// Basic health check route
app.get('/', (req, res) => {
    res.send('API is working');
});

// Error handling middleware
app.use((err, req, res, next) => {
  console.error(err.stack);  // Log the error stack for debugging
  res.status(500).json({ error: 'Something went wrong! Please try again later.' });  // Ensure JSON response
});

app.get('/api', (req, res) => {
  res.send('API is working ✅');
});

// Start the server
const PORT = 3001;
app.listen(PORT, () => console.log(`Server running on http://localhost:${PORT}`));