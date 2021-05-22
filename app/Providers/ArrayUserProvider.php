<?php


namespace App\Providers;

use App\CustomModel\CustomUser;
use \Illuminate\Contracts\Auth\UserProvider;
use \Illuminate\Contracts\Auth\Authenticatable;

class ArrayUserProvider implements UserProvider
{
    private $credential_store;

    public function __construct(array $credentials_array)
    {
        $this->credential_store = $credentials_array;
    }

    // IMPORTANT: Also implement this method!
    public function retrieveById($identifier) {
        $username = $identifier;
        $password = $this->credential_store[$username];
        return new CustomUser([
            'email' => $username,
            'password' => $password,
        ]);
    }

    public function retrieveByToken($identifier, $token) { }
    public function updateRememberToken(Authenticatable $user, $token) { }

    public function retrieveByCredentials(array $credentials)
    {
        $username = $credentials['email'];

        // Check if user even exists
        if (!isset($this->credential_store[$username])) {
            return null;
        }

        $password = $this->credential_store[$username];
        return new CustomUser([
            'email' => $username,
            'password' => $password,
            'id' => null,
        ]);
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $b = $credentials['email'] == $user->getAuthIdentifier() && $credentials['password'] == $user->getAuthPassword();
        return $b;
    }
}
