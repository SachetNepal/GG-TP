# GroceryGo Oracle Backend Notes

This project is designed to run Laravel against an existing Oracle schema managed with Oracle APEX.

## 1) Connection and driver

- `config/database.php` includes an `oracle` connection.
- Set `.env`:

```env
DB_CONNECTION=oracle
DB_HOST=127.0.0.1
DB_PORT=1521
DB_DATABASE=FREEPDB1
DB_USERNAME=your_schema_user
DB_PASSWORD=your_schema_password
DB_CHARSET=AL32UTF8
```

- Install OCI8 Laravel package:

```bash
composer require yajra/laravel-oci8
```

## 2) Schema-first modeling

- Models map directly to existing Oracle tables.
- Primary keys and table names are explicitly defined.
- No admin frontend is implemented in Laravel; Oracle APEX remains admin UI.

## 3) Oracle compatibility choices

- Explicit table names like `PRODUCT`, `ORDER_ITEM`, `COLLECTION_SLOT`.
- Explicit primary keys (`*_id`).
- Timestamps disabled where schema differs from Laravel defaults.
- Queries avoid MySQL-only syntax.

## 4) API structure

- `routes/api.php` includes:
  - Auth
  - Catalog
  - Basket/Checkout/Orders
  - Trader product management/dashboard
  - Reviews and discounts
- Role middleware: `role:customer`, `role:trader`.

