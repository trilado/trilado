<?php
/*
 * Copyright (c) 2012, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Classe modelo, abstrada, de Mapemamento de Objeto Relacional (ORM)
 * 
 * @author	Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @version	1
 *
 */
abstract class Datasource
{
	/**
	 * Construtor da classe
	 * @param	string	$class		nome do model
	 * @throws	DatabaseException	dispara se o model não tiver a anotação de Entity ou View
	 */
	abstract public function __construct($config, $class = null);
	
	/**
	 * Define com qual entidade será trabalhado
	 * @param	string	$class		nome da entidade
	 * @return	Datasource		retorna a própria instância da classe Datasource 
	 */
	abstract public function from($class);


	/**
	 * Adiciona as condições na instrunção (clausula WHERE)
	 * @param	string	$condition		condições SQL, por exemplo 'Id = ? OR slug = ?'
	 * @param	mixed	$value1			valor da primeira condição
	 * @param	mixed	$valueN			valor da x condição
	 * @throws	DatabaseException		disparado se a quantidade de argumentos for menor 2 ou se quantidade de condicionais não corresponder a quantidade de valores
	 * @return	object					retorna a própria instância da classe Datasource 
	 */
	abstract public function where();
	
	/**
	 * Adiciona as condições na instrução (clausula WHERE)
	 * @param	string	$where		condições SQL, por exemplo 'Id = ? OR slug = ?'
	 * @param	array	$params		array com os valores das condições
	 * @return	object				retorna a própria instância da classe Datasource
	 */
	abstract public function whereArray($where, $params);
	
	/**
	 * Adiciona as condições na instrução SQL (clausula WHERE)
	 * @param	string	$where		condições SQL com valores direto, por exemplo 'Description IS NOT NULL'
	 * @return	object				retorna a própria instância da classe Datasource
	 */
	abstract public function whereSQL($where);
	
	/**
	 * Define a ordem em que os resultados serão retornados
	 * @param	string	$order	nome da coluna a ser ordenada
	 * @param	string	$type	typo de ordenação (asc ou desc)
	 * @return	object			retorna a própria instância da classe Datasource
	 */
	abstract public function orderBy($order, $type = null);
	
	/**
	 * Define como ordem decrescente os resultados que serão retornados
	 * @param	string	$order	nome da coluna a ser ordenada
	 * @return	object			retorna a própria instância da classe Datasource
	 */
	abstract public function orderByDesc($order);
	
	/**
	 * Define um limite máximo de itens a serem retornados
	 * @param	int	$n	valor do limite
	 * @param	int	$o	valor do offset
	 * @return	object	retorna a própria instância da classe Datasource
	 */
	abstract public function limit($n, $o = null);
	
	/**
	 * Define a posição em que os resultados iniciam
	 * @param	int	$n	valor da posição
	 * @return	object	retorna a própria instância da classe Datasource
	 */
	abstract public function offset($n);
	
	/**
	 * Define que os resultados serão distintos
	 * @return	object	retorna a própria instância da classe Datasource
	 */
	abstract public function distinct();
	
	/**
	 * Agrupa por colunas (cláusula GROUP BY)
	 * @param	mixed	$value1			nome de uma coluna
	 * @param	mixed	$valueN			nome da x coluna
	 * @return	object					retorna a própria instância da classe Datasource 
	 */
	abstract public function groupBy();
	
	/**
	 * Gerar e retorna o SQL da consulta
	 * @return	string	retorna o SQL gerado
	 */
	abstract public function getSQL();
	
	/**
	 * Monta a instrunção SQL a partir da operações chamadas e executa a instrução
	 * @return	array				retorna um array com instâncias do Model
	 */
	abstract public function all();
	
	/**
	 * Monta a instrução SQL a partir das operações chamadas e executa a instrução
	 * @return	object	retorna uma instância do Model com os valores preenchidos de acordo com o banco
	 */
	abstract public function single();
	
	/**
	 * Monta a instrução SQL com paginação de resultado, executa a instrução
	 * @param	int	$p		o número da página que quer listar os resultados (começa com zero)
	 * @param	int	$m		quantidade máxima de itens por página
	 * @return	object		retorna um objeto com as propriedade Data (contendo um array com os resultados) e Count (contento a quantidade total de resultados)
	 */
	abstract public function paginate($p, $m);
	
	/**
	 * Calcula quantos resultados existem na tabela aplicando as regras dos métodos chamados anteriormente
	 * @param	string	$column		coluna a ser verifica a quantidade
	 * @return	int		retorna a quantidade
	 */
	abstract public function count($column = null);
	
	/**
	 * Calcula a soma de todos os valores da coluna expecificada
	 * @param	string	$column		coluna a ser somada
	 * @return	double				retorna a soma dos valores de cada linha
	 */
	abstract public function sum($column);
	
	/**
	 * Calcula o maior valor de uma coluna expecifica
	 * @param	string	$column		nome da coluna a ser calculada
	 * @return	double				retorna o maior valor	
	 */
	abstract public function max($column);
	
	/**
	 * Calcula o menor valor de uma coluna expecifica
	 * @param	string	$column		nome da coluna a ser calculada
	 * @return	double				retorna o menor valor
	 */
	abstract public function min($column);
	
	/**
	 * Calcula a média de uma coluna expecifica, somando todos os valores dessa coluna e divindo pela quantidade de linhas existentes
	 * @param	string	$column		nome da coluna a ser calculada
	 * @return	double				retorna a média calculada
	 */
	abstract public function avg($column);
	
	/**
	 * Cria uma instrução SQL de inserção no banco
	 * @param	Model	$model		model a ser inserido
	 * @return	void
	 */
	abstract public function insert(Model $model);
	
	/**
	 * Cria uma instrução SQL de atualização no banco
	 * @param	Model	$model		model a ser atualizado
	 * @return	void
	 */
	abstract public function update(Model $model);
	
	/**
	 * Cria uma instrução SQL de deleção no banco
	 * @param	Model	$model		model a ser deletado
	 * @return	void
	 */
	abstract public function delete(Model $model);
	
	/**
	 * Cria uma instrução SQL de deleção no banco
	 * @return	void
	 */
	abstract public function deleteAll();
	
	/**
	 * Pega o ID da ultima instrunção de um model específico
	 * @return	int		retorna o valor do ID
	 */
	abstract public function lastInsertId();
	
	/**
	 * Submete para o banco de dados as operações realizadas no model
	 * @return	void
	 */
	abstract public function save();
	
	/**
	 * Executa uma instrução SQL sem a utilização de Model
	 * @param	string	$sql	comando de acordo com o banco informado
	 * @return	mixed			
	 */
	abstract public function query($sql);
	
	/**
	 * Inicia uma transação
	 * @return	void
	 */
	abstract public function transaction();
	
	/**
	 * Envia a transação
	 * @return	void
	 */
	abstract public function commit();
	
	/**
	 * Cancela uma transação
	 * @return	void
	 */
	abstract public function rollback();
	
	/**
	 * Define que a consulta será realizada primeiro em cache
	 * @return	Datasource	retorna a própria instância da classe Datasource
	 */
	abstract public function cache($time = 10);
}