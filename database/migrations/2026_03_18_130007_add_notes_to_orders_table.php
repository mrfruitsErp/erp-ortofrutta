public function up(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->text('notes')->nullable()->after('delivery_slot');
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn('notes');
    });
}