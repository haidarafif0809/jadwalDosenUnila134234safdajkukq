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
    	'/login_android','/login_dosen_android','/list_jadwal_dosen','/search_jadwal_dosen','/batal_jadwal_dosen','/presensi_dosen','/tambah_ruangan','/list_ruangan','/update_ruangan','/hapus_ruangan','/cari_ruangan','/login_mahasiswa_android','/list_jadwal_mahasiswa','/presensi_mahasiswa','/search_jadwal_mahasiswa','/jadwal_besok','/search_jadwal_mahasiswa_besok','/search_jadwal_mahasiswa_lusa','/jadwal_lusa','/ubah_password_dosen','/ubah_password_mahasiswa','/versi-absen-dosen', '/versi-absen-mahasiswa','/cek_profile_dosen','/update_profile_dosen','/cek_profil_mahasiswa','/update_profil_mahasiswa'
    ];
}
