<?php

declare(strict_types=1);

namespace App\Service;

use Dreamscapes\Ldap\Core\Ldap;
use Exception;

class LdapManager
{
    /**
     * Reset timeout - if more than this many seconds have passed since the
     * last request we should reset the ldap connection
     * @var int
     */
    private const RESET_TIMEOUT = 120;

    /**
     * @var Config
     */
    protected $config;
    
    /**
     * @var LDAP
     */
    protected $ldap;
    
    /**
     * LDAP connection times tells us when to reset
     * @var int
     */
    protected $connectionLastUsed;

    /**
     * Constructor
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->ldap = null;
        $this->connectionLastUsed = null;
    }
    
    /**
     * Destroy the connection
     */
    public function __destruct()
    {
        if ($this->ldap) {
            $this->ldap->close();
        }
    }
    
    /**
     * Get an instance of the LDAP object
     *
     * @return LDAP
     */
    protected function getLdap()
    {
        $now = time();
        if (
            $this->connectionLastUsed &&
            $this->ldap &&
            $now - $this->connectionLastUsed > self::RESET_TIMEOUT
        ) {
            $this->ldap->close();
            $this->ldap = null;
        }
        $this->connectionLastUsed = $now;
        if (!empty($this->ldap)) {
            return $this->ldap;
        }

        $ldapUrl = $this->config->get('ldap_directory_url');
        $ldapBindUser = $this->config->get('ldap_directory_user');
        $ldapBindPassword = $this->config->get('ldap_directory_password');
        
        $this->ldap = new Ldap($ldapUrl);
        $this->ldap->setOption(Ldap::OPT_NETWORK_TIMEOUT, 10);
        $this->ldap->bind($ldapBindUser, $ldapBindPassword);

        return $this->ldap;
    }

    /**
     * Performs an LDAP search
     * @param string $filter
     *
     * @return array
     * @throws Exception
     */
    public function search($filter)
    {

        $ldapSearchBase = $this->config->get('ldap_directory_search_base');
        $ldapCampusIdProperty = $this->config->get('ldap_directory_campus_id_property');
        $ldapUsernameProperty = $this->config->get('ldap_directory_username_property');
        $ldapDisplayNameProperty = $this->config->get('ldap_directory_display_name_property');

        $rhett = [];
        $attributes = [
            'mail',
            'sn',
            'givenName',
            'telephoneNumber',
            $ldapCampusIdProperty,
            $ldapUsernameProperty,
            $ldapDisplayNameProperty,
        ];
        try {
            $ldap = $this->getLdap();
            $results = [];
            $cookie = '';
            do {
                $ldap->pagedResult(1000, false, $cookie);
                $response = $ldap->search($ldapSearchBase, $filter, $attributes);
                $arr = $response->getEntries();
                unset($arr['count']);
                $results = array_merge($results, $arr);
                $pagedArray = $response->pagedResultResponse();
                $cookie = !empty($pagedArray['cookie']) ? $pagedArray['cookie'] : false;
            } while ($cookie);

            if (count($results)) {
                $campusIdKey = strtolower($ldapCampusIdProperty);
                $usernameKey = strtolower($ldapUsernameProperty);
                $displayNameKey = strtolower($ldapDisplayNameProperty);
                $rhett = array_map(function ($userData) use ($campusIdKey, $usernameKey, $displayNameKey) {
                    $keys = [
                        'givenname',
                        'sn',
                        'mail',
                        'telephonenumber',
                        $campusIdKey,
                        $usernameKey,
                        $displayNameKey,
                    ];
                    $values = [];
                    foreach ($keys as $key) {
                        $value = array_key_exists($key, $userData) ? $userData[$key][0] : null;
                        $values[$key] = $value;
                    }
                    return [
                        'firstName' => $values['givenname'],
                        'lastName' => $values['sn'],
                        'email' => $values['mail'],
                        'telephoneNumber' => $values['telephonenumber'],
                        'campusId' => $values[$campusIdKey],
                        'username' => $values[$usernameKey],
                        'displayName' => $values[$displayNameKey],
                    ];
                }, $results);
                
                usort($rhett, function (array $arr1, array $arr2) {
                    if ($arr1['lastName'] == $arr2['lastName']) {
                        if ($arr1['firstName'] == $arr2['firstName']) {
                            return 0;
                        }
                        return strcmp($arr1['firstName'], $arr2['firstName']);
                    }
                    
                    return strcmp($arr1['lastName'], $arr2['lastName']);
                });
            }
        } catch (Exception $e) {
            throw new Exception("Failed to search external user source: {$e->getMessage()}");
        }
    
        return $rhett;
    }
}
