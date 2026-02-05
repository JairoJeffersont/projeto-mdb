<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class ComissaoModel extends Model {
    protected $table = 'comissao';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'id',
        'tipo_id',
        'diretorio_id',
        'data_inicio',
        'data_fim',
        'usuario_id',
        'created_at',
        'updated_at'
    ];

    public function tipo() {
        return $this->belongsTo(ComissaoTipoModel::class, 'tipo_id');
    }

    public function diretorio() {
        return $this->belongsTo(DiretorioModel::class, 'diretorio_id');
    }

    public function usuario() {
        return $this->belongsTo(UsuarioModel::class, 'usuario_id');
    }

    public function cargos() {
        return $this->hasMany(CargoComissaoModel::class, 'comissao_id');
    }
}
