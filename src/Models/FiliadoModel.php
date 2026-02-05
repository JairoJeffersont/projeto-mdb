<?php

namespace JairoJeffersont\Models;

use Illuminate\Database\Eloquent\Model;

class FiliadoModel extends Model {
    protected $table = 'filiado';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true; 


    protected $fillable = [
        'id',
        'nome',
        'email',
        'telefone',
        'data_nascimento',
        'data_filiacao',
        'data_desfiliacao',
        'endereco',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'cpf',
        'rg',
        'titulo_eleitoral',
        'zona_eleitoral',
        'secao_eleitoral',
        'diretorio_id',
        'sexo',
        'ativo',
        'foto',
        'informacoes_adicionais',
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

    public function cargosEletivos() {
        return $this->belongsToMany(CargoEletivoModel::class, 'cargo_eletivo_membros', 'filiado_id', 'cargo_id')
            ->withPivot('inicio_mandato', 'fim_mandato', 'created_at', 'updated_at');
    }

    public function comissoes() {
        return $this->belongsToMany(ComissaoModel::class, 'comissao_membros', 'filiado_id', 'comissao_id')
            ->withTimestamps();
    }
}
