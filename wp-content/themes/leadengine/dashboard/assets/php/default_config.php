<?php
  $default_audit_values = "'1', 'Config', '".date('Y-m-d H:i:s')."', '0', '1', '1', '1', '', ";
  $default_audit_template = "'intro', 'conc', 'facebook advice', 'insta advice', 'website advice', '50', '50', '50', '', '#007281', 'English'";
  $default_audit_crawl = "'1', '1', '0', '95/100', '0.8s', '2.6s'";

  $default_facebook_data = [
    "coverPhotoSize" => "100 X 100", "totalPostLastMonth" => 50, "country_page_likes" => 1500,
    "pf_picture_size" => "50 X 50", "location" => 0, "nLink" => 0, "nStatus" => 0, "nPhoto" => 0,
    "nVideo" => 0, "nOffer" => 0, "avgMessageLength" => "69.73", "runningAdds" => 0, 
    "can_post" => 1, "talking_about_count" => 0, "native_videos" => 1
  ];

  $default_instagram_data = [
    "avgEngagement" => 25.75, "postsLM" => 0, "likesPerPost" => [20, 24, 35, 20], "averageComments" => "1.00",
    "averageLikes" => "24.75", "hashtags" => [["#SocialAudify", "#AuditMadeEasy", "HappyAuditing"], [100, 40, 10]],
    "followers_count" => 2500, "follows_count" => 20
  ];
?>
