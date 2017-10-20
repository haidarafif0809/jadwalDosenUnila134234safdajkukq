	<table>

        <thead>

	        <tr><td>Tipe Jadwal</td> : <td>{{$data_jadwal->tipe_jadwal}} </td></tr>

	        @if($data_jadwal->tipe_jadwal == 'CSL' || $data_jadwal->tipe_jadwal == 'TUTORIAL')
	           <tr><td>Mata Kuliah / Materi</td> : <td>{{$data_jadwal->materi->nama_materi}} </td></tr>
	        @else 
	           <tr><td>Mata Kuliah / Materi</td> : <td>{{$data_jadwal->mata_kuliah->nama_mata_kuliah}} </td></tr>
	        @endif 
	        
	        <tr><td>Block</td> : <td>{{$data_jadwal->block->nama_block}} </td></tr>
	        <tr><td>Ruangan</td> : <td>{{$data_jadwal->ruangan->nama_ruangan}} </td></tr>
	        <tr><td>Tanggal</td> : <td>{{date('d-m-Y', strtotime($data_jadwal->tanggal))}} </td></tr>
	        <tr><td>Waktu</td> : <td>{{$data_jadwal->waktu_mulai}} s/d {{$data_jadwal->waktu_selesai}} </td></tr>
     
    		<tr></tr>

        	<tr>
				<th> NPM </th>
				<th> Nama Mahasiswa </th>
				<th> Mata Kuliah / Materi </th>
				<th> Ruangan </th>
				<th> Waktu Absen </th>
				<th> Jarak Absen</th>
				<th> Foto </th>
				<th> Keterangan </th>
			</tr>
		</thead>
		<tbody>

		@foreach($data_presensi as $data_presensis)
			<tr>
				<td>{{ $data_presensis->email }}</td>
				<td>{{ $data_presensis->name }}</td>
				<td>
					@if($data_presensis->tipe_jadwal == 'CSL' OR $data_presensis->tipe_jadwal == 'TUTORIAL')
						{{ $data_presensis->nama_materi }}
					@else
						{{ $data_presensis->mata_kuliah }}
					@endif
				</td>
				<td>{{ $data_presensis->nama_ruangan }}</td>
				<td>{{ $data_presensis->waktu }}</td>
				<td>{{ $data_presensis->jarak_absen." m" }}</td>
				<td>					
					@if(!is_null($data_presensis->foto))
				        <img src="{{public_path().('/'.$data_presensis->foto)}}" style="width:5px;height:5px;"/>
				    @else
				    	-
				    @endif
				</td>
				<td>MASUK</td>
			</tr>
		@endforeach

        </tbody>
    </table>