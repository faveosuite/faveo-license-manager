<?php

namespace Laravel\Passport\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Laravel\Passport\Client;
<<<<<<< HEAD
<<<<<<< HEAD
use Laravel\Passport\Passport;
=======
>>>>>>> 22c0e54 (table changes)
=======
use Laravel\Passport\Passport;
>>>>>>> f330c64 (optimization in progress)

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
<<<<<<< HEAD
<<<<<<< HEAD
        return $this->ensurePrimaryKeyIsSet([
=======
        return [
>>>>>>> 22c0e54 (table changes)
=======
        return $this->ensurePrimaryKeyIsSet([
>>>>>>> f330c64 (optimization in progress)
            'user_id' => null,
            'name' => $this->faker->company,
            'secret' => Str::random(40),
            'redirect' => $this->faker->url,
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
        ]);
    }

    /**
     * Ensure the primary key is set on the model when using UUIDs.
     *
     * @param  array  $data
     * @return array
     */
    protected function ensurePrimaryKeyIsSet(array $data)
    {
        if (Passport::clientUuids()) {
            $keyName = (new $this->model)->getKeyName();

            $data[$keyName] = (string) Str::orderedUuid();
        }

        return $data;
<<<<<<< HEAD
=======
        ];
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Use as Password Client.
     *
     * @return $this
     */
    public function asPasswordClient()
    {
        return $this->state([
            'personal_access_client' => false,
            'password_client' => true,
        ]);
    }

    /**
     * Use as Client Credentials.
     *
     * @return $this
     */
    public function asClientCredentials()
    {
        return $this->state([
            'personal_access_client' => false,
            'password_client' => false,
        ]);
    }
}
