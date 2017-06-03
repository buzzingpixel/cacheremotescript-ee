<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 */

/** @noinspection AutoloadingIssuesInspection */
/**
 * Class Cacheremotescript
 */
class Cacheremotescript
{
    /**
     * Read the file contents
     */
    public function cache()
    {
        // Get template class
        /** @var \EE_Template $tmpl */
        $tmpl = ee()->TMPL;

        // Fetch tag parameters
        $url = $tmpl->fetch_param('url');
        $cacheTime = (int) $tmpl->fetch_param('cache_time', 2592000);

        // Get file info about the URL
        $urlPathInfo = pathinfo($url);

        // Set cached file name
        $fileName = md5($url);

        // Check if there is an extension
        if (isset($urlPathInfo['extension']) && $urlPathInfo['extension']) {
            $fileName .= ".{$urlPathInfo['extension']}";
        }

        // Set cache path
        $path = rtrim(realpath($_SERVER['DOCUMENT_ROOT']), '/') . '/';
        $path .= 'remote-scripts-cache/';

        // Set the full file path
        $fullFilePath = "{$path}{$fileName}";

        // Set url to cache script
        $scriptUrl = rtrim(ee()->config->item('site_url'), '/') . '/';
        $scriptUrl .= "remote-scripts-cache/{$fileName}";

        // Create directory if necesary
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
            file_put_contents("{$path}.gitkeep", '');
            chmod("{$path}.gitkeep", 0777);
        }

        // Run script cache if the file does not exist
        if (! is_file($fullFilePath)) {
            // Cache the script
            $this->cacheScript($url, $fullFilePath);

            // Return the script URL
            return $scriptUrl;
        }

        // Calculate cache breaking time
        $cacheBreakTime = filemtime($fullFilePath) + $cacheTime;

        // Check if we need to break the cache
        if ($cacheBreakTime < time()) {
            // Cache the script
            $this->cacheScript($url, $fullFilePath);

            // Return the script URL
            return $scriptUrl;
        }

        // Return the script URL
        return $scriptUrl;
    }

    /**
     * Cache the script
     *
     * @param string $url
     * @param string $fullFilePath
     */
    private function cacheScript($url, $fullFilePath)
    {
        // Set options
        $options = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
            )
        );

        // Set context
        $context = stream_context_create($options);

        // Get the feed
        $content = file_get_contents($url, false, $context);

        // Write the file to cache
        file_put_contents($fullFilePath, $content);
        chmod($fullFilePath, 0777);
    }
}
