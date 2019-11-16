<?php
/**
 * User: Zemlyansky Alexander <astrolog@online.ua>
 */

namespace app\system;

/**
 * Class View
 * The base View object
 */
class View extends Object {

    public static function render($viewName, $params = []) {
        foreach ($params as $param => $value) {
            $$param = $value;
        }

        require("views/$viewName.php");
    }
}