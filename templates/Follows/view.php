<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Follow $follow
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Follow'), ['action' => 'edit', $follow->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Follow'), ['action' => 'delete', $follow->id], ['confirm' => __('Are you sure you want to delete # {0}?', $follow->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Follows'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Follow'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="follows view content">
            <h3><?= h($follow->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($follow->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Follower Id') ?></th>
                    <td><?= $this->Number->format($follow->follower_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Following Id') ?></th>
                    <td><?= $this->Number->format($follow->following_id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
