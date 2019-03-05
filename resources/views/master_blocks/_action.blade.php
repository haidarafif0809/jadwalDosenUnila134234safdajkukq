@if($model_user->count() > 1)
  <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal{{$id_master_block}}">List</button> 
@elseif($model_user->count() == 1) 
   @foreach($model_user as $model_users)
    {{  $model_users->dosen->name }}
  @endforeach
@endif
 
  <!-- Modal -->
  <div class="modal fade" id="myModal{{$id_master_block}}" role="dialog">
    <div class="modal-dialog modal-sm">
      
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">List Pj Dosen</h4>
        </div>
        <div class="modal-body">
	        @foreach($model_user as $model_users)
               {{  $model_users->dosen->name }} <br>          
                @endforeach
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div> 