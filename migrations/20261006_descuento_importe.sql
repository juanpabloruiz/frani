-- Los descuentos nuevos se guardan como importes. Se conserva la columna
-- descuento (porcentaje) para interpretar las ventas anteriores sin alterarlas.
ALTER TABLE facturas
    ADD COLUMN IF NOT EXISTS descuento_importe DECIMAL(10,2) NULL DEFAULT NULL AFTER descuento;
