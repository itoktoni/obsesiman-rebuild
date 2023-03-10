<?php

namespace Plugins;

use App\Dao\Enums\EnvType;
use App\Dao\Enums\KontrakType;
use App\Dao\Enums\RoleLevel;
use App\Dao\Enums\TicketStatus;
use App\Dao\Models\Inventaris;
use App\Dao\Models\Location;
use App\Dao\Models\Product;
use App\Dao\Models\SystemGroup;
use App\Dao\Models\SystemLink;
use App\Dao\Models\SystemMenu;
use App\Dao\Models\SystemPermision;
use App\Dao\Models\SystemRole;
use App\Dao\Models\TicketSystem;
use App\Dao\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Query
{
    public static function groups($role = false)
    {
        if (env('APP_ENV') == EnvType::Production) {
            if (Session::has('groups')) {
                $cache = Session::get('groups');
                if ($role && !empty($cache)) {
                    $cache = $cache->where('system_role_code', auth()->user()->role);
                }
                return $cache;
            }
        }

        $groups = [];
        try {
            $groups = SystemGroup::with([
                'has_menu' => function ($query) {
                    $query->orderBy('system_menu_sort', 'DESC');
                },
                'has_menu.has_link' => function ($query) {
                    $query->orderBy('system_link_sort', 'DESC');
                },
            ])
                ->leftJoin('system_group_connection_role', 'system_group_connection_role.system_group_code', 'system_group.system_group_code')
                ->orderBy('system_group_sort', 'DESC')
                ->get();
            Session::put('groups', $groups, 12000);
        } catch (\Throwable$th) {
            //throw $th;
        }

        if ($role) {
            $groups = $groups->where('system_role_code', auth()->user()->role);
        }

        return $groups;
    }

    public static function getMenu($action = false)
    {
        if (env('APP_ENV') == EnvType::Production) {
            if (Session::has('menu')) {
                $cache = Session::get('menu');
                if ($action && !empty($cache)) {
                    $cache = $cache->where('menu_action', $action)->first();
                }
                return $cache;
            }
        }

        $menu = [];
        try {
            $menu = DB::table((new SystemMenu())->getTable())
            ->select([
                DB::raw('COALESCE(system_menu.system_menu_code, system_link.system_link_code) as menu_code'),
                DB::raw('COALESCE(system_menu.system_menu_controller, system_link.system_link_controller) as menu_controller'),
                DB::raw('COALESCE(system_menu.system_menu_action, system_link.system_link_action) as menu_action'),
                DB::raw('COALESCE(system_menu.system_menu_name, system_link.system_link_name) as menu_name'),
                DB::raw('COALESCE(system_menu.system_menu_url, system_link.system_link_url) as menu_url'),
            ])
            ->leftJoin('system_menu_connection_link', 'system_menu.system_menu_code', '=', 'system_menu_connection_link.system_menu_code')
            ->leftJoin((new SystemLink())->getTable(), 'system_menu_connection_link.system_link_code', '=', 'system_link.system_link_code')
            ->get();
        } catch (\Throwable $th) {
            //throw $th;
        }

        if ($action) {
            $menu = $menu->where('menu_code', $action)->first();
        }

        return $menu;
    }

    public static function role()
    {
        if (env('APP_ENV') == EnvType::Production) {
            if (Session::has('role')) {
                return Session::get('role');
            }
        }

        $role = [];
        try {
            $role = SystemRole::get();
            Session::put('role', $role, 1200);
        } catch (\Throwable$th) {
            //throw $th;
        }

        return $role;
    }

    public static function permision()
    {
        if (env('APP_ENV') == EnvType::Production) {
            if (Session::has('permision')) {
                return Session::get('permision');
            }
        }

        $permision = [];
        try {
            $permision = SystemPermision::query()->get();
            Session::put('permision', $permision, 1200);
        } catch (\Throwable$th) {
            //throw $th;
        }

        return $permision;
    }

    public static function upsert($model, $where, $data)
    {
        $batch = $model->where($where)->first();
        if ($batch) {
            $batch->update($data);
        } else {
            $model->create($data);
        }
    }

    public static function autoNumber($tablename, $fieldid, $prefix = 'AUTO', $codelength = 15)
    {
        $db = DB::table($tablename);
        $db->select(DB::raw('max(' . $fieldid . ') as maxcode'));
        $db->where($fieldid, "like", "$prefix%");

        $ambil = $db->first();
        $data = $ambil->maxcode;

        if ($db->count() > 0) {
            $code = substr($data, strlen($prefix));
            $countcode = ($code) + 1;
        } else {
            $countcode = 1;
        }
        $newcode = $prefix . str_pad($countcode, $codelength - strlen($prefix), "0", STR_PAD_LEFT);
        return $newcode;
    }

    public static function getInventaris()
    {
        $product = Inventaris::with(['has_name', 'has_location', 'has_type', 'has_brand', 'has_instansi'])
        ->get()
            ->mapWithKeys(function ($item) {
                $name = $item->field_name;

                if($group = $item->has_name ?? false){
                    $name = $name.' - '.$group->field_name;
                }

                if($type = $item->has_type){
                    $type_name = $type->field_name ?? '';
                    $name = $name . ' ' . $type_name;
                }

                if($location = $item->has_location){
                    $location_name = $location->field_name ?? '';
                    $name = $name . ' - ' . $location_name;

                    if($instansi = $item->has_location->has_instansi->first()){
                        $instansi_name = $instansi->field_name ?? '';
                        $name = $name . ' ' . $instansi_name;
                    }
                }

                // if($building = $item->has_location->has_building ?? false){
                //     $building_name = $building->field_name ?? '';
                //     $name = $name . ' - ' . $building_name;
                // }

                $id = $item->field_code ?? '' . '';
                return [$id => $name];
            });

        return $product;
    }

    public static function getLocation()
    {
        $location = Location::with(['has_building', 'has_floor'])
        ->get()
            ->mapWithKeys(function ($item) {
                $name = $item->field_name;
                if($item->has_building){
                    $name = $name.' - '.$item->has_building->field_name;
                }
                if($item->has_floor){
                    $name = $name.' - '.$item->has_floor->field_name;
                }
                $id = $item->field_primary . '';
                return [$id => $name];
            });

        return $location;
    }

    public static function getUserByRole($role)
    {
        $data = [];
        $user = User::select(User::field_primary(), User::field_name())
        ->where(User::field_type(), $role)
        ->get();
        if($user){
            $data = $user->pluck(User::field_name(), User::field_primary());
        }
        return $data;
    }

    public static function getUserWhatsapp(){
        return User::select(User::field_primary(), User::field_name())
        ->whereNotNull(User::field_phone())->get()->pluck(User::field_name(), User::field_primary());
    }

    public static function getTotalTicket()
    {
        return TicketSystem::select(TicketSystem::field_primary())->count();
    }

    public static function getTotalProcessTicket($percent = false)
    {
        $process = TicketSystem::select(TicketSystem::field_primary())
            ->where(TicketSystem::field_status(), '!=', TicketStatus::Open)
            ->where(TicketSystem::field_status(), '!=', TicketStatus::Finish)
            ->count();
        if ($process > 0 && $percent) {
            return ($process / self::getTotalTicket()) * 100;
        }

        return $process;
    }

    public static function getTotalCloseTicket($percent = false)
    {
        $close = TicketSystem::select(TicketSystem::field_primary())->where(TicketSystem::field_status(), TicketStatus::Finish)->count();
        if ($close > 0 && $percent) {
            return ($close / self::getTotalTicket()) * 100;
        }

        return $close;
    }

    public static function getTotalOpenTicket($percent = false)
    {
        $open = TicketSystem::select(TicketSystem::field_primary())->where(TicketSystem::field_status(), TicketStatus::Open)->count();
        if ($open > 0 && $percent) {
            return ($open / self::getTotalTicket()) * 100;
        }

        return $open;
    }

    public static function getImplementor($type, $model)
    {
        if ($type == KontrakType::Kontrak) {
            return $model->has_vendor->field_name ?? '';
        } else {
            return $model->has_implementor->field_name ?? '';
        }
    }

    public static function getTeknisi($data){

        $teknisi = [];
        if (env('APP_ENV') == EnvType::Production) {
            if (Session::has('teknisi')) {
                $teknisi = Session::get('teknisi');
            }
            else{
                $teknisi = self::getUserByRole(RoleLevel::Teknisi);
                Session::put('teknisi', $teknisi, 1200);
            }
        }

        $data_teknisi = '';
        if($data){
            foreach($data as $user){
                if(isset($teknisi[$user])){
                    $data_teknisi = $data_teknisi.', '.$teknisi[$user];
                }
            }
        }

        return ltrim($data_teknisi, ',');
    }
}
