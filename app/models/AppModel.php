<?php

class AppModel extends Model 
{
	public static function single($filters = array())
	{
		$class = get_called_class();
		$db = Database::factory();
		$entity = $db->{$class};
		$filter = self::filters($filters, 'AND');
		if(isset($filter->SQL))
			$entity->whereArray($filter->SQL, $filter->Values);
		return $entity->single();
	}

	public static function listing($filters = array(), $o = 'Id', $t = 'ASC')
	{
		$class = get_called_class();
		$db = Database::factory();
		$entity = $db->{$class}->orderBy($o, $t);
		$filter = self::filters($filters, 'AND');
		if(isset($filter->SQL))
			$entity->whereArray($filter->SQL, $filter->Values);
		return $entity->all();
	}

	public static function allQuery($sql, $column = 'Id', $type = 'asc', $filters = array(), $op = 'OR', $class = null)
	{
		$filter = self::filters($filters, $op);

		$where = isset($filter->SQL) && trim($filter->SQL) ? ' WHERE ' . $filter->SQL : '';
		$orderby = ' ORDER BY ' . $column . ' ' . $type;
		$sql .= $where . $orderby;

		if(!$class)
			$class = get_called_class();
		
		$db = Database::factory();
		return $db->query($sql, $class, $filter->Values);
	}

	public static function countQuery($sql, $filters = array(), $op = 'OR')
	{
		$filter = self::filters($filters, $op);

		$where = isset($filter->SQL) && trim($filter->SQL) ? ' WHERE ' . $filter->SQL : '';
		
		$sql .= $where;

		$db = Database::factory();
		$result = $db->query($sql, 'stdClass', $filter->Values);
		$count = 0;
		if(isset($result[0]->count))
			$count = $result[0]->count;

		return $count;
	}

	public static function searchQuery($sql, $page = 1, $max = 10, $column = 'Id', $type = 'asc', $filters = array(), $op = 'OR', $groupBy = '')
	{
		$filter = self::filters($filters, $op);

		$where = isset($filter->SQL) && trim($filter->SQL) ? ' WHERE ' . $filter->SQL : '';
		$orderby = ' ORDER BY ' . $column . ' ' . $type;
		$limit = ' LIMIT ' . $max . ' OFFSET ' . ($max * ($page - 1));
		
		$matches = array();
		if(preg_match('/^SELECT(.*)FROM/misU', $sql, $matches)) 
		{
			$columns = explode(',', $matches[1]);
			$columns[0] = str_replace('*', 'Id', $columns[0]);
			$sqlNew = preg_replace('/^SELECT(.+)FROM/misU', 'SELECT COUNT(' . trim($columns[0]) . ') AS count FROM', $sql);
		}

		$sqlCount = $sqlNew . $where;
		$sqlData = $sql . $where . $groupBy . $orderby . $limit;

		$db = Database::factory();
		$result = new stdClass;
		$result->Data = $db->query($sqlData, get_called_class(), $filter->Values);
		$result_count = $db->query($sqlCount, 'stdClass', $filter->Values);
		$count = 0;
		if(isset($result_count[0]->count))
			$count = $result_count[0]->count;
		$result->Count = $count;

		return $result;
	}

	protected static function filters($filters = array(), $op = 'OR')
	{
		$obj = new stdClass;
		if(is_array($filters))
		{
			$fields = array();
			$values = array();
			foreach ($filters as $k => $v)
			{
				if(is_array($v))
				{
					if($v[1] == 'IN')
					{
						$fields[] = $v[0] . ' IN(' . implode(',', array_fill(0, count($v[2]), '?')) . ') ';
						$values = array_merge($values, $v[2]);
					}
					else
					{
						$fields[] = $v[0] . ' ' . $v[1] . ' ?';
						$values[] = $v[2];
					}
				}
				else
				{
					if(preg_match('/^%(.*)%$/', $v) !== 0)
						$fields[] = $k .' LIKE ?';
					else
						$fields[] = $k .' = ?';
					$values[] = $v;
				}
			}
			$obj->SQL = implode(" {$op} ", $fields);
			$obj->Values = $values;
		}
		return $obj;
	}
}