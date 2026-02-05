<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAgendaModel extends Model {
    protected $table = 'tipo_agenda';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['descricao'];

    public function agendas() {
        return $this->hasMany(AgendaModel::class, 'tipo_id');
    }
}
