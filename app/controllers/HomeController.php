<?php
class HomeController extends Controller
{
	public function index()
	{
		return $this->_print('hello world!');
	}
}