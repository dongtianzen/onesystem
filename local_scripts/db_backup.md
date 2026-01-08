ddev drush sql:dump \
  --extra-dump=--single-transaction \
  --extra-dump=--quick \
  --extra-dump=--skip-lock-tables \
| gzip > db_backup/db-$(date +%F-%H%M).sql.gz


vendor/drush/drush/drush sql:dump \
  --extra-dump=--single-transaction \
  --extra-dump=--quick \
  --extra-dump=--skip-lock-tables \
| gzip > db_backup/db-$(date +%F-%H%M)_before_translate_en_nodes.sql.gz



###
--extra-dump=--single-transaction 是为了在“网站还在跑”的情况下，保证备份出来的是一个“时间点一致”的数据库快照，并且不锁表。



vendor/drush/drush/drush scr local_scripts/add_alias_translation_bulk.php -- --source=en --target=zh-hans --limit=10 --dry-run



vendor/drush/drush/drush scr local_scripts/step_03_translate_en_node_bulk.php


<!-- upload -->
scp -i /Users/Dong/Documents/tou/aliyun/onebandwebsite_key.pem \
/Users/dong/Documents/tao/git/www/onesystem/web/sites/default/files/private/translate/translated_page_en.jsonl \
root@39.99.178.28:/var/www/html/onebandsystem/web/sites/default/files/private/translate/



<!-- download -->
scp -i /Users/Dong/Documents/tou/aliyun/onebandwebsite_key.pem root@39.99.178.28:/var/www/html/onebandsystem/web/sites/default/files/private/translate/ /Users/dong/Downloads/


ddev drush scr local_scripts/openai/step_02_translate_jsonl_openai.php


ddev drush scr local_scripts/openai/step_03_import_translated_en_jsonl.php
