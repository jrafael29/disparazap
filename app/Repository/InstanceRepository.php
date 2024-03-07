<?php

namespace App\Repository;

use App\Models\Instance;

class InstanceRepository
{

    function createInstance($userId, $description, $phonenumber)
    {
        if (empty($userId) || empty($description) || empty($phonenumber)) return false;

        $instanceName = $userId . '-instance-';

        $instance = Instance::query()->create([
            'user_id' => $userId,
            'name' => $instanceName,
            'description' => $description,
            'phonenumber' => $phonenumber
        ]);

        $instance->name = $instance->name . $instance->id;
        $instance->save();
        return $instance;
    }

    function deleteInstanceByName($name)
    {
        Instance::query()->where('name', $name)->first()?->delete();
        return true;
    }

    function updateInstance($name, $values)
    {
        Instance::query()->where('name', $name)->update($values);
    }
}
