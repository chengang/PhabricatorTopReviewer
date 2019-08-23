<?php

final class PhabricatorTRApplication extends PhabricatorApplication {

  public function getBaseURI() {
    return '/topreviewer/';
  }

  public function getName() {
    return pht('TopReviewer');
  }

  public function getShortDescription() {
    return pht('Lovely people');
  }

  public function getFontIcon() {
    return 'fa-user-secret';
  }

  public function getTitleGlyph() {
    return "\xE2\x98\x84";
  }

  public function getApplicationGroup() {
    return self::GROUP_DEVELOPER;
  }

  public function isPinnedByDefault($viewer) {
    return true;
  }

  public function getRoutes() {
    return array(
      '/topreviewer/' => array(
        '' => 'PhabricatorTRController',
      ),
    );
  }

}
