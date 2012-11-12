<?php

namespace AW\Bundle\TwitterOAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AW\Bundle\TwitterOAuthBundle\Entity\User
 *
 * @ORM\Table(name="awtwitteroauth_user")
 * @ORM\Entity(repositoryClass="AW\Bundle\TwitterOAuthBundle\Entity\UserRepository")
 */
class User
{
    /**
     * @var integer $id Twitter ID.
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string $id_str Twitter ID as a string.
     * @ORM\Column(name="id_str", type="string", length=255)
     */
    private $idStr;

    /**
     * @var string $screen_name
     * @ORM\Column(name="screen_name", type="string", length=255)
     */
    private $screenName;

    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string $location
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @var integer $friends_count
     * @ORM\Column(name="friends_count", type="integer")
     */
    private $friendsCount;

    /**
     * @var integer $followers_count
     * @ORM\Column(name="followers_count", type="integer")
     */
    private $followersCount;

    /**
     * @var string $profile_image_url
     * @ORM\Column(name="profile_image_url", type="string", length=255)
     */
    private $profileImageUrl;

    /**
     * @var string $profile_image_url_https
     * @ORM\Column(name="profile_image_url_https", type="string", length=255)
     */
    private $profileImageUrlHttps;

    /**
     * @var boolean $default_profile_image Whether the user uses Twitter's default profile image.
     * @ORM\Column(name="default_profile_image", type="boolean")
     */
    private $defaultProfileImage;

    /**
     * @var text $allDataSerialized
     * @ORM\Column(name="all_data_serialized", type="text")
     */
    private $allDataSerialized;

    /**
     * @var string $token
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @var string $tokenSecret
     * @ORM\Column(name="token_secret", type="string", length=255, nullable=true)
     */
    private $tokenSecret;

    public function __construct(\stdClass $data)
    {
        $this->id = $data->id;
        $this->idStr = $data->id_str;
        $this->updateWithNewData($data);
    }

    public function updateWithNewData(\stdClass $data)
    {
        if ($this->id != $data->id || $this->idStr != $data->id_str) {
            throw new \AW\Bundle\TwitterOAuthBundle\Exception('IDs don\'t match');
        }

        $this->screenName = $data->screen_name;
        $this->name = $data->name;
        $this->location = $data->location;
        $this->friendsCount = $data->friends_count;
        $this->followersCount = $data->followers_count;
        $this->profileImageUrl = $data->profile_image_url;
        $this->profileImageUrlHttps = $data->profile_image_url_https;
        $this->defaultProfileImage = $data->default_profile_image;
        $this->allDataSerialized = serialize($data);
    }

    public function setToken(array $token)
    {
        $this->token = $token['oauth_token'];
        $this->tokenSecret = $token['oauth_token_secret'];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdStr()
    {
        return $this->idStr;
    }

    public function getScreenName()
    {
        return $this->screenName;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getFriendsCount()
    {
        return $this->friendsCount;
    }

    public function getFollowersCount()
    {
        return $this->followersCount;
    }

    /**
     * @param string $size One of bigger - 73px by 73px
     *                            normal - 48px by 48px
     *                            mini - 24px by 24px
     * @return string
     */
    public function getProfileImageUrl($size = 'normal')
    {
        return $this->changeImageUrlSize($this->profileImageUrl, $size);
    }

    /**
     * @param string $size One of bigger - 73px by 73px
     *                            normal - 48px by 48px
     *                            mini - 24px by 24px
     * @return string
     */
    public function getProfileImageUrlHttps()
    {
        return $this->changeImageUrlSize($this->profileImageUrlHttps, $size);
    }

    private function changeImageUrlSize($url, $size)
    {
        return preg_replace('/(normal|bigger|mini)(\.[a-zA-Z]{3,4})$/i', $size . '$2', $url);
    }

    /**
     * @return boolean Whether the user still has the default Twitter profile photo.
     */
    public function getDefaultProfileImage()
    {
        return $this->defaultProfileImage;
    }

    public function getAllDataSerialized()
    {
        return $this->allDataSerialized;
    }
}
