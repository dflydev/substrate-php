<?php

require_once('substrate_stones_IPriorityOrderedStone.php');
require_once('substrate_stones_IOrderedStone.php');

class substrate_stones_OrderedUtil {
    
    public static $HIGHEST_PRECEDENCE = 0;
    
    public static $LOWEST_PRECEDENCE = 500000;
    
    static protected function compare($o1, $o2) {
        $p1 = $o1 instanceof substrate_stones_IPriorityOrderedStone;
        $p2 = $o2 instanceof substrate_stones_IPriorityOrderedStone;
        if ( $p1 && ! $p2 ) return 1;
        if ( $p2 && ! $p1 ) return -1;
        $i1 = self::GET_STONE_ORDER($o1);
        $i2 = self::GET_STONE_ORDER($o2);
        return ($i1 < $i2) ? 1 : ($i1 > $i2) ? -1 : 0;
    }
    static public function SORT(array $toBeSorted) {
        usort($toBeSorted, array('substrate_stones_OrderedUtil', 'compare'));
        return $toBeSorted;
    }
    static protected function GET_STONE_ORDER($object) {
        $order = $object instanceof substrate_stones_IOrderedStone ? $object->getStoneOrder() : null;
        return $order !== null ? $order : self::$LOWEST_PRECEDENCE;
    }
}
