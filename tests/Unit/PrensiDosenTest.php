<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Presensi;


class PrensiDosenTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPresensiDosen()
    {

         $response = $this->json('POST', '/presensi_dosen', ['username' => 'dosen1@gmail.com','id_jadwal' => '1', 'id_ruangan' => '1', 'longitude' => '4777', 'latitude' => '435756', 'foto' => 'andaglos.jpg']);



        $response
            ->assertJson([
                'message' => 'Berhasil',
                'value'	=> 1
            ]);
        


    }
}


