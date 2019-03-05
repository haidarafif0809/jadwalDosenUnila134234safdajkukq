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

    // Membuat sample pj dosen
    $pjDosenRole = new Role();
    $pjDosenRole->name = "pj_dosen";
    $pjDosenRole->display_name = "PJ Dosen";
    $pjDosenRole->save();  
    // Membuat sample perekap
    $perekapRole = new Role();
    $perekapRole->name = "perekap";
    $perekapRole->display_name = "Perekap";
    $perekapRole->save(); 

    // Membuat sample admin
    $admin = new User();
    $admin->name = 'Admin Larapus';
    $admin->email = 'admin@gmail.com';
    $admin->password = bcrypt('rahasia'); 
    $admin->no_hp = '-';
    $admin->alamat = '-';
    $admin->status = '1';
    $admin->id_role = '1';
    $admin->save();
    $admin->attachRole($adminRole);

    // Membuat sample perekap
    $perekap = new User();
    $perekap->name = "Perekap";
    $perekap->email = 'perekap@gmail.com';
    $perekap->password = bcrypt('rahasia'); 
    $perekap->no_hp = '-';
    $perekap->alamat = '-';
    $perekap->id_role = '6';
    $perekap->save();
    $perekap->attachRole($perekapRole);

    // Membuat sample pj dosen
    $pj_dosen = new User();
    $pj_dosen->name = "PJ Dosen 1";
    $pj_dosen->email = 'pjdosen1@gmail.com';
    $pj_dosen->password = bcrypt('rahasia'); 
    $pj_dosen->no_hp = '-';
    $pj_dosen->alamat = '-';
    $pj_dosen->id_role = '5';
    $pj_dosen->save();
    $pj_dosen->attachRole($pjDosenRole);

    $pj_dosen = new User();
    $pj_dosen->name = "PJ Dosen 2";
    $pj_dosen->email = 'pjdosen2@gmail.com';
    $pj_dosen->password = bcrypt('rahasia'); 
    $pj_dosen->no_hp = '-';
    $pj_dosen->alamat = '-';
    $pj_dosen->id_role = '5';
    $pj_dosen->save();
    $pj_dosen->attachRole($pjDosenRole);

    $pj_dosen = new User();
    $pj_dosen->name = "PJ Dosen 3";
    $pj_dosen->email = 'pjdosen3@gmail.com';
    $pj_dosen->password = bcrypt('rahasia'); 
    $pj_dosen->no_hp = '-';
    $pj_dosen->alamat = '-';
    $pj_dosen->id_role = '5';
    $pj_dosen->save();
    $pj_dosen->attachRole($pjDosenRole);

    // Membuat sample dosen
    $dosen = new User();
    $dosen->name = "Dosen 1";
    $dosen->email = 'dosen1@gmail.com';
    $dosen->password = bcrypt('rahasia'); 
    $dosen->no_hp = '-';
    $dosen->alamat = '-';
    $dosen->id_role = '2';
    $dosen->save();
    $dosen->attachRole($dosenRole);

    // Membuat sample dosen
    $dosen = new User();
    $dosen->name = "Dosen 2";
    $dosen->email = 'dosen2@gmail.com';
    $dosen->password = bcrypt('rahasia'); 
    $dosen->no_hp = '-';
    $dosen->alamat = '-';
    $dosen->id_role = '2';
    $dosen->save();
    $dosen->attachRole($dosenRole);

    // Membuat sample dosen
    $dosen = new User();
    $dosen->name = "Dosen 3";
    $dosen->email = 'dosen3@gmail.com';
    $dosen->password = bcrypt('rahasia'); 
    $dosen->no_hp = '-';
    $dosen->alamat = '-';
    $dosen->id_role = '2';
    $dosen->save();
    $dosen->attachRole($dosenRole);

    // Membuat sample mahasiswa
    $mahasiswa = new User();
    $mahasiswa->name = "Sample Mahasiswa";
    $mahasiswa->email = 'mahasiswa@gmail.com';
    $mahasiswa->password = bcrypt('rahasia'); 
    $mahasiswa->no_hp = '-';
    $mahasiswa->alamat = '-';
    $mahasiswa->id_angkatan = 1;
    $mahasiswa->id_role = '3';
    $mahasiswa->save();
    $mahasiswa->attachRole($mahasiswaRole);

    // Membuat sample pimpinan
    $pimpinan = new User();
    $pimpinan->name = "Sample Pimpinan";
    $pimpinan->email = 'pimpinan@gmail.com';
    $pimpinan->password = bcrypt('rahasia'); 
    $pimpinan->no_hp = '-';
    $pimpinan->alamat = '-';
    $pimpinan->id_role = '4';
    $pimpinan->save();
    $pimpinan->attachRole($pimpinanRole);
    }
}
