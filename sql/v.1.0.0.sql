-- rename column "importe" to "precio_unitario" in table "producto";
ALTER TABLE producto RENAME COLUMN importe TO precio_unitario;
DROP TABLE auth_group_permissions;
DROP TABLE auth_user_groups;
DROP TABLE auth_group;
DROP TABLE auth_user_user_permissions;
DROP TABLE auth_permission;
DROP TABLE django_admin_log;
DROP TABLE django_migrations;
DROP TABLE django_session;
DROP TABLE django_content_type;
ALTER TABLE `personal`
DROP FOREIGN KEY `fk_personal_user_id`;
ALTER TABLE `personal`
DROP INDEX `fk_personal_user_id_idx` ;
DROP TABLE auth_user;
RENAME TABLE beneficiario  TO contrato_persona;
ALTER TABLE contrato_persona CHANGE rol_id rol_id INT(11);
ALTER TABLE rol CHANGE rol_id id INT(11);
ALTER TABLE rol CHANGE descripcion nombre VARCHAR(45);
ALTER TABLE contrato_persona ADD CONSTRAINT fk_contrato_persona_rol_id FOREIGN KEY (rol_id) REFERENCES rol(id);
UPDATE contrato_persona SET rol_id = 3 WHERE rol_id IS NULL;
-- INSERT titular_id de la tabla contrato en la tabla contrato_persona asignando el rol de 'Titular' (id=1)
INSERT INTO contrato_persona (contrato_id, persona_id, rol_id) SELECT id, titular_id, 1 FROM contrato WHERE titular_id IS NOT NULL;
ALTER TABLE `pago` DROP FOREIGN KEY `fk_pago_personal_id`;
ALTER TABLE `pago` DROP INDEX `fk_pago_personal_id_idx` ;
ALTER TABLE `pago` DROP COLUMN personal_id;
ALTER TABLE `pago` DROP COLUMN facturacion_id;
UPDATE pago SET operacion = NULL WHERE operacion IS NOT NULL;
ALTER TABLE `pago` CHANGE operacion numero_operacion VARCHAR(50);
ALTER TABLE `pago` ADD COLUMN fecha_operacion DATETIME AFTER `numero_operacion`;
ALTER TABLE `amortizacion` DROP COLUMN `id`, DROP PRIMARY KEY, ADD PRIMARY KEY (`pago_id`, `cuota`);
ALTER TABLE `cronograma` DROP COLUMN `id`, DROP PRIMARY KEY, ADD PRIMARY KEY (`contrato_id`, `cuota`);
UPDATE persona, tipo_via SET direccion = CONCAT(tipo_via.nombre, ' ' ,persona.direccion) WHERE persona.tipo_via_id = tipo_via.id AND persona.direccion IS NOT NULL;
ALTER TABLE `persona` DROP FOREIGN KEY `fk_persona_tipo_via_id`;
ALTER TABLE `persona` DROP INDEX `fk_persona_tipo_via_id_idx` ;
ALTER TABLE `persona` DROP COLUMN `tipo_via_id`;
ALTER TABLE `persona` DROP FOREIGN KEY `fk_persona_tipo_documento_identidad_id`;
ALTER TABLE `persona` DROP INDEX `fk_persona_tipo_documento_identidad_id_idx` ;
ALTER TABLE `persona` CHANGE tipo_documento_identidad_id tipo_documento_identidad_id INT(11);
ALTER TABLE `tipo_documento_identidad` CHANGE id id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `persona` ADD CONSTRAINT fk_persona_tipo_documento_identidad_id FOREIGN KEY (tipo_documento_identidad_id) REFERENCES tipo_documento_identidad (id);
ALTER TABLE `persona_natural` DROP FOREIGN KEY `estado_civil_id`;
ALTER TABLE `persona_natural` DROP INDEX `estado_civil_id`;
ALTER TABLE `estado_civil` CHANGE estado_civil_id id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `persona` ADD COLUMN estado_civil_id INT(11) AFTER correo_electronico;
UPDATE persona, persona_natural SET persona.estado_civil_id = persona_natural.estado_civil_id WHERE persona.id = persona_natural.persona_id AND persona_natural.estado_civil_id IS NOT NULL;
ALTER TABLE `persona` ADD CONSTRAINT fk_persona_estado_civil_id FOREIGN KEY (estado_civil_id) REFERENCES estado_civil (id);
ALTER TABLE `persona` ADD COLUMN es_proveedor TINYINT(1) DEFAULT 0 AFTER es_convenio;
UPDATE `tipo_documento_identidad` SET `nombre` = 'DNI' WHERE (`id` = '1');
UPDATE `tipo_documento_identidad` SET `nombre` = 'LE' WHERE (`id` = '4');
UPDATE `tipo_documento_identidad` SET `nombre` = 'RUC' WHERE (`id` = '5');
UPDATE persona SET sexo = NULL WHERE sexo = "";
ALTER TABLE `estado_civil` CHANGE descripcion nombre VARCHAR(245);
ALTER TABLE `oficina` DROP COLUMN serie_recibo;
-- DROP TABLES
DROP TABLE IF EXISTS certificado_defuncion;
DROP TABLE IF EXISTS causa_def;
DROP TABLE IF EXISTS correlativo;
DROP TABLE IF EXISTS correo;
DROP TABLE IF EXISTS documento_identidad;
DROP TABLE IF EXISTS domicilio;
DROP TABLE IF EXISTS item_facturacion;
DROP TABLE IF EXISTS facturacion;
DROP TABLE IF EXISTS sitio_ocu;
DROP TABLE IF EXISTS telefono;
DROP TABLE IF EXISTS tipo_domicilio;
DROP TABLE IF EXISTS tipo_telefono;
DROP TABLE IF EXISTS persona_juridica;
DROP TABLE IF EXISTS tipo_via;
DROP TABLE IF EXISTS persona_natural;
-- 
DROP PROCEDURE IF EXISTS sp_cronograma_vigente;
DROP PROCEDURE IF EXISTS sp_pago;

DELIMITER $$
CREATE PROCEDURE `sp_current_schedule`( IN id INT, IN fecha DATE )
BEGIN
SELECT cronograma.cuota,
       cronograma.fecha_inicio,
       cronograma.fecha_vencimiento,
       cronograma.saldo - COALESCE(a.capital, 0.00)  AS 'saldo',
       cronograma.capital - COALESCE(a.capital, 0.00)  AS 'capital',
       ROUND(GREATEST((POW((1+contrato.tea/100), LEAST(DATEDIFF(cronograma.fecha_vencimiento, cronograma.fecha_inicio),DATEDIFF( fecha, cronograma.fecha_inicio) )/360)-1)*(cronograma.saldo - COALESCE(a.capital,0.00))-COALESCE(a.interes,0.00),0.00), 2) AS 'interes',
       ROUND(GREATEST((POW((1+contrato.tea/100), GREATEST(DATEDIFF(fecha, cronograma.fecha_vencimiento),0)/360)-1)*(cronograma.capital - COALESCE(a.capital, 0.00))-COALESCE(a.mora, 0.00), 0.00), 2) AS 'mora',
	   (
			( cronograma.capital - COALESCE(a.capital,0.00) ) +
			( ROUND(GREATEST((POW((1+contrato.tea/100),LEAST(DATEDIFF(cronograma.fecha_vencimiento, cronograma.fecha_inicio), DATEDIFF( fecha, cronograma.fecha_inicio) )/360)-1)*(cronograma.saldo - COALESCE(a.capital, 0.00))-COALESCE(a.interes, 0.00), 0.00), 2) ) +
			( ROUND(GREATEST((POW((1+contrato.tea/100),GREATEST(DATEDIFF(fecha,cronograma.fecha_vencimiento),0)/360)-1)*(cronograma.capital - COALESCE(a.capital, 0.00))-COALESCE(a.mora,0.00), 0.00), 2) )
	   ) AS 'importe'
FROM   cronograma
       INNER JOIN contrato ON contrato.id = cronograma.contrato_id
       LEFT JOIN (
		SELECT amortizacion.contrato_id , amortizacion.cuota,
			   COALESCE(SUM(amortizacion.capital),0.00) AS 'capital' ,
			   COALESCE(SUM(amortizacion.interes), 0.00) AS 'interes',
			   COALESCE(SUM(amortizacion.mora),0.00) AS 'mora'
		FROM   cronograma
			   LEFT JOIN amortizacion ON cronograma.contrato_id = amortizacion.contrato_id AND cronograma.cuota = amortizacion.cuota
		WHERE cronograma.estado = 0 AND cronograma.contrato_id = id  ORDER BY cronograma.cuota ASC
		) a ON cronograma.contrato_id = a.contrato_id AND a.cuota = cronograma.cuota
WHERE  cronograma.estado = 0 AND contrato.id = id
ORDER BY cronograma.cuota ASC;
END$$
DELIMITER ;
;

DELIMITER $$
CREATE PROCEDURE `sp_payments`( contrato_id INT, fecha_emision DATE, fecha_calculo DATE, moneda_id INT, recibo VARCHAR(255), personal_id INT, importe DECIMAL(16,2), tipo_comprobante_id INT, operacion INT, oficina_id INT, tipo_ingreso INT, referencia TEXT, OUT pago_id INT )
BEGIN
    DECLARE done INT DEFAULT 0;

    DECLARE n, dias_interes, dias_mora INT;
    DECLARE fecha_inicio, fecha_vencimiento DATE;
    DECLARE tea DECIMAL(16,2);
    DECLARE capital, capital_cronograma, capital_amortizacion DECIMAL(16,2);
    DECLARE interes, interes_amortizacion, mora_amortizacion DECIMAL(16,2);
    DECLARE importe_capital, importe_mora, importe_interes DECIMAL(16,2);
    DECLARE mora DECIMAL(16,2);
    DECLARE ultima_cuota INT;
    DECLARE cur CURSOR FOR SELECT cuota FROM `cronograma` WHERE `cronograma`.`contrato_id` = contrato_id AND `cronograma`.`estado` = 0 ORDER BY cuota ASC;
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	DECLARE exit handler for sqlexception
	  BEGIN

        SHOW ERRORS LIMIT 1;
	  ROLLBACK;
	END;

	DECLARE exit handler for sqlwarning
	 BEGIN

        SHOW WARNINGS LIMIT 1;
	 ROLLBACK;
	END;



    INSERT INTO pago (	`id`,`fecha_emision`,`fecha_calculo`, `recibo`,`importe`,`estado`,
						`contrato_id`, `personal_id`, `tipo_comprobante_id`, `moneda_id`,
						`operacion`, `oficina_id`, `tipo_ingreso`, `referencia`  )
    VALUES (NULL, fecha_emision, fecha_calculo, recibo, importe, 0, contrato_id,
			personal_id, tipo_comprobante_id, moneda_id, operacion,
			oficina_id, tipo_ingreso, referencia );
    SET pago_id = LAST_INSERT_ID();

    INSERT INTO `pago_producto` (`pago_id`, `cantidad`, `producto_id`, `precio_unitario`, `importe` )
    VALUES (pago_id, 1, 23, importe, importe);

    SET tea = (SELECT COALESCE(`contrato`.`tea`,0.00)  FROM `contrato` WHERE contrato.id = contrato_id);
    OPEN cur;
    retry:
    REPEAT
        FETCH cur INTO n;

        IF importe <= 0 THEN
            LEAVE retry;
        END IF;

        SET ultima_cuota  = (SELECT cuota FROM `cronograma` WHERE `cronograma`.`contrato_id` = contrato_id AND `cronograma`.`estado` = 0 ORDER BY 1 DESC LIMIT 1);
        SET fecha_vencimiento  = (SELECT `cronograma`.`fecha_vencimiento` FROM `cronograma` WHERE `cronograma`.`contrato_id` = contrato_id AND `cronograma`.`cuota` = n );
        SET fecha_inicio  = (SELECT `cronograma`.`fecha_inicio` FROM `cronograma` WHERE `cronograma`.`contrato_id` = contrato_id AND `cronograma`.`cuota` = n );

        SET capital_cronograma  = (SELECT `cronograma`.`capital` FROM `cronograma` WHERE `cronograma`.`contrato_id` = contrato_id AND `cronograma`.`cuota` = n );
        SET capital_amortizacion = (SELECT COALESCE(SUM(`amortizacion`.`capital`),0.00) FROM `amortizacion` WHERE `amortizacion`.`contrato_id` = contrato_id AND `amortizacion`.`cuota` = n);

        SET capital  = (SELECT `cronograma`.`saldo` FROM `cronograma` WHERE `cronograma`.`contrato_id` = contrato_id AND `cronograma`.`cuota` = n);
        SET interes_amortizacion = (SELECT COALESCE(SUM(`amortizacion`.`interes`),0.00) FROM `amortizacion` WHERE `amortizacion`.`contrato_id` = contrato_id AND `amortizacion`.`cuota` = n);
        SET mora_amortizacion = (SELECT COALESCE(SUM(`amortizacion`.`mora`),0.00) FROM `amortizacion` WHERE `amortizacion`.`contrato_id` = contrato_id AND `amortizacion`.`cuota` = n);

        SET dias_interes =  DATEDIFF(fecha_calculo, fecha_inicio);

        IF DATEDIFF(fecha_calculo, fecha_vencimiento) < 0 THEN
           SET dias_mora = 0;
        ELSE
           SET dias_mora = DATEDIFF(fecha_calculo, fecha_vencimiento);
           SET dias_interes =  DATEDIFF(fecha_vencimiento, fecha_inicio);
        END IF;

        SET mora  = ROUND(GREATEST((POW((1+tea/100),dias_mora/360)-1)*(capital_cronograma-capital_amortizacion)-mora_amortizacion,0.00), 2);
        SET interes  = ROUND(GREATEST((POW((1+tea/100),dias_interes/360)-1)*(capital-capital_amortizacion)-interes_amortizacion,0.00), 2);

        SET importe_mora  = LEAST(importe, mora);
        SET importe = GREATEST(importe - mora, 0.00);
        SET importe_interes  = LEAST(importe, interes);
        SET importe  = GREATEST(importe - interes, 0.00);
        SET importe_capital   = LEAST(importe, capital_cronograma-capital_amortizacion);
        SET importe  = GREATEST(importe - (capital_cronograma-capital_amortizacion),0.00);


        INSERT INTO `amortizacion`
            (`pago_id`, `cuota`, `contrato_id`, `capital`, `interes`, `mora` )
        VALUES
            (pago_id, n, contrato_id, importe_capital, importe_interes, importe_mora);

        IF  importe_capital = (capital_cronograma-capital_amortizacion) THEN
            UPDATE `cronograma` SET `cronograma`.`estado` = 1 WHERE `cronograma`.`contrato_id` = contrato_id AND `cronograma`.`cuota` = n;
            IF n = ultima_cuota THEN
                UPDATE contrato SET `contrato`.`estado_id`  = 2 WHERE `contrato`.`id` = contrato_id;
            END IF;
        END IF;

    UNTIL done END REPEAT;
    UPDATE `contrato` SET `contrato`.`ultimo_movimiento` = fecha_emision WHERE `contrato`.`id` = contrato_id;

END$$

DELIMITER ;
;


