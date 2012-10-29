## Sample Use In A Controller Action ##


    $twitterService = $this->get('aw_twitter_oauth');
    $twitterUser = $twitterService->getUserFromSession();
    if (!$twitterUser) {
        return $this->redirect(
            $this->generateUrl('aw_twitter_oauth_auth',
                array('continue' => $this->getRequest()->getUri())));
    }

## Install ##

* If you use Git, run `git submodule add  -- "git clone https://bitbucket.org/addictionworldwide/twitter-oauth-bundle-for-symfony2.git" "src/"`

* If you don't use Git, download the source and put it into your bundles
  directory (typically "src/")

* Enable AWTwitterOAuthBundle in your app/AppKernel.php

* Review `app/console doctrine:schema:update --dump-sql`

* Run `app/console doctrine:schema:update --force` if the above was OK.
