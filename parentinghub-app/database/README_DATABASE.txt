ParentingHub database - import guide

CONTENTS
parentinghub_database.sql : Full database for a NEW EMPTY database.
verify_database.sql      : Read-only checks after import.

WHAT IS INCLUDED
- All 31 original application tables and their supplied data are preserved,
  with the old products table renamed to legacy_products_archive (140 rows).
- A new products table contains all 287 products from the current catalog.
- Product prices, names, image links, categories, brands, descriptions, options,
  tags and the full original product JSON are retained.
- 3 triggers keep products synchronized when the catalog row is inserted,
  updated or deleted, including changes made through the existing PHP admin API.
- Existing user accounts, password hashes, sessions, orders, content and settings
  are carried over from the supplied snapshot. No new administrator is invented.
- Missing weights stay NULL; no physical weights or stock quantities are invented.
  in_stock preserves the catalog's availability flag; it is not a quantity count.

IMPORTANT ARCHITECTURE
The supplied PHP backend uses ph_users/ph_orders and ph_catalog_state.
ph_catalog_state (state_key='catalog') remains the catalog source of truth.
The new products table is its automatically maintained SQL projection.
Use the admin panel to edit products; direct edits to products alone will NOT
change the storefront and will be replaced at the next catalog update.
legacy_products_archive is historical and is not part of the current storefront.
The old users/orders/CMS tables remain for compatibility and are not merged into
ph_users/ph_orders. Existing articles/CMS tables may be older than catalog JSON.
This release fixes the conflicting product storage without redesigning the whole
website API. It is not a fully normalized rewrite of every application feature.

REQUIREMENTS
The main database was exported from MariaDB 10.11. The included verification file
is compatible with MariaDB 10.4+ and intentionally avoids JSON_TABLE(), so it also
works with common XAMPP MariaDB installations.
The import account needs CREATE, ALTER, INDEX, INSERT, UPDATE, DELETE, SELECT,
REFERENCES and TRIGGER privileges on the selected database.
No CREATE DATABASE or CREATE ROUTINE privilege is needed by this SQL file.
No hard-coded DEFINER account is embedded. Triggers use the importing account;
retain that account's required privileges after installation.

IMPORT WITH CPANEL / PHPMYADMIN
1. Back up the current live database. This file reflects only the supplied export,
   not orders/users created after that export.
2. Create a NEW database in cPanel (e.g. accountprefix_parentinghub_clean).
3. Add your database user to it with the required privileges.
4. In phpMyAdmin select that EMPTY database, choose Import, select
   parentinghub_database.sql and click Import.
5. Do not ignore any import error. DDL is not fully transactional: on failure,
   use another empty test database after fixing the reported issue.
6. Run verify_database.sql. Expected: 287 current products, 140 archived products,
   287 catalog products, 3 triggers, zero projection mismatches and a catalog-minus-products value of 0.
   If a previous attempt already created tables, DO NOT import the full database again;
   read CURRENT_DATABASE_RECOVERY.txt and run only verify_database.sql.
7. In the cleaned website, update backend/.env with the new DB_NAME, DB_USER,
   DB_PASS, DB_HOST and DB_PORT. Keep credentials private and do not post them here.
8. Test /api/health, product listing, existing login, admin catalog changes,
   parent dashboard and checkout on staging before switching the live site.

This file intentionally does not DROP, TRUNCATE or replace an existing live schema.
Importing over existing tables is unsupported. Keep the original dump as backup.
Product images themselves remain in website assets or external hosting; SQL stores
paths/URLs. External images and third-party services have not been checked here.
The application enforces the official fixed PKR 200 delivery fee per non-empty order.
Legacy shipping settings are removed/ignored; runtime checkout enforces one fixed PKR 200 delivery fee per order.
