<?php

namespace AW\Bundle\TwitterOAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AW\Bundle\TwitterOAuthBundle\Lib;
use AW\Bundle\TwitterOAuthBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * HTTP param: continue: where to redirect to after authenticating.
     */
    public function authAction(Request $request)
    {
        $this->requireContinueParam($request);

        $tw = new Lib\TwitterOAuth(
            $this->container->getParameter('twitter_key'),
            $this->container->getParameter('twitter_secret'));

        $callbackUrl = $this->generateUrl('aw_twitter_oauth_callback', array('continue' => $request->get('continue')), true);
        $requestToken = $tw->getRequestToken($callbackUrl);
        $this->get('session')->set('twitter_tmp_token', $requestToken['oauth_token']);
        $this->get('session')->set('twitter_tmp_secret', $requestToken['oauth_token_secret']);

        if ($tw->http_code == 200) {
            return $this->redirect($tw->getAuthorizeURL($requestToken['oauth_token']));
        }
        else {
            // The user probably clicked 'Cancel' on Twitter's auth page.
            $this->get('session')->remove('twitter_tmp_token');
            $this->get('session')->remove('twitter_tmp_secret');
            return $this->render('AWTwitterOAuthBundle:Default:fail.html.twig');
        }
    }

    public function callbackAction(Request $request)
    {
        $this->requireContinueParam($request);

        $tw = new Lib\TwitterOAuth(
            $this->container->getParameter('twitter_key'),
            $this->container->getParameter('twitter_secret'),
            $this->get('session')->get('twitter_tmp_token'),
            $this->get('session')->get('twitter_tmp_secret'));

        $accessToken = $tw->getAccessToken($_REQUEST['oauth_verifier']);
        if (!$accessToken) {
            throw new \AW\Bundle\TwitterOAuthBundle\Exception('No access token - may be expired');
        }
        $this->get('session')->set('twitter_access_token', $accessToken);
        $this->get('session')->remove('twitter_tmp_token');
        $this->get('session')->remove('twitter_tmp_secret');

        // Call 'account/verify_credentials' to get the user's profile data.
        $data = $tw->get('account/verify_credentials');
        $this->get('session')->set('twitter_id', $data->id);

        // Save the user's details.
        $user = $this->getDoctrine()->getRepository('AWTwitterOAuthBundle:User')->find($data->id);
        if ($user) {
            $user->updateWithNewData($data);
        }
        else {
            $user = new User($data);
        }

        // Save the user's access token too, for doing API calls when the HTTP
        // session isn't available (e.g. cron jobs).
        $user->setToken($accessToken);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $this->redirect($request->get('continue'));
    }

    private function requireContinueParam(Request $request)
    {
        if (!$request->get('continue')) {
            throw new \AW\Bundle\TwitterOAuthBundle\Exception('No continue parameter in the request query string');
        }
    }
}
