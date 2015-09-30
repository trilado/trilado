<?php

class ControllerCommand extends AbstractCommand
{
	public function run(array $args)
	{
		parent::run($args);
		$this->createController();
	}

	protected function createController()
	{
		$model = $this->model;
		$name = $model . 'Controller';
		$file = 'app/controllers/' . $name . '.php';
		
		if(file_exists($file) && !$this->getOption('force')) {
			echo "Controller $name ja existe. Use --force para sobrescreve-lo.\n";
		    exit(1);
		}
		$content = $this->loadTemplate('controller', array(
			'__MODEL__' => $model,
			'__model__' => Inflector::uncamelize($model, '_'),
			'__CONTROLLER__' => $name,
		));

		$success = file_put_contents($file, $content);

		if($success === false) {
			echo "Erro ao criar o arquivo $file.\n";
			exit(1);
		}
		echo "Arquivo $file criado.\n";
	}

	protected function loadTemplate($type, $vars = array())
	{
		$content = file_get_contents('crud/template/' . $type . '.tpl');
		foreach ($vars as $key => $value) {
			$content = str_replace($key, $value, $content);
		}
		return $content;
	}
}