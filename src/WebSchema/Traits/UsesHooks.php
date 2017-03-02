<?php
/**
 * Created by IntelliJ IDEA.
 * User: David
 * Date: 02/03/2017
 * Time: 18:10
 */

namespace WebSchema\Traits;

trait UsesHooks
{
    protected $hooks;

    protected function removeHooks()
    {
        foreach ($this->hooks as $handle => $data) {
            remove_filter($handle, $data[0], $data[1]);
        }
    }

    /**
     * @param $handle
     * @return bool
     */
    protected function removeHook($handle)
    {
        if (!empty($this->hooks[$handle])) {
            $data = $this->hooks[$handle];

            return remove_filter($handle, $data[0], $data[1]);
        }

        return false;
    }

    /**
     * @param string   $handle
     * @param callable $callback
     * @param int      $priority
     * @param int      $acceptedArgs
     */
    protected function addAction($handle, callable $callback, $priority = 10, $acceptedArgs = 1)
    {
        $this->addFilter($handle, $callback, $priority, $acceptedArgs);
    }

    /**
     * @param string   $handle
     * @param callable $callback
     * @param int      $priority
     * @param int      $acceptedArgs
     */
    protected function addFilter($handle, callable $callback, $priority = 10, $acceptedArgs = 1)
    {
        add_filter($handle, $callback, $priority, $acceptedArgs);
        $this->addHook($handle, $callback, $priority);
    }

    /**
     * @param string   $handle
     * @param callable $callback
     * @param int      $priority
     */
    protected function addHook($handle, callable $callback, $priority = 10)
    {
        $this->hooks[$handle] = [$callback, $priority];
    }
}