<?php
/**
 * Optional override — copy to db.local.php only if this machine needs different Oracle settings.
 * Default credentials live in db.php (committed).
 */
return [
    'username' => 'NEPSA',
    'password' => 'Nepsa@12345',
    'connection_string' => '192.168.1.64:1521/XEPDB1',
];
