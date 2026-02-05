<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class PermissaoModel extends Model {
    protected $table = 'permissao';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = ['descricao'];

    public function usuarios() {
        return $this->hasMany(UsuarioModel::class, 'permissao_id');
    }
}
