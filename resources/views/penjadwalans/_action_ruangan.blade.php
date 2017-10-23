@if($model_user->count() > 1)
  <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModals{{$id_jadwal}}">List</button> 
@elseif($model_user->count() == 1)
@foreach($model_user as $model_users)
    {{  $model_users->ruangan->nama_ruangan }}
@endforeach
@elseif($model_user->count() < 1)
    {{ $nama_ruangan->ruangan->nama_ruangan }}
@endif
 
  <!-- Modal -->
  <div class="modal fade" id="myModals{{$id_jadwal}}" role="dialog">
    <div class="modal-dialog modal-sm">
      
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">List Ruangan</h4>
        </div>
        <div class="modal-body">
	        @foreach($model_user as $model_users)
               {{  $model_users->ruangan->nama_ruangan }} <br>          
                @endforeach
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div> 