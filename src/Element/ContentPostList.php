<?php

namespace Mvo\ContaoFacebookImport\Element;


use Contao\BackendTemplate;
use Contao\Config;
use Contao\ContentElement;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Mvo\ContaoFacebookImport\Facebook\Tools;
use Mvo\ContaoFacebookImport\Model\FacebookPostModel;

/**
 * @property int  mvo_facebook_node
 * @property int  mvo_facebook_numberOfPosts
 * @property bool fullsize
 */
class ContentPostList extends ContentElement
{
    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'ce_mvo_facebook_post_list';

    /**
     * Parse the template
     *
     * @return string Parsed element
     */
    public function generate()
    {
        if (TL_MODE === 'BE') {
            $objTemplate           = new BackendTemplate('be_wildcard');
            $objTemplate->title    = 'Facebook Posts';
            $objTemplate->wildcard = sprintf(
                'showing %s posts',
                ($this->mvo_facebook_numberOfPosts > 0) ? $this->mvo_facebook_numberOfPosts : 'all available'
            );
            return $objTemplate->parse();
        }

        return parent::generate();
    }

    /**
     * Compile the content element
     *
     * @return void
     */
    protected function compile()
    {
        $this->Template = new FrontendTemplate($this->strTemplate);
        $this->Template->setData($this->arrData);

        // get posts
        $arrOptions = [
            'order' => 'postTime DESC'
        ];
        if ($this->mvo_facebook_numberOfPosts > 0) {
            $arrOptions['limit'] = $this->mvo_facebook_numberOfPosts;
        }

        $objPosts = FacebookPostModel::findBy(
            ['pid = ? AND visible = ?'],
            [$this->mvo_facebook_node, true],
            $arrOptions
        );

        $arrPosts = [];
        if (null != $objPosts) {
            $i     = 0;
            $total = $objPosts->count();

            /** @var FacebookPostModel $post */
            foreach ($objPosts as $post) {
                // base data
                $headline = Tools::formatText($post->message, 5);
                if (false !== $posBreak = strpos($headline, '<br>')) {
                    $headline = substr($headline, 0, $posBreak);
                }

                $arrPost = [
                    'postId'        => $post->postId,
                    'message'       => Tools::formatText($post->message),
                    'mediumMessage' => Tools::formatText($post->message, 50),
                    'headline'      => $headline,
                    'time'          => $post->postTime,
                    'datetime'      => date(Config::get('datimFormat'), $post->postTime),
                    'href'          => sprintf('https://facebook.com/%s', $post->postId),
                ];

                // css enumeration
                $arrPost['class'] = ((1 == $i % 2) ? ' even' : ' odd') .
                                    ((0 == $i) ? ' first' : '') .
                                    (($total - 1 == $i) ? ' last' : '');
                $i++;

                // image
                if (null != $post->image
                    && null != $objFile = FilesModel::findByUuid($post->image)
                ) {
                    $objImageTemplate = new FrontendTemplate('image');

                    $arrMeta = deserialize($objFile->meta, true);
                    $strAlt  = (array_key_exists('caption', $arrMeta)
                                && is_array($arrMeta['caption'])
                                && array_key_exists('caption', $arrMeta['caption']))
                               && '' != $arrMeta['caption']['caption']
                        ? $arrMeta['caption']['caption'] : 'Facebook Post Image';

                    $this->addImageToTemplate(
                        $objImageTemplate,
                        [
                            'singleSRC' => $objFile->path,
                            'alt'       => $strAlt,
                            'size'      => deserialize($this->size),
                            'fullsize'  => $this->fullsize
                        ]
                    );
                    $arrPost['image']    = $objImageTemplate->parse();
                    $arrPost['hasImage'] = true;
                } else {
                    $arrPost['hasImage'] = false;
                }

                $arrPosts[] = $arrPost;
            }
        }
        $this->Template->posts    = $arrPosts;
        $this->Template->hasPosts = 0 != count($arrPosts);

        if (!$this->Template->hasPosts) {
            self::loadLanguageFile('templates');
            $this->Template->empty = $GLOBALS['TL_LANG']['MSC']['mvo_facebook_emptyPostList'];
        }
    }
}