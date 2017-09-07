<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    	'/login_android',"/tambah_ruangan",'list_ruangan','/login_dosen_android','/list_jadwal_dosen','/search_jadwal_dosen','/batal_jadwal_dosen','/presensi_dosen'
    ];
}
