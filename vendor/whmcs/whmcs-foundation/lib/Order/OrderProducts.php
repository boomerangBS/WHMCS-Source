<?php

namespace WHMCS\Order;

class OrderProducts
{
    private $orderForm;
    private $formProducts = [];
    private $products;
    public function __construct(\WHMCS\OrderForm $orderForm)
    {
        $this->orderForm = $orderForm;
    }
    public function getFormProducts() : array
    {
        return $this->formProducts;
    }
    public function getProducts() : \Illuminate\Database\Eloquent\Collection
    {
        if($this->areProductsNotFetched()) {
            $this->fetchProducts();
        }
        return is_null($this->products) ? new \Illuminate\Database\Eloquent\Collection() : $this->products;
    }
    public function obtainProducts() : \self
    {
        $productsData = $this->orderForm->getCartDataByKey("products", []);
        $this->formProducts = is_array($productsData) ? $this->filterFormProducts($productsData, "isProductDataCorrect") : [];
        $this->formProducts = $this->filterFormProducts($this->formProducts, "isProductExists");
        return $this;
    }
    private function filterFormProducts($productsData, string $filter) : array
    {
        return array_values(array_filter($productsData, function ($product) {
            static $filter = NULL;
            return $this->{$filter}($product);
        }));
    }
    private function isProductDataCorrect($productData)
    {
        return (bool) ($productData["pid"] ?? false);
    }
    private function isProductExists($productData) : array
    {
        $products = $this->getProducts();
        return isset($products[$productData["pid"]]);
    }
    private function areProductsNotFetched()
    {
        return is_null($this->products) && !empty($this->formProducts);
    }
    private function fetchProducts() : void
    {
        $productIds = array_unique(array_column($this->formProducts, "pid"));
        $this->products = \WHMCS\Product\Product::whereIn("id", $productIds)->with("productGroup")->get()->keyBy("id");
    }
}

?>