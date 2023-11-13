<?php

namespace Plugins;

use Illuminate\Support\Facades\Log;

class Notes
{
    const create = 'Create';
    const update = 'Update';
    const delete = 'Delete';
    const validation = 'Validation';
    const error = 'Error';
    const data = 'List';
    const single = 'Data';
    const token = 'Token';

    public static function data($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::data;
        $log['message'] = 'Data berhasil diambil';
        $log['data'] = $data;
        Log::info($log);
        return response()->json($log, 200);
    }

    public static function single($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::single;
        $log['message'] = 'Data di dapat';
        $log['data'] = $data;
        Log::info($log);
        return response()->json($log, 200);;
    }

    public static function create($data = null)
    {
        $log['status'] = true;
        $log['code'] = 201;
        $log['name'] = self::create;
        $log['message'] = 'Data berhasil di buat';
        $log['data'] = $data;
        Log::info($log);
        return response()->json($log, 201);
    }

    public static function token($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::token;
        $log['message'] = 'Data token '.self::token;
        $log['data'] = $data;
        Log::info($log);
        return response()->json($log, 200);;
    }

    public static function update($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::update;
        $log['message'] = 'Data berhasil di ubah';
        $log['data'] = $data;
        // if(request()->wantsJson()){
        //     $log['data'] = is_array($data) ? $data : $data->toArray();
        // }
        Log::info($log);
        return response()->json($log, 200);
    }

    public static function delete($data = null)
    {
        $log['status'] = true;
        $log['code'] = 204;
        $log['name'] = self::delete;
        $log['message'] = 'Data berhasil di hapus';
        $log['data'] = $data;
        Log::warning($log);
        return response()->json($log, 204);
    }

    public static function error($data = null, $message = null)
    {
        $log['status'] = false;
        $log['code'] = 400;
        $log['name'] = self::error;
        $log['message'] = $message ?? 'Data '.self::error;
        $log['data'] = $data;
        Log::error($log);
        return response()->json($log, 400);
    }

    public static function validation($message = null, $data = null)
    {
        $log['status'] = false;
        $log['code'] = 422;
        $log['name'] = self::error;
        $log['message'] = $message;
        $log['data'] = 'Validation Error';
        Log::warning($log);
        return response()->json($log, 422);
    }

    public static function notFound($data = null, $url = null)
    {
        $log['status'] = false;
        $log['code'] = 404;
        $log['name'] = self::error;
        $log['message'] = 'Url tidak ditemukan';
        $log['data'] = $data;
        Log::warning($log);
        return response()->json($log, 404);;
    }
}
