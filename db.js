const mysql = require('mysql2');

const connection = mysql.createConnection({
  host: '185.199.220.110', // Your real DB IP
  user: 'timetvmcorg_mosquesuk1',
  password: 'mosquesuk11',
  database: 'timetvmcorg_mosquesuk'
}).promise();  // <-- Added .promise() here

connection.connect((err) => {
  if (err) {
    console.error('Database connection failed: ' + err.stack);
    return;
  }
  console.log('Connected to the database.');
});

module.exports = connection;
