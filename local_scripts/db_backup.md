ddev drush sql:dump \
  --extra-dump=--single-transaction \
  --extra-dump=--quick \
  --extra-dump=--skip-lock-tables \
| gzip > db_backup/db-$(date +%F-%H%M).sql.gz


vendor/drush/drush/drush sql:dump \
  --extra-dump=--single-transaction \
  --extra-dump=--quick \
  --extra-dump=--skip-lock-tables \
| gzip > db_backup/db-$(date +%F-%H%M)_after_create_url_alias.sql.gz



###
--extra-dump=--single-transaction 是为了在“网站还在跑”的情况下，保证备份出来的是一个“时间点一致”的数据库快照，并且不锁表。



vendor/drush/drush/drush scr local_scripts/add_alias_translation_bulk.php -- --source=en --target=zh-hans --limit=10 --dry-run
