<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Stevebauman\Location\Facades\Location;

use App\Models\ConnectInstance;
use App\Http\Resources\ConnectInstanceResource;

use App\Models\User;
use App\Http\Resources\UserResource;

use App\Models\Permission;
use App\Http\Resources\PermissionResource;

use App\Models\ProductCategory;
use App\Http\Resources\ProductCategoryResource;

use App\Models\Store;

class CoreController extends Controller
{
    public function state(Request $request)
    {
        if (!File::exists( public_path('storage') )){
            File::link( storage_path('app/public'), public_path('storage') );
        }
        
        $response = [
            'auth_user' => null,
            'connect_instance' => null,
            'products_structured_collection' => ( new ProductController )->structured_collection()->original,
            'datalists_collection' => ( new CoreController )->datalists()->original,
            //'stores_resource_collection' => ( new StoreController )->index()->response()->getData(true),
            'stores_home_list' => ( new StoreController )->home_collection()->original,
        ];

        $connect_instance_data = $request->connect_instance;

        if ($connect_instance_data){
            
            $request_location = $request->ip();
            if ($position = Location::get()) $request_location = $position;

            $connect_instance_data['request_location'] = $request_location;
            
            // Deal with ConnectInstance
            if ( $connect_instance_data["id"] && ConnectInstance::where('id', $connect_instance_data["id"] )->exists() && ConnectInstance::find($connect_instance_data["id"])['status'] !== "ended" ){
                $connect_instance_data['last_active_datetime'] = now()->toDateTimeString();
                $response['connect_instance'] = ( new ConnectInstanceController )->update( new Request( array_filter( $connect_instance_data) ), $connect_instance_data["id"] );

                // Deal with User
                $auth_user = auth('api')->user();
                if ($auth_user){
                    $response['auth_user'] = new UserResource( $auth_user );
                    $response['products_structured_collection'] = ( new ProductController )->structured_collection($auth_user->id)->original;
                }
            } else {    
                $response['connect_instance'] = ( new ConnectInstanceController )->store( new Request( array_filter( $connect_instance_data) ) );
            }     
        }
        return response()->json($response);
    }

    public function datalists()
    {
        $wilayas = [
            [ 'id'=> 1, 'name'=> 'Adrar', 'tier'=> 2 ],
            [ 'id'=> 2, 'name'=> 'Chlef', 'tier'=> 1 ],
            [ 'id'=> 3, 'name'=> 'Laghouat', 'tier'=> 3 ],
            [ 'id'=> 4, 'name'=> 'Oum el-Bouaghi', 'tier'=> 3 ],
            [ 'id'=> 5, 'name'=> 'Batna', 'tier'=> 1 ],
            [ 'id'=> 6, 'name'=> 'Béjaïa', 'tier'=> 1 ],
            [ 'id'=> 7, 'name'=> 'Biskra', 'tier'=> 2 ],
            [ 'id'=> 8, 'name'=> 'Béchar', 'tier'=> 3 ],
            [ 'id'=> 9, 'name'=> 'Blida', 'tier'=> 1 ],
            [ 'id'=> 10, 'name'=> 'Bouïra', 'tier'=> 2 ],
            [ 'id'=> 11, 'name'=> 'Tamanghasset', 'tier'=> 3 ],
            [ 'id'=> 12, 'name'=> 'Tébessa', 'tier'=> 3 ],
            [ 'id'=> 13, 'name'=> 'Tlemcen', 'tier'=> 1 ],
            [ 'id'=> 14, 'name'=> 'Tairet', 'tier'=> 3 ],
            [ 'id'=> 15, 'name'=> 'Tizi Ouzou', 'tier'=> 1 ],
            [ 'id'=> 16, 'name'=> 'Alger', 'tier'=> 1 ],
            [ 'id'=> 17, 'name'=> 'Djelfa', 'tier'=> 2 ],
            [ 'id'=> 18, 'name'=> 'Jijel', 'tier'=> 2 ],
            [ 'id'=> 19, 'name'=> 'Sétif', 'tier'=> 1 ],
            [ 'id'=> 20, 'name'=> 'Saïda', 'tier'=> 3 ],
            [ 'id'=> 21, 'name'=> 'Skikda', 'tier'=> 1 ],
            [ 'id'=> 22, 'name'=> 'Sidi Bel Abbès', 'tier'=> 1 ],
            [ 'id'=> 23, 'name'=> 'Annaba', 'tier'=> 1 ],
            [ 'id'=> 24, 'name'=> 'Guelma', 'tier'=> 2 ],
            [ 'id'=> 25, 'name'=> 'Constantine', 'tier'=> 1 ],
            [ 'id'=> 26, 'name'=> 'Médéa', 'tier'=> 3 ],
            [ 'id'=> 27, 'name'=> 'Mostaganem', 'tier'=> 1 ],
            [ 'id'=> 28, 'name'=> 'M\'sila', 'tier'=> 3 ],
            [ 'id'=> 29, 'name'=> 'Mascra', 'tier'=> 3 ],
            [ 'id'=> 30, 'name'=> 'Ouargla', 'tier'=> 3 ],
            [ 'id'=> 31, 'name'=> 'Oran', 'tier'=> 1 ],
            [ 'id'=> 32, 'name'=> 'El Bayadh', 'tier'=> 3 ],
            [ 'id'=> 33, 'name'=> 'Illizi', 'tier'=> 3 ],
            [ 'id'=> 34, 'name'=> 'Bordj Bou Arréridj', 'tier'=> 3 ],
            [ 'id'=> 35, 'name'=> 'Boumerdès', 'tier'=> 1 ],
            [ 'id'=> 36, 'name'=> 'El Taref', 'tier'=> 3 ],
            [ 'id'=> 37, 'name'=> 'Tindouf', 'tier'=> 3 ],
            [ 'id'=> 38, 'name'=> 'Tissemslit', 'tier'=> 3 ],
            [ 'id'=> 39, 'name'=> 'El Oued', 'tier'=> 3 ],
            [ 'id'=> 40, 'name'=> 'Khenchela', 'tier'=> 3 ],
            [ 'id'=> 41, 'name'=> 'Souk Ahras', 'tier'=> 3 ],
            [ 'id'=> 42, 'name'=> 'Tipaza', 'tier'=> 2 ],
            [ 'id'=> 43, 'name'=> 'Mila', 'tier'=> 3 ],
            [ 'id'=> 44, 'name'=> 'Aïn Defla', 'tier'=> 3 ],
            [ 'id'=> 45, 'name'=> 'Naâma', 'tier'=> 3 ],
            [ 'id'=> 46, 'name'=> 'Aïn Témouchent', 'tier'=> 3 ],
            [ 'id'=> 47, 'name'=> 'Ghardaïa', 'tier'=> 3 ],
            [ 'id'=> 48, 'name'=> 'Relizane', 'tier'=> 3 ],
        ];
        $response = [
            'wilayas' => $wilayas,
            'countries' => [],
            'permissions' => PermissionResource::collection( Permission::all() ),
            'product_categories' => ProductCategoryResource::collection( ProductCategory::orderBy('name','ASC')->get() ),
        ];
        return response()->json($response);
    }

    public function check(Request $request)
    {
        $check_type = $request->check_type;
        $resource_name = $request->resource_name;
        $test_value = $request->test_value;

        switch ($resource_name) {
            case 'email':
                $success = true;
                $found = User::where('email', $test_value )->exists();
                $message = ($found) ? "Record found" : "Record not found";
                break;

            case 'username':
                $success = true;
                $found = User::where('username', $test_value )->exists() || Store::where('username', $test_value )->exists();
                $message = ($found) ? "Record found" : "Record not found";
                break;
            
            default:
                $success = false;
                $found = null;
                $message = "Invalid query record";
                break;
        }

        $response = [
            "success" => $success,
            "message" => $message,
            "found" => $found,
            "record"    => null,
        ];
        return response()->json($response);
    }
}
