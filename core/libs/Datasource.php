<?php

abstract class Datasource
{
	abstract public function connection();
	
	abstract public function where();
	
	abstract public function whereArray($where, $params);
	
	abstract public function whereSQL($where);
	
	abstract public function orderBy($order, $type = null);
	
	abstract public function orderByDesc($order);
	
	abstract public function limit($n, $o = null);
	
	abstract public function offset($n);
	
	abstract public function distinct();
	
	abstract public function getSQL();
	
	abstract public function all();
	
	abstract public function single();
	
	abstract public function paginate($p, $m);
	
	abstract public function count($column = null);
	
	abstract public function sum($column);
	
	abstract public function max($column);
	
	abstract public function min($column);
	
	abstract public function avg($column);
	
	abstract public function insert(Model $model);
	
	abstract public function update(Model $model);
	
	abstract public function delete(Model $model);
	
	abstract public function deleteAll();
	
	abstract public function lastInsertId();
	
	abstract public function groupBy();
}