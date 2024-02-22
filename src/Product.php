<?php
// src/Product.php
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity]
#[ORM\Table(name: 'product')]
class Product
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Address::class, inversedBy:"product")]
    #[ORM\JoinColumn(name: 'address_id', referencedColumnName: 'id')]
    private Address|null $address = null;
    
    public function id(): int
    {
        return $this->id;
    }
    public function name(): string
    {
        return $this->name; 
    }

    public function getAddress(): Address
    {
        return $this->address; 
        
    }
    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}

#[ORM\Entity]
#[ORM\Table(name: 'address')]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    #[ORM\Column(type: 'string')]
    private string $street;

    #[ORM\Column(type: 'string')]
    private string $postalCode;

    #[ORM\Column(type: 'string')]
    private string $city;

    #[ORM\Column(type: 'string')]
    private string $country;

    #[ORM\OneToMany(targetEntity: Product::class,mappedBy: 'address')]
    private $products;
    
       public function getCountry(): string
    {
        return $this->country; 
    }
    public function getStreet(): string
    {
        return $this->street; 
    }
    public function getPostal(): string
    {
        return $this->postalCode; 
    }
    public function getCity(): string
    {
        return $this->city; 
    }
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }
 
    
}
?>