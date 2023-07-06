<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Http\Controllers\Controller;
use Lang;

/**
 * Handles all the language related operations like language change, getting languages etc.
 */
class LanguageController extends Controller
{
    /**
     * Gets language file content as array based on current language chosen
     * by the user (if not chosen by the user then language chosen by the admin will be fetched)
     * NOTE : currently we are caching the entire language file, but this has to change
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array                                language file as a single array
     */
    public function getLanguageFile()
    {
        $languages = array_unique([Lang::getFallback(), App::getLocale()]);

        $languageArray = [];

        foreach ($languages as $lang) {
            $this->appendCoreLanguage($lang, $languageArray);
        }

        header('Content-Type: text/javascript');
        // caching for 30 days
        header('Cache-Control: max-age=2592000');
        echo 'translator = '.json_encode($languageArray).';';
        exit();
    }

    /**
     * Fetches language array of given language for core Helpdesk and merges
     * it in $languageArray
     *
     * @param  string  $languageName
     * @param  array  $languageArray
     * @param  array  $languageArray
     * @return  void
     */
    private function appendCoreLanguage(string $languageName, array &$languageArray): void
    {
        $path = base_path('lang/'.$languageName);
        $this->updateLanguageArray($path, $languageArray);
    }

    /**
     * Returns an array of filenames with .php extension in given directory path
     *
     * @param  string  $path  path to directory from which .php files
     * @return  array          empty array if given path is not a directory otherwise
     *                         array containing app .php filenames with path
     */
    private function getLanguageFileArray(string $path): array
    {
        if (! is_dir($path)) {
            return [];
        }

        return glob($path.DIRECTORY_SEPARATOR.'*.php');
    }

    /**
     * Function which actually fetches language array data from all ".php" lanaguge
     * files availanle in the given path and merges that into $languageArray
     *
     * @param  string  $path
     * @param  array  $languageArray
     * @return  void
     */
    private function updateLanguageArray(string $path, &$languageArray): void
    {
        $files = $this->getLanguageFileArray($path);
        foreach ($files as $file) {
            $name = basename($file, '.php');
            // merge lang files with same name
            if (array_key_exists($name, $languageArray)) {
                $languageArray[$name] = array_merge($languageArray[$name], require $file);
            } else {
                $languageArray[$name] = require $file;
            }
        }
    }
}
