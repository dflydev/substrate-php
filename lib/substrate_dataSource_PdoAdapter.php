<?php

require_once('substrate_stones_IFactoryStone.php');
class substrate_dataSource_PdoAdapter implements substrate_stones_IFactoryStone {
    private $dsn;
    private $username;
    private $password;
    public function __construct($dsn, $username, $password) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
    }
    public function getObject() {
        $pdo = new PDO($this->dsn, $this->username, $this->password);
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        return $pdo;
    }
    public function getObjectType() {
        return PDO.getClass();
    }
}

?>
