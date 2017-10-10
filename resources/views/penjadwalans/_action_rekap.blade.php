@role('admin') 
<div class="dropdown">
    <button class="btn btn-warning dropdown-toggle" type="button" data-toggle="dropdown">Rekap
    <span class="caret"></span></button>
    <ul class="dropdown-menu">
      <li><a href="{{ route('penjadwalans.rekap_kehadiran_dosen', [$id_jadwal, $tipe_jadwal]) }}" class="btn-dosen">Dosen</a></li>
      <li><a href="{{ route('penjadwalans.rekap_kehadiran_mahasiswa',[$id_jadwal, $id_block, $tipe_jadwal]) }}" class="btn-mahasiswa">Mahasiswa</a></li>
    </ul>
</div> 
@endrole	