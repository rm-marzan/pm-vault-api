<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemsExport implements FromCollection, WithHeadings
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function collection()
    {
        $items = User::with(['items.organization', 'items.folder', 'items.login'])
        ->find($this->userId)
        ->items
        ->map(function ($item) {
            if($item->type == 2 || $item->type == 3){
                return null;
            }
            return [
                'foldername' => !empty($item->folder->foldername) ? $item->folder->foldername : '',
                'type' => $item->type == 1 ? 'login' : 'note',
                'name' => $item->name,
                'notes' => $item->notes,
                'urls' => !empty($item->login->urls) ? $item->login->urls : '',
                'username' => !empty($item->login->username) ? $item->login->username : '',
                'password' => !empty($item->login->password) ? $item->login->password : '',
            ];
        })->filter();

        $collection = new Collection($items);
        return $collection;
    }

    public function headings(): array
    {
        return [
            'folder_name',
            'type',
            'item_name',
            'notes',
            'login_url',
            'login_username',
            'login_password'
        ];
    }
}