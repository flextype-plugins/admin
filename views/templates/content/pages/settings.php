<div class="row">
    <div class="col-4">
        <div class="form-group">
            <?php
                echo (
                    Form::label('page_visibility', __('admin_pages_visibility'),  ['for' => 'pageTitle']).
                    Form::select('page_visibility', ['visible' => 'visible', 'draft' => 'draft'], $page_visibility, ['class' => 'form-control', 'id' => 'pageTitle'])
                );
            ?>
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            <?php
                echo (
                    Form::label('page_template', __('admin_pages_template'),  ['for' => 'pageTemplate']).
                    Form::select('page_template', $templates, $page_template, ['class' => 'form-control', 'id' => 'pageTemplate'])
                );
            ?>
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            <?php
                echo (
                    Form::label('page_date', __('admin_pages_date'), ['for' => 'pageDate']).
                    Form::input('page_date', $page_date, ['class' => 'form-control', 'id' => 'pageDate'])
                );
            ?>
        </div>
    </div>
</div>
