<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 */

// Get addon json path
$addonPath = realpath(dirname(__FILE__));
$addonJsonPath = "{$addonPath}/addon.json";

// Get the addon json file
$addonJson = json_decode(file_get_contents($addonJsonPath));

// Return info about the addon for ExpressionEngine
return array(
    'author' => $addonJson->author,
    'author_url' => $addonJson->authorUrl,
    'description' => $addonJson->description,
    'docs_url' => $addonJson->docsUrl,
    'name' => $addonJson->label,
    'namespace' => $addonJson->namespace,
    'settings_exist' => $addonJson->settingsExist,
    'version' => $addonJson->version,
);
