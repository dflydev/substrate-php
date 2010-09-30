<?php

class tests_Pair {
    private $leader;
    private $follower;
    public function __construct($leader = null, $follower = null) {
        $this->leader = $leader;
        $this->follower = $follower;
    }
    public function leader() { return $this->leader; }
    public function setLeader($leader = null) { $this->leader = $leader; }
    public function follower() { return $this->follower; }
    public function setFollower($follower = null) { $this->follower = $follower; }
}

?>