<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 23/02/2017
 * Time: 22:42
 */

namespace WebSchema\Models\AMP;

use WebSchema\Traits\IsSingleton;
use WebSchema\Traits\UsesHooks;

class Route
{
    use IsSingleton;
    use UsesHooks;

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
        $this->addFilter('do_parse_request', function () {
            $this->register();
            return true;
        });

        $this->addFilter('request', function (array $queryVars) {
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

        $this->uri = ($_SERVER['PATH_INFO']) ?: $_SERVER['REQUEST_URI'];
        $this->active = false;

        if ($_GET[self::QUERY_VAR] == 'on') {
            //AMP is on
            $this->active = true;
        } elseif (preg_match('/^\/' . self::QUERY_VAR . '\/?/i', $this->uri)) {
            $uri = str_replace('/' . self::QUERY_VAR, '', $this->uri);

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
                $_SERVER['PHP_SELF'] = $index;
            }

            $this->active = true;
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

    public static function shutdown()
    {
        self::$instance->removeHooks();
    }

    /**
     * @return bool
     */
    public function isAMP()
    {
        return $this->active;
    }

    /**
     * @return string
     */
    public function getURI()
    {
        return $this->uri;
    }
}