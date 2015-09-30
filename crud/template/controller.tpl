<?php

class __CONTROLLER__Controller extends AppController
{
	public function index($p = 1)
	{
		$q = Request::get('q');
		$m = 30;
		$__model__s = __MODEL__::search($p, $m);
		$this->_set('m', $m);
		return $this->_view($__model__s);
	}

	public function add()
	{
		$__model__ = new __MODEL__();
		if(Request::isPost())
		{
			try
			{
				$this->_data($__model__);
				$__model__->save();

				$this->_success('Operação realizada com sucesso.');
				$this->_redirect('~/__model__');
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
		return $this->_view($__model__);
	}

	public function edit($id)
	{
		$__model__ = __MODEL__::single(array(
			'Id' => $id
		));
		if(!$__model__)
			throw new PageNotFoundException('Página não encontrada.');

		if(Request::isPost())
		{
			try
			{
				$this->_data($__model__);
				$__model__->save();

				$this->_success('Operação realizada com sucesso.');
				$this->_redirect('~/__model__');
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
		return $this->_view('add', $__model__);
	}

	public function remove($id)
	{
		$__model__ = __MODEL__::single(array(
			'Id' => $id
		));
		if(!$__model__)
			throw new PageNotFoundException('Página não encontrada.');

		try
		{
			$__model__->delete();
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
		$this->_redirect('~/__model__');
	}
}