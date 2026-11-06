-- ParentingHub post-import verification
-- Compatible with MariaDB 10.4+ / common XAMPP MariaDB builds.
-- This file is READ-ONLY. It does not modify any data.
-- It intentionally avoids JSON_TABLE(), which is unavailable in older MariaDB versions.

SELECT VERSION() AS database_version;

SELECT COUNT(*) AS current_products_expected_287
FROM products;

SELECT COUNT(*) AS archived_products_expected_140
FROM legacy_products_archive;

SELECT JSON_LENGTH(payload, '$.products') AS catalog_products_expected_287
FROM ph_catalog_state
WHERE state_key = 'catalog';

SELECT COUNT(*) AS sync_triggers_expected_3
FROM information_schema.TRIGGERS
WHERE TRIGGER_SCHEMA = DATABASE()
  AND TRIGGER_NAME IN (
    'ph_catalog_products_after_insert',
    'ph_catalog_products_after_update',
    'ph_catalog_products_after_delete'
  );

-- The products table stores the original product JSON in source_json.
-- These checks verify that the SQL projection matches its source JSON without JSON_TABLE().
SELECT COUNT(*) AS product_projection_mismatches_expected_0
FROM products p
WHERE COALESCE(JSON_UNQUOTE(JSON_EXTRACT(p.source_json, '$.id')), '') <> p.id
   OR COALESCE(JSON_UNQUOTE(JSON_EXTRACT(p.source_json, '$.name')), '') <> p.name
   OR CAST(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p.source_json, '$.price')), 'null') AS DECIMAL(12,2)) <> p.price;

SELECT
  (SELECT JSON_LENGTH(payload, '$.products')
   FROM ph_catalog_state
   WHERE state_key = 'catalog'
   LIMIT 1)
  -
  (SELECT COUNT(*) FROM products)
  AS catalog_minus_products_expected_0;

SELECT brand, COUNT(*) AS products
FROM products
GROUP BY brand
ORDER BY products DESC;

SELECT COUNT(*) AS existing_user_accounts
FROM ph_users;

SELECT COUNT(*) AS existing_orders
FROM ph_orders;
