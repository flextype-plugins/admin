<?php
namespace Flextype;
use Flextype\Component\Http\Http;
use Flextype\Component\Arr\Arr;
?>
<div class="content <?php if(Arr::last(explode("/", Http::getUriString())) == 'edit'): ?> page-editor <?php endif ?>">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
