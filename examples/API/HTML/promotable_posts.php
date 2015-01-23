<?php
namespace FacebookAds\Object;
use FacebookAds\Cursor;
use FacebookAds\Object\AbstractArchivableCrudObject;

class PagePosts extends AbstractCrudObject {
  public function getStats(array $fields = array(), array $params = array()) {
    return $this->getManyByConnection(
      '476563889048827', $fields, $params, 'promotable_posts');
  }
}

$fields = array('name',);
PagePosts::getStats($fields,array());

?>
