//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if (! \defined('\IPS\SUITE_UNIQUE_KEY')) {
    exit;
}

/**
 * @mixin \IPS\Member
 */
class stripeverification_hook_Member extends _HOOK_CLASS_
{
    /**
     * @return \IPS\stripeverification\System\Verification
     */
    public function markMemberVerified()
    {
        $verification = $this->verification ?: new \IPS\stripeverification\System\Verification();
        $verification->member_id = $this->member_id;
        $verification->verified = 1;
        $verification->verified_at = \IPS\DateTime::create()->getTimestamp();
        $verification->submitted_at = null;
        $verification->save();

        $groups = explode(',', $this->mgroup_others);
        $groupsToAdd = explode(',', \IPS\Settings::i()->stripeverification_verification_group);
        $newGroups = array_filter(array_unique(array_merge($groups, $groupsToAdd)));

        $this->mgroup_others = implode(',', $newGroups);
        $this->save();

        $notification = new \IPS\Notification(\IPS\Application::load('stripeverification'), 'verified');
        $notification->recipients->attach($this);
        $notification->send();

        return $verification;
    }

    /**
     * @return \IPS\stripeverification\System\Verification
     */
    public function markMemberVerificationProcessing(bool $force = false)
    {
        $verification = $this->verification ?: new \IPS\stripeverification\System\Verification();

        if (! $this->verified || $force) {
            $verification->member_id = $this->member_id;
            $verification->verified = 0;
            $verification->verified_at = null;
            $verification->submitted_at = \IPS\DateTime::create()->getTimestamp();
            $verification->save();
        }

        return $verification;
    }

    /**
     * @return \IPS\stripeverification\System\Verification
     */
    public function markMemberUnverified()
    {
        $verification = $this->verification ?: new \IPS\stripeverification\System\Verification();
        $verification->member_id = $this->member_id;
        $verification->verified = 0;
        $verification->verified_at = null;
        $verification->submitted_at = null;
        $verification->save();

        $groups = explode(',', $this->mgroup_others);
        $groupsToRemove = explode(',', \IPS\Settings::i()->stripeverification_verification_group);
        $newGroups = array_filter(array_unique($groups), static function ($groupId) use ($groupsToRemove) {
            return ! \in_array($groupId, $groupsToRemove, false);
        });

        $this->mgroup_others = implode(',', $newGroups);
        $this->save();

        $notification = new \IPS\Notification(\IPS\Application::load('stripeverification'), 'unverified');
        $notification->recipients->attach($this);
        $notification->send();

        return $verification;
    }

    /**
     * @return \IPS\stripeverification\System\Verification|false
     */
    public function get_verification()
    {
        try {
            return \IPS\stripeverification\System\Verification::load($this->member_id, 'member_id');
        } catch (\OutOfRangeException) {
            return false;
        }
    }

    /**
     * @return false|\IPS\nexus\Subscription
     */
    public function get_verification_subscription()
    {
        if (\IPS\Application::appIsEnabled('nexus') && $packageId = \IPS\Settings::i()->stripeverification_commerce_subscription) {
            try {
                $package = \IPS\nexus\Subscription\Package::load($packageId);

                return \IPS\nexus\Subscription::loadByMemberAndPackage($this, $package);
            } catch (\OutOfRangeException) {
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function get_verification_processing()
    {
        return ! $this->verified && isset($this->verification?->submitted_at);
    }

    /**
     * @return bool
     */
    public function get_verified()
    {
        $verified = $this->verification?->verified && \IPS\stripeverification\Manager\LicenseKey::i()->isValid();

        if (\IPS\Application::appIsEnabled('nexus') && \IPS\Settings::i()->stripeverification_commerce_enabled) {
            $verified = $verified && $this->verification_subscription;
        }

        return $verified;
    }

    /**
     * @return \IPS\DateTime|null
     */
    public function get_verified_at()
    {
        return $this->verified && $this->verification?->verified_at ? \IPS\DateTime::ts($this->verification->verified_at) : null;
    }

    /**
     * @return \IPS\DateTime|null
     */
    public function get_verification_submitted_at()
    {
        return $this->verification?->submitted_at ? \IPS\DateTime::ts($this->verification->submitted_at) : null;
    }
}
