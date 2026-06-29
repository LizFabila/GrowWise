<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // ----- VISTAS (ya usan CREATE OR REPLACE, no necesitan cambios) -----
        DB::statement("
            CREATE OR REPLACE VIEW `alertas_activas` AS
            SELECT a.id AS id, a.user_id AS user_id, CONCAT(u.nombre, ' ', u.apellido) AS usuario,
                   a.titulo AS titulo, a.mensaje AS mensaje, a.prioridad AS prioridad,
                   a.created_at AS created_at,
                   TIMESTAMPDIFF(MINUTE, a.created_at, CURRENT_TIMESTAMP()) AS minutos_transcurridos,
                   CASE WHEN TIMESTAMPDIFF(HOUR, a.created_at, CURRENT_TIMESTAMP()) < 1 THEN 'Reciente'
                        WHEN TIMESTAMPDIFF(HOUR, a.created_at, CURRENT_TIMESTAMP()) < 24 THEN 'Hoy'
                        ELSE 'Antigua' END AS antiguedad,
                   m.nombre AS modulo, s.nombre AS sensor
            FROM alertas a
            JOIN users u ON a.user_id = u.id
            LEFT JOIN modulos m ON a.modulo_id = m.id
            LEFT JOIN sensores s ON a.sensor_id = s.id
            WHERE a.estado = 'Pendiente'
            ORDER BY FIELD(a.prioridad, 'Crítica', 'Alta', 'Media', 'Baja') ASC, a.created_at DESC
        ");

        DB::statement("
            CREATE OR REPLACE VIEW `dashboard_resumen` AS
            SELECT
                u.id AS user_id,
                (SELECT COUNT(*) FROM siembras WHERE siembras.user_id = u.id AND siembras.estado = 'Activa') AS siembras_activas,
                (SELECT COUNT(*) FROM siembras WHERE siembras.user_id = u.id) AS total_siembras,
                (SELECT COUNT(*) FROM alertas WHERE alertas.user_id = u.id AND alertas.estado = 'Pendiente') AS alertas_pendientes,
                (SELECT COUNT(*) FROM modulos WHERE modulos.user_id = u.id AND modulos.activo = 1) AS modulos_activos,
                (SELECT COUNT(*) FROM cosechas WHERE cosechas.user_id = u.id) AS total_cosechas,
                (SELECT COALESCE(SUM(cosechas.cantidad_kg), 0) FROM cosechas WHERE cosechas.user_id = u.id AND cosechas.fecha_cosecha >= CURDATE() - INTERVAL 30 DAY) AS kg_ultimo_mes,
                (SELECT COALESCE(AVG(e.rendimiento), 0) FROM evaluaciones e JOIN siembras s ON e.siembra_id = s.id WHERE s.user_id = u.id) AS rendimiento_promedio
            FROM users u
        ");

        DB::statement("
            CREATE OR REPLACE VIEW `estadisticas_cosechas` AS
            SELECT
                u.id AS user_id,
                CONCAT(u.nombre, ' ', u.apellido) AS usuario,
                YEAR(c.fecha_cosecha) AS año,
                MONTH(c.fecha_cosecha) AS mes,
                COUNT(*) AS total_cosechas,
                SUM(c.cantidad_kg) AS total_kg,
                AVG(c.cantidad_kg) AS promedio_kg,
                COUNT(CASE WHEN c.calidad = 'Excelente' THEN 1 END) AS excelentes,
                COUNT(CASE WHEN c.calidad = 'Buena' THEN 1 END) AS buenas,
                COUNT(CASE WHEN c.calidad = 'Regular' THEN 1 END) AS regulares,
                COUNT(CASE WHEN c.calidad = 'Mala' THEN 1 END) AS malas
            FROM users u
            JOIN cosechas c ON u.id = c.user_id
            GROUP BY u.id, u.nombre, u.apellido, YEAR(c.fecha_cosecha), MONTH(c.fecha_cosecha)
        ");

        DB::statement("
            CREATE OR REPLACE VIEW `monitoreo_actual` AS
            SELECT
                m.id AS modulo_id, m.nombre AS modulo, m.user_id AS user_id,
                s.id AS sensor_id, s.nombre AS sensor, s.tipo AS tipo, s.unidad AS unidad,
                l.valor AS valor, l.created_at AS ultima_lectura,
                TIMESTAMPDIFF(MINUTE, l.created_at, CURRENT_TIMESTAMP()) AS minutos_desde_lectura,
                CASE WHEN TIMESTAMPDIFF(MINUTE, l.created_at, CURRENT_TIMESTAMP()) > 60 THEN 'Obsoleto' ELSE 'Actualizado' END AS estado_lectura
            FROM modulos m
            JOIN sensores s ON m.id = s.modulo_id
            LEFT JOIN lecturas_sensores l ON s.id = l.sensor_id
            WHERE l.id IN (SELECT MAX(lecturas_sensores.id) FROM lecturas_sensores GROUP BY lecturas_sensores.sensor_id) OR l.id IS NULL
        ");

        DB::statement("
            CREATE OR REPLACE VIEW `siembras_detalle` AS
            SELECT
                s.id AS id, s.user_id AS user_id, CONCAT(u.nombre, ' ', u.apellido) AS usuario,
                c.nombre AS cultivo, c.tipo AS tipo_cultivo, m.nombre AS modulo, m.ubicacion AS ubicacion,
                s.charola AS charola, s.fecha_siembra AS fecha_siembra, s.fecha_estimada_cosecha AS fecha_estimada_cosecha,
                s.fecha_cosecha_real AS fecha_cosecha_real, s.estado AS estado,
                TO_DAYS(s.fecha_estimada_cosecha) - TO_DAYS(CURDATE()) AS dias_restantes,
                CASE
                    WHEN s.estado = 'Completada' THEN 100
                    WHEN s.fecha_estimada_cosecha IS NULL THEN 0
                    ELSE LEAST(ROUND((TO_DAYS(CURDATE()) - TO_DAYS(s.fecha_siembra)) * 100.0 / GREATEST(TO_DAYS(s.fecha_estimada_cosecha) - TO_DAYS(s.fecha_siembra), 1), 0), 100)
                END AS porcentaje_progreso
            FROM siembras s
            JOIN users u ON s.user_id = u.id
            JOIN cultivos c ON s.cultivo_id = c.id
            JOIN modulos m ON s.modulo_id = m.id
        ");

        // ----- PROCEDIMIENTOS (ahora con DROP IF EXISTS) -----

        // Eliminar procedimiento si ya existe
        DB::statement('DROP PROCEDURE IF EXISTS sp_costo_beneficio_vendedor');
        DB::statement("
            CREATE PROCEDURE `sp_costo_beneficio_vendedor` (IN `p_vendedor_id` INT)
            BEGIN
                DECLARE v_total_vendido DECIMAL(10,2) DEFAULT 0;
                DECLARE v_inversion_total DECIMAL(10,2) DEFAULT 0;
                DECLARE v_beneficio_neto DECIMAL(10,2) DEFAULT 0;
                DECLARE v_rentabilidad DECIMAL(10,2) DEFAULT 0;
                DECLARE v_roi DECIMAL(10,2) DEFAULT 0;
                DECLARE v_producto_mas_rentable VARCHAR(100) DEFAULT NULL;
                DECLARE v_producto_mas_vendido VARCHAR(100) DEFAULT NULL;
                DECLARE v_productos_publicados INT DEFAULT 0;
                DECLARE v_stock_restante DECIMAL(10,2) DEFAULT 0;
                DECLARE v_productos_vendidos INT DEFAULT 0;

                SELECT COALESCE(SUM(total), 0) INTO v_total_vendido
                FROM ventas
                WHERE user_id_vendedor = p_vendedor_id AND estado = 'completada';

                SET v_inversion_total = v_total_vendido * 0.25;
                SET v_beneficio_neto = v_total_vendido - v_inversion_total;

                IF v_inversion_total > 0 THEN
                    SET v_rentabilidad = (v_beneficio_neto / v_inversion_total) * 100;
                ELSE
                    SET v_rentabilidad = 0;
                END IF;

                IF v_inversion_total > 0 THEN
                    SET v_roi = (v_beneficio_neto / v_inversion_total) * 100;
                ELSE
                    SET v_roi = 0;
                END IF;

                SELECT c.nombre INTO v_producto_mas_rentable
                FROM ventas v
                JOIN pedidos p ON v.pedido_id = p.id
                JOIN pedidos_detalle pd ON p.id = pd.pedido_id
                JOIN productos_venta pv ON pd.producto_venta_id = pv.id
                JOIN cultivos c ON pv.cultivo_id = c.id
                WHERE v.user_id_vendedor = p_vendedor_id
                GROUP BY c.id, c.nombre
                ORDER BY SUM(pd.subtotal) DESC
                LIMIT 1;

                SELECT c.nombre INTO v_producto_mas_vendido
                FROM ventas v
                JOIN pedidos p ON v.pedido_id = p.id
                JOIN pedidos_detalle pd ON p.id = pd.pedido_id
                JOIN productos_venta pv ON pd.producto_venta_id = pv.id
                JOIN cultivos c ON pv.cultivo_id = c.id
                WHERE v.user_id_vendedor = p_vendedor_id
                GROUP BY c.id, c.nombre
                ORDER BY SUM(pd.cantidad) DESC
                LIMIT 1;

                SELECT COUNT(*) INTO v_productos_publicados
                FROM productos_venta
                WHERE user_id = p_vendedor_id AND estado != 'eliminado';

                SELECT COALESCE(SUM(stock), 0) INTO v_stock_restante
                FROM productos_venta
                WHERE user_id = p_vendedor_id AND estado = 'disponible';

                SELECT COALESCE(SUM(pd.cantidad), 0) INTO v_productos_vendidos
                FROM ventas v
                JOIN pedidos p ON v.pedido_id = p.id
                JOIN pedidos_detalle pd ON p.id = pd.pedido_id
                WHERE v.user_id_vendedor = p_vendedor_id;

                SELECT
                    v_total_vendido AS total_vendido,
                    v_inversion_total AS inversion_total,
                    v_beneficio_neto AS beneficio_neto,
                    v_rentabilidad AS rentabilidad,
                    v_roi AS roi,
                    v_producto_mas_rentable AS producto_mas_rentable,
                    v_producto_mas_vendido AS producto_mas_vendido,
                    v_productos_publicados AS productos_publicados,
                    v_stock_restante AS stock_restante,
                    v_productos_vendidos AS productos_vendidos;
            END
        ");

        // Si tienes otro procedimiento, haz lo mismo:
        DB::statement('DROP PROCEDURE IF EXISTS sp_generar_alerta_por_lectura');
        DB::statement("
            CREATE PROCEDURE `sp_generar_alerta_por_lectura` (IN `p_sensor_id` INT, IN `p_valor` DECIMAL(8,2))
            BEGIN
                -- Contenido del procedimiento (puede estar vacío o con lógica)
            END
        ");
    }

    public function down()
    {
        // Eliminar vistas y procedimientos (opcional)
        DB::statement('DROP VIEW IF EXISTS alertas_activas');
        DB::statement('DROP VIEW IF EXISTS dashboard_resumen');
        DB::statement('DROP VIEW IF EXISTS estadisticas_cosechas');
        DB::statement('DROP VIEW IF EXISTS monitoreo_actual');
        DB::statement('DROP VIEW IF EXISTS siembras_detalle');
        DB::statement('DROP PROCEDURE IF EXISTS sp_costo_beneficio_vendedor');
        DB::statement('DROP PROCEDURE IF EXISTS sp_generar_alerta_por_lectura');
    }
};
