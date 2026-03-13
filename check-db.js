const mysql = require('mysql2/promise');

(async () => {
  try {
    const pool = mysql.createPool({
      host: '127.0.0.1',
      user: 'root',
      password: '',
      database: 'information_schema'
    });
    
    const conn = await pool.getConnection();
    const [dbs] = await conn.query(
      'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?',
      ['timetvmcorg_mosquesuk']
    );
    conn.release();
    await pool.end();
    
    if(dbs.length > 0) {
      console.log('✅ Database exists');
    } else {
      console.log('❌ Database not found - needs import');
    }
  } catch(e) {
    console.error('❌ Error:', e.message);
  }
})();
