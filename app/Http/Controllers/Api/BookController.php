<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JsonStorageService;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * @var JsonStorageService
     */
    private $jsonStorageService;

    /**
     * BookController constructor.
     * @param JsonStorageService $jsonStorageService
     */
    public function __construct(JsonStorageService $jsonStorageService)
    {
        $this->jsonStorageService = $jsonStorageService;
    }

    /**
     * @param $bookName
     * @return array
     * @throws \Exception
     */
    public function getBySlug($bookName)
    {
        $validator = Validator::make(
            ['name' => $bookName],
            ['name' => 'string|min:2'],
            $messages = [
                'min' => ':attribute not valid, should be great then 1',
            ]
        );

        $messageBag = $validator->errors();
        $count = $messageBag->count();
        if ($count) {
            return $messageBag->getMessages();
        }

        return $this->jsonStorageService->getBookByNameSlug($bookName);
    }
}
