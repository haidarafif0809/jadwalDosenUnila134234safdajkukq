                       <!-- KOLOM slide  1-->
                        <div class="form-group{{ $errors->has('slide') ? ' has-error' : '' }}">
                            {!! Form::label('slide', 'Foto Slide', ['class' => 'col-md-2 control-label']) !!}
                            <div class="col-md-2">
                                {!! Form::file('slide') !!}
                                    @if (isset($setting_slide) && $setting_slide->slide)
                                        <p>
                                            {!! Html::image(asset('img/'.$setting_slide->slide), null, ['class' => 'img-rounded img-responsive']) !!}
                                        </p>
                                    @endif
                                {!! $errors->first('slide', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('judul_slide') ? ' has-error' : '' }}">
                            {!! Form::label('judul_slide', 'Judul Slide', ['class'=>'col-md-2 control-label']) !!}
                            <div class="col-md-4">
                                {!! Form::text('judul_slide', null, ['class'=>'form-control','required','autocomplete'=>'off']) !!}
                                {!! $errors->first('judul_slide', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
 
                        <!-- TOMBOL SIMPAN -->
                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-2">
                            {!! Form::submit('Simpan', ['class'=>'btn btn-primary']) !!}
                            </div>
                        </div>