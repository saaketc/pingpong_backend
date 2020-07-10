<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tweet $tweet
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $tweet->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $tweet->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Tweets'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="tweets form content">
            <?= $this->Form->create($tweet) ?>
            <fieldset>
                <legend><?= __('Edit Tweet') ?></legend>
                <?php
                    echo $this->Form->control('content');
                    echo $this->Form->control('user_id', ['options' => $users]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
