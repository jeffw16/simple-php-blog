<?php
$blog_name = "My Blog";
$blog_author = "John Doe";
$blog_url = "https://blog.blog/blog.php";
$blog_pg = "blog.php";
$blog_img = "https://blog.blog/pic.png";
$posts_per_page = 5;

require_once('assets/php/Parsedown.php');
$pd = new Parsedown();
$posts = array(
    'postid' => array(
        'title' => 'Post name',
        'date' => new DateTime('20200101'),
    )
);
// load text from Markdown files in blogposts folder
// & in front of $post_data allows for us to update the object originally referenced (i.e. $post) - otherwise this function is useless
foreach ( $posts as $post_id => &$post_data ) {
    $filename = __DIR__ . '/blogposts/' . $post_id . '.md';
    $res = fopen($filename, 'r');
    $post_data['text'] = fread($res, filesize($filename));
    fclose($res);
}
unset($post_id);
unset($post_data);
?>
<style>
    p {
        line-height: 22.5px;
    }
</style>
<meta property="og:title" content="<?php if ( !empty($_GET['post']) ) { echo $posts[$_GET['post']]['title']; } else { echo $blog_name; } ?>" />
<meta property="og:type" content="article" />
<meta property="article:author" content="<?php echo $blog_author; ?>" />
<meta property="article:published_time" content="<?php if ( !empty($_GET['post']) ) { echo $posts[$_GET['post']]['date']->format("Y-m-d\TH:i:sP"); } else { echo date_create()->format("Y-m-d\TH:i:sP"); } ?>" />
<meta property="og:url" content="<?php echo $blog_url; if ( !empty($_GET['post']) ) { echo "?post=" . $_GET['post']; } ?>" />
<meta property="og:image" content="<?php echo $blog_img; ?>" />
<div class="container">
  <div class="row">
    <div class="col-md-2"></div>
    <div class="col-xs-12 col-md-8">
      <div class="page-header"><h1><?php echo $blog_name; ?></h1></div>
      <?php
      if ( !empty($_GET['post']) ) {
          $post_id = $_GET['post'];
          if ( !array_key_exists($post_id, $posts) ) {
              echo "Post not found.";
          } else {
              $post_data = $posts[$post_id];
              ?>
              <h2 id="<?php echo $post_id; ?>"><?php echo $post_data['title']; ?></h2>
              <p class="lead"><?php echo $post_data['date']->format('F j, Y'); ?></p>
              <?php
              echo $pd->text($post_data['text']);
          }
          ?>
          <p><a href="<?php echo $blog_url; ?>">Back to blog</a></p>
          <?php
          unset($post_id);
          unset($post_data);
      } else {
          ?>
          <div class="row">
            <div class="col-sm-4 col-sm-push-8">
              <img src="<?php echo $blog_img; ?>" width="85%">
            </div>
            <div class="col-sm-8 col-sm-pull-4">
              <p>Welcome to my blog!</p>
              <p><a href="#archive">Archives</a></p>
            </div>
          </div>
          <hr />
          <?php
          $count = 0;
          $i = $_GET['start'] > 0 ? $_GET['start'] : 0;
          $post_ids = array_keys($posts);
          // foreach ( $posts as $post_id => &$post_data ) {
          while ( $i < sizeof($posts) && $count < $posts_per_page ) {
            $post_id = $post_ids[$i];
            $post_data = $posts[$post_id];
            ?>
            <h2 id="<?php echo $post_id; ?>"><?php echo $post_data['title']; ?> <small><a href="<?php echo $blog_pg; ?>?post=<?php echo $post_id; ?>"><span class="glyphicon glyphicon-link"></span></a></small></h2>
            <p class="lead"><?php echo $post_data['date']->format('F j, Y'); ?></p>
            <?php
            echo $pd->text($post_data['text']);
            ?>
            <hr />
            <?php
            $count++;
            $i++;
          }
          unset($post_id);
          unset($post_data);
          ?>
          <nav aria-label="Page navigation">
            <ul class="pagination">
              <?php
              $count = 0;
              while ( $count <= sizeof($posts) ) {
                ?>
                <li<?php echo $_GET['start'] == $count ? ' class="active"' : ''; echo '><a'; echo $_GET['start'] != $count ? ' href="' . $blog_pg . '?start=' . $count . '"' : ''; echo '>' . (($count / $posts_per_page) + 1); ?></a></li>
                <?php
                $count += $posts_per_page;
              }
              unset($count);
              ?>
            </ul>
          </nav>
          <hr />
          <h2 id="archive">Archive</h2>
          <ul>
              <?php
              $i = 0;
              while ( $i < sizeof($posts) ) {
                  $post_id = $post_ids[$i];
                  $post_data = $posts[$post_id];
              // foreach ( $posts as $post_id => &$post_data ) {
                  echo "<li><a href=\"blog?post=" . $post_id . "\">" . $post_data['title'] . "</a> (" . $post_data['date']->format('F j, Y') . ")</li>";
                  $i++;
              }
              ?>
          </ul>
          <?php
      }
      ?>
      <a href="#">Back to top</a>
    </div>
    <div class="col-md-2"></div>
  </div>
</div>
<?php require_once('footer_v1.php'); ?>
