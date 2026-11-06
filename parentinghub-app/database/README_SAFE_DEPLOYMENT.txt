This production update intentionally does NOT include a full database dump.
Do not replace the live ParentingHub database.

The application now contains safe additive one-time migrations for:
- community integrity tables/counters
- the six known previously published Learn guides if they are missing

Normally no manual SQL import is required. If the automatic article repair cannot run because the cPanel DB user lacks CREATE/INSERT permission, use ONLY:
  seo_article_repair_safe.sql
It uses INSERT IGNORE and does not update/delete products, orders, users, or existing article rows.
