const mysql = require('mysql2/promise');

const connection = mysql.createPool({
  host: '185.199.220.110',
  user: 'timetvmcorg_mosquesuk1',
  password: 'mosquesuk11',
  database: 'timetvmcorg_mosquesuk',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
});

module.exports = connection;
