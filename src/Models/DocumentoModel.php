<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoModel extends Model {
    protected $table = 'documento';
    public $incrementing = false;

    protected $fillable = [
        'tipo_id',
        'titulo',
        'arquivo',
        'diretorio_id',
        'usuario_id',
        'created_at',
        'updated_at'
    ];

    public function tipo() {
        return $this->belongsTo(DocumentoTipoModel::class, 'tipo_id');
    }

    public function diretorio() {
        return $this->belongsTo(DiretorioModel::class, 'diretorio_id');
    }

    public function criador() {
        return $this->belongsTo(UsuarioModel::class, 'usuario_id');
    }
}
