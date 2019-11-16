<?php
/**
 * User: Zemlyansky Alexander <astrolog@online.ua>
 */

namespace app\system;

class App {

    public static $defaultControllerName = 'site';

    protected static $config;

    public static function getConfig($param) {
        if (!static::$config) {
            $config = require('config.php');
            return $config[$param];
        }

        return static::$config;
    }

    /**
     * Convert url to framework router params.
     * Just over-simple thing, no special checks made (BeeJee task doesn't wait too long)
     *
     * @param $url string Url to show
     * @return array
     */
    private function urlPartsToRouteElements($url) {
        $urlPath = trim(explode('?', $url)[0], '/');

        $urlPathParts = explode('/', $urlPath);
        $count = count($urlPathParts);

        $controller = static::$defaultControllerName;
        $action = 'index';

        if ($count > 1) {
            $controller = $urlPathParts[$count - 2];
            $action = $urlPathParts[$count - 1];
        } elseif ($count == 1 && !empty($urlPathParts[0])) {
            // action is also here
            $action = $urlPathParts[$count - 1];
        }

        return [
            'controller' => $controller,
            'action' => $action,
        ];
    }

    /**
     * Launches the framework itself
     *
     * @param $url
     */
    public function run($url) {
        $routeInfo = $this->urlPartsToRouteElements($url);

        $controller = ucfirst($routeInfo['controller']);
        $action = $routeInfo['action'];

        $fullClassName = "app\\controllers\\{$controller}Controller";

        try {
            if (class_exists($fullClassName)) {
                /** @var $controllerInstance Controller */
                $controllerInstance = new $fullClassName();

                if (method_exists($controllerInstance, "action" . ucfirst($action))) {

                    // here goes all!
                    if (!$_POST['ajax']) {
                        ob_start();
                        ob_implicit_flush(false);

                        $controllerOutput = $controllerInstance->run($action);

                        $out = ob_get_clean() . $controllerOutput;

                        $totalOutput = View::render('frames/default', [
                            'content' => $out
                        ]);
                    } else {
                        $controllerInstance->run($action);
                    }

                    // ЗДЕСЬ ВСЁ И ВЫВОДИТСЯ!!
                    echo $totalOutput;

                } else {
                    $controllerInstance->error404();
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public static function isAdminLogin() {
        return $_SESSION['is_admin_logged'] == 1;
    }

}