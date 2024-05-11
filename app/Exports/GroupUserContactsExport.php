<?php

namespace App\Exports;

use App\Models\UserContactGroup;
use App\Models\UserGroup;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;

class GroupUserContactsExport implements FromCollection
{
    public function __construct(public UserGroup $group)
    {
    }
    public function collection()
    {
        $arrayDeModels = $this->group->userContacts->map(function ($item, $index) {
            // return $item->contact->toArray();
            return [
                'id' => !$index ? '0' : $index,
                'phonenumber' => $item->contact->phonenumber,
                'description' => $item->contact->description
            ];
        });
        return $arrayDeModels;
    }
}
