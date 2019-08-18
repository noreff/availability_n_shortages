<?php

namespace App;

use App\Config\Config;
use \PDO;

class DbConnect
{
    private $pdo;

    public function connect() {
        if ($this->pdo === null) {
            $this->pdo = new PDO('sqlite:' . Config::DB_PATH);
        }
        return $this->pdo;
    }
}
