<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Table;

abstract class AbstractTable implements Contracts\TableInterface
{
    protected $data = [];
    protected $columns;
    protected $castColumns;
    protected $totalData;
    protected $totalFiltered;
    protected $filters = [];
    public function list(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        if(!$request->isXHR()) {
            throw new \WHMCS\Exception("Invalid request.");
        }
        $this->processData($this->getData($request));
        return new \WHMCS\Http\Message\JsonResponse(["draw" => (int) $request->get("draw"), "recordsTotal" => $this->totalData, "recordsFiltered" => $this->totalFiltered, "data" => $this->data]);
    }
    protected function setColumns(\WHMCS\Http\Message\ServerRequest $request) : void
    {
        foreach ($request->get("columns") as $column) {
            $this->columns[] = $column["name"];
            if(in_array($column["name"], ["domainstatus", "status", "stage"])) {
                $this->filters = array_filter(explode("|", $column["search"]["value"]));
            }
        }
    }
    protected function getColumns() : array
    {
        return $this->columns;
    }
}

?>