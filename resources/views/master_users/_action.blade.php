@if($model->status == 0)
<a href="{{ $konfirmasi_url }}" class="btn btn-sm btn-primary">Iya</a> 
@elseif($model->status == 1)
<a href="{{ $no_konfirmasi_url }}" class="btn btn-sm btn-danger">Tidak</a> 
@endif 