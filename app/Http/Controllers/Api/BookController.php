<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JsonStorageService;

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

    public function getBySlug($bookName)
    {
        return $this->jsonStorageService->getBookByNameSlug($bookName);
    }
}
