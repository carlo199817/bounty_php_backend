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
    
    public function id(): int
    {
        return $this->id;
    }
    public function name(): string
    {
        return $this->name; 
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}

?>