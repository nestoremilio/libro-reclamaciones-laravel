<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclamaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();

            // 1. Identificación del consumidor
            $table->string('nombres_apellidos', 200);
            $table->string('tipo_documento', 20);
            $table->string('numero_documento', 20)->index();
            $table->string('domicilio', 300);
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 150);
            $table->boolean('es_menor_edad')->default(false);
            $table->string('apoderado_nombre', 200)->nullable();

            // 2. Identificación del bien contratado
            $table->string('tipo_bien', 20);                 // producto | servicio
            $table->decimal('monto_reclamado', 10, 2)->nullable();
            $table->string('descripcion_bien', 500);

            // 3. Detalle de la reclamación y pedido del consumidor
            $table->string('tipo_registro', 20);             // reclamo | queja
            $table->text('detalle');
            $table->text('pedido');
            $table->string('evidencia_path')->nullable();

            // Declaraciones
            $table->boolean('acepta_politicas')->default(false);
            $table->boolean('declaracion_veracidad')->default(false);

            // 4. Observaciones y acciones adoptadas por el proveedor
            $table->string('estado', 20)->default('pendiente'); // pendiente | atendido
            $table->date('fecha_limite');
            $table->text('respuesta')->nullable();
            $table->timestamp('respondido_at')->nullable();
            $table->foreignId('respondido_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamaciones');
    }
};
