<div class="page-header">
    <span class="fa fa-globe"></span>
    __TITLE__
    <ol class="breadcrumb pull-right">
        <li><a href="~/">Painel</a></li>
        <li class="active">__TITLE__</li>
    </ol>
</div>
<div class="main">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-8">
                    <a href="~/__model__/add" class="btn btn-primary">Adicionar</a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <!-- BEGIN BLOCK_COLUMNS -->
                            <th>{Label}</th>                            
                            <!-- END BLOCK_COLUMNS -->
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($model->Data as $__model__): ?>
                        <tr>
                            <!-- BEGIN BLOCK_FIELDS -->
                            <th><?= $__model__->{Name} ?></th>                            
                            <!-- END BLOCK_FIELDS -->
                            <th>
                                <a href="~/__model/edit/<?= $__model__->{Key} ?>" class="btn btn-default btn-xs">Editar</a>
                            </th>
                            <th>
                                <a href="javascript:;" onclick="Dialog.confirm('Deseja realmente excluir?', function() { window.location = '~/__model__/remove/<?= $__model__->{Key} ?>' })" class="btn btn-danger btn-xs">Excluir</a>
                            </th>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <?= Pagination::create('__model__/index', $model->Count, $p, $m) ?>
        </div>
    </div>
</div>