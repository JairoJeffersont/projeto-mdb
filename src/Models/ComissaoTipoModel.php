<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class ComissaoTipoModel extends Model {
    protected $table = 'comissao_tipo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['descricao'];

    public function comissoes() {
        return $this->hasMany(ComissaoModel::class, 'tipo_id');
    }
}
