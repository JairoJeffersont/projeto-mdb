<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDiretorioModel extends Model {
    protected $table = 'tipo_diretorio';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['descricao'];

    public function diretorios() {
        return $this->hasMany(DiretorioModel::class, 'tipo_id');
    }
}
