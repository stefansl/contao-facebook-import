<?php

namespace Mvo\ContaoFacebookImport\Model;

use Contao\Model;
use Mvo\ContaoFacebookImport\Facebook\OpenGraph;

/**
 * Reads and writes projects
 *
 * @property integer $id
 * @property integer $tstamp
 *
 * @property string  $description
 * @property string  $fbAppId
 * @property string  $fbAppSecret
 * @property string  $fbAccessToken
 * @property string  $fbPageName
 * @property integer $minimumCacheTime
 * @property integer $numberOfPosts
 * @property bool    $importEnabled
 * @property string  $uploadDirectory
 *
 * @method static FacebookModel|null findById($id, array $opt = array())
 *
 */
class FacebookModel extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $strTable = 'tl_mvo_facebook';

    /**
     * @return OpenGraph
     */
    public function getOpenGraphInstance()
    {
        return new OpenGraph($this->fbAppId, $this->fbAppSecret, $this->fbAccessToken, $this->fbPageName);
    }
}
