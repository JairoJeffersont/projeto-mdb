<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioModel extends Model {
    protected $table = 'usuario';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'id',
        'nome',
        'email',
        'senha',
        'permissao_id',
        'diretorio_id',
        'ativo',
        'token',
        'created_at',
        'updated_at'
    ];

    public function permissao() {
        return $this->belongsTo(PermissaoModel::class, 'permissao_id');
    }

    public function diretorio() {
        return $this->belongsTo(DiretorioModel::class, 'diretorio_id');
    }

    public function logs() {
        return $this->hasMany(UsuarioLogModel::class, 'usuario_id');
    }

    public function filiadosCriados() {
        return $this->hasMany(FiliadoModel::class, 'usuario_id');
    }

    public function comissoesCriadas() {
        return $this->hasMany(ComissaoModel::class, 'usuario_id');
    }

    public function documentosCriados() {
        return $this->hasMany(DocumentoModel::class, 'usuario_id');
    }
}
