<?php


namespace App\Service;


use App\Command\CoffeeListConverter;
use Exception;
use SimpleXMLElement;

/**
 * Class XmlFileLoaderService
 * @package App\Service
 */
class XmlFileLoaderService
{
    /**
     * @param string $location
     * @param string $filepath
     * @return bool|SimpleXMLElement
     * @throws Exception
     */
    public function loadFile(string $location, string $filepath)
    {
        switch ($location) {
            case CoffeeListConverter::LOCATION_REMOTE:
                return $this->loadRemoteFile($filepath);
            case CoffeeListConverter::LOCATION_LOCAL:
            default:
                return $this->loadLocalFile($filepath);
        }
    }

    /**
     * @param string $filepath
     * @return false|SimpleXMLElement
     * @throws Exception
     */
    private function loadLocalFile(string $filepath)
    {
        $fullPath = realpath($filepath);

        if (!$fullPath || empty($filepath)) {
            throw new Exception('Failed reading file. File not found.');
        }

        try {
            return simplexml_load_string(
                file_get_contents($fullPath),
                'SimpleXMLElement',
                LIBXML_NOCDATA
            );
        } catch (Exception $exception) {
            throw new Exception('Failed reading file. Invalid file contents.');
        }
    }

    /**
     * @param string $filepath
     * @return false|SimpleXMLElement
     * @throws Exception
     */
    private function loadRemoteFile(string $filepath)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $filepath);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if(curl_exec($ch) === FALSE) {
            throw new Exception('Failed retrieving file.');
        } else {
            $contents =  curl_exec($ch);
        }

        curl_close($ch);

        try {
            return simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NOCDATA);
        } catch (Exception $exception) {
            throw new Exception('Failed reading file. Invalid file contents.');
        }
    }
}