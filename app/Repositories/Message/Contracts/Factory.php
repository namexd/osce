<?php

namespace App\Repositories\Message\Contracts;

interface Factory
{
    /**
     * Get a message instance by name.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function message($name = null);
}