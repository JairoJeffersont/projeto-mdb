<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoTipoModel extends Model {
    protected $table = 'documento_tipo';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = ['id', 'descricao', 'usuario_id', 'diretorio_id'];

    public function documentos() {
        return $this->hasMany(DocumentoModel::class, 'tipo_id');
    }

    public function diretorio() {
        return $this->belongsTo(DiretorioModel::class, 'diretorio_id');
    }

    public function usuario() {
        return $this->belongsTo(UsuarioModel::class, 'usuario_id');
    }
}
