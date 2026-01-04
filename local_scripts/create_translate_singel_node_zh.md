###
# 中文singel node 创建
###
ddev drush ev '
use Drupal\node\Entity\Node;

$nid = 403;
$lang = "zh-hans";

$node = Node::load($nid);
if (!$node) throw new \Exception("Node not found");

if (!$node->hasTranslation($lang)) {
  $translation = $node->addTranslation($lang, $node->toArray());
  $translation->setPublished($node->isPublished());
  if ($translation->hasField("moderation_state") && $node->hasField("moderation_state")) {
    $translation->set("moderation_state", $node->get("moderation_state")->value);
  }
  $node->save();
  print "Translation created\n";
} else {
  print "Translation already exists\n";
}
'
ddev drush cr


###
# singel node URL Alias 复制
###
ddev drush sqlq "
INSERT INTO wan_path_alias (uuid, path, alias, langcode, status)
SELECT UUID(), path, alias, 'zh-hans', status
FROM wan_path_alias
WHERE alias='/product/4g5g直播产品/liveu/lu200全新4G直播系统' AND langcode='en'
LIMIT 1;
"
ddev drush cr
