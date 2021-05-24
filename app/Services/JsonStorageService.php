<?php


namespace App\Services;

use App\Http\Controllers\Api\BookController;
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
    public function getBookByPartialName($bookName)
    {
        $this->readDB();
        $storeMatchBooks = [];
        $matches = array_filter($this->dbData, function ($genreBooks, $genreName) use ($bookName, &$storeMatchBooks) {

            $matches = array_filter($genreBooks, function ($book) use ($bookName) {
                return preg_grep("/$bookName/iu", $book);
            });
            array_map(function ($book) use ($genreName, &$storeMatchBooks) {
                $storeMatchBooks[$genreName] = $book;
            }, $matches);
            return count($matches);
        }, ARRAY_FILTER_USE_BOTH);


        return $storeMatchBooks;
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function postBook(array $data)
    {
        $this->readDB();
        $dbData = $this->dbData;
        $author = $data[BookController::AUTHOR];
        $bookName = $data[BookController::NAME];
        $genre = $data[BookController::GENRE];
        $genreSetOfBooks = $this->setIfNeededNewGenre($genre);
        $matches = array_filter($genreSetOfBooks, function ($book, $key) use (&$genreSetOfBooks, $author, $bookName, $genre) {
            $preg_grep = preg_grep("/\b$bookName\b/iu", $book);
            if ($preg_grep) {
                $genreSetOfBooks[$key][BookController::AUTHOR] = $author;
            }
            return $preg_grep;
        }, ARRAY_FILTER_USE_BOTH);

        if (!$matches) {
            $genreSetOfBooks[] = [
                BookController::NAME => $bookName,
                BookController::AUTHOR => $author
            ];
        }
        $dbData[$genre] = $genreSetOfBooks;
        $this->loadDataInStorage($dbData);

        return $dbData;
    }

    /**
     * @param array $data
     */
    private function loadDataInStorage(array $data)
    {
        $json_encode = json_encode($data);
        file_put_contents($this->path, $json_encode);
        $this->dbData = null;
    }

    /**
     * @param $genre
     * @return mixed
     */
    private function setIfNeededNewGenre($genre)
    {
        $checkGenre = $this->checkGenre($genre);
        if (!$checkGenre) {
            $this->dbData[$genre] = [];
        }

        return $this->dbData[$genre];
    }

    private function checkGenre($genre)
    {
        $genres = array_keys($this->dbData);
        return preg_grep("/\b$genre\b/iu", $genres);
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
