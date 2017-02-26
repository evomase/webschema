<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 23/02/2017
 * Time: 22:42
 */

namespace WebSchema\Models\AMP;

class Route
{
    const QUERY_VAR = 'amp';
    /**
     * @var self
     */
    private static $instance;
    private $active = false;
    private $uri;

    private $default = [
        'PATH_INFO'   => null,
        'REQUEST_URI' => null,
        'PHP_SELF'    => null
    ];

    private function __construct()
    {
        add_filter('do_parse_request', function () {
            $this->register();
            return true;
        });

        add_filter('request', function (array $queryVars) {
            if ($this->active) {
                $queryVars = array_merge($queryVars, [self::QUERY_VAR => 'on']);

                //revert to default $_SERVER vars
                $this->setDefaultServerVars();
            }

            return $queryVars;
        });
    }

    private function register()
    {
        /**
         * @var \WP_Rewrite $wp_rewrite
         */
        global $wp_rewrite;

        $wp_rewrite->add_rule('^' . self::QUERY_VAR . '\/?', 'index.php?' . self::QUERY_VAR . '=on', 'top');

        $uri = ($_SERVER['PATH_INFO']) ?: $_SERVER['REQUEST_URI'];

        if (preg_match('/^\/' . self::QUERY_VAR . '\/?/i', $uri)) {
            $uri = str_replace('/' . self::QUERY_VAR, '', $uri);

            //for WP installations in a directory and not in domain root
            $data = parse_url(home_url());

            $index = (!empty($data['path'])) ? trailingslashit($data['path']) . $wp_rewrite->index
                : '/' . $wp_rewrite->index;

            //store original $_SERVER vars
            foreach (array_keys($this->default) as $key) {
                if (!empty($_SERVER[$key])) {
                    $this->default[$key] = $_SERVER[$key];
                }
            }

            //Non-pretty URL or with index.php in URI
            if ($_SERVER['PATH_INFO']) {
                $_SERVER['PATH_INFO'] = $uri;
                $_SERVER['REQUEST_URI'] = $index . $uri;
            } else { // Pretty URL
                $_SERVER['REQUEST_URI'] = $uri;
            }

            if ($_SERVER['PHP_SELF'] != $index) {
                $_SERVER['PHP_SELF'] = $index . $uri;
            }

            //AMP is on
            $this->active = true;
            $this->uri = $uri;
        }
    }

    private function setDefaultServerVars()
    {
        foreach ($this->default as $key => $value) {
            if ($value) {
                $_SERVER[$key] = $value;
            }
        }
    }

    public static function boot()
    {
        self::$instance = new self();
    }

    /**
     * @return bool
     */
    public static function isAMP()
    {
        return self::$instance->active;
    }

    /**
     * @return string
     */
    public static function getURI()
    {
        return self::$instance->uri;
    }
}