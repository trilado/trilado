<?php
class {Name}Controller extends Controller
{
	public function admin_index($p = 1, $o = '{Key}', $type = 'DESC')
	{
		${name} = {Name}::all($p, $);
		return $this->_view(${name});
	}
	
	public function admin_add()
	{
		${name} = new {Name}();
		if(Request::isPost())
		{
			${name} = $this->_data(${name});
			
		}
		return $this->_view(${name});
	}
	
	public function admin_edit(${Key})
	{
		${name} = {Name}::get(${Key});
		if(${name})
		{
			if(Request::isPost())
			{
				${name} = $this->_data(${name});

			}
		}
		return $this->_view(${name});
	}
}