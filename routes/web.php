<?php

use App\Dao\Enums\MenuType;
use App\Dao\Enums\ProcessType;
use App\Dao\Enums\TransactionType;
use App\Dao\Facades\EnvFacades;
use App\Dao\Models\Detail;
use App\Dao\Models\Transaksi;
use App\Exports\ExportRegister;
use App\Http\Controllers\HomeController;
use App\Jobs\downloadReport;
use Buki\AutoRoute\AutoRouteFacade as AutoRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Plugins\Query;

Route::get('console', [HomeController::class, 'console'])->name('console');

Route::get('/', function () {

    return redirect('home');

})->name('one');

Route::get('send-message', function(){
    $ip = request()->ip();
    Log::info($ip);
});

Route::get('/reset', function(){
    $transaksi = Transaksi::whereNotNull(Transaksi::field_pending_in())->get('transaksi_rfid')->pluck('transaksi_rfid')->toArray();

    Transaksi::whereIn(Transaksi::field_rfid(), $transaksi)->update([
        Transaksi::field_pending_in() => null,
        Transaksi::field_pending_out() => null,
        Transaksi::field_pending() => null,
        Transaksi::field_barcode() => null,
        Transaksi::field_delivery() => null,
        Transaksi::field_delivery_at() => null,
        Transaksi::field_delivery_by() => null,
        Transaksi::field_status_bersih() => null,
    ]);


    Detail::whereIn(Detail::field_primary(), $transaksi)->update([
        Detail::field_updated_at() => now()->subDay(3)->format('Y-m-d H:i:s'),
        Detail::field_status_transaction() => TransactionType::Kotor,
        Detail::field_status_process() => ProcessType::Grouping,
    ]);

});

Route::get('/signout', 'App\Http\Controllers\Auth\LoginController@logout')->name('signout');
Route::get('/home', 'App\Http\Controllers\HomeController@index')->middleware(['access'])->name('home');
Route::get('/error-402', 'App\Http\Controllers\HomeController@error402')->middleware(['access'])->name('error-402');
Route::get('/doc', 'App\Http\Controllers\HomeController@doc')->middleware(['access'])->name('doc');

Route::match(['POST', 'GET'], 'change-password', 'App\Http\Controllers\UserController@changePassword', ['name' => 'change-password'])->middleware('auth');
Auth::routes();

try {
    $routes = Query::groups();
} catch (\Throwable $th) {
    $routes = [];
}

if($routes){
    Route::middleware(['auth', 'access', 'auth.timeout'])->group(function () use($routes) {
        Route::prefix('admin')->group(function () use ($routes){
            if ($routes) {
                foreach ($routes as $group) {
                    Route::group(['prefix' => $group->field_primary, 'middleware' => [
                        'auth',
                        'access',
                        'auth.timeout'
                    ]], function () use ($group) {
                        // -- nested group
                        if ($menus = $group->has_menu) {
                            foreach ($menus as $menu) {

                                if($menu->field_type == MenuType::Menu){

                                    Route::group(['prefix' => 'default'], function () use ($menu) {
                                        try {
                                            AutoRoute::auto($menu->field_url, $menu->field_controller, ['name' => $menu->field_primary]);
                                        } catch (\Throwable$th) {
                                            //throw $th;
                                        }
                                    });


                                } elseif($menu->field_type == MenuType::Group){

                                    if ($links = $menu->has_link) {
                                        Route::group(['prefix' => $menu->field_url], function () use ($links) {
                                            foreach ($links as $link) {

                                                try {
                                                    AutoRoute::auto($link->field_url, $link->field_controller, ['name' => $link->field_primary]);
                                                } catch (\Throwable$th) {
                                                    //throw $th;
                                                }

                                            }
                                        });
                                    }
                                }
                            }
                        }
                        // end nested group

                    });
                }
            }
        });
    });
}

Route::post('upload_config', function (Request $request) {

    $file = $request->file('file');
    $field = $request->file('name');
    // $filename = $file->getClientOriginalName();
    $extension = $file->getClientOriginalExtension();
    $name = $field . '.' . $extension;
    $file->storeAs('/public/', $name);

    EnvFacades::setValue($field, $name);

    return $name;

})->name('upload_config');

Route::get('download', function(){
   $table = DB::connection('server')->table('item_linen')->get();
   return $table;
});