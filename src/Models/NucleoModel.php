<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class NucleoModel extends Model {
    protected $table = 'nucleo';
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = ['id', 'nome', 'descricao', 'diretorio_id', 'created_at', 'updated_at', 'usuario_id'];

    public function diretorio() {
        return $this->belongsTo(DiretorioModel::class, 'diretorio_id');
    }

    public function usuario() {
        return $this->belongsTo(UsuarioModel::class, 'usuario_id');
    }
}
