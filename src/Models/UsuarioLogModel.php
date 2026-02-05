<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioLogModel extends Model {
    protected $table = 'usuario_log';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['usuario_id', 'created_at', 'updated_at'];

    public function usuario() {
        return $this->belongsTo(UsuarioModel::class, 'usuario_id');
    }
}
