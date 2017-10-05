 <html>

	<table id="lap_detail" class="table table-bordered">

        <thead>
        	<tr>
				<th> NPM </th>
				<th> Nama Mahasiswa </th>
				<th> Mata Kuliah / Materi </th>
				<th> Ruangan </th>
				<th> Waktu Absen </th>
				<th> Jarak Absen</th>
				<th> Foto </th>
				<th> Keterangan </th>
			</tr><br>
		</thead>
		<tbody>

		@foreach($data_presensi as $data_presensis)
			<tr>
				<td>{{ $data_presensis->email }}</td>
				<td>{{ $data_presensis->name }}</td>
				<td>{{ $data_presensis->mata_kuliah }}</td>
				<td>{{ $data_presensis->nama_ruangan }}</td>
				<td>{{ $data_presensis->waktu }}</td>
				<td>{{ $data_presensis->jarak_absen." m" }}</td>
				<td>					
					@if(!is_null($data_presensis->foto))
				        <img src="{{public_path().('/'.$data_presensis->foto)}}" style="width:5px;height:5px;"/>
				    @endif
				</td>
				<td>MASUK</td>
			</tr>
		@endforeach

        </tbody>
    </table>

</html>