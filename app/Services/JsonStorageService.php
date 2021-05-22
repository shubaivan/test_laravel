<?php


namespace App\Services;

use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class JsonStorageService
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $dbData;

    /**
     * JsonStorageService constructor.
     */
    public function __construct()
    {
        $this->path = Config::get('jsonstorage.path');
        $ping = $this->ping();
        if (!$ping) {
            throw new FileNotFoundException($this->path);
        }
    }

    /**
     * @param $bookName
     * @return array
     * @throws \Exception
     */
    public function getBookByNameSlug($bookName)
    {
        $this->readDB();
        $storeMatchBooks = [];
        $matches = array_filter($this->dbData, function ($categoryBooks, $categoryName) use ($bookName, &$storeMatchBooks) {

            $matches = array_filter($categoryBooks, function ($book) use ($bookName) {
                return preg_grep("/$bookName/iu", $book);
            });
            array_map(function ($book) use ($categoryName, &$storeMatchBooks) {
                $storeMatchBooks[$categoryName] = $book;
            }, $matches);
            return count($matches);
        }, ARRAY_FILTER_USE_BOTH);


        return $storeMatchBooks;
    }

    public function postBook()
    {

    }

    private function ping()
    {
        return file_exists($this->path);
    }

    /**
     * @throws \Exception
     */
    private function readDB()
    {
        if (is_array($this->dbData)) {
            return;
        }
        $string = file_get_contents($this->path);
        $json_decode = json_decode($string, true);

        if (\JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception('json not valid');
        }

        $this->dbData = $json_decode;
    }
}
