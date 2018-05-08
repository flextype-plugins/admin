<?php
namespace Flextype;
use Flextype\Component\Http\Http;
?>

<?php View::factory('admin/templates/partials/head')->display(); ?>

<h2 class="page-heading">
    Pages
    <a class="btn pull-right btn-black" href="<?php echo Http::getBaseUrl(); ?>/admin/pages/add" role="button">Create New Page</a>
</h2>

<table class="table">
  <thead>
    <tr>
      <th scope="col">Name</th>
      <th scope="col">Url</th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($pages_list as $page) { ?>
    <tr>
      <td scope="row"><a href="<?php echo Http::getBaseUrl(); ?>/admin/pages/edit?page=<?php echo $page['slug']; ?>"><?php echo $page['title']; ?></a></td>
      <td scope="row"><a href="<?php echo Http::getBaseUrl(); ?>/<?php echo $page['slug']; ?>">/<?php echo $page['slug']; ?></a></td>
      <td scope="row" class="text-right"><a href="<?php echo Http::getBaseUrl(); ?>/admin/pages/delete?page=<?php echo $page['slug']; ?>">Delete</a></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
<?php View::factory('admin/templates/partials/footer')->display(); ?>
