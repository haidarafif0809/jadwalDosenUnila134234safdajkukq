                   @if(isset($setting_slide) && $setting_slide->slide)
                        <div class="form-group{{ $errors->has('slide') ? ' has-error' : '' }}">
                            {!! Form::label('slide', 'Foto Slide', ['class' => 'col-md-2 control-label']) !!}
                            <div class="col-md-2">
                                {!! Form::file('slide',['multiple'=>'']) !!}
                                    @if (isset($setting_slide) && $setting_slide->slide)
                                        <p>
                                            {!! Html::image(asset('img/'.$setting_slide->slide), null, ['class' => 'img-rounded img-responsive']) !!}
                                        </p>
                                    @endif
                                {!! $errors->first('slide', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div> 
                    @else 
                        <div class="form-group{{ $errors->has('slide[]') ? ' has-error' : '' }}">
                            {!! Form::label('slide[]', 'Foto Slide', ['class' => 'col-md-2 control-label']) !!}
                            <div class="col-md-2">
                                {!! Form::file('slide[]',['multiple'=>'']) !!} 
                                {!! $errors->first('slide[]', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div> 
                    @endif
                        <!-- TOMBOL SIMPAN -->
                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-2">
                            {!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
                            </div>
                        </div>