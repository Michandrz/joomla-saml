<?php

/**
 * @package     OneLogin SAML
 * @subpackage  
 * 
 * @copyright   Copyright (C) 2019 OneLogin, Inc. All rights reserved.
 * @license     MIT
 * @author Michael Andrzejewski
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Attribute CRUDI Model
 * @since 1.0
 */
class attributeModelOneloginsaml extends JModelBase
{

    const DB_ATTR_TABLE	 = '#__oneloginsaml_attrmap';
    const DB_ATTR_ID	 = 'id';
    const DB_ATTR_IDP	 = 'idp';
    const DB_ATTR_LOCAL	 = 'local';

    /**
     *
     * @var JDatabase $db reference to the system's DB
     */
    protected $db;

    /**
     * @param \Joomla\Registry\Registry $state 
     */
    public function __construct(\Joomla\Registry\Registry $state = null)
    {
	$this->db = JFactory::getDBO();
	parent::__construct($state);
    }
    
    /**
     * Creates a new attribute mapping and saves it to the database
     * 
     * @param attributeMapping $attrmapping The mapping to save. Updated with ID post save
     * @return boolean true on success
     */
    public function create(attributeMapping &$attrmapping)
    {

	//make sure we're saving something we know how to handle
	if (!is_a(attributeMapping, $attrmapping))
	{
	    return false;
	}
	//push the object to the database
	if ($this->db->insertObject(self::DB_ATTR_TABLE, &$attrmapping, self::DB_ATTR_ID))
	{
	    return true;
	} else
	{
	    return false;
	}
    }

    public function read($attrmappingid, &$attributemapping)
    {
	//make sure we were only passed an id
	if (!is_int($attrmappingid))
	{
	    return false;
	}

	//build the database query and submit it
	$query = $this->db->getQuery(true);
	$query->select($this->db->quoteName(array(self::DB_ATTR_ID, self::DB_ATTR_IDP, self::DB_ATTR_LOCAL)))
		->from($this->db->quoteName(self::DB_ATTR_TABLE))
		->where($this->db->quoteName(self::DB_ATTR_ID) . "=" . $this->db->quote($attrmappingid))
		->setLimit("1");
	$this->db->setQuery($query);

	//execute and check for errors
	$result = $this->db->loadObject();

	if (!is_object($result))
	{
	    return false;
	}

	//load the result into our object and return it
	$attributemapping = new attributeMapping($result->id);
	$attributemapping->setIdp($result->idp)
		->setLocal($reslut->local);

	return $this;
    }

    public function update($attrmapping = null)
    {
	if (!is_a($attrmapping, attributeMapping))
	{
	    return FALSE;
	}
	$this->db->updateObject(self::DB_ATTR_TABLE, $attrmapping, self::DB_ATTR_ID);
	return $this;
    }

    public function delete($attrmapping = null)
    {
	$query = $this->db->getQuery(true);

	$query->delete($this->db->quoteName(self::DB_ATTR_TABLE))
		->where($this->db->quoteName(self::DB_ATTR_ID . ' = ' . $this->db->quote($attrmapping->id)));

	$this->db->setQuery($query);

	if (!$this->db->execute)
	{
	    return false;
	}
	return $this;
    }

    public function index()
    {
	//build the database query and submit it
	$query = $this->db->getQuery(true);
	$query->select($this->db->quoteName(array(self::DB_ATTR_ID, self::DB_ATTR_IDP, self::DB_ATTR_LOCAL)))
		->from($this->db->quoteName(self::DB_ATTR_TABLE));
	$this->db->setQuery($query);

	//execute and check for errors
	$result = $this->db->loadObjectList();

	$attributeMappingIndex = array();

	foreach ($result as $attributeDbMapping)
	{
	    //load the result into our object and return it
	    $attributemapping = new attributeMapping($attributeDbMapping->id);
	    $attributemapping->setIdp($attributeDbMapping->idp)
		    ->setLocal($attributeDbMapping->local);
	    $attributeMappingIndex[] = $attributemapping;
	}
	
	return $attributeMappingIndex;
    }

}

/**
 * @attrubute object
 * @since 1.0
 */
class attributeMapping
{

    /**
     * 
     * @var int $id database issued identifier
     */
    protected
	    $id;

    /**
     * 
     * @var string $idp what the mapping is called on the IDP side
     */
    protected
	    $idp;

    /**
     *
     * @var string $local what we're mapping it to locally
     */
    protected
	    $local;

    public function __construct($id = null)
    {
	if ($id != null)
	{
	    $this->id = $id;
	}
	return $this;
    }

    public function getId()
    {
	return $this->id;
    }

    public function getIdp()
    {
	return $this->idp;
    }

    public function getLocal()
    {
	return $this->local;
    }

    public function setId($id)
    {
	$this->id = $id;
	return $this;
    }

    public function setIdp($idp)
    {
	$this->idp = $idp;
	return $this;
    }

    public function setLocal($local)
    {
	$this->local = $local;
	return $this;
    }

}
