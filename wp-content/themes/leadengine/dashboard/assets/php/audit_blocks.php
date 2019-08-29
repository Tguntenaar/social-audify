<?php
  $facebook_blocks = array(
    ["type" => "fb_likes",
      "name" => "Likes",
      "fb_name" => "country_page_likes",
      "desc" => "The likes you have on your page",
      "is_icon" => 0
    ],
    ["type" => "fb_pem",
      "name" => "Post each month",
      "fb_name" => "totalPostLastMonth",
      "desc" => "The amount of post per month",
      "is_icon" => 0
    ],
    // ["type" => "fb_dpp",
    //   "name" => "Dimension profile picture",
    //   "fb_name" => "pf_picture_size",
    //   "desc" => "The dimensions of the profile picture"
    // ],
    ["type" => "fb_apl",
      "name" => "Average post length",
      "fb_name" => "avgMessageLength",
      "desc" => "The average length of a post",
      "is_icon" => 0
    ],
    ["type" => "fb_ntv",
     "name" => "Videos",
     "fb_name" => "native_videos",
     "desc" => "The average length of a post",
     "is_icon" => 0
    ],
    ["type" => "fb_tab",
     "name" => "Talking about page",
     "fb_name" => "talking_about_count",
     "desc" => "The average length of a post",
     "is_icon" => 0
    ],
    ["type" => "fb_cp",
     "name" => "Can post",
     "fb_name" => "can_post",
     "desc" => "The average length of a post",
     "is_icon" => 1
    ],
    ["type" => "fb_loc",
     "name" => "Location",
     "fb_name" => "location",
     "desc" => "The average length of a post",
     "is_icon" => 1
     ]
  );

  $instagram_blocks = array(
    ["type" => "insta_nof",
      "name" => "Followers",
      "ig_name" => "followers_count",
      "desc" => "Number of followers"
    ],
    ["type" => "insta_ae",
      "name" => "Average engagement",
      "ig_name" => "avgEngagement",
      "desc" => "Average engagement"
    ],
    ["type" => "insta_nplm",
      "name" => "Number of post last month",
      "ig_name" => "postsLM",
      "desc" => "Number of post last month"
    ],
    ["type" => "insta_nopf",
      "name" => "Following",
      "ig_name" => "follows_count",
      "desc" => "Number of accounts followed"
    ],
    ["type" => "insta_ac",
      "name" => "Average Comments",
      "ig_name" => "averageComments",
      "desc" => "Number of accounts followed"
    ],
    ["type" => "insta_al",
      "name" => "Average Likes",
      "ig_name" => "averageLikes",
      "desc" => "Number of accounts followed"
    ]
  );

  $website_blocks = array(
    ["type" => "website_pixel",
     "name" => "Facebook Pixel",
     "db_name" => "facebook_pixel",
		 "comp_name" => "fp_c",
     "desc" => "Does the website implement Facebook Pixel"
    ],
    ["type" => "website_ga",
     "name" => "Google Analytics",
     "db_name" => "google_analytics",
		 "comp_name" => "ga_c",
     "desc" => "Does the website utilize Google Analytics"
    ],
    ["type" => "website_googletag",
     "name" => "Google Tagmanager",
     "db_name" => "google_tagmanager",
		 "comp_name" => "gt_c",
     "desc" => "Does the website implement Google Tagmanager"
    ],
    ["type" => "website_mf",
     "name" => "Mobile Friendly",
     "db_name" => "mobile_friendly",
		 "comp_name" => "mf_c",
     "desc" => "Passes the Google Mobile Friendly check"
    ],
    ["type" => "website_lt", // DEZE
     "name" => "Desktop Load Time",
     "db_name" => "load_time",
		 "comp_name" => "lt_c",
     "desc" => "The time to interactive of the desktop website in ms"
    ],
    ["type" => "website_ws", // EN DEZE, NAMEN MOETEN NOG ANDERS
     "name" => "Mobile Load Time",
     "db_name" => "website_size",
		 "comp_name" => "ws_c",
     "desc" => "The time to interactive of the mobile website in ms"
    ],
  );
?>
