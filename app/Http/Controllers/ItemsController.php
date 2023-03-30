<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Item;
use App\Models\Login;
use App\Models\Card;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Exports\ItemsExport;
use App\Imports\ItemsImport;
use Maatwebsite\Excel\Facades\Excel;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($userId)
    {
        if ($userId !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $items = User::with(['items.organization', 'items.folder', 'items.login', 'items.card', 'items.identity'])->find($userId);
        return response()->json($items);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreItemRequest $request)
    {
        $input = $request->only(['user_id', 'organization_id', 'folder_id', 'name', 'type', 'notes', 'favorite']);
        $item = Item::create($input);

        if($item->id){
            switch ($item->type) {
                case 1: // Login
                    $loginInput = $request->only(['username', 'password', 'urls']);
                    $loginInput['item_id'] = $item->id;
                    $login = Login::create($loginInput);
                    break;
                case 2: // Card
                    $cardInput = $request->only(['cardholder_name', 'brand', 'number', 'exp_month', 'exp_year', 'cvv']);
                    $cardInput['item_id'] = $item->id;
                    $card = Card::create($cardInput);
                    break;
                case 3: // Identity
                    $identityInput = $request->only(['title', 'username', 'first_name', 'middle_name', 'last_name', 'address', 'phone', 'email', 'security_number', 'license_number']);
                    $identityInput['item_id'] = $item->id;
                    $identity = Identity::create($identityInput);
                    break;
                default:
                    break;
            }
        }

        $response = [
            'success' => true,
            'data' => $item->load(['organization', 'folder', 'login', 'card', 'identity']),
            'message' => 'Item added Successfully'
        ];

        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::with('login','card','identity')->find($id);
        if(!$item){
            $response = [
                'success' => false,
                'message' => 'Item not found'
            ];
            return response()->json($response, 404);
        }
        if ($item->user_id !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateItemRequest  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateItemRequest $request, $id)
    {
        $item = Item::find($id);
        if(!$item){
            $response = [
                'success' => false,
                'message' => 'Item not found'
            ];
            return response()->json($response, 404);
        }
        if ($item->user_id !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $input = $request->only(['organization_id', 'folder_id', 'name', 'notes', 'favorite']);
        $item->update($input);
    
        if($item->id){
            switch ($item->type) {
                case 1: // Login
                    $loginInput = $request->only(['username', 'password', 'urls']);
                    $login = $item->login;
                    if(!$login){
                        $login = new Login();
                        $login->item_id = $item->id;
                    }
                    $login->fill($loginInput);
                    $login->save();
                    break;
                case 2: // Card
                    $cardInput = $request->only(['cardholder_name', 'brand', 'number', 'exp_month', 'exp_year', 'cvv']);
                    $card = $item->card;
                    if(!$card){
                        $card = new Card();
                        $card->item_id = $item->id;
                    }
                    $card->fill($cardInput);
                    $card->save();
                    break;
                case 3: // Identity
                    $identityInput = $request->only(['title', 'username', 'first_name', 'middle_name', 'last_name', 'address', 'phone', 'email', 'security_number', 'license_number']);
                    $identity = $item->identity;
                    if(!$identity){
                        $identity = new Identity();
                        $identity->item_id = $item->id;
                    }
                    $identity->fill($identityInput);
                    $identity->save();
                    break;
                default:
                    break;
            }
        }
    
        $response = [
            'success' => true,
            'data' => $item->load(['organization', 'folder', 'login', 'card', 'identity']),
            'message' => 'Item updated Successfully'
        ];
    
        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::withTrashed()->find($id);

        if(!$item){
            $response = [
                'success' => false,
                'message' => 'Item not found'
            ];
            return response()->json($response, 404);
        }
        else if ($item->user_id !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        else if($item->deleted_at == null){
            $item->delete();
        }
        else{
            $item->forceDelete();
        }

        $response = [
            'success' => true,
            'data' => $item,
            'message' => 'Item deleted successfully'
        ];

        return response()->json($response, 200);
    }

    public function destroyItems(Request $request, $userId)
    {
        if($userId !== Auth::user()->id){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $success = true;
        $items = [];
        $isSoftDelete = false;
        $msg = "Items deleted successfully";
        $itemIds = $request->input('selectedItems');
        foreach($itemIds as $id){
            $item = Item::withTrashed()
            ->find($id)
            ->with(['organization', 'folder', 'login', 'card', 'identity'])
            ->first();

            if($item){
                if($item->deleted_at == null){
                    $item->delete();
                    $isSoftDelete = true;
                    $items[] = $item;
                }
                else{
                    $item->forceDelete();
                }
            }
            else{
                $success = false;
                $msg = "Item not found";
            }
        }
        $response = [
            'success' => $success,
            'data' => $isSoftDelete ? $items : $itemIds,
            'message' => $msg
        ];

        return response()->json($response, 200);
    }

    public function trashedItems($userId)
    {
        if($userId !== Auth::user()->id){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $items = User::with(['items.login', 'items.card', 'items.identity'])
        ->find($userId)
        ->items()
        ->onlyTrashed()
        ->get();

        return response()->json($items);
    }

    public function itemRestore($id)
    {
        $item = Item::onlyTrashed()->whereId($id)->restore();

        if(!$item){
            $response = [
                'success' => false,
                'message' => 'Item not found'
            ];
            return response()->json($response, 404);
        }
        $restoredItem = Item::withTrashed()->whereId($id)
                        ->with(['organization', 'folder', 'login', 'card', 'identity'])
                        ->first();
        $response = [
            'success' => true,
            'data' => $restoredItem,
            'message' => "Item Restored Successfully"
        ];
        return response()->json($response, 200);
    }

    public function moveItemsToFolder(Request $request, $folderId)
    {
        $items = [];
        $success = true;
        $folderId = $folderId;
        $msg = "Items Moved Successfully";
        $itemIds = $request->input('selectedItems');
        foreach($itemIds as $id){
            $ids[] = $id;
            $item = Item::find($id);

            if($item){
                $item->folder_id = $folderId;
                $item->save();
                $item->load(['organization', 'folder', 'login', 'card', 'identity']);
                $items[] = $item;
            }
            else{
                $success = false;
                $msg = "Item not found";
            }
        }
        $response = [
            'success' => $success,
            'data' => $items,
            'message' => $msg
        ];

        return response()->json($response, 200);
    }

    public function moveItemsToOrg(Request $request, $orgId)
    {
        $items = [];
        $success = true;
        $orgId = $orgId;
        $msg = "Items Moved Successfully";
        $itemIds = $request->input('selectedItems');
        foreach($itemIds as $id){
            $ids[] = $id;
            $item = Item::find($id);

            if($item){
                $item->organization_id = $orgId;
                $item->save();
                $item->load(['organization', 'folder', 'login', 'card', 'identity']);
                $items[] = $item;
            }
            else{
                $success = false;
                $msg = "Item not found";
            }
        }
        $response = [
            'success' => $success,
            'data' => $items,
            'message' => $msg
        ];

        return response()->json($response, 200);
    }

    public function export($userId)
    {
        if($userId !== Auth::user()->id){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return Excel::download(new ItemsExport($userId), 'items.csv');
    }

    public function import(Request $request, $userId)
    {
        if($userId !== Auth::user()->id){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $file = $request->file('file');
        $import = new ItemsImport($userId);
        Excel::import($import, $file);

        $response = [
            'success' => true,
            'message' => 'Items imported successfully',
        ];

        return response()->json($response, 200);
    }
}
