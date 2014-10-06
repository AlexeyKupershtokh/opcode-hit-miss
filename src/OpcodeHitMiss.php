<?php

namespace AlexeyKupershtokh\OpcodeHitMiss;

class OpcodeHitMiss
{
    protected $cachedFiles = array();

    public static function register($callback)
    {
        $instance = new self();
        $instance->collectCachedFiles();
        register_shutdown_function(array($instance, 'onShutdown'), $callback);
    }

    public function collectCachedFiles()
    {
        $this->cachedFiles = array();
        if (function_exists('opcache_get_status')) {
            $opcache = opcache_get_status(true);
            $files = array_keys((array)$opcache['scripts']);
            $this->cachedFiles = array_merge($this->cachedFiles, array_fill_keys($files, 1));
        }
        if (function_exists('apc_cache_info')) {
            $opcache = apc_cache_info();
            $files = array_map(
                function ($element) {
                    return $element['filename'];
                },
                (array)$opcache['cache_list']
            );
            $this->cachedFiles = array_merge($this->cachedFiles, array_fill_keys($files, 1));
        }
        return $this->cachedFiles;
    }

    public function getIncludedFiles()
    {
        $result = array();
        $files = get_included_files();
        $filesMap = array_fill_keys($files, 0);
        return array_replace($filesMap, array_intersect_key($this->cachedFiles, $filesMap));
    }

    public function getIncludedFilesStats()
    {
        $includedFiles = $this->getIncludedFiles();
        return array(
            'count' => count($includedFiles),
            'hits' => array_sum($includedFiles),
            'misses' => count($includedFiles) - array_sum($includedFiles),
        );
    }

    public function onShutdown($callback)
    {
        call_user_func($callback, $this);
    }
}
