<?php

/*
 *  @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 *  @license     MIT
 *  @author Michael Andrzejewski <michael@jetskitechnologies.com>
 */
namespace OneLogin\Saml2;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\LibraryHelper;
use Joomla\CMS\Uri\Uri;

/**
 * Wrapper for the library interface to process Joomla style setting params
 * into array for the library.
 *
 * @since 1.6.0
 */
class samlJoomla extends Auth {
    
    /**
     * Holds the  plugin params
     * @var Joomla\Registry\Registry
     */
    public $params;

    /**
     * Library wrapper to format Joomla Style params into an array for the Lib
     * 
     * @param \JRegistry $plgParams
     * @since 1.6.0
     */
    public function __construct() {
        $this->params = LibraryHelper::getParams('oneloginsaml');
        parent::__construct($this->formatSettings());
    }

    /**
     * Fuction to turn JRegistry into an array.
     * 
     * @param \JRegistry $saml_params
     * @return type
     * @since 1.6.0
     */
    protected function formatSettings() {
        if (Factory::getApplication()->isClient('administrator')) {
            $acs = 'administrator/?option=plg_onelogin&task=login.acs';
            $sls = 'administrator/?option=plg_onelogin&task=login.sls';
        } else {
            $acs = 'component/oneloginsaml/acs';
            $sls = 'component/oneloginsaml/sls';
        }
        $spe = Uri::root() . 'index.php?option=com_oneloginsaml&task=getMetadata&format=xml';
        return array(
            'strict' => $this->params->get('strict_mode'),
            'debug' => $this->params->get('debug'),
            'sp' => array(
                'entityId' => ($this->params->get('sp_entity_id') ? $this->params->get('sp_entity_id') : $spe),
                'assertionConsumerService' => array(
                    'url' => Uri::root() . $acs,
                ),
                'singleLogoutService' => array(
                    'url' => Uri::root() . $sls,
                ),
                'NameIDFormat' => $this->params->get('nameid_format','urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified'),
                'x509cert' => $this->params->get('sp_x509cert'),
                'privateKey' => $this->params->get('sp_privatekey'),
            ),
            'idp' => array(
                'entityId' => $this->params->get('idp_entityid'),
                'singleSignOnService' => array(
                    'url' => $this->params->get('idp_sso'),
                ),
                'singleLogoutService' => array(
                    'url' => $this->params->get('idp_slo'),
                ),
                'x509cert' => $this->params->get('idp_x509cert'),
            ),
            // Security settings
            'security' => array(
                /** signatures and encryptions offered */
                // Indicates that the nameID of the <samlp:logoutRequest> sent by this SP
                // will be encrypted.
                'nameIdEncrypted' => $this->params->get('nameid_encrypted'),
                // Indicates whether the <samlp:AuthnRequest> messages sent by this SP
                // will be signed.              [The Metadata of the SP will offer this info]
                'authnRequestsSigned' => $this->params->get('authn_request_signed'),
                // Indicates whether the <samlp:logoutRequest> messages sent by this SP
                // will be signed.
                'logoutRequestSigned' => $this->params->get('logout_request_signed'),
                // Indicates whether the <samlp:logoutResponse> messages sent by this SP
                // will be signed.
                'logoutResponseSigned' => $this->params->get('logout_response_signed'),
                /** signatures and encryptions required * */
                // Indicates a requirement for the <samlp:Response>, <samlp:LogoutRequest> and
                // <samlp:LogoutResponse> elements received by this SP to be signed.
                'wantMessagesSigned' => $this->params->get('want_message_signed'),
                // Indicates a requirement for the <saml:Assertion> elements received by
                // this SP to be signed.        [The Metadata of the SP will offer this info]
                'wantAssertionsSigned' => $this->params->get('want_assertion_signed'),
                // Indicates a requirement for the NameID received by
                // this SP to be encrypted.
                'wantNameIdEncrypted' => $this->params->get('want_assertion_encrypted'),
                'relaxDestinationValidation' => true,
                'wantXMLValidation' => true,
                // Algorithm that the toolkit will use on signing process.
                'signatureAlgorithm' => $this->params->get('signature_algorithm', 'http://www.w3.org/2000/09/xmldsig#rsa-sha1'),
                // Algorithm that the toolkit will use on digest process.
                'digestAlgorithm' => $this->params->get('digest_algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1'),
            ),
        );
    }

}
