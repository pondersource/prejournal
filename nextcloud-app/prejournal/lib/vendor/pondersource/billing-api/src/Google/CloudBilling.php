<?php

namespace PonderSource\GoogleApi;

use Sabre\Xml\XmlSerializable;
use Sabre\Xml\Writer;

class CloudBilling implements XmlSerializable {
    private $sku_name;
    private $sku_id;
    private $sku_description;
    private $sku_provider_name;
    private $sku_service_name;
    private $sku_resource;
    private $sku_group;
    private $sku_usage_type;
    private $sku_effective_time;
    private $sku_usage_unit;
    private $sku_usage_unit_description;
    private $sku_base_unit;
    private $sku_base_unit_description;
    private $sku_base_unit_conversion_factor;
    private $sku_display_quantity;
    private $sku_start_usage_amount;
    private $sku_unit_price;

    /**
     * Sku name 
     */
    public function setSkuName($sku_name) {
        $this->sku_name = $sku_name;
        return $this;
    }

    /**
     * Sku id 
     */
    public function setSkuId($sku_id) {
       $this->sku_id = $sku_id;
       return $this;
    }

    /**
     * Sku description
     */
    public function setSkuDescription($sku_description) {
        $this->sku_description = $sku_description;
        return $this;
    }

    /**
     * Sku Provider Name
     */
    public function setSkuProviderName($sku_provider_name) {
        $this->sku_provider_name = $sku_provider_name;
        return $this;
    }

    /**
     * sku service name
     */
    public function setSkuServiceName($sku_service_name) {
        $this->sku_service_name = $sku_service_name;
        return $this;
    }

    /**
     * Sku resource
     */
    public function setSkuResouce($sku_resource) {
        $this->sku_resource = $sku_resource;
        return $this;
    }

    /**
     * Sku Group
     */
    public function setSkuGroup($sku_group) {
       $this->sku_group = $sku_group;
       return $this;
    }

    /**
     * Sku Usage Type
     */
    public function setSkuUsageType($sku_usage_type) {
        $this->sku_usage_type = $sku_usage_type;
        return $this;
    }

    /**
     * Sku effective time
     */
    public function setSkuEffectiveTime($sku_effective_time) {
        $this->sku_effective_time = $sku_effective_time;
        return $this;
    }

    /**
     * Sku usage unit
     */
    public function setSkuUsageUnit($sku_usage_unit) {
        $this->sku_usage_unit = $sku_usage_unit;
        return $this;
    }

    /**
     * Sku Usage Unit description
     */
    public function setSkuUsageUnitDescription($sku_usage_unit_description) {
        $this->sku_usage_unit_description = $sku_usage_unit_description;
        return $this;
    }

    /**
     * Sku Base Unit
     */
    public function setSkuBaseUnit($sku_base_unit) {
        $this->sku_base_unit = $sku_base_unit;
        return $this;
    }

    /**
     * Sku Base Unit Description
     */
    public function setSkuBaseUnitDescription($sku_base_unit_description) {
        $this->sku_base_unit_description = $sku_base_unit_description;
        return $this;
    }

    /**
     * sku base unit conversion factor
     */
    public function setSkuBaseUnitConversionDescription($sku_base_unit_conversion_factor) {
        $this->sku_base_unit_conversion_factor = $sku_base_unit_conversion_factor;
        return $this;
    }

    /**
     * Sku display quantity
     */
    public function setSkuDisplayQuantity($sku_display_quantity) {
        $this->sku_display_quantity = $sku_display_quantity;
        return $this;
    }

    /**
     * Sku start usage amount
     */
    public function setSkuStartUsageAmount($sku_start_usage_amount) {
        $this->sku_start_usage_amount = $sku_start_usage_amount;
        return $this;
    }

    /**
     * Sku unit price
     */
    public function setSkuUnitPrice($sku_unit_price) {
        $this->sku_unit_price = $sku_unit_price;
        return $this;
    }

    function xmlSerialize(Writer $writer) {
        $ns = '{http://example.org/billings}';
        
        $writer->write([
            $ns . 'sku_name' => $this->sku_name,
            $ns . 'sku_id' => $this->sku_id,
            $ns . 'sku_description' => $this->sku_description,
            $ns . 'sku_provider_name' => $this->sku_provider_name,
            $ns . 'sku_service_name' => $this->sku_service_name,
            $ns . 'sku_resource' => $this->sku_resource,
            $ns . 'sku_group' => $this->sku_group,
            $ns . 'sku_usage_type' => $this->sku_usage_type,
            $ns . 'sku_effective_time' => $this->sku_effective_time,
            $ns . 'sku_usage_unit' => $this->sku_usage_unit,
            $ns . 'sku_usage_unit_description' => $this->sku_usage_unit_description,
            $ns . 'sku_base_unit' => $this->sku_base_unit,
            $ns . 'sku_base_unit_description' => $this->sku_base_unit_description,
            $ns . 'sku_base_unit_conversion_factor' => $this->sku_base_unit_conversion_factor,
            $ns . 'sku_display_quantity' => $this->sku_display_quantity,
            $ns . 'sku_start_usage_amount' => $this->sku_start_usage_amount,
            $ns . 'sku_unit_price' => $this->sku_unit_price,
        ]);

    }
}