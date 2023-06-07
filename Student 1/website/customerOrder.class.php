<?php
require_once "product.class.php";

class CustomerOrder
{
    private $name;
    private $product_catalog = array();
    private $total_products;
    private $order_date;
    private $id;
    private $order_id;

    public function setTotalProducts($number)
    {
        $this->total_products = $number;
        for($i = 0; $i < $this->total_products; $i++)
        {
            $this->product_catalog[$i] = new Product;
        }
    }

    public function setID($id)
    {
        $this->id = $id;
    }

    public function setOrder_id($id)
    {
        $this->order_id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setProductName($number, $item)
    {
        $this->product_catalog[$number]->setProductName($item);
    }

    public function setProductQuantity($number, $quantity)
    {
        $this->product_catalog[$number]->setProductQuantity($quantity);
    }

    public function setProductId($number, $product_id)
    {
        $this->product_catalog[$number]->setProductId($product_id);
    }

    public function setProductAvailable($number, $id)
    {
        $this->product_catalog[$number]->setProductAvailability($id);
    }

    public function setOrder_date($datetime)
    {
        $this->order_date = $datetime;
    }

    public function getID()
    {
        return $this->id;
    }

    public function getOrder_id()
    {
        return $this->order_id;
    }

    public function getName()
    {
        return $this->name;
    }
    public function getTotalProducts()
    {
        return $this->total_products;
    }

    public function getProductName($item_number)
    {
        return $this->product_catalog[$item_number]->getProductName();
    }

    public function getProductQuantity($item_number)
    {
        return $this->product_catalog[$item_number]->getProductQuantity();
    }

    public function getProductId($item_number)
    {
        return $this->product_catalog[$item_number]->getProductId();
    }

    public function getProductAvailable($item_number)
    {
        return $this->product_catalog[$item_number]->getProductAvailability();
    }

    public function getOrder_date()
    {
        return $this->order_date;
    }
}
?>