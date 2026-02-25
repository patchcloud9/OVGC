<?php
$layout = 'main';
?>

<section class="hero is-dark subpage-hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <i class="fas fa-dollar-sign"></i> <?= e($title ?? 'Rates') ?>
            </h1>
            <p class="subtitle">
                Current green fees and cart rental pricing
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php require BASE_PATH . '/app/Views/partials/messages.php'; ?>

        <?php
            // use data loaded from controller
            $groups = $groups ?? [];
        ?>

        <!-- split 60/40: rules on left, scorecard on right (content managed via admin) -->
        <div class="columns" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <div class="column is-two-thirds">
                <h2 class="title is-4">Golf Course Rules</h2>
                <?php if (!empty($bulletList)): ?>
                    <ul>
                        <?php foreach ($bulletList as $line): ?>
                            <li><?= e($line) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><?= nl2br(e($pageContent['rules_text'] ?? '')) ?></p>
                <?php endif; ?>
            </div>
            <div class="column is-one-third">
                <h2 class="title is-4">Scorecard</h2>
                <?php if (!empty($pageContent['scorecard_path'])): ?>
                    <p>
                        <a href="#" id="view-scorecard" class="button is-link">
                            <i class="fas fa-file-alt"></i> View Scorecard
                        </a>
                    </p>
                    <p class="mt-1">
                        <a href="<?= e($pageContent['scorecard_path']) ?>" download class="button is-primary">
                            <i class="fas fa-file-download"></i> Download Scorecard
                        </a>
                    </p>
                <?php else: ?>
                    <p class="is-size-7 has-text-grey">Scorecard not available.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- modal for scorecard preview -->
        <?php if (!empty($pageContent['scorecard_path'])): ?>
            <div id="scorecard-modal" class="modal">
                <div class="modal-background"></div>
                <div class="modal-content" style="width:90%;height:90%;">
                    <iframe src="<?= e($pageContent['scorecard_path']) ?>" style="width:100%;height:100%;border:0;"></iframe>
                </div>
                <button class="modal-close is-large" aria-label="close"></button>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var open = document.getElementById('view-scorecard');
                    var modal = document.getElementById('scorecard-modal');
                    var closeEls = modal.querySelectorAll('.modal-close, .modal-background');
                    if (open) {
                        open.addEventListener('click', function(e){
                            e.preventDefault();
                            modal.classList.add('is-active');
                        });
                    }
                    closeEls.forEach(function(el){
                        el.addEventListener('click', function(){
                            modal.classList.remove('is-active');
                        });
                    });
                });
            </script>
        <?php endif; ?>

        <div class="columns is-multiline" style="max-width:1000px;margin:2rem auto;text-align:left;">
            <?php foreach ($groups as $group): ?>
                <div class="column is-one-third">
                    <div class="box has-text-centered">
                        <h3 class="title is-4 mt-3"><?= e($group['title']) ?></h3>
                        <?php if (!empty($group['subtitle'])): ?>
                            <p class="subtitle is-6 has-text-grey"><?= e($group['subtitle']) ?></p>
                        <?php endif; ?>
                        <table class="table is-fullwidth is-narrow is-striped">
                            <tbody>
                                <?php foreach ($group['rates'] as $rate): ?>
                                    <tr>
                                        <td><?= e($rate['description']) ?></td>
                                        <td>$<?= number_format($rate['price'],2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (!empty($group['note'])): ?>
                            <p class="subtitle is-6 has-text-grey"><?= e($group['note']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
