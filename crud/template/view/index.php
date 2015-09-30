<div class="page-header">
	<span class="fa fa-globe"></span>
	Cidades
	<ol class="breadcrumb pull-right">
		<li><a href="~/">Painel</a></li>
		<li class="active">Cidades</li>
	</ol>
</div>
<div class="main">
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="row">
				<div class="col-xs-6 col-sm-7 col-lg-8">
					<a href="~/city/add" class="btn btn-primary">Adicionar</a>
				</div>
				<div class="col-xs-6 col-sm-5 col-lg-4">
					<form method="get" action="~/city" class="form-search">
						<div class="input-group">
							<input type="text" name="q" value="<?= Request::get('q') ?>" class="form-control">
							<span class="input-group-btn">
								<button class="btn btn-default" type="submit">Buscar</button>
							</span>
						</div>
					</form>
				</div>
			</div>
			
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Nome</th>
							<th>UF</th>
							<th>URL</th>
							<th>E-mail</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($model->Data as $city): ?>
						<tr>
							<td class="capitalize">
								<a href="~/city/edit/<?= $city->idcidades ?>"><?= $city->nome_cidade ?></a>
							</td>
							<td><?= $city->estado ?></td>
							<td><?= $city->seo ?></td>
							<td><?= $city->email ?></td>
						</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>
			<?= Pagination::create('city/index', $model->Count, $p, $m) ?>
		</div>
	</div>
</div>