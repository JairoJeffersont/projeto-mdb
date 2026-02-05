<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoPoliticoModel extends Model {
    protected $table = 'grupo_politico';
    public $incrementing = false;

    protected $fillable = [
        'nome',
        'descricao',
        'diretorio_id',
        'created_at',
        'updated_at'
    ];

    public function diretorio() {
        return $this->belongsTo(DiretorioModel::class, 'diretorio_id');
    }

    public function filiados() {
        return $this->belongsToMany(FiliadoModel::class, 'grupo_politico_membros', 'grupo_id', 'filiado_id')
            ->withTimestamps();
    }
}
