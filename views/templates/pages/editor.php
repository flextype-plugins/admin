<?php

namespace Flextype;

use Flextype\Component\{I18n\I18n, Registry\Registry, Html\Html, Form\Form};

Themes::template('admin/views/partials/head')->display();

// Create editor form using Form and Html components
echo (
    Html::heading($page_frontmatter_data['title'], 2, ['class' => 'page-heading']).
    Form::open().
    Form::hidden('slug', $page_slug).
    Html::heading(I18n::find('admin_pages_frontmatter', 'admin', Registry::get('site.locale')), 3).
    Form::textarea('frontmatter', $page_frontmatter).
    Html::br().
    Html::heading('Content', 3).
    Form::textarea('editor', $page_content).
    Form::submit('save_page', I18n::find('admin_save', 'admin', Registry::get('site.locale')), ['class' => 'btn btn-black']).
    Form::close()
);

Themes::template('admin/views/partials/footer')->display();
