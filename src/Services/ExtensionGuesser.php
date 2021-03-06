<?php namespace Tado\EloquentExternalStorage\Services;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser as ExtensionGuesserFromMimetype;

/**
 * Tries to guess the extension of a stream (using mime-type detection, and mime-type to file extension mapping)
 */
class ExtensionGuesser {

    public function __construct()
    {
        $this->mimeTypeGuesser = MimeTypeGuesser::getInstance();
        $this->extensionGuesserFromMimeType = ExtensionGuesserFromMimetype::getInstance();
    }

    /**
     * Guesses the file extension that would belong to a given content
     *
     * @param $string
     * @return string
     */
    public function guess($string)
    {
        //Convert string to temp file, and recover its path
        //We can't use tmpfile because of hhvm implementation: https://github.com/facebook/hhvm/issues/2336
        $tempFile = fopen(tempnam(sys_get_temp_dir(), 'eloquent_ext_storage_'), 'w');
        fwrite($tempFile, $string);
        $tempPath = $this->getPathFromFileHandler($tempFile);

        $mimeType = $this->mimeTypeGuesser->guess($tempPath);

        return $this->extensionGuesserFromMimeType->guess($mimeType);
    }

    /**
     * @link http://stackoverflow.com/questions/5144583/getting-filename-or-deleting-file-using-file-handle
     *
     * @param $handler
     *
     * @return mixed
     */
    private function getPathFromFileHandler($handler)
    {
        return stream_get_meta_data($handler)['uri'];
    }
} 