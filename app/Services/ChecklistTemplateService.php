<?php

namespace App\Services;

use App\Models\Checklist;
use App\Models\ChecklistItem;

class ChecklistTemplateService
{
    public static function crearTemplate($tipo, $proyectoId, $responsableId = null)
    {
        $templates = self::getTemplates();
        
        if (!isset($templates[$tipo])) {
            throw new \Exception("Template no encontrado: {$tipo}");
        }
        
        $template = $templates[$tipo];
        
        $checklist = Checklist::create([
            'nombre' => $template['nombre'],
            'descripcion' => $template['descripcion'],
            'categoria' => $template['categoria'],
            'proyecto_id' => $proyectoId,
            'responsable_id' => $responsableId,
            'estado' => 'Activo',
        ]);
        
        foreach ($template['items'] as $index => $item) {
            ChecklistItem::create([
                'checklist_id' => $checklist->id,
                'titulo' => $item['titulo'],
                'descripcion' => $item['descripcion'] ?? null,
                'orden' => $index + 1,
            ]);
        }
        
        return $checklist;
    }
    
    public static function getAreaTemplates($area = null)
    {
        $allTemplates = self::getTemplates();
        
        if ($area) {
            return array_filter($allTemplates, function($template) use ($area) {
                return $template['area'] === $area;
            });
        }
        
        return $allTemplates;
    }
    
    private static function getTemplates()
    {
        return [
            // ========== TEMPLATES DE PROYECTOS ==========
            'proyecto_inicio' => [
                'nombre' => 'Inicio de Proyecto',
                'descripcion' => 'Verificaciones esenciales para iniciar un proyecto exitosamente',
                'categoria' => 'Planificación',
                'area' => 'proyectos',
                'items' => [
                    ['titulo' => 'Contrato firmado y aprobado', 'descripcion' => 'Verificar que el contrato esté debidamente firmado por ambas partes'],
                    ['titulo' => 'PM asignado al proyecto', 'descripcion' => 'Confirmar que hay un Project Manager asignado y disponible'],
                    ['titulo' => 'Alcance del proyecto definido', 'descripcion' => 'Documento de alcance revisado y aprobado por el cliente'],
                    ['titulo' => 'Equipo de trabajo conformado', 'descripcion' => 'Recursos asignados según perfil y disponibilidad'],
                    ['titulo' => 'Plan de proyecto creado', 'descripcion' => 'Cronograma inicial con hitos y entregables definidos'],
                    ['titulo' => 'Kickoff meeting realizado', 'descripcion' => 'Reunión inicial con cliente y equipo interno'],
                    ['titulo' => 'Requerimientos documentados', 'descripcion' => 'Documento de requerimientos funcionales y no funcionales'],
                    ['titulo' => 'Presupuesto aprobado', 'descripcion' => 'Presupuesto del proyecto aprobado por el cliente'],
                    ['titulo' => 'Repositorio de código creado', 'descripcion' => 'Repositorio Git configurado con permisos apropiados'],
                    ['titulo' => 'Ambientes de desarrollo configurados', 'descripcion' => 'Entornos de desarrollo y staging preparados'],
                ]
            ],
            
            'proyecto_seguimiento' => [
                'nombre' => 'Seguimiento de Proyecto',
                'descripcion' => 'Checklist semanal para seguimiento de proyectos en ejecución',
                'categoria' => 'Desarrollo',
                'area' => 'proyectos',
                'items' => [
                    ['titulo' => 'Revisión de progreso semanal', 'descripcion' => 'Evaluación del avance vs cronograma planificado'],
                    ['titulo' => 'Status report enviado', 'descripcion' => 'Reporte de estado enviado al cliente y stakeholders'],
                    ['titulo' => 'Riesgos identificados y mitigados', 'descripcion' => 'Nuevos riesgos documentados con plan de mitigación'],
                    ['titulo' => 'Reunión de equipo realizada', 'descripcion' => 'Daily standup o reunión de seguimiento con el equipo'],
                    ['titulo' => 'Métricas de calidad revisadas', 'descripcion' => 'Revisión de métricas de código y testing'],
                    ['titulo' => 'Feedback del cliente recopilado', 'descripcion' => 'Retroalimentación del cliente sobre entregables'],
                    ['titulo' => 'Ajustes al cronograma si es necesario', 'descripcion' => 'Modificaciones al plan basadas en el progreso actual'],
                ]
            ],
            
            'proyecto_cierre' => [
                'nombre' => 'Cierre de Proyecto',
                'descripcion' => 'Actividades necesarias para cerrar exitosamente un proyecto',
                'categoria' => 'Post-entrega',
                'area' => 'proyectos',
                'items' => [
                    ['titulo' => 'Entrega final completada', 'descripcion' => 'Todos los entregables han sido entregados y aceptados'],
                    ['titulo' => 'Documentación finalizada', 'descripcion' => 'Manuales de usuario y técnicos completados'],
                    ['titulo' => 'Capacitación al cliente realizada', 'descripcion' => 'Sesiones de capacitación completadas satisfactoriamente'],
                    ['titulo' => 'Garantía y soporte definidos', 'descripcion' => 'Términos de garantía y soporte post-entrega acordados'],
                    ['titulo' => 'Lecciones aprendidas documentadas', 'descripcion' => 'Retrospectiva del proyecto y mejoras identificadas'],
                    ['titulo' => 'Recursos liberados', 'descripcion' => 'Equipo de trabajo liberado para otros proyectos'],
                    ['titulo' => 'Facturación final procesada', 'descripcion' => 'Facturas finales generadas y enviadas'],
                    ['titulo' => 'Encuesta de satisfacción enviada', 'descripcion' => 'NPS y encuesta de satisfacción del cliente'],
                    ['titulo' => 'Archivos del proyecto organizados', 'descripcion' => 'Documentación y archivos organizados en repositorio'],
                ]
            ],

            // ========== TEMPLATES DE ARQUITECTURA ==========
            'arquitectura_diseno' => [
                'nombre' => 'Diseño de Arquitectura',
                'descripcion' => 'Checklist para el diseño de arquitectura de software',
                'categoria' => 'Desarrollo',
                'area' => 'arquitectura',
                'items' => [
                    ['titulo' => 'Requerimientos no funcionales analizados', 'descripcion' => 'Performance, escalabilidad, seguridad definidos'],
                    ['titulo' => 'Arquitectura de alto nivel diseñada', 'descripcion' => 'Diagrama general de la arquitectura del sistema'],
                    ['titulo' => 'Stack tecnológico seleccionado', 'descripcion' => 'Tecnologías y frameworks seleccionados y justificados'],
                    ['titulo' => 'Patrones de diseño definidos', 'descripcion' => 'Patrones arquitectónicos apropiados seleccionados'],
                    ['titulo' => 'Diagrama de componentes creado', 'descripcion' => 'Componentes principales y sus interacciones'],
                    ['titulo' => 'Diagrama de base de datos diseñado', 'descripcion' => 'Modelo entidad-relación y estructura de datos'],
                    ['titulo' => 'APIs y contratos definidos', 'descripcion' => 'Especificación de APIs y interfaces entre componentes'],
                    ['titulo' => 'Estrategia de despliegue planificada', 'descripcion' => 'Plan de deployment y configuración de infraestructura'],
                    ['titulo' => 'Documentación de arquitectura creada', 'descripcion' => 'Documento técnico de arquitectura completo'],
                ]
            ],
            
            'arquitectura_revision' => [
                'nombre' => 'Revisión de Arquitectura',
                'descripcion' => 'Revisión y validación de la arquitectura propuesta',
                'categoria' => 'Desarrollo',
                'area' => 'arquitectura',
                'items' => [
                    ['titulo' => 'Revisión por arquitecto senior', 'descripcion' => 'Arquitectura revisada por arquitecto experimentado'],
                    ['titulo' => 'Validación de escalabilidad', 'descripcion' => 'Capacidad del sistema para escalar según requerimientos'],
                    ['titulo' => 'Análisis de performance', 'descripcion' => 'Evaluación de rendimiento esperado del sistema'],
                    ['titulo' => 'Revisión de seguridad', 'descripcion' => 'Aspectos de seguridad en la arquitectura validados'],
                    ['titulo' => 'Compatibilidad tecnológica verificada', 'descripcion' => 'Tecnologías seleccionadas son compatibles entre sí'],
                    ['titulo' => 'Costos de infraestructura estimados', 'descripcion' => 'Estimación de costos de hosting y mantenimiento'],
                    ['titulo' => 'Plan de migración definido', 'descripcion' => 'Estrategia para migrar sistemas existentes si aplica'],
                    ['titulo' => 'Aprobación de stakeholders técnicos', 'descripcion' => 'Arquitectura aprobada por líderes técnicos'],
                ]
            ],

            'arquitectura_microservicios' => [
                'nombre' => 'Arquitectura de Microservicios',
                'descripcion' => 'Checklist específico para arquitecturas basadas en microservicios',
                'categoria' => 'Desarrollo',
                'area' => 'arquitectura',
                'items' => [
                    ['titulo' => 'Dominio de negocio analizado', 'descripcion' => 'Bounded contexts identificados y definidos'],
                    ['titulo' => 'Servicios identificados', 'descripcion' => 'Microservicios definidos según responsabilidades'],
                    ['titulo' => 'Comunicación entre servicios diseñada', 'descripcion' => 'APIs, message queues y patrones de comunicación'],
                    ['titulo' => 'Gestión de datos definida', 'descripcion' => 'Estrategia de persistencia por servicio'],
                    ['titulo' => 'Service discovery configurado', 'descripcion' => 'Mecanismo para descubrimiento de servicios'],
                    ['titulo' => 'API Gateway implementado', 'descripcion' => 'Gateway para enrutamiento y seguridad'],
                    ['titulo' => 'Monitoreo y logging centralizado', 'descripcion' => 'Observabilidad distribuida implementada'],
                    ['titulo' => 'Estrategia de deployment definida', 'descripcion' => 'CI/CD para microservicios independientes'],
                ]
            ],

            // ========== TEMPLATES DE UX/UI ==========
            'ux_investigacion' => [
                'nombre' => 'Investigación UX',
                'descripcion' => 'Proceso de investigación y análisis de experiencia de usuario',
                'categoria' => 'Planificación',
                'area' => 'ux_ui',
                'items' => [
                    ['titulo' => 'Research de usuarios realizado', 'descripcion' => 'Investigación de usuarios objetivo y sus necesidades'],
                    ['titulo' => 'Personas definidas', 'descripcion' => 'Perfiles de usuarios representativos creados'],
                    ['titulo' => 'User journey mapeado', 'descripcion' => 'Recorrido del usuario en la aplicación documentado'],
                    ['titulo' => 'Análisis competitivo completado', 'descripcion' => 'Estudio de soluciones similares en el mercado'],
                    ['titulo' => 'Pain points identificados', 'descripcion' => 'Problemas y frustraciones de usuarios actuales'],
                    ['titulo' => 'Entrevistas con usuarios realizadas', 'descripcion' => 'Sesiones de entrevistas con usuarios reales'],
                    ['titulo' => 'Encuestas de usuario procesadas', 'descripcion' => 'Datos cuantitativos recopilados y analizados'],
                    ['titulo' => 'Insights y recomendaciones documentados', 'descripcion' => 'Hallazgos clave y recomendaciones de diseño'],
                ]
            ],
            
            'ux_diseno' => [
                'nombre' => 'Diseño UX/UI',
                'descripcion' => 'Proceso de diseño de interfaz y experiencia de usuario',
                'categoria' => 'Desarrollo',
                'area' => 'ux_ui',
                'items' => [
                    ['titulo' => 'Wireframes de baja fidelidad', 'descripcion' => 'Bocetos iniciales de estructura de pantallas'],
                    ['titulo' => 'Wireframes de alta fidelidad', 'descripcion' => 'Wireframes detallados con contenido real'],
                    ['titulo' => 'Sistema de diseño definido', 'descripcion' => 'Colores, tipografías y componentes estandarizados'],
                    ['titulo' => 'Mockups de interfaces creados', 'descripcion' => 'Diseños visuales de todas las pantallas'],
                    ['titulo' => 'Prototipo interactivo desarrollado', 'descripcion' => 'Prototipo navegable para testing'],
                    ['titulo' => 'Guía de estilos documentada', 'descripcion' => 'Manual de uso del sistema de diseño'],
                    ['titulo' => 'Componentes UI definidos', 'descripcion' => 'Librería de componentes reutilizables'],
                    ['titulo' => 'Responsive design verificado', 'descripcion' => 'Diseño adaptativo para diferentes dispositivos'],
                    ['titulo' => 'Accesibilidad validada', 'descripcion' => 'Cumplimiento de estándares de accesibilidad'],
                ]
            ],

            'ux_testing' => [
                'nombre' => 'Testing de UX',
                'descripcion' => 'Pruebas de usabilidad y experiencia de usuario',
                'categoria' => 'Testing',
                'area' => 'ux_ui',
                'items' => [
                    ['titulo' => 'Plan de testing UX creado', 'descripcion' => 'Metodología y objetivos de testing definidos'],
                    ['titulo' => 'Usuarios de prueba reclutados', 'descripcion' => 'Participantes representativos del target'],
                    ['titulo' => 'Sesiones de usability testing', 'descripcion' => 'Pruebas de usabilidad con usuarios reales'],
                    ['titulo' => 'A/B testing implementado', 'descripcion' => 'Pruebas comparativas de diferentes versiones'],
                    ['titulo' => 'Métricas de UX definidas', 'descripcion' => 'KPIs para medir éxito de la experiencia'],
                    ['titulo' => 'Feedback de usuarios recopilado', 'descripcion' => 'Comentarios y sugerencias documentados'],
                    ['titulo' => 'Heatmaps y analytics revisados', 'descripcion' => 'Análisis de comportamiento de usuarios'],
                    ['titulo' => 'Mejoras implementadas', 'descripcion' => 'Ajustes basados en resultados de testing'],
                ]
            ],

            // ========== TEMPLATES DE TESTING ==========
            'testing_funcional' => [
                'nombre' => 'Testing Funcional',
                'descripcion' => 'Verificaciones completas de funcionalidad del sistema',
                'categoria' => 'Testing',
                'area' => 'testing',
                'items' => [
                    ['titulo' => 'Plan de pruebas documentado', 'descripcion' => 'Estrategia y casos de prueba definidos'],
                    ['titulo' => 'Casos de prueba creados', 'descripcion' => 'Casos de prueba detallados para cada funcionalidad'],
                    ['titulo' => 'Ambiente de testing configurado', 'descripcion' => 'Entorno de pruebas idéntico a producción'],
                    ['titulo' => 'Pruebas de unidad ejecutadas', 'descripcion' => 'Tests unitarios con cobertura mínima del 80%'],
                    ['titulo' => 'Pruebas de integración realizadas', 'descripcion' => 'Verificación de integración entre módulos'],
                    ['titulo' => 'Pruebas de regresión completadas', 'descripcion' => 'Verificación de funcionalidades existentes'],
                    ['titulo' => 'Pruebas de aceptación ejecutadas', 'descripcion' => 'Validación de criterios de aceptación'],
                    ['titulo' => 'Bugs reportados y tracked', 'descripcion' => 'Defectos documentados en sistema de tracking'],
                    ['titulo' => 'Reporte de testing generado', 'descripcion' => 'Resumen ejecutivo de resultados de pruebas'],
                ]
            ],
            
            'testing_performance' => [
                'nombre' => 'Testing de Performance',
                'descripcion' => 'Pruebas de rendimiento y escalabilidad',
                'categoria' => 'Testing',
                'area' => 'testing',
                'items' => [
                    ['titulo' => 'Métricas de performance definidas', 'descripcion' => 'SLAs y objetivos de rendimiento establecidos'],
                    ['titulo' => 'Herramientas de testing configuradas', 'descripcion' => 'Tools como JMeter, LoadRunner configurados'],
                    ['titulo' => 'Pruebas de carga ejecutadas', 'descripcion' => 'Testing con carga normal esperada'],
                    ['titulo' => 'Pruebas de estrés realizadas', 'descripcion' => 'Testing con carga máxima y beyond'],
                    ['titulo' => 'Pruebas de volumen completadas', 'descripcion' => 'Testing con grandes volúmenes de datos'],
                    ['titulo' => 'Pruebas de resistencia ejecutadas', 'descripcion' => 'Testing de estabilidad a largo plazo'],
                    ['titulo' => 'Bottlenecks identificados', 'descripcion' => 'Cuellos de botella encontrados y documentados'],
                    ['titulo' => 'Optimizaciones implementadas', 'descripcion' => 'Mejoras de performance aplicadas'],
                    ['titulo' => 'Benchmarks documentados', 'descripcion' => 'Métricas baseline para futuras comparaciones'],
                ]
            ],

            'testing_seguridad' => [
                'nombre' => 'Testing de Seguridad',
                'descripcion' => 'Auditoría completa de seguridad de la aplicación',
                'categoria' => 'Seguridad',
                'area' => 'testing',
                'items' => [
                    ['titulo' => 'Análisis de vulnerabilidades OWASP', 'descripcion' => 'Verificación de Top 10 vulnerabilidades'],
                    ['titulo' => 'Pruebas de inyección SQL', 'descripcion' => 'Testing de vulnerabilidades de inyección'],
                    ['titulo' => 'Pruebas de XSS realizadas', 'descripcion' => 'Cross-site scripting testing'],
                    ['titulo' => 'Validación de autenticación', 'descripcion' => 'Testing de mecanismos de login y sesiones'],
                    ['titulo' => 'Pruebas de autorización', 'descripcion' => 'Verificación de permisos y roles'],
                    ['titulo' => 'Testing de encriptación', 'descripcion' => 'Validación de datos encriptados'],
                    ['titulo' => 'Pruebas de configuración segura', 'descripcion' => 'Headers de seguridad y configuraciones'],
                    ['titulo' => 'Penetration testing ejecutado', 'descripcion' => 'Pruebas de penetración por especialista'],
                    ['titulo' => 'Reporte de seguridad generado', 'descripcion' => 'Documento con hallazgos y recomendaciones'],
                ]
            ],

            'testing_automatizado' => [
                'nombre' => 'Testing Automatizado',
                'descripcion' => 'Implementación de suite de pruebas automatizadas',
                'categoria' => 'Testing',
                'area' => 'testing',
                'items' => [
                    ['titulo' => 'Framework de testing seleccionado', 'descripcion' => 'Herramientas como Selenium, Cypress configuradas'],
                    ['titulo' => 'Tests unitarios automatizados', 'descripcion' => 'Suite completa de unit tests'],
                    ['titulo' => 'Tests de integración automatizados', 'descripcion' => 'API testing y integration tests'],
                    ['titulo' => 'Tests E2E implementados', 'descripcion' => 'End-to-end testing de flujos críticos'],
                    ['titulo' => 'CI/CD pipeline configurado', 'descripcion' => 'Tests ejecutándose en cada deployment'],
                    ['titulo' => 'Cobertura de código medida', 'descripcion' => 'Code coverage mínimo del 80% alcanzado'],
                    ['titulo' => 'Reportes automáticos configurados', 'descripcion' => 'Reportes de testing generados automáticamente'],
                    ['titulo' => 'Mantenimiento de tests planificado', 'descripcion' => 'Estrategia para mantener tests actualizados'],
                ]
            ],

            // ========== TEMPLATES ESPECÍFICOS ==========
            'pre_entrega' => [
                'nombre' => 'Pre-entrega de Proyecto',
                'descripcion' => 'Verificaciones esenciales antes de entregar un proyecto',
                'categoria' => 'Entrega',
                'area' => 'proyectos',
                'items' => [
                    ['titulo' => 'Código revisado y sin errores críticos', 'descripcion' => 'Code review completado y issues críticos resueltos'],
                    ['titulo' => 'Documentación técnica completa', 'descripcion' => 'API docs, README, y documentación de deployment'],
                    ['titulo' => 'Tests ejecutados exitosamente', 'descripcion' => 'Todas las pruebas unitarias y de integración pasan'],
                    ['titulo' => 'Base de datos migrada correctamente', 'descripcion' => 'Scripts de migración ejecutados sin errores'],
                    ['titulo' => 'Configuración de producción verificada', 'descripcion' => 'Variables de entorno y configs validadas'],
                    ['titulo' => 'Backup de seguridad realizado', 'descripcion' => 'Backup completo antes del deployment'],
                    ['titulo' => 'Performance validado', 'descripcion' => 'Tiempos de respuesta dentro de SLAs'],
                    ['titulo' => 'Seguridad auditada', 'descripcion' => 'Vulnerabilidades conocidas resueltas'],
                    ['titulo' => 'Manual de usuario entregado', 'descripcion' => 'Documentación para usuarios finales'],
                    ['titulo' => 'Plan de rollback preparado', 'descripcion' => 'Procedimiento de rollback documentado y probado'],
                ]
            ],

            'seguridad' => [
                'nombre' => 'Auditoría de Seguridad',
                'descripcion' => 'Checklist completo de verificaciones de seguridad',
                'categoria' => 'Seguridad',
                'area' => 'testing',
                'items' => [
                    ['titulo' => 'Validación de inputs implementada', 'descripcion' => 'Todos los inputs validados y sanitizados'],
                    ['titulo' => 'Autenticación robusta configurada', 'descripcion' => 'Sistema de login seguro con 2FA'],
                    ['titulo' => 'Autorización granular implementada', 'descripcion' => 'Permisos y roles correctamente definidos'],
                    ['titulo' => 'Encriptación de datos sensibles', 'descripcion' => 'Datos críticos encriptados en BD y tránsito'],
                    ['titulo' => 'Headers de seguridad configurados', 'descripcion' => 'HSTS, CSP, X-Frame-Options implementados'],
                    ['titulo' => 'Logs de seguridad activos', 'descripcion' => 'Auditoría de eventos de seguridad'],
                    ['titulo' => 'Gestión segura de sesiones', 'descripcion' => 'Timeout, renovación y invalidación de sesiones'],
                    ['titulo' => 'Protección contra ataques comunes', 'descripcion' => 'CSRF, XSS, SQL Injection mitigados'],
                    ['titulo' => 'Certificados SSL válidos', 'descripcion' => 'HTTPS configurado correctamente'],
                    ['titulo' => 'Copias de seguridad encriptadas', 'descripcion' => 'Backups protegidos y probados'],
                ]
            ],

            'calidad_codigo' => [
                'nombre' => 'Calidad de Código',
                'descripcion' => 'Verificaciones de calidad y estándares de código',
                'categoria' => 'Calidad',
                'area' => 'desarrollo',
                'items' => [
                    ['titulo' => 'Estándares de codificación aplicados', 'descripcion' => 'PSR, style guides y convenciones seguidas'],
                    ['titulo' => 'Code review completado', 'descripcion' => 'Revisión por pares de todo el código nuevo'],
                    ['titulo' => 'Métricas de calidad validadas', 'descripcion' => 'Cyclomatic complexity, duplicación controlada'],
                    ['titulo' => 'Refactoring aplicado', 'descripcion' => 'Código legacy mejorado donde sea necesario'],
                    ['titulo' => 'Documentación inline completa', 'descripcion' => 'Comentarios y docblocks apropiados'],
                    ['titulo' => 'Tests unitarios implementados', 'descripcion' => 'Cobertura mínima del 80% alcanzada'],
                    ['titulo' => 'Análisis estático ejecutado', 'descripcion' => 'SonarQube, PHPStan o similar ejecutado'],
                    ['titulo' => 'Dependencias actualizadas', 'descripcion' => 'Librerías y frameworks en versiones estables'],
                    ['titulo' => 'Performance optimizado', 'descripcion' => 'Queries optimizadas y caching implementado'],
                ]
            ],

            'deployment' => [
                'nombre' => 'Deployment y Go-Live',
                'descripcion' => 'Checklist para deployment seguro a producción',
                'categoria' => 'Entrega',
                'area' => 'proyectos',
                'items' => [
                    ['titulo' => 'Ambiente de producción preparado', 'descripcion' => 'Infraestructura configurada y probada'],
                    ['titulo' => 'Scripts de deployment validados', 'descripcion' => 'Procedimientos de deployment probados en staging'],
                    ['titulo' => 'Monitoreo configurado', 'descripcion' => 'Alertas y dashboards de monitoreo activos'],
                    ['titulo' => 'Backup pre-deployment realizado', 'descripcion' => 'Backup completo del estado actual'],
                    ['titulo' => 'DNS y dominio configurados', 'descripcion' => 'Configuración de DNS y certificados SSL'],
                    ['titulo' => 'Health checks implementados', 'descripcion' => 'Endpoints de salud para load balancers'],
                    ['titulo' => 'Rollback plan activado', 'descripcion' => 'Procedimiento de rollback listo y probado'],
                    ['titulo' => 'Smoke tests ejecutados', 'descripcion' => 'Pruebas básicas post-deployment'],
                    ['titulo' => 'Stakeholders notificados', 'descripcion' => 'Comunicación de go-live a usuarios y clientes'],
                    ['titulo' => 'Soporte post-deployment activado', 'descripcion' => 'Equipo en standby para issues inmediatos'],
                ]
            ],
        ];
    }
}