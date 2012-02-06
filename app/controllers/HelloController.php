<?php 
class HelloController extends Controller
{
	public function world()
	{
		return $this->_print('Hello World!');
	}
	
	public function test()
	{
		return $this->_page();
	}
}