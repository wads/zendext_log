<?php

class Wads_Util
{
    public static function isInt($val) {
        if(is_int($val)) {
            return true;
        }

        if(defined('PHP_INT_MIN') {
            if(is_numeric($val)) {
                $val = (int)$val;
                if(($val >= PHP_INT_MIN) && ($val <= PHP_INT_MAX)) {
                    return true;
                }
            }
        }

        return false;
    }
}