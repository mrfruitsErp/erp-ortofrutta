public function up(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->string('status')->default('draft')->change();
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->string('status')->default('open')->change();
    });
}