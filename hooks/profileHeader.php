//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if (! \defined('\IPS\SUITE_UNIQUE_KEY')) {
    exit;
}

/**
 * @mixin \IPS\Theme\class_core_front_profile
 */
class stripeverification_hook_profileHeader extends _HOOK_CLASS_
{
    /* !Hook Data - DO NOT REMOVE */
    public static function hookData()
    {
        return array_merge_recursive([
            'profileHeader' => [
                0 => [
                    'selector' => '#elProfileHeader > div.ipsColumns.ipsColumns_collapsePhone > div.ipsColumn.ipsColumn_fluid > div.ipsPos_left.ipsPad.cProfileHeader_name.ipsType_normal > h1.ipsType_reset.ipsPageHead_barText',
                    'type' => 'add_inside_end',
                    'content' => '{template="check" app="stripeverification" location="front" group="verification" params="$member"}',
                ],
            ],
            'hovercard' => [
                0 => [
                    'selector' => 'div.cUserHovercard > div.ipsPadding.ipsFlex.ipsFlex-fd:column.ipsFlex-ai:center > h2.ipsType_reset.ipsType_center.ipsPos_relative.cUserHovercard__title',
                    'type' => 'add_inside_end',
                    'content' => '{template="check" app="stripeverification" location="front" group="verification" params="$member"}',
                ],
            ],
        ], parent::hookData());
    }
    /* End Hook Data */

}
