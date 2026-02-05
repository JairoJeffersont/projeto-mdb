<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class CargoEletivoModel extends Model {
    protected $table = 'cargo_eletivo';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'id',
        'descricao',
        'diretorio_id',
        'multiplo',
        'created_at',
        'updated_at',
        'usuario_id'
    ];

    public function diretorio() {
        return $this->belongsTo(DiretorioModel::class, 'diretorio_id');
    }

     public function usuario() {
        return $this->belongsTo(UsuarioModel::class, 'usuario_id');
    }

    public function filiados() {
        return $this->belongsToMany(FiliadoModel::class, 'cargo_eletivo_membros', 'cargo_id', 'filiado_id')
            ->withPivot('inicio_mandato', 'fim_mandato', 'created_at', 'updated_at');
    }
}
