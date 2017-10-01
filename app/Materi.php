<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yajra\Auditable\AuditableTrait;

class Materi extends Model
{
	use AuditableTrait;

    protected $fillable = ['id','nama_materi'];
}
