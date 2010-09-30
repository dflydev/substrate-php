<?php

class tests_Person {
    private $name;
    public function __construct($name = null) {
        $this->name = $name;
    }
    public function name() { return $this->name; }
    public function setName($name = null) { $this->name = $name; }
}

?>