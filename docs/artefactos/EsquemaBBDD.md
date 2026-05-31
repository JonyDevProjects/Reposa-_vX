# Esquema de Base de Datos (E-Commerce de Almohadas)

Este diagrama UML estructurado en formato Entidad-Relación representa la base de datos para la tienda de almohadas ergonómicas. Ha sido refactorizado para cumplir fielmente con el informe `Inf-E-Comerce.md` y los requisitos de la EPD3 (Asegurando la existencia de relaciones 1:1, 1:N y N:M con el desglose de tablas pivote requeridas para Laravel).

## Esquema Genérico
```mermaid
erDiagram
    USER ||--|| PROFILE : "tiene un (1:1)"
    USER ||--o{ ADDRESS : "registra (1:N)"
    USER ||--o{ ORDER : "realiza (1:N)"
    USER }|--|{ PRODUCT : "marcar como favorito (N:M)"
    USER ||--o{ PAYMENT_METHOD : "usa (1:N)"

    CATEGORY ||--o{ PRODUCT : "clasifica (1:N)"
    
    PRODUCT ||--o{ ORDER_ITEM : "incluido en (1:N)"
    ORDER ||--|{ ORDER_ITEM : "contiene (1:N)"
    
    PRODUCT }|--|{ DISCOUNT : "se le aplica (N:M)"
    
    USER ||--o{ CART_ITEM : "tiene en su cesta (1:N)"
    PRODUCT ||--o{ CART_ITEM : "está en cestas (1:N)"
    ORDER ||--|| PAYMENT_METHOD : "pagado con (N:1)"

    USER {
        int id PK
        string email
        string password
        datetime created_at
    }

    PROFILE {
        int id PK
        int user_id FK
        string full_name
        string phone
    }

    ADDRESS {
        int id PK
        int user_id FK
        string street
        string city
        string zip_code
        boolean is_main
    }

    PRODUCT {
        int id PK
        int category_id FK
        string name
        float price
        int stock
    }

    CATEGORY {
        int id PK
        string name
        string slug
    }

    ORDER {
        int id PK
        int user_id FK
        float total_amount
        string status
        datetime order_date
    }

    ORDER_ITEM {
        int id PK
        int order_id FK
        int product_id FK
        int quantity
        float price_at_purchase
    }

    DISCOUNT {
        int id PK
        string code
        int percentage
        datetime valid_until
    }

    CART_ITEM {
        int id PK
        int user_id FK
        int product_id FK
        int quantity
    }

    PAYMENT_METHOD{
        int id PK
        int user_id FK
        string provider
        hash token
    }
```

## Esquema Refactorizado
```mermaid
erDiagram
    %% Relaciones 1:1
    USER ||--|| PROFILE : "tiene un (1:1)"
    
    %% Relaciones 1:N
    USER ||--o{ ADDRESS : "registra (1:N)"
    USER ||--o{ ORDER : "realiza (1:N)"
    USER ||--o{ CART_ITEM : "tiene en su cesta (1:N)"
    ORDER ||--|{ ORDER_ITEM : "contiene (1:N)"
    PRODUCT ||--o{ ORDER_ITEM : "incluido en (1:N)"
    PRODUCT ||--o{ CART_ITEM : "está en cestas (1:N)"

    %% Relaciones N:M explícitas usando Tablas Pivote (Problema 3 y 5)
    USER ||--o{ FAVORITE_PRODUCT : "marca favorito"
    PRODUCT ||--o{ FAVORITE_PRODUCT : "es marcado"
    
    CATEGORY ||--o{ CATEGORY_PRODUCT : "clasifica"
    PRODUCT ||--o{ CATEGORY_PRODUCT : "pertenece a"

    %% Definición de Entidades y Atributos

    USER {
        int id PK
        string name
        string email
        string password
        datetime created_at
    }

    PROFILE {
        int id PK
        int user_id FK
        string full_name
        string phone
        string sleep_preference "Ej. Insomnio, Dolor Cervical"
    }

    ADDRESS {
        int id PK
        int user_id FK
        string street
        string city
        string zip_code
        boolean is_main
    }

    PRODUCT {
        int id PK
        string name
        string material "Viscoelástica, Látex, etc."
        string firmness "Alta, Media, Baja"
        string dimensions "Dimensiones almohada"
        float price
        int stock
        text description
    }

    CATEGORY {
        int id PK
        string name "Ej. Cervical, Anti-Ronquidos"
        string slug
    }

    CATEGORY_PRODUCT {
        int category_id PK, FK
        int product_id PK, FK
    }

    FAVORITE_PRODUCT {
        int user_id PK, FK
        int product_id PK, FK
    }

    ORDER {
        int id PK
        int user_id FK
        float total_amount
        string status
        datetime order_date
    }

    ORDER_ITEM {
        int id PK
        int order_id FK
        int product_id FK
        int quantity
        float price_at_purchase
    }

    CART_ITEM {
        int id PK
        int user_id FK
        int product_id FK
        int quantity
    }
```

## Justificación de la Refactorización

La refactorización de este diagrama UML obedece a razones clave tanto de negocio (adaptación al nicho) como técnicas (alineación con Laravel y los problemas de la EPD3):

1. **Adaptación al Nicho de Mercado**: Se ha ampliado la entidad `PRODUCT` con atributos clave específicos de almohadas, como `material` (viscoelástica, látex), `firmness` (firmeza de la almohada) y `dimensions`. De igual forma, la entidad `PROFILE` ahora permite registrar la preferencia de sueño del usuario (`sleep_preference`), posibilitando luego recomendaciones hiper-personalizadas (ej. almohadas para dolor cervical o insomnio).
2. **Cumplimiento Estricto del Problema 3 (Categorías)**: El esquema genérico original contemplaba erróneamente una relación 1:N estructural. Se ha eliminado y sustituido por la tabla pivote `CATEGORY_PRODUCT`, haciendo explícita la relación N:M exigida por el enunciado; así, una almohada concreta puede pertenecer a múltiples categorías simultáneas (ej. "Anti-ronquidos" y "Ortopédica") y viceversa.
3. **Cumplimiento Estricto del Problema 5 (Favoritos)**: Para cumplir con la funcionalidad de la *Wishlist* orientada a las analíticas de administración ("likes"), se añadió de manera patente la tabla pivote `FAVORITE_PRODUCT`. Esto materializa correctamente la relación cardinal N:M entre `USER` y `PRODUCT`.
4. **Traducción directa hacia Laravel Eloquent**: Al separar y exponer explícitamente las tablas pivote (`CATEGORY_PRODUCT` y `FAVORITE_PRODUCT`) en el esquema en lugar de dejarlas implícitas, se mapea con exactitud milimétrica la naturaleza de la base de datos relacional y cómo el futuro ORM de código (relaciones `belongsToMany`) las gestionará. Esto sirve como el plano exacto para la inminente creación de nuestras Migraciones SQL.
