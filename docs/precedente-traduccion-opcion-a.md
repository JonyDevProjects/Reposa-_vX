# Precedente: Opción A — Columnas traducibles manualmente

## Concepto
Agregar columnas `*_es` y `*_en` directamente a las tablas que contienen
texto traducible (products, categories).

## Migración ejemplo
```php
// database/migrations/xxxx_add_translatable_columns_to_products.php
Schema::table('products', function (Blueprint $table) {
    $table->string('name_en')->nullable()->after('name');
    $table->text('description_en')->nullable()->after('description');
    $table->string('material_en')->nullable()->after('material');
});

Schema::table('categories', function (Blueprint $table) {
    $table->string('name_en')->nullable()->after('name');
});
```

## Modelo ejemplo
```php
class Product extends Model
{
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"name_{$locale}"} ?? $this->name;
    }

    public function getDescriptionAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->{"description_{$locale}"} ?? $this->description;
    }
}
```

## Seeders
```php
Product::create([
    'name' => 'Almohada Viscoelástica Premium',
    'name_en' => 'Premium Viscoelastic Pillow',
    'description' => 'Espuma de memoria de alta densidad...',
    'description_en' => 'High-density memory foam...',
    // ...
]);
```

## Ventajas
- Simple, sin dependencias externas
- Control total sobre cada columna
- Fácil de entender y mantener

## Desventajas
- Cada columna traducible necesita duplicarse (name → name_es, name_en)
- Agregar un nuevo idioma requiere migración
- El modelo crece con métodos accessor por cada campo
- No soporta fallback automático a otro idioma

---

# Implementación: Opción B — spatie/laravel-translatable

(Ver implementación en la rama feature/translatable)
