<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoTipoModel extends Model {
    protected $table = 'documento_tipo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['descricao'];

    public function documentos() {
        return $this->hasMany(DocumentoModel::class, 'tipo_id');
    }
}
