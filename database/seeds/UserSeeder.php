<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
    // Membuat role admin
    $adminRole = new Role();
    $adminRole->name = "admin";
    $adminRole->display_name = "Admin";
    $adminRole->save();

    // Membuat role dosen
    $dosenRole = new Role();
    $dosenRole->name = "dosen";
    $dosenRole->display_name = "Dosen";
    $dosenRole->save();

    // Membuat role mahasiswa
    $mahasiswaRole = new Role();
    $mahasiswaRole->name = "mahasiswa";
    $mahasiswaRole->display_name = "Mahasiswa";
    $mahasiswaRole->save();

    // Membuat role pimpinan
    $pimpinanRole = new Role();
    $pimpinanRole->name = "pimpinan";
    $pimpinanRole->display_name = "Pimpinan";
    $pimpinanRole->save();

    // Membuat sample admin
    $admin = new User();
    $admin->name = 'Admin Larapus';
    $admin->email = 'admin@gmail.com';
    $admin->password = bcrypt('rahasia'); 
    $admin->no_hp = '-';
    $admin->alamat = '-';
    $admin->save();
    $admin->attachRole($adminRole);

    // Membuat sample dosen
    $dosen = new User();
    $dosen->name = "Sample Dosen";
    $dosen->email = 'dosen@gmail.com';
    $dosen->password = bcrypt('rahasia'); 
    $dosen->no_hp = '-';
    $dosen->alamat = '-';
    $dosen->save();
    $dosen->attachRole($dosenRole);

    // Membuat sample mahasiswa
    $mahasiswa = new User();
    $mahasiswa->name = "Sample Mahasiswa";
    $mahasiswa->email = 'mahasiswa@gmail.com';
    $mahasiswa->password = bcrypt('rahasia'); 
    $mahasiswa->no_hp = '-';
    $mahasiswa->alamat = '-';
    $mahasiswa->save();
    $mahasiswa->attachRole($mahasiswaRole);

    // Membuat sample pimpinan
    $pimpinan = new User();
    $pimpinan->name = "Sample Pimpinan";
    $pimpinan->email = 'pimpinan@gmail.com';
    $pimpinan->password = bcrypt('rahasia'); 
    $pimpinan->no_hp = '-';
    $pimpinan->alamat = '-';
    $pimpinan->save();
    $pimpinan->attachRole($pimpinanRole);
    }
}
