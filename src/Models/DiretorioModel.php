<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class DiretorioModel extends Model {
    protected $table = 'diretorio';
    public $incrementing = false;

    protected $fillable = [
        'tipo_id',
        'municipio',
        'endereco',
        'telefone',
        'email',
        'created_at',
        'updated_at'
    ];

    public function tipo() {
        return $this->belongsTo(TipoDiretorioModel::class, 'tipo_id');
    }

    public function usuarios() {
        return $this->hasMany(UsuarioModel::class, 'diretorio_id');
    }

    public function filiados() {
        return $this->hasMany(FiliadoModel::class, 'diretorio_id');
    }

    public function cargosEletivos() {
        return $this->hasMany(CargoEletivoModel::class, 'diretorio_id');
    }

    public function comissoes() {
        return $this->hasMany(ComissaoModel::class, 'diretorio_id');
    }

    public function nucleos() {
        return $this->hasMany(NucleoModel::class, 'diretorio_id');
    }

    public function documentos() {
        return $this->hasMany(DocumentoModel::class, 'diretorio_id');
    }
}
