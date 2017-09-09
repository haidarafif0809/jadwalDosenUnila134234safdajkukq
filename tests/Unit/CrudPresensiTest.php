<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Presensi;


class CrudPresensiTest extends TestCase
{
	use DatabaseTransactions;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCrudPresensi()
    {

    	// test insert database-> table presensi
         $presensi = Presensi::create(["id_user" => "6", "id_ruangan" => "2", "id_jadwal" => "1", "longitude" => "105.2162817", "latitude" => "-5.3929288", "foto" => "andaglos.jpg"]);

         // cek apakah data yang di insert ke table presensi berhasil atau gagal
         $this->assertDatabaseHas('presensi',['id_user' => '6', 'id_ruangan' => '2', 'id_jadwal' => '1', 'longitude' => '105.2162817', 'latitude' => '-5.3929288', 'foto' => 'andaglos.jpg']);

         // test update database-> table presensi
         Presensi::find($presensi->id)->update(["id_user" => "7", "id_ruangan" => "3"]);

         // cek apakah update table presensi berhasil atau tidak
         $this->assertDatabaseHas('presensi',['id_user' => '7', 'id_ruangan' => '3']);

         // test delete databse -> table presensi
         Presensi::destroy($presensi->id);

         // cek apakah berhasil data berhasil di hapus
         $this->assertDatabaseMissing('presensi',["id_user" => "7", "id_ruangan" => "3", "id_jadwal" => "1", "longitude" => "105.2162817", "latitude" => "-5.3929288", "foto" => "andaglos.jpg"]);


    }
}
