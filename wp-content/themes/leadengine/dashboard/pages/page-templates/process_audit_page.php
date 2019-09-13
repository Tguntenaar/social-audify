<?php
/**
 * Template Name: process audit page
 */
?>
<?php


// Post handling
if (isset($_POST['iframe'])) {
  $audit->update('video_iframe', base64_encode($_POST['iframe']), 'Audit_template');
}

$post_names =  ['introduction', 'conclusion', 'facebook_advice', 
                'instagram_advice','website_advice', 'facebook_score', 
                'instagram_score', 'website_score'];

foreach ($post_names as $post_name) {
  if (isset($_POST[$post_name])) {
    $audit->update($post_name, $_POST[$post_name], 'Audit_template');
  }
}


if (isset($_POST['followers_count']) || isset($_POST['avgEngagement']) ||
    isset($_POST['postsLM']) || isset($_POST['follows_count']) ||
    isset($_POST['averageLikes']) || isset($_POST['averageComments'])) {
    
    if (isset($_POST['followers_count'])) {
      $audit->instagram_data->followers_count = $_POST['followers_count'];
    }

    if (isset($_POST['avgEngagement'])) {
      $audit->instagram_data->avgEngagement = $_POST['avgEngagement'];
    }

    if (isset($_POST['postsLM'])) {
      $audit->instagram_data->postsLM = $_POST['postsLM'];
    }

    if (isset($_POST['follows_count'])) {
      $audit->instagram_data->follows_count = $_POST['follows_count'];
    }

    if (isset($_POST['averageComments'])) {
      $audit->instagram_data->averageComments = $_POST['averageComments'];
    }

    if (isset($_POST['averageLikes'])) {
      $audit->instagram_data->averageLikes = $_POST['averageLikes'];
    }

    $audit->update('instagram_data', json_encode($audit->instagram_data), 'Audit_data', 0);
}

if (isset($_POST['comp-followers_count']) || isset($_POST['comp-avgEngagement']) ||
    isset($_POST['comp-postsLM']) || isset($_POST['comp-follows_count']) ||
    isset($_POST['comp-averageLikes']) || isset($_POST['comp-averageComments'])) {

    if (isset($_POST['comp-followers_count'])) {
      $audit->competitor->instagram_data->followers_count = $_POST['comp-followers_count'];
    }

    if (isset($_POST['comp-avgEngagement'])) {
      $audit->competitor->instagram_data->avgEngagement = $_POST['comp-avgEngagement'];
    }

    if (isset($_POST['comp-postsLM'])) {
      $audit->competitor->instagram_data->postsLM = $_POST['comp-postsLM'];
    }

    if (isset($_POST['comp-follows_count'])) {
      $audit->competitor->instagram_data->follows_count = $_POST['comp-follows_count'];
    }

    if (isset($_POST['comp-averageComments'])) {
      $audit->competitor->instagram_data->averageComments = $_POST['comp-averageComments'];
    }

    if (isset($_POST['comp-averageLikes'])) {
      $audit->competitor->instagram_data->averageLikes = $_POST['comp-averageLikes'];
    }

    $audit->update('instagram_data', json_encode($audit->competitor->instagram_data), 'Audit_data', 1);
}
?>