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

    public static function data($data = null, $additional = [])
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::data;
        $log['message'] = 'Data berhasil diambil';
        $log['data'] = $data;
        Log::info(self::data, $data);
        return self::sentJson($log, 200, $additional);
    }

    public static function single($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::single;
        $log['message'] = 'Data di dapat';
        $log['data'] = $data;
        Log::info(self::single, $data);
        return self::sentJson($log);
    }

    public static function create($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::create;
        $log['message'] = 'Data berhasil di buat';
        $log['data'] = $data;
        Log::info(self::create, request()->all());
        return self::sentJson($log);
    }

    public static function token($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::token;
        $log['message'] = 'Data token '.self::token;
        $log['data'] = $data;
        Log::info(self::token, $data);
        return self::sentJson($log);
    }

    public static function update($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::update;
        $log['message'] = 'Data berhasil di ubah';
        $log['data'] = $data;
        Log::info(self::update, $data);
        return self::sentJson($log);
    }

    public static function delete($data = null)
    {
        $log['status'] = true;
        $log['code'] = 200;
        $log['name'] = self::delete;
        $log['message'] = 'Data berhasil di hapus';
        $log['data'] = $data;
        Log::warning(self::delete, $log);
        return self::sentJson($log, 204);
    }

    public static function error($data = null, $message = null)
    {
        $log['status'] = false;
        $log['code'] = 400;
        $log['name'] = self::error;
        $log['message'] = $message ?? $data;
        $log['data'] = [$data];
        Log::error(self::error, $log);
        return self::sentJson($log, 400);
    }

    public static function validation($message = null, $data = null)
    {
        $log['status'] = false;
        $log['code'] = 422;
        $log['name'] = self::validation;
        $log['message'] = $message;
        $log['data'] = 'Validation Error';
        Log::warning(self::validation, $log);
        return self::sentJson($log, 422);
    }

    public static function notFound($data = null, $url = null)
    {
        $log['status'] = false;
        $log['code'] = 404;
        $log['name'] = self::error;
        $log['message'] = 'Url tidak ditemukan';
        $log['data'] = $data;
        Log::warning(self::error, $log);
        return self::sentJson($log, 404);
    }

    public static function sentJson($data, $status = 200, $additional = []){
        if ($additional) {
            $data = array_merge($data, $additional);
        }

        if (request()->wantsJson()) {

            if ($status >= 400) {
                $data['data'] = [];
            }
            return response()->json($data, 200);
        }

        return $data;
    }
}
