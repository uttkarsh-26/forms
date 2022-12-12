<?php

namespace Drupal\amapceo_radar\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RadarFormsAccessCheck.
 */
class RadarFormsAccessCheck implements AccessInterface {

  public function access(Route $route, Request $request, AccountInterface $account) {
    if ($account->isAnonymous()) {
      return AccessResult::forbidden();
    }
    return AccessResult::allowed();
    //return AccessResult::forbidden();
    //return AccessResult::neutral();
  }

}
