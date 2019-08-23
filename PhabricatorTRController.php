<?php

final class PhabricatorTRController
  extends PhabricatorXHProfController {

  public function shouldAllowPublic() {
    return true;
  }

  public function handleRequest(AphrontRequest $request) {
    $viewer = $request->getViewer();

    $actions = id(new DifferentialTransaction())->loadAllWhere(
      '%Q ORDER BY id DESC LIMIT %d, %d',
      "transactionType='differential:action' and newValue='\"accept\"'", 0, 10);

    $top_reviewers = array();
    foreach ($actions as $action) {
      $author_phid = $action->getAuthorPHID();
      $user = id(new PhabricatorPeopleQuery())
        ->setViewer($viewer)
        ->withPHIDs(array($author_phid))
        ->executeOne();
      $top_reviewer_real_name = $user->getRealName();
      if (array_key_exists($top_reviewer_real_name, $top_reviewers))
        $top_reviewers[$top_reviewer_real_name]++;
      else
        $top_reviewers[$top_reviewer_real_name] = 1;
    }
    arsort($top_reviewers);

    $list = new PHUIObjectItemListView();
    $list_id = 1;
    foreach ($top_reviewers as $top_reviewer_real_name => $top_reviewer_accept_times) {
      $item = id(new PHUIObjectItemView())
        ->setObjectName($list_id)
        ->setHeader($top_reviewer_real_name);
      if ($list_id == 1)
        $item->addAttribute("Gold Medal");
      elseif ($list_id == 2)
        $item->addAttribute("Silver Medal");
      elseif ($list_id == 3)
        $item->addAttribute("Bronze Medal");
      $item->addIcon('flag-6', pht("Accepted: $top_reviewer_accept_times Times"));
      $list->addItem($item);
      $list_id++;
    }

    $list->setNoDataString(pht('There is no people yet.'));

    $crumbs = $this->buildApplicationCrumbs();
    $crumbs->addTextCrumb(pht('Accepted the most'));

    return $this->buildApplicationPage(
      array($crumbs, $list),
      array(
        'title' => pht('Top Reviewers - Accepted the most'),
      ));
  }
}
