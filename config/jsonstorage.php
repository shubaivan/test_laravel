<?php

use Illuminate\Support\Str;

return [
    'path' => env(base_path() .'/' . 'STORAGE_FILE_PATH', base_path() .'/bin/storage.json'),
];
