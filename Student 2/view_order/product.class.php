<?php
class Product
{
    private $name;
    private $quantity;
    private $id;
    private $availability;

    public function setProductName($name)
    {
        $this->name = $name;
    }

    public function setProductQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function setProductId($id)
    {
        $this->id = $id;
    }

    public function setProductAvailability($availability)
    {
        $this->availability = $availability;
    }

    public function getProductName()
    {
        return $this->name;
    }

    public function getProductQuantity()
    {
        return $this->quantity;
    }

    public function getProductId()
    {
        return $this->id;
    }

    public function getProductAvailability()
    {
        return $this->availability;
    }
}
?>