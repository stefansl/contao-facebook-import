<?php

// models
$GLOBALS['TL_MODELS']['tl_mvo_facebook']       = 'Mvo\\ContaoFacebookImport\\Model\\FacebookModel';
$GLOBALS['TL_MODELS']['tl_mvo_facebook_post']  = 'Mvo\\ContaoFacebookImport\\Model\\FacebookPostModel';
$GLOBALS['TL_MODELS']['tl_mvo_facebook_event'] = 'Mvo\\ContaoFacebookImport\\Model\\FacebookEventModel';

// BE
$GLOBALS['BE_MOD']['mvo_facebook_integration'] = [
    'mvo_facebook' => [
        'tables'       => ['tl_mvo_facebook', 'tl_mvo_facebook_post', 'tl_mvo_facebook_event'],
        'importPosts'  => ['mvo_contao_facebook.listener.import_posts', 'onForceImport'],
        'importEvents' => ['mvo_contao_facebook.listener.import_events', 'onForceImport'],
    ]
];

$GLOBALS['TL_CSS'][] = 'bundles/mvocontaofacebookimport/css/backend_svg.css';

// FE
$GLOBALS['TL_CTE']['mvo_facebook']['mvo_facebook_post_list']  = 'Mvo\\ContaoFacebookImport\\Element\\ContentPostList';
$GLOBALS['TL_CTE']['mvo_facebook']['mvo_facebook_event_list'] = 'Mvo\\ContaoFacebookImport\\Element\\ContentEventList';

// data import
$GLOBALS['TL_CRON']['minutely'][] = ['mvo_contao_facebook.listener.import_posts', 'onImportAll'];
$GLOBALS['TL_CRON']['minutely'][] = ['mvo_contao_facebook.listener.import_events', 'onImportAll'];