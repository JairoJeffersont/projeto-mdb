<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class CargoComissaoModel extends Model {
    protected $table = 'cargo_comissao';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'id',
        'descricao',
        'comissao_id',
        'multiplo',
        'usuario_id'
    ];

    // pertence à comissão
    public function comissao() {
        return $this->belongsTo(ComissaoModel::class, 'comissao_id');
    }

    public function usuario() {
        return $this->belongsTo(UsuarioModel::class, 'usuario_id');
    }

    // possui filiados membros
    public function filiados() {
        return $this->belongsToMany(
            FiliadoModel::class,
            'cargo_comissao_membros',
            'cargo_id',
            'filiado_id'
        )->withTimestamps();
    }
}
