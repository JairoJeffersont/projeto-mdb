<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoModel extends Model {

    protected $table = 'documento';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'ano',
        'id',
        'tipo_id',
        'titulo',
        'arquivo',
        'diretorio_id',
        'usuario_id'
    ];

    public function tipo() {
        return $this->belongsTo(DocumentoTipoModel::class, 'tipo_id');
    }

    public function diretorio() {
        return $this->belongsTo(DiretorioModel::class, 'diretorio_id');
    }

    public function usuario() {
        return $this->belongsTo(UsuarioModel::class, 'usuario_id');
    }
}
