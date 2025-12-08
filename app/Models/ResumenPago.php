<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class ResumenPago extends Model
{
    protected $table = null;
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'year', 'month', 'month_name', 'producto_id', 'total'
    ];

    public function newQuery()
    {
        $query = new Builder(DB::query());
        $query->setModel($this);

        $query->fromSub("
        SELECT
            EXTRACT(YEAR FROM fecha_emision) as year,
            EXTRACT(MONTH FROM fecha_emision) as month,
            CASE EXTRACT(MONTH FROM fecha_emision)
                WHEN 1 THEN 'Enero'
                WHEN 2 THEN 'Febrero'
                WHEN 3 THEN 'Marzo'
                WHEN 4 THEN 'Abril'
                WHEN 5 THEN 'Mayo'
                WHEN 6 THEN 'Junio'
                WHEN 7 THEN 'Julio'
                WHEN 8 THEN 'Agosto'
                WHEN 9 THEN 'Septiembre'
                WHEN 10 THEN 'Octubre'
                WHEN 11 THEN 'Noviembre'
                WHEN 12 THEN 'Diciembre'
            END as month_name,
            COALESCE(producto_id,0) as producto_id,
            SUM(importe) as total,
            CONCAT(
                EXTRACT(YEAR FROM fecha_emision),
                LPAD(EXTRACT(MONTH FROM fecha_emision), 2, '0'),
                COALESCE(CAST(producto_id AS CHAR), '')
            ) as id,
            estado
        FROM pago
        WHERE estado = 0
        GROUP BY year, month, month_name, producto_id, estado
        ", 'resumen_pagos');

        return $query;
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
}
