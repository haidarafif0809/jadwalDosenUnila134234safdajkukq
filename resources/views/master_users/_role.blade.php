@if($model_role->count() > 1)
  <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal{{$id_role}}">List</button> 
@elseif($model_role->count() == 1) 
   @foreach($model_role as $model_roles)
    {{  $model_roles->role->display_name }}
  @endforeach
@endif
 
  <!-- Modal -->
  <div class="modal fade" id="myModal{{$id_role}}" role="dialog">
    <div class="modal-dialog modal-sm">
      
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">List Dosen</h4>
        </div>
        <div class="modal-body">
	        @foreach($model_role as $model_roles)
               {{  $model_roles->role->display_name }} <br>          
                @endforeach
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div> 