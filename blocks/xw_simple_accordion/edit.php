<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Service\UserInterface $uih
 * @var Concrete\Core\Block\View\BlockView $view
 * @var Concrete\Core\Form\Service\Form $form
 * @var array $rows
 * @var string $uniqID
 * @var string $framework
 * @var string $semantic
 */
echo $uih->tabs([
                    ['content', t('Content'), true],
                    ['settings', t('Settings')],
                ]);
?>
<div class="xw-simple-accordion-form  tab-content">
    <div id="content" class="tab-pane show active">
        <div id="xw-simple-accordion-content-form-<?php echo $uniqID ?>">

            <?php if (is_array($rows) && $rows !== []) { ?>
                <h3><?php echo t('Accordion Items') ?></h3>
            <?php } ?>

            <item-list v-slot="slotProps" :items="items" :default-item="defaultItem" @add-item="onAddItem">
                <template>
                    <div class="floating-block-actions">
                        <button type="button" class="btn btn-primary btn-block" @click="slotProps.addNewItem"><?php echo t('Add Item') ?></button>
                    </div>
                    <draggable v-model="items" handle=".drag-handle" v-bind="dragOptions" @start="drag = true" @end="drag = false">
                        <transition-group type="transition" :name="!drag ? 'flip-list' : null">
                            <item class="card xw-item-list__item" v-for="(item, index) in items" :index="index" :key="item.id">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-11">
                                            <div class="btn-group-sm float-end">
                                                <button type="button" class="btn btn-outline-secondary xw-item-list__edit-item xw-item-list__item-expander" data-bs-toggle="collapse" :data-bs-target="`.item--${index}`"><?php echo t('Edit') ?></button>
                                                <button type="button" class="btn btn-danger xw-item-list__remove-item"  @click="slotProps.deleteEvent(index)"><?php echo t('Remove') ?></button>
                                            </div>
                                            <span>{{item.title}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div :class="`collapse item--${index}`">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <?php echo $form->label('', t('Title')) ?>
                                            <?php echo $form->text('', ['name' => 'title[]', 'v-model' => 'item.title']) ?>
                                        </div>

                                        <div class="form-group">
                                            <?php echo $form->label('', t('Description:')) ?>
                                            <rich-text-editor input-name="<?php echo $view->field('description') ?>[]" height="160" v-model="item.description"></rich-text-editor>
                                        </div>

                                        <div class="form-group">
                                            <?php echo $form->label('', t('State')) ?>
                                            <?php echo $form->select($view->field('state') . '[]', ['closed' => t('Closed'), 'open' => t('Open')], ['id' => '', 'v-model' => 'item.state', 'class' => 'bs-select']) ?>
                                        </div>
                                        <input class="xw-item-entry-sort" type="hidden" name="<?php echo $view->field('sortOrder') ?>[]" :value="index"/>
                                    </div>
                                </div>
                            </item>
                        </transition-group>
                    </draggable>
                </template>
            </item-list>
        </div>
    </div>

    <div id="settings" class="tab-pane">
        <fieldset>
            <div class="form-group">
                <?php echo $form->label($view->field('framework'), t('Use Framework Markup')) ?>
                <?php echo $form->select($view->field('framework'), ['' => t('None'), 'bootstrap' => t('Bootstrap')], $framework) ?>
                <div class="help-block">
                    <?php  echo t('If your theme uses the bootstrap framework, then select that. Otherwise, just choose none') ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->label($view->field('semantic'), t('Semantic Tag for Title')) ?>
                <?php echo $form->select($view->field('semantic'), ['h2' => t('Heading %d', 2), 'h3' => t('Heading %d', 3), 'h4' => t('Heading %d', 4), 'p' => t('Paragraph'), 'span' => tc('HTML Element', 'Span')], $semantic) ?>
            </div>
        </fieldset>
    </div>
</div>

<script>
    Concrete.Vue.activateContext('itemList', function (Vue, config){
        new Vue({
            el: '#xw-simple-accordion-content-form-<?php echo $uniqID ?>',
            components: config.components,
            methods: {
                onAddItem($item) {}
            },
            data: {
                drag: false,
                items: <?php echo json_encode($rows, JSON_NUMERIC_CHECK) ?>,
                defaultItem: {
                    get id() {
                        return _.uniqueId('item')
                    },
                    title: '',
                    description: '',
                    state: '',
                }
            },
            computed: {
                dragOptions() {
                    return {
                        animation: 200,
                        disabled: false,
                        ghostClass: 'ghost'
                    };
                }
            }
        })
    });
</script>
