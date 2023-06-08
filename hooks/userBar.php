//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if (! \defined('\IPS\SUITE_UNIQUE_KEY')) {
    exit;
}

/**
 * @mixin \IPS\Theme\class_core_front_global
 */
class stripeverification_hook_userBar extends _HOOK_CLASS_
{
    /* !Hook Data - DO NOT REMOVE */
public static function hookData() {
 return array_merge_recursive( array (
  'userBar' => 
  array (
    0 => 
    array (
      'selector' => '#elUserNav > li.cNotifications.cUserNav_icon',
      'type' => 'add_after',
      'content' => '<li class=\'cNotifications cUserNav_icon\'>
			<a href=\'{url="app=stripeverification&module=system&controller=verification"}\' id=\'elVerificationStatus\' data-ipsTooltip title=\'{lang="stripeverification_userbar" escape="true"}\' data-ipsDialog data-ipsDialog-size="narrow" data-ipsDialog-title=\'{lang="stripeverification_userbar" escape="true"}\' >
				<i class=\'fa fa-solid fa-circle-check\'></i>
            </a>
		</li>',
    ),
    1 => 
    array (
      'selector' => '#elUserLink > i.fa.fa-caret-down',
      'type' => 'add_before',
      'content' => '{template="check" app="stripeverification" location="front" group="verification" params="\IPS\Member::loggedIn(), 16"}',
    ),
  ),
  'userLink' => 
  array (
    0 => 
    array (
      'selector' => 'a[data-ipshover]',
      'type' => 'add_after',
      'content' => '{template="check" app="stripeverification" location="front" group="verification" params="$member"}',
    ),
  ),
), parent::hookData() );
}
/* End Hook Data */

}
