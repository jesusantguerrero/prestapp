<?php

namespace App\Contracts;

class Features
{
    /**
     * Determine if the given feature is enabled.
     *
     * @param  string  $feature
     * @return bool
     */
    public static function enabled(string $feature)
    {
        return in_array($feature, config('icloan.features', []));
    }

    /**
     * Determine if the feature is enabled and has a given option enabled.
     *
     * @param  string  $feature
     * @param  string  $option
     * @return bool
     */
    public static function optionEnabled(string $feature, string $option)
    {
        return static::enabled($feature) &&
               config("jetstream-options.{$feature}.{$option}") === true;
    }

    /**
     * Determine if the application is allowing profile photo uploads.
     *
     * @return bool
     */
    public static function allowPublicInvoices()
    {
        return static::enabled(static::publicInvoices());
    }

    /**
     * Determine if the application is using any client portal features.
     *
     * @return bool
     */
    public static function hasClientPortalFeatures()
    {
        return static::enabled(static::clientPortal());
    }

    /**
     * Determine if the application is using any team features.
     *
     * @return bool
     */
    public static function hasAgentProfileFeatures()
    {
        return static::enabled(static::agentProfile());
    }

    /**
     * Determine if invitations are sent to team members.
     *
     * @return bool
     */
    public static function sendsTeamInvitations()
    {
        return static::optionEnabled(static::teams(), 'invitations');
    }

    /**
     * Determine if the application has terms of service / privacy policy confirmation enabled.
     *
     * @return bool
     */
    public static function hasTermsAndPrivacyPolicyFeature()
    {
        return static::enabled(static::termsAndPrivacyPolicy());
    }

    /**
     * Determine if the application is using any account deletion features.
     *
     * @return bool
     */
    public static function hasAccountDeletionFeatures()
    {
        return static::enabled(static::accountDeletion());
    }

    /**
     * Enable the profile photo upload feature.
     *
     * @return string
     */
    public static function publicInvoices()
    {
        return 'public-invoices';
    }

    /**
     * Enable the client portal feature.
     *
     * @return string
     */
    public static function clientPortal()
    {
        return 'client-portal';
    }

    /**
     * Enable the teams feature.
     *
     * @param  array  $options
     * @return string
     */
    public static function agentProfile(array $options = [])
    {
        if (! empty($options)) {
            config(['jetstream-options.teams' => $options]);
        }

        return 'teams';
    }

    /**
     * Enable the terms of service and privacy policy feature.
     *
     * @return string
     */
    public static function termsAndPrivacyPolicy()
    {
        return 'terms';
    }

    /**
     * Enable the account deletion feature.
     *
     * @return string
     */
    public static function accountDeletion()
    {
        return 'account-deletion';
    }
}
