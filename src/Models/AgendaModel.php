<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class AgendaModel extends Model
{
    protected $table = 'agenda';
    public $incrementing = false;

    protected $fillable = [
        'tipo_id', 'titulo', 'descricao', 'data_inicio', 'data_fim',
        'diretorio_id', 'usuario_id', 'created_at', 'updated_at'
    ];

    public function tipo()
    {
        return $this->belongsTo(TipoAgendaModel::class, 'tipo_id');
    }

    public function diretorio()
    {
        return $this->belongsTo(DiretorioModel::class, 'diretorio_id');
    }

    public function criador()
    {
        return $this->belongsTo(UsuarioModel::class, 'usuario_id');
    }
}
