<?php

class IgrejaControllerController extends AppController
{
	public function index($p = 1)
	{
		$q = Request::get('q');
		$m = 30;
		$igrejas = Igreja::search($p, $m);
		$this->_set('m', $m);
		return $this->_view($igrejas);
	}

	public function add()
	{
		$igreja = new Igreja();
		if(Request::isPost())
		{
			try
			{
				$this->_data($igreja);
				$igreja->save();

				$this->_success('Operação realizada com sucesso.');
				$this->_redirect('~/igreja');
			}
			catch(ValidationException $e)
			{
				$this->_error($e->getMessage());
			}
			catch(Exception $e)
			{
				$this->_error('Ocorreu um erro e não foi possível salvar.');
			}
		}
		return $this->_view($igreja);
	}

	public function edit($id)
	{
		$igreja = Igreja::single(array(
			'Id' => $id
		));
		if(!$igreja)
			throw new PageNotFoundException('Página não encontrada.');

		if(Request::isPost())
		{
			try
			{
				$this->_data($igreja);
				$igreja->save();

				$this->_success('Operação realizada com sucesso.');
				$this->_redirect('~/igreja');
			}
			catch(ValidationException $e)
			{
				$this->_error($e->getMessage());
			}
			catch(Exception $e)
			{
				$this->_error('Ocorreu um erro e não foi possível salvar.');
			}
		}
		return $this->_view('add', $igreja);
	}

	public function remove($id)
	{
		$igreja = Igreja::single(array(
			'Id' => $id
		));
		if(!$igreja)
			throw new PageNotFoundException('Página não encontrada.');

		try
		{
			$igreja->delete();
			$this->_success('Operação realizada com sucesso.');
		}
		catch(ValidationException $e)
		{
			$this->_error($e->getMessage());
		}
		catch(Exception $e)
		{
			$this->_error('Ocorreu um erro e não foi possível salvar.');
		}
		$this->_redirect('~/igreja');
	}
}