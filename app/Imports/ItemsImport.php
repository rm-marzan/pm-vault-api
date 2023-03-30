<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Login;
use App\Models\Folder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemsImport implements ToCollection, WithHeadingRow
{
    private $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function collection(Collection $rows)
    {
        foreach($rows as $row){
            Validator::make($row->toArray(), [
                'folder_name' => 'max:50',
                'type' => 'max:10',
                'item_name' => 'required|max:255',
                'notes' => 'nullable',
                'login_url' => 'nullable',
                'login_username' => 'nullable|max:255',
                'login_password' => 'nullable|max:255',
            ])->validate();

            if(!empty($row['folder_name'])){
                $folder = Folder::create([
                    'foldername' => $row['folder_name'] ?? '',
                    'user_id' => $this->userId,
                ]);
            }

            $item = Item::create([
                'user_id' => $this->userId,
                'folder_id' => !empty($folder->id) ? $folder->id : "",
                'name' => $row['item_name'] ?? '',
                'notes' => $row['notes'] ?? '',
                'type' => $row['type'] == 'login' ? 1 : '',
            ]);

            if($item->type == 1){
                $login = Login::create([
                    'item_id' => !empty($item->id) ? $item->id : "",
                    'username' => $row['login_username'] ?? '',
                    'password' => $row['login_password'] ?? '',
                    'urls' => $row['login_url'] ?? '',
                ]);
            }
        }
    }
}
