<?php

namespace Illuminate\Database\PDO;

use Doctrine\DBAL\Driver\AbstractSQLServerDriver;

class SqlServerDriver extends AbstractSQLServerDriver
{
<<<<<<< HEAD
<<<<<<< HEAD
    /**
     * @return \Doctrine\DBAL\Driver\Connection
     */
=======
>>>>>>> 22c0e54 (table changes)
=======
    /**
     * @return \Doctrine\DBAL\Driver\Connection
     */
>>>>>>> f330c64 (optimization in progress)
    public function connect(array $params)
    {
        return new SqlServerConnection(
            new Connection($params['pdo'])
        );
    }
}
