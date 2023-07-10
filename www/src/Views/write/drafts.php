<?php View::header(true, "Brouillons") ?>
<br><br>
<link rel="stylesheet" href="/css/drafts.css">
<div class="container-lg container-fluid">
    <?php Session::alert("alert_draft_delete") ?>
    <div class="container-fluid container-lg">
        <?php if ($data['count'] > 0) : ?>
        <h2>Brouillons Enregistrés</h2>
        <?php else : ?>
        <h1>0 résultats <a href="<?= URLROOT ?>/write/new" class='h4'>Créer un Brouillon</a></h1>
        <?php endif; ?>
        <br><br>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3" id="drafts-container">
            <?php foreach ($data['drafts'] as $draft) : ?>
            <?php $draft_link = URLROOT . '/write/edit-draft/' . $draft->draft_id;
                $delete_link = URLROOT . '/write/delete-draft/' . $draft->draft_id;
                ?>

            <div class="col mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class='draft-name'><a href="<?= $draft_link ?>"
                                class='text-dark text-decoration-none'><?= ht($draft->draft_name, 40) ?></a></h4>
                        <div class="p-2"></div>
                        <h5 class='mb-3'>
                            <span style='font-weight: 400; font-size: 22px;'>
                                <?= Str::isEmptyStr($draft->title) ? "<i class='fs-6'>Titre</i>" : ht($draft->title, 40) ?>
                            </span>
                        </h5>
                        <?php if (!Str::isEmptyStr($draft->tagline)) : ?>
                        <p class="card-text tagline"><?= ht($draft->tagline, 100) ?></p>
                        <?php endif; ?>

                        <div class="py-2">
                            <small class="text-muted"><span style='font-weight: 500; font-size: 14px;'>Créé le: </span>
                                <?= Str::formatEpoch(strtotime($draft->created_at), "d/m H:i") ?></small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <div class="btn-group">
                                <a class="btn btn-sm btn-outline-danger"
                                    href="<?= $delete_link ?>">&nbsp;Supprimer&nbsp;</a>
                                <a class="btn btn-sm btn-outline-primary " href="<?= $draft_link ?>">Modifier</a>
                            </div>
                            <small class="text-muted"><span style='font-weight: 500; font-size: 14px;'>Dernière
                                    modification:
                                </span> <?= Str::formatEpoch(strtotime($draft->last_updated), "d/m H:i") ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach ?>

        </div>
        <?php if ($data['count'] > 3) : ?>
        <a class="btn btn-primary" style='margin-bottom: -40px!important'
            href="<?= URLROOT ?>/write/load-drafts/<?= $data['last_draft_id'] ?>" id="drafts-more-btn">Charger plus </a>
        <?php endif; ?>
    </div>

</div>
<?= View::formToken($data['last_draft_id'], "last_draft_id") ?>
<?php View::footer(true) ?>