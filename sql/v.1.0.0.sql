-- rename column "importe" to "precio_unitario" in table "producto";
ALTER TABLE producto RENAME COLUMN importe TO precio_unitario;
