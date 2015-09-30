<div class="page-header">
    <span class="fa fa-globe"></span>
    <?= App::$action == 'add' ? 'Adicionar' : 'Editar' ?> __TITLE__
    <ol class="breadcrumb pull-right">
        <li><a href="~/">Painel</a></li>
        <li><a href="~/__model__">__TITLE__</a></li>
        <li class="active"><?= App::$action == 'add' ? 'Adicionar' : 'Editar' ?></li>
    </ol>
</div>
<div class="main">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8">
                    <form role="form" method="post" action="">
                        <div class="row">
                            <!-- BEGIN BLOCK_FIELDS -->
                            <div class="col-sm-6">
                                <?= BForm::input('{Label}', '{Name}', $model->{Name}, array('class' => '{CSS}'), {Required}) ?>
                            </div>
                            <!-- END BLOCK_FIELDS -->

                        </div>
                        <button type="submit" class="btn btn-primary"><span class="fa fa-floppy-o"></span> Salvar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>