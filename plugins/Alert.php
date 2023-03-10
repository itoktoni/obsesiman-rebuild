<?php

namespace Plugins;

use Coderello\SharedData\Facades\SharedData;

class Alert
{
    const create = 'buat';
    const update = 'simpan';
    const delete = 'hapus';
    const failed = 'gagal';
    const error = 'error';
    const success = 'success';
    const warning = 'warning';
    const danger = 'danger';
    const primary = 'primary';

    public static function create($data = null)
    {
        session()->put(self::success, $data ?? 'Data berhasil di ' . self::create . ' !');
    }

    public static function update($data = null)
    {
        session()->put(self::success, $data ?? 'Data berhasil di ' . self::update . ' !');
    }

    public static function delete($data = null)
    {
        session()->put(self::success, $data ?? 'Data berhasil di ' . self::delete . ' !');
    }

    public static function error($data = null)
    {
        session()->put(self::error, env('APP_ENV', 'local') == 'production' ? 'Data Error !' : $data . ' !');
    }
}
