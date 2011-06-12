<?php
/** * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Newscoop\Entity\Acl\Role;

/**
 * Base user entity
 * @entity
 * @inheritanceType("SINGLE_TABLE")
 * @discriminatorColumn(name="Reader", type="string")
 * @discriminatorMap({"N" = "Newscoop\Entity\User\Staff", "Y" = "Newscoop\Entity\User\Subscriber"})
 * @table(name="liveuser_users")
 */
abstract class User
{
    /**
     * @id @generatedValue
     * @column(type="integer", name="Id")
     * @var int
     */
    private $id;

    /**
     * @column(type="integer", name="KeyId")
     * @var int
     */
    private $token;

    /**
     * @column(name="Name")
     * @var string
     */
    private $name;

    /**
     * @column(name="UName")
     * @var string
     */
    private $username;

    /**
     * @column(name="Password")
     * @var string
     */
    private $password;

    /**
     * @column(name="EMail")
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $reader;

    /**
     * @column(type="datetime", name="time_created")
     * @var DateTime
     */
    private $timeCreated;

    /**
     * @column(name="Phone")
     * @var string
     */
    private $phone;

    /**
     * @column(name="Title")
     * @var string
     */
    private $title;

    /**
     * @column(name="Gender")
     * @var string
     */
    private $gender;

    /**
     * @column(name="Age")
     * @var string
     */
    private $age;

    /**
     * @column(name="City")
     * @var string
     */
    private $city;

    /**
     * @column(name="StrAddress")
     * @var string
     */
    private $streetAddress;

    /**
     * @column(name="PostalCode")
     * @var string
     */
    private $postalCode;

    /**
     * @column(name="State")
     * @var string
     */
    private $state;

    /**
     * @column(name="CountryCode")
     * @var string
     */
    private $country;

    /**
     * @column(name="Fax")
     * @var string
     */
    private $fax;

    /**
     * @column(name="Contact")
     * @var string
     */
    private $contactPerson;

    /**
     * @column(name="Phone2")
     * @var string
     */
    private $phoneSecond;

    /**
     * @column(name="Employer")
     * @var string
     */
    private $employer;

    /**
     * @column(name="EmployerType")
     * @var string
     */
    private $employerType;

    /**
     * @column(name="Position")
     * @var string
     */
    private $position;

    /**
     */
    public function __construct()
    {
        $this->timeCreated = new DateTime('now');
        $this->token = mt_rand((int) "1 000 000 000", (int) "9 999 999 999");
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get id
     *
     * @return int
     * @deprecated
     */
    public function getUserId()
    {
        return $this->getId();
    }

    /**
     * Get key id
     *
     * @return string
     */
    public function getKeyId()
    {
        return $this->token;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Newscoop\Entity\User
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get real name
     *
     * @return string
     * @deprecated
     */
    public function getRealName()
    {
        return $this->getName();
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Newscoop\Entity\User
     */
    public function setUsername($username)
    {
        $this->username = (string) $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Newscoop\Entity\User
     */
    public function setPassword($password)
    {
        $this->password = sha1($password);
        return $this;
    }

    /**
     * Get password hash
     *
     * @return string
     */
    public function getPasswordHash()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Newscoop\Entity\User
     */
    public function setEmail($email)
    {
        $this->email = (string) $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get time created
     *
     * @return DateTime
     */
    public function getTimeCreated()
    {
        return $this->timeCreated;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Newscoop\Entity\User
     */
    public function setPhone($phone)
    {
        $this->phone = (string) $phone;
        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Newscoop\Entity\User
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return Newscoop\Entity\User
     */
    public function setGender($gender)
    {
        $this->gender = (string) $gender;
        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set age
     *
     * @param string $age
     * @return Newscoop\Entity\User
     */
    public function setAge($age)
    {
        $this->age = (string) $age;
        return $this;
    }

    /**
     * Get age
     *
     * @return string
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Newscoop\Entity\User
     */
    public function setCity($city)
    {
        $this->city = (string) $city;
        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set street address
     *
     * @param string $streetAddress
     * @return Newscoop\Entity\User
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = (string) $streetAddress;
        return $this;
    }

    /**
     * Get street address
     *
     * @return string
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * Set postal code
     *
     * @param string $postalCode
     * @return Newscoop\Entity\User
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = (string) $postalCode;
        return $this;
    }

    /**
     * Get postal code
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Newscoop\Entity\User
     */
    public function setState($state)
    {
        $this->state = (string) $state;
        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Newscoop\Entity\User
     */
    public function setCountry($country)
    {
        $this->country = (string) $country;
        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return Newscoop\Entity\User
     */
    public function setFax($fax)
    {
        $this->fax = (string) $fax;
        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set contact person
     *
     * @param string $contactPerson
     * @return Newscoop\Entity\User
     */
    public function setContactPerson($contactPerson)
    {
        $this->contactPerson = (string) $contactPerson;
        return $this;
    }

    /**
     * Get contact person
     *
     * @return string
     */
    public function getContactPerson()
    {
        return $this->contactPerson;
    }

    /**
     * Set second phone
     *
     * @param string $phoneSecond
     * @return Newscoop\Entity\User
     */
    public function setPhoneSecond($phoneSecond)
    {
        $this->phoneSecond = (string) $phoneSecond;
        return $this;
    }

    /**
     * Get second phone
     *
     * @return string
     */
    public function getPhoneSecond()
    {
        return $this->phoneSecond;
    }

    /**
     * Set employer
     *
     * @param string $employer
     * @return Newscoop\Entity\User
     */
    public function setEmployer($employer)
    {
        $this->employer = (string) $employer;
        return $this;
    }

    /**
     * Get employer
     *
     * @return string
     */
    public function getEmployer()
    {
        return $this->employer;
    }

    /**
     * Set employer type
     *
     * @param string $employerType
     * @return Newscoop\Entity\User
     */
    public function setEmployerType($employerType)
    {
        $this->employerType = (string) $employerType;
        return $this;
    }

    /**
     * Get employer type
     *
     * @return string
     */
    public function getEmployerType()
    {
        return $this->employerType;
    }

    /**
     * Set position
     *
     * @param string $position
     * @return Newscoop\Entity\User
     */
    public function setPosition($position)
    {
        $this->position = (string) $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }
    /**
     * To string strategy
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}

