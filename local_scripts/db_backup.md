ddev drush sql:dump \
  --extra-dump=--single-transaction \
  --extra-dump=--quick \
  --extra-dump=--skip-lock-tables \
| gzip > db_backup/db-$(date +%F-%H%M).sql.gz


vendor/drush/drush/drush sql:dump \
  --extra-dump=--single-transaction \
  --extra-dump=--quick \
  --extra-dump=--skip-lock-tables \
| gzip > db_backup/db-$(date +%F-%H%M)_before_upgrade11.sql.gz



###
--extra-dump=--single-transaction 是为了在“网站还在跑”的情况下，保证备份出来的是一个“时间点一致”的数据库快照，并且不锁表。



vendor/drush/drush/drush scr local_scripts/openai/step_01_export_page_en_for_translation.php



vendor/drush/drush/drush scr local_scripts/step_03_translate_en_node_bulk.php


<!-- upload -->
scp -i /Users/Dong/Documents/tou/aliyun/onebandwebsite_key.pem \
/Users/dong/Documents/tao/git/www/onesystem/web/sites/default/files/private/translate/terms_translated_en.jsonl \
root@39.99.178.28:/var/www/html/onebandsystem/web/sites/default/files/private/translate/terms_translated_en.jsonl



<!-- download -->
scp -i /Users/Dong/Documents/tou/aliyun/onebandwebsite_key.pem root@39.99.178.28:/var/www/html/onebandsystem/web/sites/default/files/private/translate/terms_export_zh.jsonl /Users/dong/Downloads/terms_export_zh.jsonl

scp -i /Users/Dong/Documents/tou/aliyun/onebandwebsite_key.pem root@39.99.178.28:/var/www/html/onebandsystem/db_backup/db-2026-09-16-2154_before_upgrade10.sql.gz /Users/dong/Downloads/


vendor/drush/drush/drush scr local_scripts/openai/term/step_03_import_terms_en_translation.php




