<?php

namespace App\Repository;

use App\Models\Instance;

class InstanceRepository
{

    function createInstance()
    {
    }

    function deleteInstanceByName($name)
    {
        Instance::query()->where('name', $name)->first()?->delete();
        return true;
    }
}
