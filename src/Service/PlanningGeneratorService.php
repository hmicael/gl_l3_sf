<?php

// src/Service/PlanningGeneratorService.php
namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Ldap\Ldap;

class PlanningGeneratorService {
    private $ldap;
    private $parameterBag;
    private $security;

    public function __construct(ParameterBagInterface $parameterBag, Security $security,)
    {
        $this->parameterBag = $parameterBag;
        $this->security = $security;
        $this->ldap = Ldap::create('ext_ldap', [
            'host' => $this->parameterBag->get('LDAP_HOST'),
            'port' => $this->parameterBag->get('LDAP_PORT'),
            'encryption' => 'ssl',
        ]);
    }

    public function getGroups($groupsCN = null): array
    {
        if (! $groupsCN) {
            $groupsCN = $ldapHost = $this->security->getUser()->getAttributes()['departmentNumber'];
        }
        $ldapGroupDn = $this->parameterBag->get('LDAP_GROUP_DN');
        $ldapUserUid = $this->parameterBag->get('LDAP_USER_UID');
        $ldapUserPassword = $this->parameterBag->get('LDAP_USER_PASSWORD');
        $this->ldap->bind($ldapUserUid, $ldapUserPassword);
        
        // Recherche des groupes avec upsTypeGroupe=A010 et cn dans $groupsCN
        $filter = sprintf("(&(objectClass=groupOfNames)(upsTypeGroupe=A010)(|(cn=%s)))", implode(')(cn=', $groupsCN));
        $ldapQuery = $this->ldap->query($this->parameterBag->get('LDAP_GROUP_DN'), $filter);
        $results = $ldapQuery->execute();

        $filieres = [];
        foreach ($results as $entry) {
            $attr = $entry->getAttributes();
            $filieres[] = [
                'cn' => $attr['cn'][0],
                'description' => $attr['description'][0],
            ];
        }

        return $filieres;
    }

    public function getDescription($cn) : string
    {
        $ldapGroupDn = $this->parameterBag->get('LDAP_GROUP_DN');
        $ldapUserUid = $this->parameterBag->get('LDAP_USER_UID');
        $ldapUserPassword = $this->parameterBag->get('LDAP_USER_PASSWORD');
        $this->ldap->bind($ldapUserUid, $ldapUserPassword);
        
        $filter = sprintf("(&(objectClass=groupOfNames)(cn=%s))", $cn);
        $ldapQuery = $this->ldap->query($this->parameterBag->get('LDAP_GROUP_DN'), $filter);
        $results = $ldapQuery->execute();

        $description = [];
        foreach ($results as $entry) {
            $attr = $entry->getAttributes();
            $description[] = $attr['description'][0];
        }

        return $description[0];
    }
}