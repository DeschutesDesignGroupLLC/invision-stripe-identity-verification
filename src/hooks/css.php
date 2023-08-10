//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if (! \defined('\IPS\SUITE_UNIQUE_KEY')) {
    exit;
}

/**
 * @mixin \IPS\Theme\class_core_front_global
 */
class stripeverification_hook_css extends _HOOK_CLASS_
{
    /* !Hook Data - DO NOT REMOVE */
    public static function hookData()
    {
        return array_merge_recursive([
            'globalTemplate' => [
                0 => [
                    'selector' => 'html > head',
                    'type' => 'add_inside_end',
                    'content' => '<link rel=\'stylesheet\' href=\'{expression="\IPS\Http\Url::external(reset(\IPS\Theme::i()->css(\'stripeverification.css\', \'stripeverification\', \'front\')))->setQueryString(\'v\',\IPS\Theme::i()->cssCacheBustKey())"}\' media=\'all\'>
',
                ],
            ],
        ], parent::hookData());
    }
    /* End Hook Data */

}
