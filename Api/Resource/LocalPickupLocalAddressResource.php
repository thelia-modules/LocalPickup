<?php

namespace LocalPickup\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use LocalPickup\Api\State\LocalPickupLocalAddressProvider;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/front/local-pickup-address/{id}',
            name: 'api_local_pickup_address_get_front',
            provider: LocalPickupLocalAddressProvider::class
        ),
    ],
    normalizationContext: ['groups' => [LocalPickupLocalAddressResource::GROUP_FRONT_READ]]
)]
class LocalPickupLocalAddressResource
{
    public const GROUP_FRONT_READ = 'front:local_pickup_address:read';

    /**
     * @var int|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $id = null;

    /**
     * @var string|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $label = null;

    /**
     * @var int|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $customer = null;

    /**
     * @var int|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $title = null;

    /**
     * @var string|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $company = null;

    /**
     * @var string|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $firstname = null;

    /**
     * @var string|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $lastname = null;

    /**
     * @var string|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $address1 = null;

    /**
     * @var string|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $address2 = null;

    /**
     * @var string|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $address3 = null;

    /**
     * @var string|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $zipcode = null;

    /**
     * @var string|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $city = null;

    /**
     * @var int|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $country = null;

    /**
     * @var string|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $phone = null;

    /**
     * @var string|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $cellphone = null;

    /**
     * @var int|null
     */
    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $default = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     * @return void
     */
    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return int|null
     */
    public function getCustomer(): ?int
    {
        return $this->customer;
    }

    /**
     * @param int|null $customer
     * @return void
     */
    public function setCustomer(?int $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return int|null
     */
    public function getTitle(): ?int
    {
        return $this->title;
    }

    /**
     * @param int|null $title
     * @return void
     */
    public function setTitle(?int $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return void
     */
    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @param string|null $address1
     * @return void
     */
    public function setAddress1(?string $address1): void
    {
        $this->address1 = $address1;
    }

    /**
     * @return string|null
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * @param string|null $address2
     * @return void
     */
    public function setAddress2(?string $address2): void
    {
        $this->address2 = $address2;
    }

    /**
     * @return string|null
     */
    public function getAddress3(): ?string
    {
        return $this->address3;
    }

    /**
     * @param string|null $address3
     * @return void
     */
    public function setAddress3(?string $address3): void
    {
        $this->address3 = $address3;
    }

    /**
     * @return string|null
     */
    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    /**
     * @param string|null $zipcode
     * @return void
     */
    public function setZipcode(?string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     * @return void
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return int|null
     */
    public function getCountry(): ?int
    {
        return $this->country;
    }

    /**
     * @param int|null $country
     * @return void
     */
    public function setCountry(?int $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @return void
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string|null
     */
    public function getCellphone(): ?string
    {
        return $this->cellphone;
    }

    /**
     * @param string|null $cellphone
     * @return void
     */
    public function setCellphone(?string $cellphone): void
    {
        $this->cellphone = $cellphone;
    }

    /**
     * @return int|null
     */
    public function getDefault(): ?int
    {
        return $this->default;
    }

    /**
     * @param int|null $default
     * @return void
     */
    public function setDefault(?int $default): void
    {
        $this->default = $default;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string|null $company
     * @return void
     */
    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return void
     */
    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }
}
