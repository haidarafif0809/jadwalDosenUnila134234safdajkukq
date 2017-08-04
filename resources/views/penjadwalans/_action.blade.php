
<!-- Small modal -->
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">Dosen Lainnya</button> 
  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Dosen</h4>
        </div>
        <div class="modal-body">
	        @foreach($model_user as $model_users)
               {{  $model_users->name }}           
                @endforeach
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>