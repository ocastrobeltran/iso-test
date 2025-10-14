<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('calificacion', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('puntaje')->nullable();
            $table->text('comentario')->nullable();
            $table->integer('usuario_id')->nullable()->index('usuario_id');
            $table->integer('ticket_id')->nullable()->index('ticket_id');
        });

        Schema::create('cambio', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('descripcion')->nullable();
            $table->date('fecha')->nullable();
            $table->string('estado', 255)->nullable();
            $table->integer('usuario_id')->nullable()->index('usuario_id');
            $table->integer('proyecto_id')->nullable()->index('proyecto_id');
        });

        Schema::create('checklist', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('criterios')->nullable();
            $table->string('estado', 255)->nullable();
            $table->integer('proyecto_id')->nullable()->index('proyecto_id');
        });

        Schema::create('configuracion', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('version', 255)->nullable();
            $table->string('titulo', 255)->nullable();
            $table->text('descripcion')->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->integer('proyecto_id')->nullable()->index('proyecto_id');
        });

        Schema::create('contrato', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('total_horas')->nullable();
            $table->string('estado', 255)->nullable();
            $table->integer('cliente_id')->nullable()->index('cliente_id');
        });

        Schema::create('contrato_proveedor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('contrato_id')->index('contrato_id');
            $table->integer('proveedor_id')->index('proveedor_id');
            $table->unique(['contrato_id', 'proveedor_id']);
        });

        Schema::create('cronograma', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('estado', 255)->nullable();
            $table->integer('proyecto_id')->nullable()->index('proyecto_id');
            $table->integer('usuario_id')->nullable()->index('usuario_id');
        });

        Schema::create('detalleconsumo', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('actividad')->nullable();
            $table->text('modulo')->nullable();
            $table->float('horas')->nullable();
            $table->integer('usuario_id')->nullable()->index('usuario_id');
        });

        Schema::create('diagnostico', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('descripcion')->nullable();
            $table->text('resultado')->nullable();
            $table->boolean('es_recurrente')->nullable();
            $table->integer('ticket_id')->nullable()->index('ticket_id');
            $table->integer('usuario_id')->nullable()->index('usuario_id');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('historial', function (Blueprint $table) {
            $table->integer('id', true);
            $table->date('fecha')->nullable();
            $table->text('descripcion')->nullable();
            $table->integer('usuario_id')->nullable()->index('usuario_id');
            $table->integer('proyecto_id')->nullable()->index('proyecto_id');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('mejora', function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('origen')->nullable();
            $table->text('descripcion')->nullable();
            $table->date('fecha_propuesta')->nullable();
            $table->string('estado', 255)->nullable();
            $table->integer('proyecto_id')->nullable()->index('proyecto_id');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('proveedor', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre', 255)->nullable();
            $table->string('tipo_servicio', 255)->nullable();
            $table->text('contacto')->nullable();
        });

        Schema::create('proyecto', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre', 255)->nullable();
            $table->string('estado', 255)->nullable();
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->text('objetivos')->nullable();
            $table->text('riesgos')->nullable();
        });

        Schema::create('proyecto_contrato', function (Blueprint $table) {
            $table->bigIncrements('id'); // Nuevo campo autoincremental y PRIMARY KEY
            $table->integer('proyecto_id');
            $table->integer('contrato_id')->index('contrato_id');
            $table->unique(['proyecto_id', 'contrato_id']); // Evita duplicados
        });

        Schema::create('proyecto_detalleconsumo', function (Blueprint $table) {
            $table->integer('proyecto_id');
            $table->integer('detalle_id')->index('detalle_id');

            $table->primary(['proyecto_id', 'detalle_id']);
        });

        Schema::create('proyecto_servicio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('proyecto_id');
            $table->integer('servicio_id')->index('servicio_id');
            $table->unique(['proyecto_id', 'servicio_id']); 
        });

        Schema::create('proyecto_ticket', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedBigInteger('ticket_id');
            $table->unique(['proyecto_id', 'ticket_id']);
            $table->foreign('proyecto_id')->references('id')->on('proyecto')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign('ticket_id')->references('id')->on('ticket')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::create('recursosproyectos', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('tipo', 255)->nullable();
            $table->text('ubicacion')->nullable();
            $table->integer('proyecto_id')->nullable()->index('proyecto_id');
        });

        Schema::create('servicio', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre', 255)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('prioridad', 255)->nullable();
            $table->string('impacto', 255)->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('ticket', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('titulo', 255)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('estado', 255)->nullable();
            $table->dateTime('fecha_creacion')->nullable();
            $table->dateTime('fecha_asignacion')->nullable();
            $table->dateTime('fecha_cierre')->nullable();
            $table->dateTime('fecha_resolucion')->nullable();
            $table->text('solucion')->nullable();
            $table->json('comentarios')->nullable();
        });

        Schema::create('ticket_detalleconsumo', function (Blueprint $table) {
            $table->integer('ticket_id');
            $table->integer('detalle_id')->index('detalle_id');

            $table->primary(['ticket_id', 'detalle_id']);
        });

        Schema::create('ticket_servicio', function (Blueprint $table) {
            $table->bigIncrements('id'); 
            $table->integer('ticket_id');
            $table->integer('servicio_id')->index('servicio_id');
            $table->unique(['ticket_id', 'servicio_id']);
        });

        Schema::create('ticket_usuario', function (Blueprint $table) {
            $table->integer('ticket_id');
            $table->integer('usuario_id')->index('usuario_id');
            $table->string('rol', 255)->nullable();

            $table->primary(['ticket_id', 'usuario_id']);
        });

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('rol', 255)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('calificacion', function (Blueprint $table) {
            $table->foreign(['usuario_id'], 'calificacion_ibfk_1')->references(['id'])->on('usuario')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ticket_id'], 'calificacion_ibfk_2')->references(['id'])->on('ticket')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('cambio', function (Blueprint $table) {
            $table->foreign(['usuario_id'], 'cambio_ibfk_1')->references(['id'])->on('usuario')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['proyecto_id'], 'cambio_ibfk_2')->references(['id'])->on('proyecto')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('checklist', function (Blueprint $table) {
            $table->foreign(['proyecto_id'], 'checklist_ibfk_1')->references(['id'])->on('proyecto')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('configuracion', function (Blueprint $table) {
            $table->foreign(['proyecto_id'], 'configuracion_ibfk_1')->references(['id'])->on('proyecto')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('contrato', function (Blueprint $table) {
            $table->foreign(['cliente_id'], 'contrato_ibfk_1')->references(['id'])->on('usuario')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('contrato_proveedor', function (Blueprint $table) {
            $table->foreign(['proveedor_id'], 'contrato_proveedor_ibfk_1')->references(['id'])->on('proveedor')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['contrato_id'], 'contrato_proveedor_ibfk_2')->references(['id'])->on('contrato')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('cronograma', function (Blueprint $table) {
            $table->foreign(['proyecto_id'], 'cronograma_ibfk_1')->references(['id'])->on('proyecto')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['usuario_id'], 'cronograma_ibfk_2')->references(['id'])->on('usuario')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('detalleconsumo', function (Blueprint $table) {
            $table->foreign(['usuario_id'], 'detalleconsumo_ibfk_1')->references(['id'])->on('usuario')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('diagnostico', function (Blueprint $table) {
            $table->foreign(['ticket_id'], 'diagnostico_ibfk_1')->references(['id'])->on('ticket')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['usuario_id'], 'diagnostico_ibfk_2')->references(['id'])->on('usuario')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('historial', function (Blueprint $table) {
            $table->foreign(['usuario_id'], 'historial_ibfk_1')->references(['id'])->on('usuario')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['proyecto_id'], 'historial_ibfk_2')->references(['id'])->on('proyecto')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('mejora', function (Blueprint $table) {
            $table->foreign(['proyecto_id'], 'mejora_ibfk_1')->references(['id'])->on('proyecto')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('proyecto_contrato', function (Blueprint $table) {
            $table->foreign(['proyecto_id'], 'proyecto_contrato_ibfk_1')->references(['id'])->on('proyecto')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['contrato_id'], 'proyecto_contrato_ibfk_2')->references(['id'])->on('contrato')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('proyecto_detalleconsumo', function (Blueprint $table) {
            $table->foreign(['proyecto_id'], 'proyecto_detalleconsumo_ibfk_1')->references(['id'])->on('proyecto')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['detalle_id'], 'proyecto_detalleconsumo_ibfk_2')->references(['id'])->on('detalleconsumo')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('proyecto_servicio', function (Blueprint $table) {
            $table->foreign(['proyecto_id'], 'proyecto_servicio_ibfk_1')->references(['id'])->on('proyecto')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['servicio_id'], 'proyecto_servicio_ibfk_2')->references(['id'])->on('servicio')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('proyecto_ticket', function (Blueprint $table) {
            $table->foreign(['proyecto_id'], 'proyecto_ticket_ibfk_1')->references(['id'])->on('proyecto')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ticket_id'], 'proyecto_ticket_ibfk_2')->references(['id'])->on('ticket')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('recursosproyectos', function (Blueprint $table) {
            $table->foreign(['proyecto_id'], 'recursosproyectos_ibfk_1')->references(['id'])->on('proyecto')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('ticket_detalleconsumo', function (Blueprint $table) {
            $table->foreign(['ticket_id'], 'ticket_detalleconsumo_ibfk_1')->references(['id'])->on('ticket')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['detalle_id'], 'ticket_detalleconsumo_ibfk_2')->references(['id'])->on('detalleconsumo')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('ticket_servicio', function (Blueprint $table) {
            $table->foreign(['ticket_id'], 'ticket_servicio_ibfk_1')->references(['id'])->on('ticket')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['servicio_id'], 'ticket_servicio_ibfk_2')->references(['id'])->on('servicio')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('ticket_usuario', function (Blueprint $table) {
            $table->foreign(['usuario_id'], 'ticket_usuario_ibfk_1')->references(['id'])->on('usuario')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ticket_id'], 'ticket_usuario_ibfk_2')->references(['id'])->on('ticket')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->primary(['permission_id', 'model_id', 'model_type'],
                'model_has_permissions_permission_model_type_primary');
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->primary(['role_id', 'model_id', 'model_type'],
                'model_has_roles_role_model_type_primary');
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->primary(['permission_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_usuario', function (Blueprint $table) {
            $table->dropForeign('ticket_usuario_ibfk_1');
            $table->dropForeign('ticket_usuario_ibfk_2');
        });

        Schema::table('ticket_servicio', function (Blueprint $table) {
            $table->dropForeign('ticket_servicio_ibfk_1');
            $table->dropForeign('ticket_servicio_ibfk_2');
        });

        Schema::table('ticket_detalleconsumo', function (Blueprint $table) {
            $table->dropForeign('ticket_detalleconsumo_ibfk_1');
            $table->dropForeign('ticket_detalleconsumo_ibfk_2');
        });

        Schema::table('recursosproyectos', function (Blueprint $table) {
            $table->dropForeign('recursosproyectos_ibfk_1');
        });

        Schema::table('proyecto_ticket', function (Blueprint $table) {
            $table->dropForeign('proyecto_ticket_ibfk_1');
            $table->dropForeign('proyecto_ticket_ibfk_2');
        });

        Schema::table('proyecto_servicio', function (Blueprint $table) {
            $table->dropForeign('proyecto_servicio_ibfk_1');
            $table->dropForeign('proyecto_servicio_ibfk_2');
        });

        Schema::table('proyecto_detalleconsumo', function (Blueprint $table) {
            $table->dropForeign('proyecto_detalleconsumo_ibfk_1');
            $table->dropForeign('proyecto_detalleconsumo_ibfk_2');
        });

        Schema::table('proyecto_contrato', function (Blueprint $table) {
            $table->dropForeign('proyecto_contrato_ibfk_1');
            $table->dropForeign('proyecto_contrato_ibfk_2');
        });

        Schema::table('mejora', function (Blueprint $table) {
            $table->dropForeign('mejora_ibfk_1');
        });

        Schema::table('historial', function (Blueprint $table) {
            $table->dropForeign('historial_ibfk_1');
            $table->dropForeign('historial_ibfk_2');
        });

        Schema::table('diagnostico', function (Blueprint $table) {
            $table->dropForeign('diagnostico_ibfk_1');
            $table->dropForeign('diagnostico_ibfk_2');
        });

        Schema::table('detalleconsumo', function (Blueprint $table) {
            $table->dropForeign('detalleconsumo_ibfk_1');
        });

        Schema::table('cronograma', function (Blueprint $table) {
            $table->dropForeign('cronograma_ibfk_1');
            $table->dropForeign('cronograma_ibfk_2');
        });

        Schema::table('contrato_proveedor', function (Blueprint $table) {
            $table->dropForeign('contrato_proveedor_ibfk_1');
            $table->dropForeign('contrato_proveedor_ibfk_2');
        });

        Schema::table('contrato', function (Blueprint $table) {
            $table->dropForeign('contrato_ibfk_1');
        });

        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropForeign('configuracion_ibfk_1');
        });

        Schema::table('checklist', function (Blueprint $table) {
            $table->dropForeign('checklist_ibfk_1');
        });

        Schema::table('cambio', function (Blueprint $table) {
            $table->dropForeign('cambio_ibfk_1');
            $table->dropForeign('cambio_ibfk_2');
        });

        Schema::table('calificacion', function (Blueprint $table) {
            $table->dropForeign('calificacion_ibfk_1');
            $table->dropForeign('calificacion_ibfk_2');
        });

        Schema::dropIfExists('usuario');

        Schema::dropIfExists('users');

        Schema::dropIfExists('ticket_usuario');

        Schema::dropIfExists('ticket_servicio');

        Schema::dropIfExists('ticket_detalleconsumo');

        Schema::dropIfExists('ticket');

        Schema::dropIfExists('sessions');

        Schema::dropIfExists('servicio');

        Schema::dropIfExists('recursosproyectos');

        Schema::dropIfExists('proyecto_ticket');

        Schema::dropIfExists('proyecto_servicio');

        Schema::dropIfExists('proyecto_detalleconsumo');

        Schema::dropIfExists('proyecto_contrato');

        Schema::dropIfExists('proyecto');

        Schema::dropIfExists('proveedor');

        Schema::dropIfExists('password_reset_tokens');

        Schema::dropIfExists('mejora');

        Schema::dropIfExists('jobs');

        Schema::dropIfExists('job_batches');

        Schema::dropIfExists('historial');

        Schema::dropIfExists('failed_jobs');

        Schema::dropIfExists('diagnostico');

        Schema::dropIfExists('detalleconsumo');

        Schema::dropIfExists('cronograma');

        Schema::dropIfExists('contrato_proveedor');

        Schema::dropIfExists('contrato');

        Schema::dropIfExists('configuracion');

        Schema::dropIfExists('checklist');

        Schema::dropIfExists('cambio');

        Schema::dropIfExists('calificacion');

        Schema::dropIfExists('cache_locks');

        Schema::dropIfExists('cache');
    }
};
